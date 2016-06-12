<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 12.06.2016
 * Time: 15:14
 */
namespace app\models\ajax;

use yii\base\Module;

abstract class AjaxModule extends Module {

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
}