<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 12.06.2016
 * Time: 15:19
 */

namespace app\modules\cart\widgets;

use Yii;
use yii\base\Widget;
use app\models\ajax\AjaxInterface;

class CartWidget extends Widget implements AjaxInterface {


    /** ������������ ������������ ���� ������� */
    public static function getRegisterWidgets() {
        return array();
    }
    /** ������������ ���� ����������� ������� */
    public static function getAjaxHandlers() {
        return array();
    }

    public function run() {

        //--- ���������� ����� � �������
        $view = $this->getView();
        $view->registerCssFile('@web/css/widgets/cart/top-cart.css');

        return $this->render('cart/index', []);
    }
}