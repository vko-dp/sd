<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 08.05.2016
 * Time: 10:19
 */
namespace app\modules\product\controllers;

use Yii;
use app\controllers\AjaxController;
use app\modules\product\widgets\ProductWidget;
use app\models\ICache;

class DefaultController extends AjaxController {

    const LIMIT = 20;

    /**
     * регистрируем виджеты обрабатывающие аякс запросы
     */
    protected function _registerAjaxWidgets() {
        $this->_registerWidget('app\modules\product\widgets\ProductWidget');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex() {

        $view = Yii::$app->getView();
        $view->title = 'Товары';
        $view->params['breadcrumbs'][] = $view->title;

        //--- фильтры и сортировка
        $params = array();

        //--- опыты  с имагиком
        //--- записываем источник
//        ICache::i()->writeSource(3093, 'position', Yii::getAlias('@webroot/iCache/fsdfasdfds.jpeg'));

        /** @var ProductWidget $products товары */
        $products = ProductWidget::widget([
            'productParams' => $params,
            'createPagerParams' => [
                'setPageSize' => self::LIMIT,
                'pagerParams' => [
                    'defaultPageSize' => 3,
                ]
            ]
        ]);

        return $this->render('index', [
            'totalCount' => ProductWidget::$totalCount,
            'products' => $products
        ]);
    }
}