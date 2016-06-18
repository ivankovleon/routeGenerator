
<?php
/**
 * @var $map app\models\Map
 * @var $pointx
 * @var $pointy
 */
use yii\helpers\Html;

$this->registerJsFile(
    Yii::$app->homeUrl.'scripts/fabric.min.js',
    [yii\web\View::POS_HEAD]
);
$js_pointx = json_encode($pointx);
$js_pointy = json_encode($pointy);
$js_imgUrl = json_encode(Yii::$app->homeUrl . 'uploads/' . $map->file_name);
$script = <<< JS
    (function() {
        var canvas = this.__canvas = new fabric.Canvas('c', { selection: false });
        canvas.setWidth(1000);
        canvas.setHeight(1000);

        fabric.Image.fromURL($js_imgUrl, function(img) {
           img.set({originX: 'left', originY: 'top'});
           canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
        });

        var pointX = $js_pointx;
        var pointY = $js_pointy;
        fabric.Object.prototype.originX = fabric.Object.prototype.originY = 'center';

        function makeCircle(left, top, line1, line2) {
            var c = new fabric.Circle({
                left: left,
                top: top,
                strokeWidth: 2,
                radius: 6,
                fill: '#fff',
                stroke: '#666'
            });
            c.hasControls = c.hasBorders = false;

            c.line1 = line1;
            c.line2 = line2;

            return c;
        }

        function makeLine(coords) {
            return new fabric.Line(coords, {
                fill: 'red',
                stroke: 'red',
                strokeWidth: 2,
                selectable: false
            });
        }

        function makeRoute() {

            var lines = [];
            for(var i = 0;i < pointX.length-1;i++) {
                lines.push(makeLine([pointX[i], pointY[i], pointX[i+1], pointY[i+1]]));
                canvas.add(lines[i]);
            }
            canvas.add(makeCircle(pointX[0], pointY[0], null,lines[0]));

            for(var i = 1;i < pointX.length-1;i++) {
                canvas.add(makeCircle(pointX[i], pointY[i], lines[i-1],lines[i]));
            }
            canvas.add(makeCircle(pointX[pointX.length-1], pointY[pointX.length-1], lines[lines.length-1]));
        }

        makeRoute();


        canvas.on('object:moving', function(e) {
            var p = e.target;
            p.line1 && p.line1.set({ 'x2': p.left, 'y2': p.top });
            p.line2 && p.line2.set({ 'x1': p.left, 'y1': p.top });
            p.line3 && p.line3.set({ 'x1': p.left, 'y1': p.top });
            p.line4 && p.line4.set({ 'x1': p.left, 'y1': p.top });
            canvas.renderAll();
        });
    })();
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>

<canvas id="c"></canvas>
