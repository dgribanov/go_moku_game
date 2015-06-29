<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Move extends ActiveRecord
{
    public static function tableName()
    {
        return 'moves';
    }

    public function attributeLabels()
    {
        return [
            'move_id'     => Yii::t('app', 'ID'),
            'game_id'     => Yii::t('app', 'Game ID'),
            'gamer_id'    => Yii::t('app', 'First player'),
            'abs'         => '',
            'ord'         => '',
        ];
    }

    public function safeAttributes()
    {
        return ['game_id', 'gamer_id', 'abs', 'ord'];
    }
}