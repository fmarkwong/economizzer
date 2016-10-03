<?php

use yii\helpers\Html;

$this->title = Yii::t('app', 'New Transaction', [
    'modelClass' => 'Transaction',
]);
?>
<div class="cashbook-create">

    <h2><?= Html::encode($title) ?></h2>
    <?= $this->render('_form', compact('transaction', 'action', 'showCategoryField')) ?>

</div>
