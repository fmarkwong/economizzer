<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Category;
use kartik\widgets\DatePicker;

?>

<div class="cashbook-form">

<div class="col-md-8">
    <?php $form = ActiveForm::begin([
        'action' => 'create',
        'id' => 'transactionform',
        'options' => [
            'enctype'=>'multipart/form-data',
            'class' => 'form-horizontal',
            ],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        ],
    ]); ?>

    <!-- <ul class="nav nav&#45;tabs"> -->
    <!--     <li class="active"><a href="#home" data&#45;toggle="tab"><i class="fa fa&#45;cube"></i> <?php echo Yii::t('app', 'Basic Information');?></a></li> -->
    <!--     <li><a href="#profile" data&#45;toggle="tab"><i class="fa fa&#45;cubes"></i> <?php echo Yii::t('app', 'Additional');?></a></li> -->
    <!-- </ul> -->
    <div class="tab-content">
        <div class="tab-pane active" id="home">
        <p>
        <?php 
        // echo $form->field($transaction, 'type_id')->radioList([
        //     '1' => Yii::t('app', 'Revenue'), 
        //     '2' => Yii::t('app', 'Expense'),
        //     ], ['itemOptions' => ['class' =>'radio-inline','labelOptions'=>array('style'=>'padding:4px;')]])->label('');
        echo $form->field($transaction, 'type_id')->hiddenInput(['value' => '2'])->label(false);
        ?>

        <?php
            echo DatePicker::widget([
                'model' => $transaction,
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
        
        <?php // echo $form->field($transaction, 'category_id')->dropDownList(ArrayHelper::map(Category::find()->where(['user_id' => Yii::$app->user->identity->id, 'is_active' => 1])->orderBy("desc_category ASC")->all(), 'id_category', 'desc_category'),['prompt'=>Yii::t('app','Select')])  ?>

        <?=
        $form->field($transaction, 'category_id', [
            'inputOptions' => [
                'class' => 'selectpicker '
            ]
        ])->dropDownList(app\models\Category::getHierarchy(), ['prompt' => Yii::t('app', 'Select'), 'class'=>'form-control required']);
        ?>

        <?= $form->field($transaction, 'description')->textInput(['maxlength' => 100]) ?>
        <?= $form->field($transaction, 'value')->textInput(['size' => 10]) ?>

        </div>
    </div>

    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
        <?= Html::submitButton($transaction->isNewRecord ? '<i class="fa fa-floppy-o"></i> '.Yii::t('app', 'Save') : '<i class="fa fa-floppy-o"></i> '.Yii::t('app', 'Save'), ['class' => $transaction->isNewRecord ? 'btn btn-primary grid-button' : 'btn btn-primary grid-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<div class="col-md-4">
<!-- ADS test -->
</div>
</div>
