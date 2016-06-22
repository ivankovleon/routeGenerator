<?php
/**
 * @var $model app\models\Map
 */
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?= Html::beginTag('div', ['class' => 'col-xs-4 col-sm-4 col-md-4 col-lg-4' ,'data' => ['subscription-id' => $model->id]]); ?>
    <div class="well padding-fix">
        <?php
        if ($model->file_name) {
            echo Html::img(Yii::$app->homeUrl . 'uploads/sm_' . $model->file_name, ['alt' => 'avatar', 'class' => 'img-rounded map-preview']);
        } else {
            //echo Html::img(Yii::$app->homeUrl . 'img/avatars/sm_default_avatar.png', ['alt' => 'avatar', 'class' => 'img-rounded post-avatar']);
        }
        ?>
        <h2><strong>
                <?= Html::a($model->name, Url::to(['site/create-route', 'mapId' => $model->id])) ?>
        </strong></h2>

    </div>
<?= Html::endTag('div');?>
