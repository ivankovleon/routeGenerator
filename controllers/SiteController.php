<?php

namespace app\controllers;

use app\models\Map;
use app\models\MapUploadImageForm;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use Imagine\Gd\Imagine;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCustomizeMap($mapId,  $pointx, $pointy)
    {
        $map = Map::findIdentity($mapId);

        return $this->render('customizeMap',['map' => $map, 'pointx' =>$pointx, 'pointy' =>$pointy]);
    }
    public function actionCreateMap(){
        $map = new Map();
        $map->author_id = Yii::$app->user->getId();
        if($map->load(Yii::$app->request->post()) &&  $map->validate()) {
            ini_set('max_execution_time', 300000);
            $image = UploadedFile::getInstance($map, 'file');
            if (!is_null($image)) {
                $ext = end(explode(".", $image->name));
                $map->file_name = Yii::$app->security->generateRandomString().".{$ext}";
                Yii::$app->params['uploadPath'] = Yii::$app->basePath . '/web/uploads/';
                $path = Yii::$app->params['uploadPath']  . $map->file_name;
                $image->saveAs($path);
                if($map->save()) {
                    Yii::$app->getSession()->setFlash('success', 'Крата успешно добавлена');
                }
                $img = imagecreatefromjpeg($path);
                $w = imagesx($img);
                $h = imagesy($img);
                $imgSize = $w*$h;
                $yi = 0;
                $xi = 0;
                $setColor = imagecolorallocate ($img, 0, 0, 0);
                $x1 = 192;
                $y1 = 192;
                $z1 = 64;
//                $x3 = 60;
//                $y3 = 80;
//                $z3 = 200;

                for($i = 0; $i < $imgSize; $i++){
                    if($xi >= $w){$yi++; $xi = 0;}
                    $color = imagecolorat($img, $xi, $yi);
                    $rgb = imagecolorsforindex($img, $color);
                    $r = $rgb['red'];
                    $g = $rgb['green'];
                    $b = $rgb['blue'];
                    $x2 = $r * 0.4124 + $g * 0.3576 + $b * 0.1805;
                    $y2 = $r * 0.2126 + $g * 0.7152 + $b * 0.0722;
                    $z2 = $r * 0.0193 + $g * 0.1192 + $b * 0.9505;
                    $x4 = $r * 0.4124 + $g * 0.3576 + $b * 0.1805;
                    $y4 = $r * 0.2126 + $g * 0.7152 + $b * 0.0722;
                    $z4 = $r * 0.0193 + $g * 0.1192 + $b * 0.9505;
                    $d1 = Sqrt(pow(($x1-$x2), 2)+pow(($y1-$y2), 2)+pow(($z1-$z2), 2));
//                    $d2 = abs(Sqrt(pow(($x3-$x4), 2)+pow(($y3-$y4), 2)+pow(($z3-$z4), 2)));
//                    $compare1 = $g - $r;
                    if($d1 < 100){
//                        $r = 0;
//                        $g = 0;
//                        $b = 0;
                        imagesetpixel ($img, $xi, $yi, $setColor);
                    }
//                    if($d2 < 100){
////                        $r = 0;
////                        $g = 0;
////                        $b = 0;
//                        imagesetpixel ($img, $xi, $yi, $setColor);
//                    }
                    $xi++;
                }


                $routeLength = 800;
                $controlPointsNumber = 12;
                $mapScale = 4000;
                $pixelInSm = 37.79527559;
                $smLength = $mapScale;
                $mLength = $smLength/100;
                $pixelLength = $mLength/$pixelInSm;
                $sectionLengthM = $routeLength/($controlPointsNumber);
                $sectionLengthP = (int) ($sectionLengthM/$pixelLength);
                $firstQuarter = 1;
                $secondQuarter = 2;
                $thirdQuarter = 3;
                $fourthQuarter = 4;

                for($i = 0; $i < $controlPointsNumber; $i++){
                    $sectionLength[$i] = 0;
                }
                for($i = 0; $i < $controlPointsNumber-1; $i++){
                    $sectionLength[$i] = $sectionLengthM - $sectionLengthM/3 + rand(0, $sectionLengthM*2/3);
                }
                for($i = 0; $i < $controlPointsNumber-1; $i++){
                    $sectionLength[$controlPointsNumber] = $sectionLengthM - $sectionLength[$i];
                }
//                $sectionLengthPtoInt = (int) $sectionLengthP;


                $drawRound = imagecolorallocate ($img, 255, 0, 255);
                $pointStartx = 90;
                $pointStarty = 95;
                imagefilledellipse ($img, $pointStartx, $pointStarty, 15, 15, $drawRound);
                for($i = 0; $i < $controlPointsNumber; $i++){
                    $pointx[$i] = 0;
                    $pointy[$i] = 0;
                    $sectionLengthx[$i] = 0;
                    $sectionLengthy[$i] = 0;
                }
                $pointx[0] = $pointStartx;
                $pointy[0] = $pointStarty;

                $a = 0;
                for($i = 1; $i <= $controlPointsNumber; $i++){
                    $sectionLengthx[$i] = rand(0, $sectionLength[$i-1]);
                    $sectionLengthy[$i] = $sectionLength[$i-1] - $sectionLengthx[$i-1];

//                    $quarterRandom = 0;
//                    $quarter = 0;
//                    while($quarter == 1 && $quarterRandom == 3){
//                        $quarterRandom = rand(1, 4);
//                    }
//                    while($quarter == 3 && $quarterRandom == 1){
//                        $quarterRandom = rand(1, 4);
//                    }
//                    while($quarter == 2 && $quarterRandom == 4){
//                        $quarterRandom = rand(1, 4);
//                    }
//                    while($quarter == 4 && $quarterRandom == 2){
//                        $quarterRandom = rand(1, 4);
//                    }

                    $quarterRandom = 0;
                    $quarter = 0;
                    if($pointx[$i-1]+$sectionLengthx[$i-1] > $w){
                        $quarterRandom = rand(2, 3);
                        $quarter = $quarterRandom;
                    } else if($pointx[$i-1]-$sectionLengthx[$i-1] < 0){
                        $quarterRandom = rand(1, 2);
                        if($quarterRandom == 1) {
                            $quarter = $quarterRandom;
                        } else $quarter = 4;
                    } else if($pointy[$i-1]+$sectionLengthy[$i-1] > $h){
                        $quarterRandom = rand(1, 2);
                        $quarter = $quarterRandom;
                    } else if($pointy[$i-1]-$sectionLengthy[$i-1] < 0){
                        $quarterRandom = rand(3, 4);
                        $quarter = $quarterRandom;
                    } else {
                        $quarterRandom = rand(1, 4);
                        $quarter = $quarterRandom;
                    }

                    if($pointx[$i-1]-$sectionLengthx[$i-1] < 0 && $pointy[$i-1]-$sectionLengthy[$i-1] < 0){
                        $quarter = 4;
                    }
                    if($pointx[$i-1]+$sectionLengthx[$i-1] > $w && $pointy[$i-1]-$sectionLengthy[$i-1] < 0){
                        $quarter = 3;
                    }
                    if($pointx[$i-1]-$sectionLengthx[$i-1] < 0 && $pointy[$i-1]+$sectionLengthy[$i-1] > $h){
                        $quarter = 1;
                    }
                    if($pointx[$i-1]+$sectionLengthx[$i-1] > $w && $pointy[$i-1]+$sectionLengthy[$i-1] > $h){
                        $quarter = 2;
                    }
//                    $quarterRandom = rand(1, 4);
//                    $quarter = $quarterRandom;
                    if($quarter == $firstQuarter){
                        $pointx[$i] = $pointx[$i-1]+$sectionLengthx[$i-1];
                        $pointy[$i] = $pointy[$i-1]-$sectionLengthy[$i-1];
                    } else if($quarter == $secondQuarter){
                        $pointx[$i] = $pointx[$i-1]-$sectionLengthx[$i-1];
                        $pointy[$i] = $pointy[$i-1]-$sectionLengthy[$i-1];
                    } else if($quarter == $thirdQuarter){
                        $pointx[$i] = $pointx[$i-1]-$sectionLengthx[$i-1];
                        $pointy[$i] = $pointy[$i-1]+$sectionLengthy[$i-1];
                    } else if($quarter == $fourthQuarter){
                        $pointx[$i] = $pointx[$i-1]+$sectionLengthx[$i-1];
                        $pointy[$i] = $pointy[$i-1]+$sectionLengthy[$i-1];
                    }

                    imagefilledellipse ($img, $pointx[$i], $pointy[$i], 15, 15, imagecolorallocate ($img, 255, 0, 0));
                    imageline($img, $pointx[$i-1], $pointy[$i-1], $pointx[$i], $pointy[$i], imagecolorallocate ($img, 255, 0, 0));
                    $a += 50;
                }
                $k = 1;
                for($i = 1; $i <= $controlPointsNumber; $i++){
                    if($k<10){
                        imagechar($img, 3, $pointx[$i], $pointy[$i], $k, imagecolorallocate ($img, 0, 0, 255));
                    } else {
                        imagechar($img, 3, $pointx[$i], $pointy[$i], $k, imagecolorallocate ($img, 0, 0, 255));
                        imagechar($img, 3, $pointx[$i]+5, $pointy[$i], $k%10, imagecolorallocate ($img, 0, 0, 255));
                    }
                    $k++;
                }


                $newpath = Yii::$app->params['uploadPath'] . 'test4.jpg';
                imagejpeg($img, $newpath);
                //$this->redirect(Url::to(['site/customize-map', 'id' =>  $map->id,'pointx' => $pointx, 'pointy' => $pointy]));
                return $this->actionCustomizeMap($map->id,$pointx,$pointy);
            }
        }
        return $this->render('createMap',['map'=>$map]);
    }

