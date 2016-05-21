<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 08.05.2016
 * Time: 10:19
 */
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use app\models\Position;
use app\models\sd\ICache;

class PositionController extends Controller {

    const LIMIT = 20;

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
            'defaultPageSize' => 5,
            'totalCount' => $totalCount,
        ]);
        $pager->setPageSize(self::LIMIT);

        //--- �������� ������
        $tblPosition = new Position();
        $data = $tblPosition->getPosition($pager->limit, $pager->offset, $params);

        //--- �����  � ��������
        //--- ���������� ��������
        ICache::i()->writeSource(1, 'position', Yii::$app->getBasePath() . '/www/iCache/4.jpg');

        return $this->render('index', [
            'positions' => $data,
            'totalCount' => $totalCount,
            'pager' => $pager,
            'imageSrc' => ['src' => Yii::$app->getUrlManager()->getBaseUrl() . '/iCache/thumb.jpg']
        ]);
    }
}