<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 13.06.2016
 * Time: 10:39
 */
namespace app\models;

use yii\db\ActiveRecord;

class UserGroup extends ActiveRecord {

    public static function tableName() {
        return 'santeh_user_groups';
    }
}