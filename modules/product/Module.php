<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 12.06.2016
 * Time: 9:59
 */
namespace app\modules\product;

use app\models\ajax\AjaxModule;
use app\modules\cart\widgets\CartWidget;

class Module extends AjaxModule {

    public $layout = 'main';

    /** @var array хтмл виджетов */
    protected $_widgets = array();

    public function init() {
        parent::init();

        $this->_widgets = [
            'cart' => CartWidget::widget([])
        ];
    }

    /**
     * @param $name
     * @return string
     */
    public function getWidget($name) {
        return isset($this->_widgets[$name]) ? $this->_widgets[$name] : '';
    }
}