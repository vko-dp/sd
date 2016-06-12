<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 08.05.2016
 * Time: 10:19
 */
namespace app\modules\product\controllers;

use Yii;
use yii\data\Pagination;
use yii\helpers\Html;
use app\controllers\AjaxController;
use app\modules\product\models\Product;
use app\widgets\Pager;
use app\models\ICache;

class DefaultController extends AjaxController {

    const LIMIT = 20;

    /**
     * регистрируем виджеты обрабатывающие аякс запросы
     */
    protected function _registerAjaxWidgets() {
        $this->_registerWidget('app\widgets\Pager', array('getNextPage'));
    }

    public function actionIndex() {

        //--- фильтры и сортировка
        $params = array(
            'filter' => array(
                'show_up' => 'yes'
            ),
            'sorter' => 'name_position asc'
        );

        $totalCount = Product::find()->where($params['filter'])->count();
        //--- пагинатор
        $pager = new Pagination([
            'defaultPageSize' => 3,
            'totalCount' => $totalCount,
        ]);
        $pager->setPageSize(self::LIMIT);

        //--- получаем товары
        $tblPosition = new Product();
        $data = $tblPosition->getProduct($pager->limit, $pager->offset, $params);

        //--- опыты  с имагиком
        //--- записываем источник
//        ICache::i()->writeSource(3093, 'position', Yii::getAlias('@webroot/iCache/fsdfasdfds.jpeg'));

        return $this->render('index', [
            'positions' => $data,
            'totalCount' => $totalCount,
            'pager' => Pager::widget([
                'pagination' => $pager,
                'maxButtonCount' => 5,
                'isAjaxBtn' => true,
                'ajaxPagerParams' => [
                    'called' => __CLASS__,
                    'container' => 'id-position-list-container',
                    'params' => [
                        'name' => Html::encode('дядя вася'),
                        'id' => 56565656,
                        'params' => $params
                    ]
                ]
            ])
        ]);
    }

    /**
     * @param Pagination $pager
     * @param array $params
     * @return array
     */
    public static function getDataForPager(Pagination $pager, array $params) {

        //--- получаем товары
        $tblPosition = new Product();
        $data = $tblPosition->getProduct($pager->limit, $pager->offset, $params['params']);

        Yii::$app->controllerNamespace = 'app\modules\product\controllers';
        $controller = Yii::$app->createControllerByID('default');
        $controller->setViewPath('@app/modules/product/views/default');

        return array(
            'html' => $controller->renderPartial('index', [
                'positions' => $data
            ]),
            'params' => array()
        );
    }
}