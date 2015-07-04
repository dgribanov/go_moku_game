<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class UserInfo extends ActiveRecord
{
    public $username;

    public static function tableName()
    {
        return 'user_info';
    }

    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ID'),
            'username'   => Yii::t('app', 'Username'),
            'rating'     => Yii::t('app', 'Rating'),
            'language'   => Yii::t('app', 'Language'),
        ];
    }

    /**
     * Get user language
     *
     * @param string $userId
     * @return string $language
     */
    public static function getUserLanguage($userId)
    {
        $language = self::findOne($userId)->language;
        return $language;
    }

    /**
     * Get user rating
     *
     * @param string $userId
     * @return string $rating
     */
    public static function getUserRating($userId)
    {
        $rating = self::findOne($userId)->rating;
        return $rating;
    }

    /**
     * Find all available users
     *
     * @param string $userId
     * @return UserInfo $users
     */
    public static function findAvailableUsers($userId)
    {
        $users = self::findBySql('
                SELECT u.username, ui.rating, ui.user_id
                FROM `user_info` ui
                JOIN `user` u ON u.id = ui.user_id
                WHERE ui.user_id <> :uid AND
                NOT EXISTS(
                    SELECT 1
                    FROM `messages`
                    WHERE messages.active > 0 AND ((
                        messages.from = :uid AND
                        messages.to = ui.user_id
                        ) OR (
                        messages.from = ui.user_id AND
                        messages.to = :uid
                        ))
                ) AND
                NOT EXISTS(
                    SELECT 1
                    FROM `games`
                    WHERE games.winner IS NULL AND ((
                        games.first_gamer_id = :uid AND
                        games.second_gamer_id = ui.user_id
                        ) OR (
                        games.first_gamer_id = ui.user_id AND
                        games.second_gamer_id = :uid
                        ))
                )
            ', [':uid' => $userId]);
        return $users;
    }
}
