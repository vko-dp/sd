<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 12.06.2016
 * Time: 9:59
 */
namespace app\modules\product;

class Module extends \yii\base\Module {

    public $layout = 'main';

    public function init() {
        parent::init();

        $this->params['foo'] = 'bar';
        // ... остальной инициализирующий код ...
    }
}