<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to login:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['id' => 'username']) ?>

    <?= $form->field($model, 'password')->passwordInput(['id' => 'password']) ?>

    <?= $form->field($model, 'rememberMe', [
        'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
    ])->checkbox(['id' => 'rememberMe']) ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::a(
                'Login', '#',
                [
                    'class' => 'btn btn-primary',
                    'onclick' => 'submit("'. Url::to(["site/login"]) . '");'
                ]
            )
            ?>
            <?= Html::a(
                'Sign in', '#',
                [
                    'class' => 'btn btn-primary',
                    'onclick' => 'submit("'. Url::to(["site/register"]) . '");'
                ]
            )
            ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
    $this->registerJs('
        function submit(url){
            var username = $("#username").val();
                var password = $("#password").val();
                var rememberMe = $("#rememberMe").val();
                $.post(url, {
                        username: username,
                        password: password,
                        rememberMe: rememberMe
                    }
                ).success(function(data){
                            var data = jQuery.parseJSON(data);
                            if(data.length == 0){
                                window.location = "' . Url::to(["site/index"], true) . '"
                            }
                        });
        }', View::POS_END);
?>
