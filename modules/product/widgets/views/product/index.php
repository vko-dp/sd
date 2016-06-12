<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 08.05.2016
 * Time: 10:22
 */

use yii\helpers\Html;

?>
<?php if(!Yii::$app->request->isAjax): ?>

    <ul id="<?= $containerId ?>">
<?php endif; ?>

<?php foreach ($products as $value): ?>
    <li>
        <?= Html::tag('img', '', $value['src']['sq60']) ?>&nbsp;
        <?= Html::decode($value['name_position'] . "(" . $value['price_position'] . ")") . " | ID: " . $value['src']['id'] ?>:
        <?= Yii::$app->formatter->asDate($value['create_date']) ?>
    </li>
<?php endforeach; ?>

<?php if(!Yii::$app->request->isAjax): ?>
    </ul>

    <?= $pager ?>
<?php endif; ?>