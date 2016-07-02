<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 12.06.2016
 * Time: 13:58
 */

namespace app\models\ajax;

interface AjaxInterface {

    /** регистрируем подключенные аякс виджеты */
    public static function getAjaxHandlers();
    /** регистрируем аякс обработчики виджета */
    public static function getRegisterWidgets();
}