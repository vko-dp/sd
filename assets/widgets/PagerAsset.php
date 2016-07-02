<?php

namespace app\assets\widgets;

use app\assets\AppAsset;
use yii\web\View;

class PagerAsset extends AppAsset {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $jsOptions = [
        'position' => View::POS_END
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];

    public $css = [
        'css/widgets/pager.css',
    ];
    public $js = [
        'js/widgets/pager.js'
    ];
}