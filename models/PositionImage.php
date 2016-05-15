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

class PositionImage extends ActiveRecord {

    public static function tableName() {
        return 'santeh_img_position';
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getByIds(array $ids) {

        $data = $this->find()
            ->where(array(
                'id_position' => $ids,
                'main_img' => 'yes'
            ))
            ->asArray()
            ->all();

        $return = array();
        if($data) {
            foreach($data as $v) {
                $options = array(
                    'src' => $v['link_img'],
                    'alt' => $v['title'],
                    'title' => $v['title']
                );
                $return[$v['id_position']] = array(
                    'id' => $v['id'],
                    'sq40' => array_merge($options, array('width' => 40)),
                );
            }
        }
        return $return;
    }

    /**
     * @param Event $event
     * @throws \yii\base\InvalidConfigException
     */
    public function preparePosition(Event $event) {

        $ids = array_keys($event->sender->data);
        $images = $this->getByIds($ids);
        foreach($event->sender->data as &$v) {
            if(!isset($v['src'])) {
                $v['src'] = array();
            }
            $options = array(
                'src' => Yii::$app->getUrlManager()->getBaseUrl() . '/i/no_photo.jpg',
                'alt' => 'нет фото',
                'title' => 'нет фото',
            );
            $v['src'] = isset($images[$v['id']]) ? $images[$v['id']] : array(
                'id' => null,
                'sq40' => array_merge($options, array('width' => 40)),
            );
        }
        unset($v);
    }
}