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
use app\modules\cart\models\Order;

class CartWidget extends Widget implements AjaxInterface {


    /** регистрируем подключенные аякс виджеты */
    public static function getRegisterWidgets() {
        return array();
    }
    /** регистрируем аякс обработчики виджета */
    public static function getAjaxHandlers() {
        return array();
    }

    public function run() {

//        $cookies = Yii::$app->response->cookies;
//        $cookies->add(new \yii\web\Cookie([
//            'name' => 'current_order_sid',
//            'value' => uniqid(),
//        ]));

        //--- получаем данные для корзины
        $tblOrder = new Order();
        $orderData = $tblOrder->getCurrentOrder();

        Yii::$app->params['addAjaxWidgetData']([
            'Cart' => [
                'orderId' => isset($orderData['id']) ? $orderData['id'] : 0,
                'totalItems' => isset($orderData['howmany']) ? (int)$orderData['howmany'] : 0,
                'sum' => isset($orderData['summa']) ? round($orderData['summa'], 2) : 0,
            ]
        ]);

        return $this->render('cart/index', $orderData);
    }
}