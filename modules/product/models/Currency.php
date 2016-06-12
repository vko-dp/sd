<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 14.05.2016
 * Time: 9:32
 */
namespace app\modules\product\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Event;

class Currency extends ActiveRecord {

    protected static $_rates = array();

    public static function tableName() {
        return 'santeh_currency';
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    function getRates() {
        if(!self::$_rates) {
            self::$_rates = $this->find()
                ->orderBy('id')
                ->indexBy('name')
                ->asArray()
                ->all();
        }
        return self::$_rates;
    }

    /**
     * пересчитывает цену каждого товара в соответствии с текущим курсом
     * @param Event $event
     * @return mixed
     */
    function preparePosition(Event $event) {

        $rates = $this->getRates();
        if(is_array($event->sender->data)) {
            foreach($event->sender->data as &$value) {
                $currency = 1;
                if(is_object($value)) {
                    /** @var ActiveRecord $value */
                    $currency = ($value->__isset('valuta') && isset($rates[$value->__get('valuta')])) ? $rates[$value->__get('valuta')]['rate'] : $currency;
                    if($value->__isset('price_position')) {
                        $value->__set('price_position', round($value->__get('price_position') * $currency, 2));
                    }
                } elseif(is_array($value)) {
                    $currency = (isset($value['valuta']) && isset($rates[$value['valuta']])) ? $rates[$value['valuta']]['rate'] : $currency;
                    if(isset($value['price_position'])) {
                        $value['price_position'] = round($value['price_position'] * $currency, 2);
                    }
                }
            }
            unset($value);
        }
    }
}