//                    var_R = ( R / 255 )        //R from 0 to 255
//var_G = ( G / 255 )        //G from 0 to 255
//var_B = ( B / 255 )        //B from 0 to 255
//
//if ( var_R > 0.04045 ) var_R = ( ( var_R + 0.055 ) / 1.055 ) ^ 2.4
//else                   var_R = var_R / 12.92
//if ( var_G > 0.04045 ) var_G = ( ( var_G + 0.055 ) / 1.055 ) ^ 2.4
//else                   var_G = var_G / 12.92
//if ( var_B > 0.04045 ) var_B = ( ( var_B + 0.055 ) / 1.055 ) ^ 2.4
//else                   var_B = var_B / 12.92
//
//var_R = var_R * 100
//var_G = var_G * 100
//var_B = var_B * 100
//
////Observer. = 2°, Illuminant = D65
//X = var_R * 0.4124 + var_G * 0.3576 + var_B * 0.1805
//Y = var_R * 0.2126 + var_G * 0.7152 + var_B * 0.0722
//Z = var_R * 0.0193 + var_G * 0.1192 + var_B * 0.9505


    /*
        public function definitionPixels(){
            $img = imagecreatefromjpeg($path);
            $w = imagesx($img);
            $h = imagesy($img);
            $imgSize = $w*$h;
            $yi = 0;
            $xi = 0;
            for($i = 0; $i < $imgSize; $i++){
                if($xi >= $w){$yi++; $xi = 0;}
                $color = imagecolorat($img, $xi, $yi);
                $rgb = imagecolorsforindex($img, $color);
                $r = $rgb['red'];
                $g = $rgb['green'];
                $b = $rgb['blue'];
                $xi++;
            }
        }*/
}
