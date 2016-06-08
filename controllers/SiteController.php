<?php

namespace app\controllers;

use app\models\Map;
use app\models\MapUploadImageForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

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


    public function actionCreateMap(){
        $map = new Map();
        $map->author_id = Yii::$app->user->getId();
        if($map->load(Yii::$app->request->post()) &&  $map->validate()) {
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
            }
        }

        return $this->render('createMap',['map'=>$map]);
    }

}
