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
        <?= isset($value['src']['sq60']) ? Html::tag('img', '', $value['src']['sq60']) . '&nbsp;' : ''; ?>
        <?= Html::decode($value['name_position']); ?><br />
        ID : <?= $value['id']; ?><br />
        цена : <?= isset($value['price_currency']) ? number_format($value['price_currency'], 2, '.', ' ') . " {$value['currency_nick']}" : $value['price_position'] . " {$value['valuta']}"; ?><br />
        дата : <?= Yii::$app->formatter->asDate($value['create_date'], 'd.m.Y'); ?><br />
    </li>
<?php endforeach; ?>

<?php if(!Yii::$app->request->isAjax): ?>
    </ul>

    <?= $pager ?>
<?php endif; ?>