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
use app\models\Position;
use app\widgets\Pager;
use app\models\sd\ICache;

class PositionController extends AjaxController {

    const LIMIT = 20;

    /**
     * ������������ ������� �������������� ���� �������
     */
    protected function _registerAjaxWidgets() {
        $this->_registerWidget('app\widgets\Pager', array('getNextPage'));
    }

    public function actionIndex() {

        //--- ������� � ����������
        $params = array(
            'filter' => array(
                'show_up' => 'yes'
            ),
            'sorter' => 'name_position asc'
        );

        $totalCount = Position::find()->where($params['filter'])->count();
        //--- ���������
        $pager = new Pagination([
            'defaultPageSize' => 3,
            'totalCount' => $totalCount,
        ]);
        $pager->setPageSize(self::LIMIT);

        //--- �������� ������
        $tblPosition = new Position();
        $data = $tblPosition->getPosition($pager->limit, $pager->offset, $params);

        //--- �����  � ��������
        //--- ���������� ��������
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
                        'name' => 'dyadya petya',
                        'id' => 56565656
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
        return array(
            'html' => '',
            'params' => array()
        );
    }
}