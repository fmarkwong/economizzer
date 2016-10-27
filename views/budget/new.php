<?php

use yii\helpers\Html;

$this->title = $title;
$js = null;
$action = 'create';
?>
<div class="cashbook-create">

    <h2><?= Yii::t('app', $this->title) ?></h2>

    <?= $this->render('_form', compact('budget', 'showSavingsGoalField', 'filterCategories', 'js', 'action', 'showCategoryField')) ?>

</div>
