<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 08.05.2016
 * Time: 11:28
 */
namespace app\modules\product\models;

use Yii;
use yii\db\ActiveRecord;

class ProductImage extends ActiveRecord {

    const I_CACHE_ALIAS_CONFIG = 'position';

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
        return 'santeh_img_position';
    }
    /**
     * @return array
     */
    public static function primaryKey(){

        return ['id'];
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getByProductIds(array $ids) {

        $data = $this->find()
            ->andWhere(array(
                'id_position' => $ids,
                'main_img' => 'yes'
            ))
            ->indexBy('id_position')
            ->asArray()
            ->all();
        return $data;
    }

    /**
     * статичная функция для ICache - возвращает часть пути к изображению
     * @param $id
     * @return string
     */
    public static function getPathPart($id) {

        $data = (new \yii\db\Query())
            ->select("santeh_position.id_catalog")
            ->from('santeh_img_position')
            ->leftJoin('santeh_position', 'santeh_position.id = santeh_img_position.id_position')
            ->where(array(
                'trash' => 0,
                'santeh_img_position.id' => $id,
            ))
            ->one();

        if(isset($data['id_catalog'])) {
            return $data['id_catalog'] . '/';
        }
        return '';
    }
}