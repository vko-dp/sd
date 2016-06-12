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

    public $data = array();

    const FETCH_ALL_POSITION = 'fetch_all_position_ready';

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
        if(isset($params['filter'])) {
            $query->where($params['filter']);
        }
        if(isset($params['sorter'])) {
            $query->orderBy($params['sorter']);
        }

        $this->data = $query->offset($offset)
            ->limit($limit)
            ->indexBy('id')
            ->asArray()
            ->all();

        //--- ������������� ���� � ������������ � ������� �����
        $this->on(self::FETCH_ALL_POSITION, [new Currency(), 'preparePosition']);
        //--- ��������� ������� ����
        $this->on(self::FETCH_ALL_POSITION, [new ProductImage(), 'preparePosition']);
        $this->trigger(self::FETCH_ALL_POSITION);
        return $this->data;
    }
}