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
     * @return User $users
     */
    public static function findMessagesTo()
    {
        $userId = Yii::$app->user->id;
        $messages = Message::findBySql('
                SELECT users.username AS from_user, messages.message_id
                FROM messages
                JOIN users ON messages.from = users.id
                WHERE messages.active > 0 AND messages.answer IS NULL AND messages.to = :uid',
            [':uid' => $userId]
        );
        return $messages;
    }

    /**
     * Find all invited users
     *
     * @return User $users
     */
    public static function findMessagesFrom()
    {
        $userId = Yii::$app->user->id;
        $messages = Message::findBySql('
                SELECT users.username AS to_user, messages.message_id, messages.to,
                    CASE
                        WHEN messages.answer IS NULL THEN "'. Yii::t('app', 'Not answered yet') . '"
                        WHEN messages.answer = 0 THEN "' . Yii::t('app', 'Rejected') . '"
                        ELSE "' . Yii::t('app', 'Confirmed') . '"
                    END user_answer
                FROM messages
                JOIN users ON messages.to = users.id
                WHERE messages.active > 0 AND messages.from = :uid',
            [':uid' => $userId]
        );
        return $messages;
    }
}