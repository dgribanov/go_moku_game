<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Game extends ActiveRecord
{
    public $user1;
    public $user2;
    public $current_name;
    public $winner_name;

    public static function tableName()
    {
        return 'games';
    }

    public function attributeLabels()
    {
        return [
            'game_id'           => Yii::t('app', 'ID'),
            'first_gamer_id'    => Yii::t('app', 'First player'),
            'second_gamer_id'   => Yii::t('app', 'Second player'),
            'message_id'        => Yii::t('app', 'Message'),
            'current'           => Yii::t('app', 'Next turn'),
            'previous'          => Yii::t('app', 'Previous turn'),
            'user1'             => Yii::t('app', 'First player'),
            'user2'             => Yii::t('app', 'Second player'),
            'current_name'      => Yii::t('app', 'Next turn'),
            'winner_name'       => Yii::t('app', 'Winner'),
        ];
    }

    /**
     * Find all active games
     *
     * @return User $users
     */
    public static function findActiveGames()
    {
        $userId = Yii::$app->user->id;
        $games = Game::findBySql('
                SELECT u1.username AS user1, u2.username AS user2, cur.username AS current_name, g.game_id, g.current
                FROM `games` g
                JOIN `users` u1 ON g.first_gamer_id = u1.id
                JOIN `users` u2 ON g.second_gamer_id = u2.id
                JOIN `users` cur ON g.current = cur.id
                WHERE g.winner IS NULL AND :uid IN (g.first_gamer_id, g.second_gamer_id)',
            [':uid' => $userId]
        );
        return $games;
    }

    /**
     * Find all previous games
     *
     * @return User $users
     */
    public static function findPreviousGames()
    {
        $userId = Yii::$app->user->id;
        $games = Game::findBySql('
                SELECT u1.username AS user1, u2.username AS user2, uw.username AS winner_name, g.game_id
                FROM `games` g
                JOIN `users` u1 ON g.first_gamer_id = u1.id
                JOIN `users` u2 ON g.second_gamer_id = u2.id
                JOIN `users` uw ON g.winner = uw.id
                WHERE g.winner IS NOT NULL AND :uid IN (g.first_gamer_id, g.second_gamer_id)',
            [':uid' => $userId]
        );
        return $games;
    }

    public function findWinner($id)
    {
        $result = '';
        $winner_name = Game::findBySql('
            SELECT u.username AS winner_name
            FROM `games` g
            JOIN `users` u ON g.previous = u.id
            WHERE g.game_id = :id AND EXISTS (
                SELECT *
                FROM `moves` m1
                WHERE m1.game_id = g.game_id
                GROUP BY m1.abs
                HAVING COUNT(*) = 3 AND COUNT(DISTINCT m1.gamer_id) = 1
                UNION
                SELECT *
                FROM `moves` m2
                WHERE m2.game_id = g.game_id
                GROUP BY m2.ord
                HAVING COUNT(*) = 3 AND COUNT(DISTINCT m2.gamer_id) = 1
                UNION
                SELECT *
                FROM `moves` m3
                WHERE m3.game_id = g.game_id AND m3.ord = m3.abs
                GROUP BY m3.gamer_id
                HAVING COUNT(*) = 3
                UNION
                SELECT *
                FROM `moves` m4
                WHERE m4.game_id = g.game_id AND (m4.ord + m4.abs) = 4
                GROUP BY m4.gamer_id
                HAVING COUNT(*) = 3
            )', [':id' => $id]
        )->asArray()->one();

        if(!empty($winner_name)){
            $result = Yii::t('app', 'You win {winner_name} ! Yor rating +1 !',
                ['winner_name' => $winner_name['winner_name']]);
        }

        return $result;
    }
}