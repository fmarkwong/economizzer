<?php

use yii\helpers\Html;

$this->title = Yii::t('app', 'Update') . ' ' . Yii::t('app', $model->desc_category) . ' ' . Yii::t('app', 'Category');

?>
<div class="category-update">

    <h2><?= $this->title ?></h2>
    <hr/>

    <?= $this->render('_form', [
        'model' => $model,
        'showParentOrSubRadioList' => false,
        'showParentCategoryDropDown' => $model->parent_or_sub == 'sub',
        'js' => null, 
    ]) ?>
</div>
