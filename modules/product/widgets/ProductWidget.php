<?php
/**
 * виджет для вывода товарных позиций
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 12.06.2016
 * Time: 11:17
 */

namespace app\modules\product\widgets;

use Yii;
use yii\base\Widget;
use yii\data\Pagination;
use app\widgets\Pager;
use app\modules\product\models\Product;
use app\models\ajax\AjaxInterface;

class ProductWidget extends Widget implements AjaxInterface {

    public static $totalCount = 0;

    /** @var array товарные позиции */
    public $products = array();
    /** @var array параметры фильтрации и сортировки */
    public $productParams = array();
    /** @var string | Pager пагинатор */
    public $pager = '';
    public $limit = 0;
    public $offset = 0;
    public $productListContainerSelId = 'id-product-list-container';
    /** @var array создать пейджер с параметрами */
    public $createPagerParams = array();

    /** регистрируем подключенные аякс виджеты */
    public static function getRegisterWidgets() {
        return array(
            'app\widgets\Pager'
        );
    }
    /** регистрируем аякс обработчики виджета */
    public static function getAjaxHandlers() {
        return array();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function run() {

        $tblPosition = new Product();
        if($this->createPagerParams && isset($this->createPagerParams['setPageSize'], $this->createPagerParams['pagerParams'])) {
            $params = isset($this->productParams['filter']) ? $this->productParams['filter'] : array();
            self::$totalCount = $tblPosition->getCount($params);

            //--- пагинатор
            $this->pager = new Pagination(array_merge($this->createPagerParams['pagerParams'], [
                'totalCount' => self::$totalCount,
            ]));
            $this->pager->setPageSize($this->createPagerParams['setPageSize']);
        }
        if(!is_string($this->pager) && is_object($this->pager)) {
            $this->limit = $this->pager->limit;
            $this->offset = $this->pager->offset;
        }
        if(!$this->products) {
            //--- получаем товары
            $this->products = $tblPosition->getProduct($this->limit, $this->offset, $this->productParams);
        }

        return $this->render('product/index', [
            'products' => $this->products,
            'containerId' => $this->productListContainerSelId,
            'pager' => Pager::widget([
                'pagination' => $this->pager,
                'maxButtonCount' => 5,
                'isAjaxBtn' => true,
                'ajaxPagerParams' => [
                    'called' => __CLASS__,
                    'container' => $this->productListContainerSelId,
                    'params' => [
                        'productParams' => $this->productParams
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

        $html = ProductWidget::widget(array_merge([
            'pager' => $pager
        ], $params));

        return array(
            'html' => $html,
            'params' => array()
        );
    }
}