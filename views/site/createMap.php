<?php
/**
 *
 * @var \app\models\Map $map
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\file\FileInput;
?>
<div class="create-map">

    <div class="user-form">
        <div class="row">
            <div class="col-lg-5">
                <div class="well">
                    <h3 class="page-header" style="margin-top: 10px;">Параметры</h3>
                    <?php $form = ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]); ?>

                    <?= $form->field($map, 'name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($map, 'control_points_number')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($map, 'root_route_length')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($map, 'map_scale')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($map, 'file')->widget(FileInput::classname(), [
                        'options' => ['accept' => 'image/*'],
                        'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png']],
                    ])->label("Загрузить карту") ?>

                    <div class="form-group">
                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary'])?>
                    </div>
                    <?php ActiveForm::end(); ?>

                </div>

            </div>
        </div>
    </div>
</div>
