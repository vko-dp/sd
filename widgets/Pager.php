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
use app\controllers\AjaxController;
use yii\helpers\Html;

class Pager extends LinkPager {

    public $isAjaxBtn = false;

    public $ajaxPagerParams = array();

    public function run() {

        if($this->registerLinkTags) {
            $this->registerLinkTags();
        }
        if($this->isAjaxBtn) {
            //--- подключаем стили и скрипты
            $view = $this->getView();
            $view->registerCssFile('@web/css/widgets/pager.css');
            $view->registerJsFile('@web/js/widgets/pager.js', ['position' => $view::POS_END]);
            //--- сохраняем параметры для подгрузки
            Yii::$app->params['ajaxWidgetsData'] = ArrayHelper::merge(Yii::$app->params['ajaxWidgetsData'], [
                'Pager' => array_merge($this->ajaxPagerParams, [
                    'maxButtonCount' => $this->maxButtonCount,
                    'defaultPageSize' => $this->pagination->defaultPageSize,
                    'currentPage' => $this->pagination->getPage(),
                    'pageSize' => $this->pagination->getPageSize(),
                    'totalCount' => $this->pagination->getPageCount()
                ])
            ]);
            $btn = Html::tag('span', 'показать еще', [
                'class' => 'pagination-ajax-btn btn-show-next-page',
            ]);
        } else {
            $btn = '';
        }
        $buttons =  $this->renderPageButtons();
        echo Html::tag('div', "{$btn}{$buttons}");
    }

    /**
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
        }

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
            'pagerHtml' => $pagerHtml
        ], $data);
    }
}