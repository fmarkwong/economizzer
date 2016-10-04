<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Category;
use kartik\widgets\DatePicker;

?>

<div class="cashbook-create">
<h2><?= Html::encode($title) ?></h2>
<div class="cashbook-form">

<div class="col-md-8">
    <?php $form = ActiveForm::begin([
        'id' => 'cashbookform',
        'action' => [$action],
        'options' => [
            'enctype'=>'multipart/form-data',
            'class' => 'form-horizontal',
            ],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        ],
    ]); ?>
    <br>
    <p>

    <?php
        echo DatePicker::widget([
            'model' => $budget,
            'form' => $form,
            'attribute' => 'date',
            'type' => DatePicker::TYPE_INPUT,
            'size' => 'sm',
            'pluginOptions' => [
                'autoclose'=>true,
                'todayHighlight' => true,
                'format' => 'yyyy-mm-dd',
            ]
        ]);
    ?>
    <?php if (!$budget->isNewRecord) echo Html::hiddenInput('budget_id', $budget->id) ?>
    <?php if ($budget->isNewRecord) echo $form->field($budget, 'category_id')->hiddenInput(['value' => $budget->category_id])->label(false) ?> 

    <?php if ($showCategoryField) {
            echo $form->field($budget, 'category_id', [
                'inputOptions' => [
                    'class' => 'selectpicker '
                ]
            ]
            )->dropDownList(app\models\Category::getHierarchy($filterCategories), ['prompt' => Yii::t('app', 'Select'), 'class'=>'form-control required']);
        }
    ?>

    <?php if (!$showSavingsGoalField) echo $form->field($budget, 'budgeted_value')->textInput(['size' => 10]) ?>
    <?php //if ($showSavingsGoalField) echo $form->field($budget, 'savings_goal')->textInput(['size' => 10]) ?>
    <?php if ($showSavingsGoalField): ?>
        <div class="form-group field-savings-goal"> 
            <label class="col-lg-2 control-label" for="savings-goal">Savings Goal</label>
            <div class="col-lg-4">
                <?= Html::textInput('savings-goal', null, ['id' => 'savings-goal', 'class' => 'form-control', 'size' => 10]) ?>
            </div>
            <div class="col-lg-7"><div class="help-block"></div></div>
        </div> 
    <?php endif ?>


    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
        <?= Html::submitButton($budget->isNewRecord ? '<i class="fa fa-floppy-o"></i> '.Yii::t('app', 'Save') : '<i class="fa fa-floppy-o"></i> '.Yii::t('app', 'Save'), ['class' => $budget->isNewRecord ? 'btn btn-primary grid-button' : 'btn btn-primary grid-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<div class="col-md-4">
<!-- ADS test -->
</div>
</div>
</div>
