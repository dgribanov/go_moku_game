<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => Yii::t('app', 'Go-Moku Game'),
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => Yii::t('app', 'GameCenter'), 'url' => ['/site/index']],
                    ['label' => Yii::t('app', 'About'), 'url' => ['/site/about']],
                    Yii::$app->user->isGuest ?
                        ['label' => Yii::t('app', 'Login'), 'url' => ['/site/login-form']] :
                        ['label' => Yii::t('app', 'Logout ({username})',
                            ['username' => Yii::$app->user->identity->username]),
                            'url' => ['/site/logout'],
                            'linkOptions' => ['data-method' => 'post']],
                        ['label' => Yii::t('app', 'Eng'), 'url' => ['/site/change-language', 'lang' => 'en-US']],
                        ['label' => Yii::t('app', 'Ру'), 'url' => ['/site/change-language', 'lang' => 'ru-RU']],
                ],
            ]);
            NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; My Company <?= date('Y') ?></p>
            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
