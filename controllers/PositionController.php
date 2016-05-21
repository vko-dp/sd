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
use tpmanc\imagick\Imagick;

class PositionController extends Controller {

    const LIMIT = 20;

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
            'defaultPageSize' => 5,
            'totalCount' => $totalCount,
        ]);
        $pager->setPageSize(self::LIMIT);

        //--- получаем товары
        $tblPosition = new Position();
        $data = $tblPosition->getPosition($pager->limit, $pager->offset, $params);

        //--- опыты  с имагиком
        $img = Imagick::open(Yii::$app->getBasePath() . '/www/images/image.jpg');
        $img->getWidth();
        $img->getHeight();

        $sourcePath = Yii::$app->getBasePath() . '/www/images/image.jpg';
        $resultPath = Yii::$app->getBasePath() . '/www/images/thumb.jpg';
        Imagick::open($sourcePath)->thumb(200, 200)->saveTo($resultPath);

        return $this->render('index', [
            'positions' => $data,
            'totalCount' => $totalCount,
            'pager' => $pager,
            'imageSrc' => ['src' => Yii::$app->getUrlManager()->getBaseUrl() . '/images/thumb.jpg']
        ]);
    }
}