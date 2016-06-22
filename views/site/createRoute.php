<?php
/**
 *
 * @var $map app\models\Map
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
$this->registerJsFile(
    Yii::$app->homeUrl.'scripts/fabric.min.js',
    [yii\web\View::POS_HEAD]
);
$js_imgUrl = json_encode(Yii::$app->homeUrl . 'uploads/' . $map->file_name);
$js_controllerUrl = json_encode(Url::to(['site/create-route', 'mapId' => $map->id]));
$script = <<< JS
    (function() {
        var canvas = this.__canvas = new fabric.Canvas('c', { selection: false });
        canvas.setWidth(1000);
        canvas.setHeight(1000);

        fabric.Image.fromURL($js_imgUrl, function(img) {
           img.set({originX: 'left', originY: 'top'});
           canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
        });

        function makeCircle(left, top) {
            var c = new fabric.Circle({
                strokeWidth: 3,
                radius: 12,
                fill: '#0f0',
                stroke: '#666'
            });
            c.left = left - c.radius;
            c.top = top - c.radius;
            c.hasControls = c.hasBorders = false;
            return c;
        }

        //canvas.onclick = function(e) {
        //var x = (e.pageX - canvas.offsetLeft);
        //var y = (e.pageY - canvas.offsetTop);
        //event(x, y); // выхов функции действия
         //   canvas.add(makeCircle(x,y));
	    //};
	    var startPoint = null;
        canvas.on('object:moving', function(e) {

        });
        canvas.on('mouse:up', function(e) {
            if(!startPoint){
                var pointer = canvas.getPointer(e.e);
                startPoint = makeCircle(pointer.x,pointer.y);
                canvas.add(startPoint);
            }
        });

        $('#create-route-form').on('click','.generate-route-btn',function (event){
        event.preventDefault();
        if(startPoint) {
            $.ajax({
                data: ({
                    pointsNumber : $('#map-control_points_number').val(),
                    routeLength: $('#map-root_route_length').val(),
                    mapScale: $('#map-map_scale').val(),
                    startPoint: {
                        x: startPoint.left + startPoint.radius,
                        y: startPoint.top + startPoint.radius
                    }
                }),
                type: "post",
                dataType: 'json',
                url: $js_controllerUrl,
                success: function (data) {
                    window.location.replace(data.href);
                }
            });
        }
    });
    })();
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
<div class="create-map">
    <div class="map-form">
        <div class="row" style="padding-bottom: 15px;">
            <div class="col-lg-5">
                <h3 class="page-header" style="margin-top: 10px;">Параметры карты: <?php echo $map->name ?></h3>
                <?php $form = ActiveForm::begin(['id'=>'create-route-form']); ?>
                <?= $form->field($map, 'control_points_number')->textInput(['maxlength' => true]) ?>
                <?= $form->field($map, 'root_route_length')->textInput(['maxlength' => true]) ?>
                <?= $form->field($map, 'map_scale')->textInput(['maxlength' => true]) ?>
                <?= Html::submitButton('генерировать маршрут', ['class' => 'btn btn-primary generate-route-btn']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <canvas id="c"></canvas>
            </div>
        </div>
    </div>
</div>