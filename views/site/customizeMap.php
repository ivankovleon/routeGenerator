
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

        function makeCircle(left, top, line1, line2, num) {
            var c = new fabric.Circle({
                left: left,
                top: top,
                strokeWidth: 2,
                radius: 8,
                fill: '#fff',
                stroke: '#666'
            });
            c.hasControls = c.hasBorders = false;

            c.line1 = line1;
            c.line2 = line2;
            c.num = num;
            if(c.num) {
                c.num.circle = c;
            }


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
        function makeNumber(num,left,top) {
            var n = new fabric.Text(num.toString(), {
                  fontSize: 12,
                  left: left,
                  top: top+1,
                  fontWeight: 'bold',
                  hasBorders: false,
                  hasControls: false
            });

            n.circle = null;
            return n;
        }
        function makeRoute() {

            var lines = [];
            for(var i = 0;i < pointX.length-1;i++) {
                lines.push(makeLine([pointX[i], pointY[i], pointX[i+1], pointY[i+1]]));
                canvas.add(lines[i]);
            }

            var firstNum = makeNumber(1, pointX[0],pointY[0]);
            canvas.add(makeCircle(pointX[0], pointY[0], null,lines[0],firstNum));
            canvas.add(firstNum);

            for(var i = 1;i < pointX.length-1;i++) {
                var num = makeNumber(i+1, pointX[i],pointY[i]);
                canvas.add(makeCircle(pointX[i], pointY[i], lines[i-1],lines[i],num));
                canvas.add(num);
            }
            var lastNum = makeNumber(pointX.length, pointX[pointX.length-1],pointY[pointY.length-1]);
            canvas.add(makeCircle(pointX[pointX.length-1], pointY[pointY.length-1], lines[lines.length-1],null,lastNum));
            canvas.add(lastNum);
        }

        makeRoute();


        canvas.on('object:moving', function(e) {
            var p = e.target;

            if(p.circle) {
                p.circle.line1 && p.circle.line1.set({ 'x2': p.left, 'y2': p.top });
                p.circle.line2 && p.circle.line2.set({ 'x1': p.left, 'y1': p.top });
                p.circle && p.circle.set({ 'left': p.left, 'top': p.top-2 });
            } else {
                p.line1 && p.line1.set({ 'x2': p.left, 'y2': p.top });
                p.line2 && p.line2.set({ 'x1': p.left, 'y1': p.top });
                p.num && p.num.set({ 'left': p.left, 'top': p.top+2 });
            }
            canvas.renderAll();
        });


         $('#row').on('click','#download',function (event){

         });

         function downloadCanvas(link, canvasId, filename) {
            link.href = document.getElementById(canvasId).toDataURL();
            link.download = filename;
         }

        document.getElementById('download').addEventListener('click', function() {
            downloadCanvas(this, 'c', 'test.png');
        }, false);

    })();
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
<div class="row">
    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="margin-bottom: 15px">
        <a class="btn btn-primary" href="#" id="download">Скачать</a>
    </div>

</div>
<canvas id="c"></canvas>
