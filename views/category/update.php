<?php

use yii\helpers\Html;

$this->title = Yii::t('app', "Update $model->desc_category Category");

?>
<div class="category-update">

    <h2><?= Html::encode($this->title) ?></h2>
    <hr/>

    <?= $this->render('_form', [
        'model' => $model,
        'showParentOrSubRadioList' => false,
        'showParentCategoryDropDown' => $model->parent_or_sub == 'sub',
        'js' => null, 
    ]) ?>
</div>
