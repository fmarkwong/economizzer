<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\color\ColorInput;
use yii\helpers\ArrayHelper;

$this->registerJs($js);
?>

<div class="category-form">
    <div class="row">
        <div class="col-md-8">
            <br>

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

            <!-- Parent or Sub radio list-->
<?php
            if ($showParentOrSubRadioList) echo $form->field($model, 'parent_or_sub')->radioList([
                'parent' => 'Parent Category',
                'sub'    => 'Sub Category'
            ])->label('Parent/Sub')
?>

            <!-- Category Name text field -->
            <?= $form->field($model, 'desc_category')->textInput(['maxlength' => 45]) ?>


            <!-- Parent Category drop down -->
<?php
            if ($showParentCategoryDropDown) {

                $categoryQuery = app\models\Category::find()->where([
                    'parent_id' => null,
                    'user_id' => Yii::$app->user->identity->id, 
                    'is_active' => 1
                ]);

                if ($model->parent_or_sub == 'sub') {
                    $categoryQuery->andWhere(['not in','desc_category', ['Savings Goals', 'Debt', 'Income']]);
                }
                $categoryQuery->orderBy("desc_category ASC");

                
                echo $form->field($model, 'parent_id')->dropDownList(ArrayHelper::map($categoryQuery->all(), 'id_category', 'desc_category'), ['id' => 'parent-category-form'])->label('Parent Category');
            }
?>



            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10">
                <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-floppy-o"></i> '.Yii::t('app', 'Save') : '<i class="fa fa-floppy-o"></i> '.Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-primary grid-button' : 'btn btn-primary grid-button']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
</div>
<div class="col-md-4">
</div>
</div>
