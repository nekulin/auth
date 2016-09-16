<?php
/* @var $this yii\web\View */
/* @var $user \app\models\User */
/* @var $model \app\models\ProfileForm */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\widgets\DetailView;

$this->title = 'Личная информация';
?>

<h1>Личные данные</h1>

<?= DetailView::widget([
    'model' => $user,
    'attributes' => [
        'username',
        'email',
    ],
]) ?>

<?php $form = ActiveForm::begin([
    'id' => 'profile-form'
]); ?>
<div class="row">
    <div class="col-lg-5">
        <?= $form->field($model, 'username')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>





