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

    /** @var bool ���� ������� - true|false ������� ���/������������� ������ �� ��������� */
    private static $_fetchAdmin = false;
    /** @var array ���������� ���������� */
    protected $_defaultSorter = array(
        'name_position' => 'asc'
    );

    public $data = array();

    /**
     * @param bool|true $param
     * @return $this
     */
    public function setFetchAdmin($param = true) {
        self::$_fetchAdmin = (bool)$param;
        return $this;
    }

    /**
     * ����������� ����� ����� � ������� ������������� �� ����������� ��������� ��������� � ����������
     * @return $this|\yii\db\ActiveQuery
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
     * @param int $limit
     * @param int $offset
     * @param array $params
     * @return array
     */
    public function getProduct($limit = 20, $offset = 0, array $params = array()) {

        $query = $this->find();

        //--- ����������
        if(isset($params['filter'])) {
            $query->where($params['filter']);
        }

        //--- ����������
        $params['sorter'] = isset($params['sorter']) ?  $params['sorter'] : $this->_defaultSorter;
        $query->orderBy($params['sorter']);

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