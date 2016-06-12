<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 08.05.2016
 * Time: 10:11
 */
namespace app\modules\product\models;

use Yii;
use yii\db\ActiveRecord;


class Product extends ActiveRecord {

    const FETCH_ALL_POSITION = 'fetch_all_position_ready';

    public $data = array();
    /** @var array для фильтрации только активных товаров */
    protected $_activeProducts = array(
        'show_up' => 'yes',
    );
    /** @var array дефольтная сортировка */
    protected $_defaultSorter = array(
        'name_position' => 'asc'
    );

    public static function tableName() {
        return 'santeh_position';
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param array $params
     * @return array
     */
    public function getProduct($limit = 20, $offset = 0, array $params = array()) {

        $query = $this->find();

        //--- фильтрация
        $params['filter'] = (isset($params['filter']) && is_array($params['filter'])) ? array_merge($this->_activeProducts, $params['filter']) : $this->_activeProducts;
        $query->where($params['filter']);

        //--- сортировка
        $params['sorter'] = isset($params['sorter']) ?  $params['sorter'] : $this->_defaultSorter;
        $query->orderBy($params['sorter']);

        $this->data = $query->offset($offset)
            ->limit($limit)
            ->indexBy('id')
            ->asArray()
            ->all();

        //--- устанавливаем цены в соответствии с курсами валют
        $this->on(self::FETCH_ALL_POSITION, [new Currency(), 'preparePosition']);
        //--- добавляем главное фото
        $this->on(self::FETCH_ALL_POSITION, [new ProductImage(), 'preparePosition']);
        $this->trigger(self::FETCH_ALL_POSITION);
        return $this->data;
    }

    /**
     * @param array $params
     * @return int|string
     */
    public function getCount($params = array()) {
        $query = $this->find();
        $params = array_merge($this->_activeProducts, $params);
        $query->where($params);
        return $query->count();
    }
}