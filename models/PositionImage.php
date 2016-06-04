<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 08.05.2016
 * Time: 11:28
 */
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Event;
use app\models\sd\ICache;

class PositionImage extends ActiveRecord {

    const I_CACHE_ALIAS_CONFIG = 'position';

    public static function tableName() {
        return 'santeh_img_position';
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getByPositionIds(array $ids) {

        $data = $this->find()
            ->where(array(
                'id_position' => $ids,
                'main_img' => 'yes'
            ))
            ->indexBy('id_position')
            ->asArray()
            ->all();
        return $data;
    }

    /**
     * @param Event $event
     * @throws \yii\base\InvalidConfigException
     */
    public function preparePosition(Event $event) {

        $ids = array_keys($event->sender->data);
        $images = $this->getByPositionIds($ids);

        foreach($event->sender->data as &$v) {

            //--- получаем и расширяем алиасы урлов данными для шаблона
            $dataUrlAlias = ICache::i()->getUrlData(self::I_CACHE_ALIAS_CONFIG, $images[$v['id']]['id'], $v['id_catalog'] . '/');
            foreach($dataUrlAlias as &$src) {
                $src = array(
                    'src' => $src,
                    'alt' => isset($images[$v['id']]['id']) ? $images[$v['id']]['title'] : 'нет фото',
                    'title' => isset($images[$v['id']]['id']) ? $images[$v['id']]['title'] : 'нет фото',
                );
            }
            $v['src'] = array_merge(['id' => isset($images[$v['id']]['id']) ? $images[$v['id']]['id'] : null], $dataUrlAlias);
        }
        unset($v);
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
                'santeh_img_position.id' => $id,
            ))
            ->one();

        if(isset($data['id_catalog'])) {
            return $data['id_catalog'] . '/';
        }
        return '';
    }
}