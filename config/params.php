<?php

return [
    'adminEmail' => 'admin@example.com',
    'ajaxWidgets' => array(),
    'ajaxWidgetsData' => array(),
    'addAjaxWidgetData' => function(array $data) {
        Yii::$app->params['ajaxWidgetsData'] = array_merge(Yii::$app->params['ajaxWidgetsData'], $data);
    }
];
