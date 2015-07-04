<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php
$this->title = 'GameCenter';
?>

<h3 style="float: right;"><?= \Yii::t('app', 'Your rating: {rating}', ['rating' => $rating]) ?></h3>

<h3><?= \Yii::t('app', 'Available Users:') ?></h3>
<div>
<?= GridView::widget([
    'dataProvider' => $availableUsers,
    'showHeader' => true,
    'summary'=>'',
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'username',
        'rating',
        ['class' => 'yii\grid\ActionColumn',
            'buttons' => [
                'invite' => function ($url,$model,$key) { return Html::a(\Yii::t('app', 'Invite this user'), Url::to(['site/invite', 'id' => $model->user_id]));},
            ],
            'header' => \Yii::t('app', 'Action'),
            'template' => '<div>{invite}</div>',
            'headerOptions'=>['class' => 'custom-header'],
        ],
    ]
]);
?>
</div>

<h3><?= \Yii::t('app', 'Your invitations:') ?></h3>
<div>
<?= GridView::widget([
    'dataProvider' => $messagesFrom,
    'showHeader' => true,
    'summary'=>'',
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'to_user',
        'user_answer',
        ['class' => 'yii\grid\ActionColumn',
            'buttons' => [
                'invite' => function ($url,$model,$key) {
                    if($model->user_answer === \Yii::t('app', 'Rejected')){
                        return Html::a(\Yii::t('app', 'Invite again'), Url::to(['site/invite', 'id' => $model->to]));
                    } else {
                        return \Yii::t('app', 'Wait for answer');
                    }
                },
                'delete' => function ($url,$model,$key) { return Html::a(\Yii::t('app', 'Delete'), Url::to(['site/delete-message', 'id' => $model->message_id]));},
            ],
            'header' => \Yii::t('app', 'Action'),
            'template' => '<div>{invite} | {delete}</div>',
            'headerOptions'=>['class' => 'custom-header'],
        ],
    ]
]);
?>
</div>

<h3><?= \Yii::t('app', 'Invitations to you:') ?></h3>
<div>
    <?= GridView::widget([
        'dataProvider' => $messagesTo,
        'showHeader' => true,
        'summary'=>'',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'from_user',
            ['class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'yes' => function ($url,$model,$key) { return Html::a(\Yii::t('app', 'Yes'), Url::to(['site/confirm', 'id' => $model->message_id]));},
                    'no' => function ($url,$model,$key) { return Html::a(\Yii::t('app', 'No'), Url::to(['site/reject', 'id' => $model->message_id]));},
                ],
                'header' => \Yii::t('app', 'Action'),
                'template' => '<div>{yes} | {no}</div>',
                'headerOptions'=>['class' => 'custom-header'],
            ],
        ]
    ]);
    ?>
</div>

<h3><?= \Yii::t('app', 'Your active games:') ?></h3>
<div>
    <?= GridView::widget([
        'dataProvider' => $activeGames,
        'showHeader' => true,
        'summary'=>'',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'user1',
            'user2',
            'current_name',
            ['class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'play' => function ($url,$model,$key) {
                            if($model->current == \Yii::$app->user->id){
                                return Html::a(\Yii::t('app', 'Play'), Url::to(['site/play', 'id' => $model->game_id]));
                            } else {
                                return \Yii::t('app', 'Not your turn');
                            }
                        },
                ],
                'header' => \Yii::t('app', 'Action'),
                'template' => '<div>{play}</div>',
                'headerOptions'=>['class' => 'custom-header'],
            ],
        ]
    ]);
    ?>
</div>

<h3><?= \Yii::t('app', 'Your previous games:') ?></h3>
<div>
    <?= GridView::widget([
        'dataProvider' => $previousGames,
        'showHeader' => true,
        'summary'=>'',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'user1',
            'user2',
            'winner_name',
            ['class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'show' => function ($url,$model,$key) {return Html::a(\Yii::t('app', 'Show'), Url::to(['site/play', 'id' => $model->game_id, 'show' => true]));},
                ],
                'header' => \Yii::t('app', 'Action'),
                'template' => '<div>{show}</div>',
                'headerOptions'=>['class' => 'custom-header'],
            ],
        ]
    ]);
    ?>
</div>
