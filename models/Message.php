<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Message extends ActiveRecord
{
    public $from_user;
    public $to_user;
    public $user_answer;

    public static function tableName()
    {
        return 'messages';
    }

    public function attributeLabels()
    {
        return [
            'message_id'    => Yii::t('app', 'ID'),
            'from'          => Yii::t('app', 'From'),
            'to'            => Yii::t('app', 'To'),
            'answer'        => Yii::t('app', 'Answer'),
            'active'        => Yii::t('app', 'Active'),
            'from_user'     => Yii::t('app', 'From user'),
            'to_user'       => Yii::t('app', 'To user'),
            'user_answer'   => Yii::t('app', 'Answer'),
        ];
    }

    public function rules()
    {
        return [
            [ ['from', 'to'], 'required' ]
        ];
    }

    /**
     * Find all invited users
     *
     * @param string $userId
     * @return Message $messages
     */
    public static function findMessagesTo($userId)
    {
        $messages = self::findBySql('
                SELECT u.username AS from_user, m.message_id
                FROM `messages` m
                JOIN `user` u ON m.from = u.id
                WHERE m.active > 0 AND m.answer IS NULL AND m.to = :uid',
            [':uid' => $userId]
        );
        return $messages;
    }

    /**
     * Find all invited users
     *
     * @param string $userId
     * @return Message $messages
     */
    public static function findMessagesFrom($userId)
    {
        $messages = self::findBySql('
                SELECT u.username AS to_user, m.message_id, m.to,
                    CASE
                        WHEN m.answer IS NULL THEN "'. Yii::t('app', 'Not answered yet') . '"
                        WHEN m.answer = 0 THEN "' . Yii::t('app', 'Rejected') . '"
                        ELSE "' . Yii::t('app', 'Confirmed') . '"
                    END user_answer
                FROM `messages` m
                JOIN `user` u ON m.to = u.id
                WHERE m.active > 0 AND m.from = :uid',
            [':uid' => $userId]
        );
        return $messages;
    }

    /**
     * Find message
     *
     * @param string $fromId
     * @param string $toId
     * @return Message $message
     */
    public static function findMessage($fromId, $toId)
    {
        $message = self::find()
            ->where([
                'from' => $fromId,
                'to' => $toId,
                'answer' => false,
                'active' => true
            ])
            ->one();

        return $message;
    }
}