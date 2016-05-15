<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 08.05.2016
 * Time: 10:11
 */
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Event;


class Position extends ActiveRecord {

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
    public function getPosition($limit = 20, $offset = 0, array $params = array()) {

        $query = self::find();
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

        $event = new Event(['data' => $this->data]);
        //--- устанавливаем цены в соответствии с курсами валют
        $this->on(self::FETCH_ALL_POSITION, [new Currency(), 'preparePosition'], $event);
        //--- добавляем главное фото
        $this->on(self::FETCH_ALL_POSITION, [new PositionImage(), 'preparePosition'], $event);
        $this->trigger(self::FETCH_ALL_POSITION);
        return $this->data;
    }
}