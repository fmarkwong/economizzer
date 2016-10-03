<?php

use yii\helpers\Html;

$this->title = Yii::t('app', 'Update', [
    'modelClass' => 'Category',
]) . ' ' . $model->desc_category;

$js = "$('.field-category-parent_or_sub').hide();";

if ($model->parent_or_sub === 'sub') {
    $js .= "$('.field-category-type_id').hide();";
}
?>
<div class="category-update">

    <h2><?= Html::encode($this->title) ?></h2>
    <hr/>

    <?= $this->render('_form', [
        'model' => $model,
        'js'    => $js,
    ]) ?>

</div>
