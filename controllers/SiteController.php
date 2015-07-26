<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\ContactForm;
use app\models\UserInfo;
use app\models\Message;
use app\models\Game;
use app\models\Move;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\helpers\Url;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        /*if (Yii::$app->user->isGuest) {
            Yii::$app->language = 'en-US';
        } else {
            Yii::$app->language = Yii::$app->user->identity->language;
        }*/
        if (!Yii::$app->user->isGuest) {
            Yii::$app->language = UserInfo::getUserLanguage(Yii::$app->user->id);
        }

        return true;
    }

    public function actionChangeLanguage($lang)
    {
        $user = UserInfo::findOne(Yii::$app->user->id);
        $user->language = $lang;
        if($user->validate()){
            $user->save();
        }

        return $this->redirect(Url::to(['site/index']));
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(Url::to(['/user/security/login']));
        }

        $userId = Yii::$app->user->id;

        $availableUsers = new ActiveDataProvider([
            'query' => UserInfo::findAvailableUsers($userId),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $messagesFrom = new ActiveDataProvider([
            'query' => Message::findMessagesFrom($userId),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $messagesTo = new ActiveDataProvider([
            'query' => Message::findMessagesTo($userId),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $activeGames = new ActiveDataProvider([
            'query' => Game::findActiveGames($userId),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $previousGames = new ActiveDataProvider([
            'query' => Game::findPreviousGames($userId),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $rating = UserInfo::getUserRating($userId);

        return $this->render('index', [
            'availableUsers' => $availableUsers,
            'messagesTo' => $messagesTo,
            'messagesFrom' => $messagesFrom,
            'activeGames' => $activeGames,
            'previousGames' => $previousGames,
            'rating' => $rating
        ]);
    }

    public function actionInvite($id)
    {
        $userId = Yii::$app->user->id;
        $message = Message::findMessage($userId, $id);

        if(!$message){
            $message = new Message();
            $message->from = $userId;
            $message->to = $id;
        } else {
            $message->answer = null;
        }

        if($message->validate()){
            $message->save();
        }

        return $this->redirect(Url::to(['site/index']));
    }

    public function actionDeleteMessage($id)
    {
        Message::findOne($id)->delete();
        return $this->redirect(Url::to(['site/index']));
    }

    public function actionConfirm($id)
    {
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            $message = Message::findOne($id);
            $message->answer = true;
            $message->active = false;
            if ($message->validate()) {
                $message->save();
            }

            $game = new Game();
            $game->message_id = $message->message_id;
            $game->first_gamer_id = $message->to;
            $game->second_gamer_id = $message->from;
            $game->current = $message->to;
            if ($game->validate()) {
                $game->save();
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->redirect(Url::to(['site/index']));
    }

    public function actionReject($id)
    {
        $message = Message::findOne($id);
        $message->answer = false;
        if ($message->validate()) {
            $message->save();
        }

        return $this->redirect(Url::to(['site/index']));
    }

    public function actionPlay($id, $show = false)
    {
        $game = Game::find()->where(['game_id' => $id])->asArray()->one();
        $moves = Move::find()
            ->select('*, user.username')
            ->innerJoin('user', 'user.id = moves.gamer_id')
            ->where(['game_id' => $id])
            ->orderBy('move_id')
            ->asArray()->all();
        $moves = array_map(function($move) use ($game){
                if($move['gamer_id'] === $game['first_gamer_id']){
                    $move['symbol'] = 'x';
                } else {
                    $move['symbol'] = 'o';
                }
                return $move;
            },
            $moves);

        if($show) {
            $symbol = '';
        } else {
            $symbol = ((int)$game['first_gamer_id'] === Yii::$app->user->id) ? 'x' : 'o';
        }

        return $this->render('game', [
            'game_id' => $game['game_id'],
            'current' => Yii::$app->user->id,
            'moves' => $moves,
            'symbol' => $symbol
        ]);
    }

    public function actionMakeMove()
    {
        $request = Yii::$app->request->post();
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            $game = Game::findOne($request['game_id']);
            if($game->first_gamer_id === (int)$request['gamer_id']){
                $game->previous = $game->first_gamer_id;
                $game->current = $game->second_gamer_id;
            } else {
                $game->previous = $game->second_gamer_id;
                $game->current = $game->first_gamer_id;
            }
            if ($game->validate()) {
                $game->save();
            }

            $move = new Move();
            $move->game_id = (int)$request['game_id'];
            $move->gamer_id = (int)$request['gamer_id'];
            $move->abs = (int)$request['abs'];
            $move->ord = (int)$request['ord'];
            if ($move->validate()) {
                $move->save();
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        $result = $this->checkResult($game->game_id);
        if(empty($result)) {
            set_time_limit(0);

        } else {
            $this->finishGame($game->game_id);
        }

        return Json::encode($result);
    }

    private function checkResult($id)
    {
        $winner = Game::findWinner($id);
        return $winner;
    }

    private function finishGame($id)
    {
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            $game = Game::findOne($id);
            $game->winner = $game->previous;
            if ($game->validate()) {
                $game->save();
            }

            $user = UserInfo::findOne($game->winner);
            $user->rating += 1;
            if ($user->validate()) {
                $user->save();
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
}
