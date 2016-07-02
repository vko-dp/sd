<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 05.06.2016
 * Time: 11:21
 */
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

abstract class AjaxController extends Controller {

    const OK_STATUS = 'ok';
    const ERROR_STATUS = 'error';

    public $responseStatus = self::OK_STATUS;
    public $responseData = array();
    public $responseDebug = array();

    /** для регистрации виджетов у которых есть аякс методы */
    abstract protected function _registerAjaxWidgets();

    /**
     * @param $widget
     */
    protected function _registerWidget($widget) {

        $currHandlers = $widget::getAjaxHandlers();
        if($currHandlers && !isset(Yii::$app->params['ajaxWidgets'][$widget])) {
            Yii::$app->params['ajaxWidgets'][$widget] = $currHandlers;
        }
        $includeWidgets = $widget::getRegisterWidgets();
        if($includeWidgets) {
            foreach($includeWidgets as $w) {
                $handlers = $w::getAjaxHandlers();
                if($handlers && !isset(Yii::$app->params['ajaxWidgets'][$w])) {
                    Yii::$app->params['ajaxWidgets'][$w] = $handlers;
                }
            }
        }
    }

    /**
     * все аякс запросы будут обрабатываться в виджетах
     */
    public function actionAjax() {

        $request = Yii::$app->request;
        $this->responseStatus = self::ERROR_STATUS;
        if($request->isAjax) {

            $this->_registerAjaxWidgets();

            $action = $request->getBodyParam('action', false);
            $data = $request->getBodyParam('data', array());

            $widget = false;
            $ajaxWidgets = Yii::$app->params['ajaxWidgets'];
            foreach($ajaxWidgets as $k => $v) {
                if(in_array($action, $v)) {
                    $widget = $k;
                    break;
                }
            }

            if(!$widget) {
                //--- нет обработчика
                $this->responseData = 'Отсутствует обработчик аякс запроса!';
                $this->responseDebug = Yii::$app->params['ajaxWidgets'];
            } else {

                $request->setBodyParams($data);
                $widget::$action($this);
            }

            $response = Yii::$app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $response->data = array(
                'debug' =>  $this->responseDebug,
                'status' => $this->responseStatus,
                'data' => $this->responseData
            );
            $response->send();
        }
    }
}