<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface {

    /** @var array данные авторизованного пользователя */
    private static $_user = array();
    /** @var bool флаг выборки - true|false админка все пользователи/представление только не удаленные с активированным профилем */
    private static $_fetchAdmin = false;

    public $username = '';

    /**
     * @return string
     */
    public static function tableName() {
        return 'santeh_user';
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
     * @return $this|\yii\db\ActiveQuery
     */
    public static function find() {
        $find = parent::find();
        return self::$_fetchAdmin ? $find : $find->where([
            'trash' => 0,
            'activated' => 'yes'
        ]);
    }


    /** @var array активированные записи пользователей */
    protected $_activeUser = array(
        'activated' => 'yes'
    );


    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        
        if(!self::$_user) {
            self::$_user = self::getById($id);
        }
        if(isset(self::$_user['id']) && self::$_user['id'] == $id) {
            return new static(['username' => self::$_user['name_user']]);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {}

    /**
     * @inheritdoc
     */
    public function getAuthKey() {}

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {}

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username) {

        if(!self::$_user) {
            self::$_user = self::getByNick($username);
        }
        if(isset(self::$_user['name_user']) && (strcasecmp(self::$_user['name_user'], $username) === 0)) {
            return new static(['username' => self::$_user['name_user']]);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return isset(self::$_user['id']) ? self::$_user['id'] : null;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) {

        if(isset(self::$_user['password']) && (strcasecmp(self::$_user['password'], md5($password)) === 0)) {
            return new static(['username' => self::$_user['name_user']]);
        }
        return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInfo() {
        return $this->hasMany(UserInfo::className(), ['id_parrent' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserGroup() {
        return $this->hasOne(UserGroup::className(), ['id' => 'id_parrent']);
    }

    /**
     * @param $id
     * @return array|null|ActiveRecord
     */
    public static function getById($id) {

        return self::find()
            ->with('userInfo', 'userGroup')
            ->andWhere(['id' => (int)$id])
            ->asArray()
            ->one();
    }

    /**
     * @param $name
     * @return array|null|ActiveRecord
     */
    public static function getByNick($name) {

        return self::find()
            ->with('userInfo', 'userGroup')
            ->andWhere(['name_user' => $name])
            ->asArray()
            ->one();
    }
}
