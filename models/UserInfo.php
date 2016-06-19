<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 13.06.2016
 * Time: 10:34
 */
namespace app\models;

use yii\db\ActiveRecord;

class UserInfo extends ActiveRecord {

    public static function tableName() {
        return 'santeh_user_dopinfo';
    }
}