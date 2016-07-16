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

class Currency extends ActiveRecord {

    protected static $_rates = array();

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
        return 'santeh_currency';
    }
    /**
     * @return array
     */
    public static function primaryKey(){

        return ['id'];
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
}