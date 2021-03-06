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
use app\modules\product\behavior\ProductBehavior;

class Product extends ActiveRecord {

    /** @var bool флаг выборки - true|false админка все/представление только не удаленные */
    private static $_fetchAdmin = false;
    /** @var array дефольтная сортировка */
    protected $_defaultSorter = array(
        'name_position' => 'asc'
    );

    /** @var array полученные данные */
    public $data = array();

    /**
     * @return array
     */
    public function behaviors() {
        return [
            'ProductBehavior' => ProductBehavior::className()
        ];
    }

    /**
     * @param bool|true $param
     * @return $this
     */
    public function setFetchAdmin($param = true) {
        self::$_fetchAdmin = (bool)$param;
        return $this;
    }

    /**
     * перегружаем метод чтобы в системе представления не фильтровать постоянно удаленных и неактивных
     * @return \yii\db\ActiveQuery
     */
    public static function find() {
        $find = parent::find();
        return self::$_fetchAdmin ? $find : $find->where([
            'trash' => 0,
            'show_up' => 'yes',
        ]);
    }

    /**
     * @return string
     */
    public static function tableName() {
        return 'santeh_position';
    }

    /**
     * @return array
     */
    public static function primaryKey(){

        return ['id'];
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
        if(isset($params['filter'])) {
            $query->andWhere($params['filter']);
        }

        //--- сортировка
        $params['sorter'] = isset($params['sorter']) ?  $params['sorter'] : $this->_defaultSorter;
        $query->orderBy($params['sorter']);

        $this->data = $query->offset($offset)
            ->limit($limit)
            ->indexBy('id')
            ->asArray()
            ->all();
        //--- расширяемся данными
        $this->trigger(ProductBehavior::FETCH_ALL_POSITION);

        return $this->data;
    }

    /**
     * @param array $params
     * @return int|string
     */
    public function getCount($params = array()) {
        $query = $this->find();
        if($params) {
            $query->where($params);
        }
        return $query->count();
    }
}