<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 08.05.2016
 * Time: 10:22
 */

use yii\helpers\Html;

if(!Yii::$app->request->isAjax) {

    /* @var $this yii\web\View */
    $this->title = 'Товары';
    $this->params['breadcrumbs'][] = $this->title;
}

?>
<?php if(!Yii::$app->request->isAjax): ?>

    <h1>Товары (<?= $totalCount ?>)</h1>

    <ul id="id-position-list-container">
        <?php endif; ?>

        <?php foreach ($positions as $value): ?>
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