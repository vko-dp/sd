<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 08.05.2016
 * Time: 10:22
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
$this->title = 'Товары';
$this->params['breadcrumbs'][] = $this->title;

?>
    <h1>Товары (<?= $totalCount ?>)</h1>
    <p><?= Html::tag('img', '', $imageSrc) ?></p>
    <ul>
        <?php foreach ($positions as $value): ?>
            <li>
                <?= Html::tag('img', '', $value['src']['sq40']) ?>&nbsp;
                <?= Html::decode($value['name_position'] . "(" . $value['price_position'] . ")") ?>:
                <?= Yii::$app->formatter->asDate($value['create_date']) ?>
            </li>
        <?php endforeach; ?>
    </ul>

<?= LinkPager::widget(['pagination' => $pager]) ?>