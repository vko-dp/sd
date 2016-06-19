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
use yii\base\Event;
use app\models\ICache;

class ProductImage extends ActiveRecord {

    const I_CACHE_ALIAS_CONFIG = 'position';

    /** @var bool ���� ������� - true|false ������� ���/������������� ������ �� ��������� */
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
     * ����������� ����� ����� � ������� ������������� �� ����������� ��������� ��������� � ����������
     * @return $this|\yii\db\ActiveQuery
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
     * @param array $ids
     * @return array
     */
    public function getByProductIds(array $ids) {

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
        $images = $this->getByProductIds($ids);

        foreach($event->sender->data as &$v) {

            //--- �������� � ��������� ������ ����� ������� ��� �������
            $dataUrlAlias = ICache::i()->getUrlData(self::I_CACHE_ALIAS_CONFIG, $images[$v['id']]['id'], $v['id_catalog'] . '/');
            foreach($dataUrlAlias as &$src) {
                $src = array(
                    'src' => $src,
                    'alt' => isset($images[$v['id']]['id']) ? $images[$v['id']]['title'] : '��� ����',
                    'title' => isset($images[$v['id']]['id']) ? $images[$v['id']]['title'] : '��� ����',
                );
            }
            $v['src'] = array_merge(['id' => isset($images[$v['id']]['id']) ? $images[$v['id']]['id'] : null], $dataUrlAlias);
        }
        unset($v);
    }

    /**
     * ��������� ������� ��� ICache - ���������� ����� ���� � �����������
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