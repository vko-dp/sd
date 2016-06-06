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

    /** äëÿ ğåãèñòğàöèè âèäæåòîâ ó êîòîğûõ åñòü àÿêñ ìåòîäû */
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
     * âñå àÿêñ çàïğîñû áóäóò îáğàáàòûâàòüñÿ â âèäæåòàõ
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
                //--- íåò îáğàáîò÷èêà
                $this->responseData = 'Îòñóòñòâóåò îáğàáîò÷èê àÿêñ çàïğîñà!';
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