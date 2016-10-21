<?php

use yii\helpers\Html;

$this->title = Yii::t('app', 'Create category', [
    'modelClass' => 'Category',
]);

$js = "$('.field-parent-category-form').hide();";
?>
<div class="category-create">

    <h2><?= Html::encode($this->title) ?></h2>
    <hr/>

    <?php $model->parent_or_sub = 'parent'; //default value ?>
    <?php $model->parent_id = null; //default value ?>
    <?= $this->render('_form', [
        'model' => $model,
        'showParentOrSubRadioList' => true,
        'showParentCategoryDropDown' => true, 
        'js' => $js, 
    ]) ?>

</div>
