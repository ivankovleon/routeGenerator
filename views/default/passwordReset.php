<?php
/**
 * Created by PhpStorm.
 * User: Ilya
 * Date: 06.10.2015
 * Time: 0:49
 */

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Reset password';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="defsite-reset-password">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please choose your new password:</p>

    <div class="row">
        <div class="col-lg-5">
            <div class="well opacity">
                <?php $form = ActiveForm::begin(['id' => 'password-reset-form']); ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'reset-button']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>