<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 21.05.2016
 * Time: 19:06
 */
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\ICache;
use yii\web\Response;

class ICacheController extends Controller {

    public function actionIndex() {

        $data = ICache::i()->getImageData();

        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', $data['mimeType']);
        $response->format = Response::FORMAT_RAW;
        $response->stream = fopen($data['path'], 'r');
        $response->send();
    }
}