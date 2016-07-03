<?php

namespace app\modules\cart\models;

use Yii;
use yii\db\ActiveRecord;

class Order extends ActiveRecord {

    /** @var bool флаг выборки - true|false админка все/представление только не удаленные */
    private static $_fetchAdmin = false;

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
        ]);
    }

    /**
     * @return string
     */
    public static function tableName() {
        return 'santeh_order';
    }

    /**
     * возвращает данные текущего заказа
     * @return array|null|ActiveRecord
     */
    public function getCurrentOrder() {
        $sid = Yii::$app->request->cookies->getvalue('current_order_sid', null);
        $orderData = array();
        if(!is_null($sid)) {
            $orderData = self::find()
                ->andWhere([
                    'session_hash' => $sid,
                    'status_create' => 'no'
                ])
                ->orderBy('id desc')
                ->limit(1)
                ->asArray()
                ->one();
        }
        return is_null($orderData) ? array() : $orderData;
    }
}