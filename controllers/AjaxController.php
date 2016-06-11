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
     * @param $name
     * @param array $handlers
     */
    protected function _registerWidget($name, array $handlers) {
        if(!isset(Yii::$app->params['ajaxWidgets'][$name])) {
            Yii::$app->params['ajaxWidgets'][$name] = $handlers;
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

                $request->setBodyParams(self::recursiveToWin1251($data));
                $widget::$action($this);
            }

            $response = Yii::$app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $response->data = array(
                'debug' =>  self::recursiveToUtf8($this->responseDebug),
                'status' => $this->responseStatus,
                'data' => self::recursiveToUtf8($this->responseData)
            );
            $response->send();
        }
    }

    /**
     * рекурсивно переводим массив из win-1251 в utf8
     * @param mixed $data
     * @return array|string
     */
    public static function recursiveToUtf8($data) {

        if (is_array($data)) {
            $newData = array();
            foreach($data as $key => $value) {
                $newData[iconv('cp1251', 'UTF-8//IGNORE', $key)] = self::recursiveToUtf8($value);
            }
            return $newData;

        } else if (is_string($data)) {
            return iconv('cp1251', 'UTF-8//IGNORE', $data);
        } else {
            return $data;
        }
    }

    /**
     * рекурсивно переводим массив из utf8 в win-1251
     * @param mixed $data
     * @return array|string
     */
    public static function recursiveToWin1251(&$data){

        if (is_array($data)){

            foreach($data as $key => &$value){

                $newData[self::convertUtfToCp2151($key)] = self::recursiveToWin1251($value);
            }
            unset($value);

            return $data;
        } elseif (is_string($data)){

            return self::convertUtfToCp2151($data);
        } else {

            return $data;
        }
    }

    /**
     * @static
     * @param string $str
     * @return string
     */
    public static function convertUtfToCp2151(&$str){

        $str = html_entity_decode(
            iconv('UTF-8','windows-1251//IGNORE',
                mb_convert_encoding($str,"HTML-ENTITIES", "UTF-8")
            )
            , ENT_COMPAT, "windows-1251");
        return $str;
    }

    public static function utf8_unescape($str) {
        return preg_replace_callback(
            '/\\\\u([0-9a-fA-F]{4})/',
            function ($match) {
                return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
            },
            $str
        );
    }

    static function jsonToArray ($json) {
        return self::recursiveToWin1251( json_decode( self::recursiveToUtf8($json), true ) );
    }

    static function arrayToJson ($array) {
        return self::recursiveToWin1251( self::utf8_unescape( json_encode( self::recursiveToUtf8($array) ) ) );
    }
}