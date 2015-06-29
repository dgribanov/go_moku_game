<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    //public $language = 'en-US';

    public static function tableName()
    {
        return 'users';
    }

    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ID'),
            'username'   => Yii::t('app', 'Username'),
            'password'   => Yii::t('app', 'Password'),
            'auth_key'   => Yii::t('app', 'Auth Key'),
            'rating'     => Yii::t('app', 'Rating'),
            'language'   => Yii::t('app', 'Language'),
        ];
    }

    public function rules()
    {
        return [
            [ 'username', 'required' ],
            [ ['username', ], 'unique' ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Find all available users
     *
     * @return User $users
     */
    public static function findAvailableUsers()
    {
        $userId = Yii::$app->user->id;
        $users = User::find()->where('
                users.id <> :uid AND
                NOT EXISTS(
                    SELECT 1
                    FROM messages
                    WHERE messages.active > 0 AND ((
                        messages.from = :uid AND
                        messages.to = users.id
                        ) OR (
                        messages.from = users.id AND
                        messages.to = :uid
                        ))
                ) AND
                NOT EXISTS(
                    SELECT 1
                    FROM games
                    WHERE games.winner IS NULL AND ((
                        games.first_gamer_id = :uid AND
                        games.second_gamer_id = users.id
                        ) OR (
                        games.first_gamer_id = users.id AND
                        games.second_gamer_id = :uid
                        ))
                )
            ', [':uid' => $userId]);
        return $users;
    }

    /**
     * Find all invited users
     *
     * @return User $users
     */
    public static function findInvitedUsers()
    {
        $userId = Yii::$app->user->id;
        $users = User::find()->where('
                users.id <> :uid AND
                EXISTS(
                    SELECT 1
                    FROM messages
                    WHERE messages.active > 0 AND
                        (messages.from = :uid AND
                        messages.to = users.id)
                )
            ', [':uid' => $userId]);
        return $users;
    }

    /**
     * Find all invitations
     *
     * @return User $users
     */
    public static function findInvitations()
    {
        $userId = Yii::$app->user->id;
        $users = User::find()->where('
                users.id <> :uid AND
                EXISTS(
                    SELECT 1
                    FROM messages
                    WHERE messages.active > 0 AND
                        (messages.from = users.id AND
                        messages.to = :uid)
                )
            ', [':uid' => $userId]);
        return $users;
    }
}
