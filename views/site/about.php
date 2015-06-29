<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = Yii::t('app', 'About Go-Moku');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Yii::t('app', 'Go-Moku is ancient chinese game.') ?>
    </p>
</div>
