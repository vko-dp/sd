<?php

namespace app\modules\product\behavior;

use Yii;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\product\models\Currency;
use app\modules\product\models\ProductImage;
use app\models\ICache;
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 16.07.2016
 * Time: 15:50
 */
class ProductBehavior extends Behavior {

    const FETCH_ALL_POSITION = 'fetch_all_position_ready';

    /**
     * @return array
     */
    public function events() {
        return [
            self::FETCH_ALL_POSITION => 'prepareProductInfo',
        ];
    }

    /**
     * 1. устанавливаем цены в соответствии с курсами валют
     * 2. добавляем главное фото
     * @param Event $event
     */
    function prepareProductInfo(Event $event) {

        if (!empty($event->sender->data)) {

            $tblCurrency = new Currency();
            $rates = $tblCurrency->getRates();

            $tblImages = new ProductImage();
            $ids = array_keys($event->sender->data);
            $images = $ids ? $tblImages->getByProductIds($ids) : array();

            foreach($event->sender->data as &$v) {

                //--- пересчитываем цену каждого товара в соответствии с текущим курсом
                $currency = 1;
                $currency = (isset($v['valuta']) && isset($rates[$v['valuta']])) ? $rates[$v['valuta']]['rate'] : $currency;
                $v['price_currency'] = isset($v['price_position']) ? round($v['price_position'] * $currency, 2) : 0;
                $v['currency_nick'] = 'GRN';

                //--- получаем и расширяем алиасы урлов данными для шаблона
                $dataUrlAlias = ICache::i()->getUrlData(ProductImage::I_CACHE_ALIAS_CONFIG, $images[$v['id']]['id'], $v['id_catalog'] . '/');
                foreach($dataUrlAlias as &$src) {
                    $src = array(
                        'src' => $src,
                        'alt' => isset($images[$v['id']]['id']) ? $images[$v['id']]['title'] : '',
                        'title' => isset($images[$v['id']]['id']) ? $images[$v['id']]['title'] : '',
                    );
                }
                $v['src'] = array_merge(['id' => isset($images[$v['id']]['id']) ? $images[$v['id']]['id'] : null], $dataUrlAlias);
            }
            unset($v);
        }
    }
}