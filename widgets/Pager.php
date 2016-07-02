<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 04.06.2016
 * Time: 19:57
 */

namespace app\widgets;

use Yii;
use yii\data\Pagination;
use yii\widgets\LinkPager;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\controllers\AjaxController;
use app\models\ajax\AjaxInterface;
use app\assets\widgets\PagerAsset;

class Pager extends LinkPager implements AjaxInterface {

    public $isAjaxBtn = false;

    public $ajaxPagerParams = array();

    /** регистрируем подключенные аякс виджеты */
    public static function getRegisterWidgets() {
        return array();
    }
    /** регистрируем аякс обработчики виджета */
    public static function getAjaxHandlers() {
        return array(
            'getNextPage'
        );
    }

    public function run() {

        if($this->registerLinkTags) {
            $this->registerLinkTags();
        }
        $currentPage = $this->pagination->getPage();
        $pageCount = $this->pagination->getPageCount();
        if($this->isAjaxBtn && !($currentPage >= $pageCount - 1)) {

            //--- подключаем стили и скрипты
            PagerAsset::register($this->getView());

            //--- сохраняем параметры для подгрузки
            Yii::$app->params['ajaxWidgetsData'] = ArrayHelper::merge(Yii::$app->params['ajaxWidgetsData'], [
                'Pager' => array_merge([
                    'maxButtonCount' => $this->maxButtonCount,
                    'defaultPageSize' => $this->pagination->defaultPageSize,
                    'currentPage' => $currentPage + 1,
                    'pageSize' => $this->pagination->getPageSize(),
                    'totalPage' => $pageCount,
                    'totalCount' => intval($this->pagination->totalCount)
                ], $this->ajaxPagerParams)
            ]);
            $indicator = Html::tag('img', '', [
                'src' => Url::to('@web/i/pager-stat-btn.gif'),
                'class' => 'pagination-ajax-indicator',
                'alt' => ''
            ]);
            $btn = Html::tag('span', "{$indicator} показать еще", [
                'class' => 'pagination-ajax-btn btn-show-next-page',
            ]);
        } else {
            $btn = '';
        }
        $buttons =  $this->renderPageButtons();
        echo (Yii::$app->request->isAjax ? $buttons : Html::tag('div', "{$btn}{$buttons}"));
    }

    /**
     * обработчик аякса кнопки показать еще
     * данные должен вернуть метод вызывающего класса
     * @param AjaxController $controller
     */
    public static function getNextPage(AjaxController $controller) {

        $request = Yii::$app->request;
        $called = $request->getBodyParam('called', '');
        $params = $request->getBodyParam('params', array());
        $maxButtonCount = $request->getBodyParam('maxButtonCount', 3);
        $defaultPageSize = $request->getBodyParam('defaultPageSize', 3);
        $currentPage = $request->getBodyParam('currentPage', 0);
        $pageSize = $request->getBodyParam('pageSize', 20);
        $totalCount = $request->getBodyParam('totalCount', 0);

        if(!in_array('getDataForPager', get_class_methods($called))) {

            $controller->responseStatus = AjaxController::ERROR_STATUS;
            $controller->responseData = "Отсутствует метод: {$called}::getDataForPager()";
        } else {

            //--- пагинатор
            $pager = new Pagination([
                'defaultPageSize' => $defaultPageSize,
                'totalCount' => $totalCount,
            ]);
            $pager->setPage($currentPage);
            $pager->setPageSize($pageSize);

            //--- получаем хтмл страницы и доп. параметры если есть ['html' => '', 'params' => array()]
            $data = $called::getDataForPager($pager, $params);

            $pagerHtml = Pager::widget([
                'pagination' => $pager,
                'maxButtonCount' => $maxButtonCount,
                'isAjaxBtn' => false
            ]);

            $controller->responseStatus = AjaxController::OK_STATUS;
            $controller->responseData = array_merge([
                'pagerHtml' => preg_replace('|(\s+href\=\"[^\"]*)(ajax)([^\"]*\")|isU', '$1$3', $pagerHtml),
                'currentPage' => $pager->getPage(),
                'totalCount' => $pager->getPageCount()
            ], $data);
        }
    }
}