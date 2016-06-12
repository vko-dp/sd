<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 12.06.2016
 * Time: 15:13
 */
namespace app\modules\cart;

use app\models\ajax\AjaxModule;

class Module extends AjaxModule {

    public $layout = 'main';

    public function init() {
        parent::init();

        $this->params['foo'] = 'bar';
        // ... остальной инициализирующий код ...
    }
}