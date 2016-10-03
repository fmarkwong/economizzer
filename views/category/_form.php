<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\color\ColorInput;
use yii\helpers\ArrayHelper;

$this->registerJs($js);

?>

<div class="category-form">

<div class="col-md-8">

<h4>Parent Category</h4>
    <?php $form = ActiveForm::begin([
        'id' => 'categoryform',
        'options' => [
            'class' => 'form-horizontal',
            ],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        ],
    ]); ?>


    <?= $form->field($model, 'is_active')->hiddenInput(['value' => '1'])->label(false); ?>
    <?= $form->field($model, 'parent_id')->hiddenInput(['value' => null])->label(false); ?>

    <!-- Parent or Sub drop down -->
    <?= $form->field($model, 'parent_or_sub')->radioList([
        'parent' => 'Parent Category',
        'sub'    => 'Sub Category'
    ])->label('Parent/Sub')?>

    <!-- Category Name text field -->
    <?= $form->field($model, 'desc_category')->textInput(['maxlength' => 45]) ?>

    <!-- Category Type text field -->
    <?= $form->field($model, 'type_id')->dropDownList([
        1 => 'Revenue',
        2 => 'Expense'

    ]) ?>

    <!-- Parent Category drop down -->
    <?= $form->field($model, 'parent_id')->dropDownList(ArrayHelper::map(app\models\Category::find()->where([
        'parent_id' => null,
        'user_id' => Yii::$app->user->identity->id, 
        'is_active' => 1
        ])->orderBy("desc_category ASC")->all(), 'id_category', 'desc_category'), ['prompt'=>Yii::t('app','None'), 'id' => 'parent-category-form'])  ?>



    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
        <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-floppy-o"></i> '.Yii::t('app', 'Save') : '<i class="fa fa-floppy-o"></i> '.Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-primary grid-button' : 'btn btn-primary grid-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<div class="col-md-4">
</div>
</div>
