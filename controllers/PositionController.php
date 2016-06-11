<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 08.05.2016
 * Time: 10:19
 */
namespace app\controllers;

use Yii;
use yii\data\Pagination;
use yii\helpers\Html;
use app\models\Position;
use app\widgets\Pager;
use app\models\sd\ICache;

class PositionController extends AjaxController {

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

        $totalCount = Position::find()->where($params['filter'])->count();
        //--- пагинатор
        $pager = new Pagination([
            'defaultPageSize' => 3,
            'totalCount' => $totalCount,
        ]);
        $pager->setPageSize(self::LIMIT);

        //--- получаем товары
        $tblPosition = new Position();
        $data = $tblPosition->getPosition($pager->limit, $pager->offset, $params);

        //--- опыты  с имагиком
        //--- записываем источник
//        ICache::i()->writeSource(3093, 'position', Yii::getAlias('@webroot/iCache/9.jpg'));

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
            ]),
            'imageSrc' => ['src' => Yii::getAlias('@web/iCache/thumb.jpg')]
        ]);
    }

    /**
     * @param Pagination $pager
     * @param array $params
     * @return array
     */
    public static function getDataForPager(Pagination $pager, array $params) {

        ob_start();
        print_r(array($pager->limit, $pager->offset));
        $string = ob_get_contents();
        ob_end_clean();
        file_put_contents('dump.txt', $string);

        //--- получаем товары
        $tblPosition = new Position();
        $data = $tblPosition->getPosition($pager->limit, $pager->offset, $params['params']);

        return array(
            'html' => Yii::$app->createControllerByID('position')->renderPartial('index', [
                'positions' => $data
            ]),
            'params' => array()
        );
    }
}