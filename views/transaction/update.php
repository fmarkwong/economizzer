<?php

use yii\helpers\Html;

$this->title = $title; 

?>
<div class="cashbook-create">

    <h2><?= Yii::t('app', $this->title) ?></h2>
    <?= $this->render('_form', compact('transaction', 'showCategoryField', 'action')) ?>

</div>
