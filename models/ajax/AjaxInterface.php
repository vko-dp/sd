<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 12.06.2016
 * Time: 13:58
 */

namespace app\models\ajax;

interface AjaxInterface {

    /** ������������ ������������ ���� ������� */
    public static function getAjaxHandlers();
    /** ������������ ���� ����������� ������� */
    public static function getRegisterWidgets();
}