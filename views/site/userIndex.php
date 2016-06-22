<?php
/**
 * @var \yii\data\ActiveDataProvider $listDataProvider
 */
use yii\widgets\ListView;

?>
<div class="site-index">

    <div class="row">

            <?=
            ListView::widget([
                'dataProvider' => $listDataProvider,
                'layout' => "{items}",
                'itemView' => function ($model, $key, $index, $widget) {
                    return $this->render('_map',['model' => $model]);
                },
            ]);
            ?>
    </div>

</div>