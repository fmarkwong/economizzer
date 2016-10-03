<?php

use yii\helpers\Html;

$this->title = $title;
$action = 'update';
?>
<div class="cashbook-create">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', compact('budget', 'savings', 'filterCategories', 'showCategoryField', 'showSavingsGoalField', 'action')) ?>

</div>
