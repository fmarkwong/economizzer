<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Cashbook;
use app\models\Account;
use app\models\TotalSaving;
use app\helpers\CashBookHelper;
use app\helpers\ViewHelper;

$this->title = Yii::t('app', 'Budget');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-4 col-sm-offset-4 month-picker">
        <?= Html::a(null, ['/cashbook/previous-month'], ['class' => 'glyphicon glyphicon-circle-arrow-left']) ?>
        <h2 class="month-picker"><?= Yii::t('app', $month) . " $year"?></h2>
        <?= Html::a(null, ['/cashbook/next-month'], ['class' => 'glyphicon glyphicon-circle-arrow-right']) ?>
    </div>
</div> <!-- row -->

<div class="row">
    <div class="col-sm-16">

        <div class="cashbook-index"> <!-- BUDGETS SECTION -->
            <h2>
              <span><?= Html::encode($this->title) ?></span>
              <?php $color = CashBookHelper::balanceColor($totalLeftToBudget) ?>
              <span style="color: <?=CashBookHelper::balanceColor($totalLeftToBudget)?>; font-size: 20px; vertical-align: middle"> <?= Yii::t('app', 'Left to budget') . ": $totalLeftToBudget" ?></span>
                <?php //echo Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Create').'', ['/budget/new'], ['class'=>'btn btn-primary grid-button pull-right']) ?>
            </h2>
            <div style='float:right; font-size: 1.2em'><?= Yii::t('app', 'Add or update values by clicking on the value') ?>.</div>
                <br>
            <hr>

            <?php ViewHelper::displayAllFlashes() ?>

            <div id="w1" class="grid-view">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><a href="/budgeter/web/cashbook/index?sort=parent_category_id" data-sort="parent_category_id"><?= Yii::t('app', 'Category') ?></a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=budgeted_value" data-sort="budgeted_value"><?= Yii::t('app', 'Budgeted Value') ?></a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=value" data-sort="value"><?= Yii::t('app', 'Actual Value') ?></a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=value" data-sort="value"><?= Yii::t('app', 'Balance') ?></a></th>
                        </tr>
                    </thead>
                    <tbody>
<?php 
                    foreach($categories as $category) {
                        $parent_category = $category['desc_category']; 
                        $sub_categories = app\models\Category::subCategories($category['id_category']); 
                        $category_balance = $category['budgeted_total'] - $category['actual_total'];
                        echo $this->render('_parent_category_row', compact('parent_category', 'category', 'category_balance'));

                        foreach($sub_categories as $subCategory) {
                            $categoryId    = $subCategory->id_category;
                            $currentBudget = $subCategory->getCurrentBudget();
                            $budgetId      = $currentBudget ? $currentBudget->id: null;
                            $transactionId = $currentBudget ? $currentBudget->transaction_id : null;
                            $budgetedValue = $currentBudget ? $currentBudget->budgeted_value : 0;
                            $actualValue   = $currentBudget ? $currentBudget->actual_value : 0;
                            $subCategoryBalance = $budgetedValue - $actualValue;
                            echo $this->render('_sub_category_row', compact('subCategory', 'subCategoryBalance', 'actualValue', 'budgetedValue', 'budgetId', 'categoryId', 'transactionId'));
                        }
                    } 
?>
                    </tbody>
                </table> 
            </div> <!-- w1 gridview-->
        </div><!-- BUDGETS SECTION END -->
        <br>
        <?php if ($savingsCategory): ?> <!-- SAVING GOALS SECTION -->
                <?php //echo Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Create').'', ['/budget/new-savings'], ['class'=>'btn btn-primary grid-button pull-right']) ?>
        <div class="cashbook-index">
            <div id="w1" class="grid-view">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><a href="/budgeter/web/cashbook/index?sort=parent_category_id" data-sort="parent_category_id">Category</a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=budgeted_value" data-sort="budgeted_value">Budgeted Value</a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=value" data-sort="value">Actual Value</a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=value" data-sort="value">Balance</a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=value" data-sort="value">Total Savings</a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=value" data-sort="value">Goal / Completed</a></th>
                        </tr>
                    </thead>
                    <tbody>
<?php 
                    foreach($savingsCategory as $category) {
                        $parent_category = $category['desc_category']; 
                        $sub_categories = app\models\Category::subCategories($category['id_category']); 
                        $totalSavingsTotal = app\models\Account::getTotalSavingTotal(); 
                        // $totalSavingsGoal = $category['savings_goal_total'];
                        $totalSavingsGoal = app\models\Account::getTotalSavingGoal();  
                        $category_balance = $category['budgeted_total'] - $category['actual_total'];
                        $totalPercentageCompleted = ($totalSavingsGoal > 0 ? ($totalSavingsTotal / $totalSavingsGoal) : 0);
                        $formatter = Yii::$app->formatter;
                        $totalPercentageCompleted = $formatter->asPercent($totalPercentageCompleted);
                        echo $this->render('_savings_parent_category_row', compact('parent_category', 'category', 'category_balance', 'totalSavingsGoal', 'totalPercentageCompleted', 'totalSavingsTotal'));

                        foreach($sub_categories as $subCategory) {
                            $categoryId    = $subCategory->id_category;
                            $currentBudget = $subCategory->getCurrentBudget(); //TODO: add nullBudget object if not found so we don't have to keep checking for null below
                            $budgetId      = $currentBudget ? $currentBudget->id: 0;
                            $transaction   = $currentBudget ? $currentBudget->transaction : null;
                            $transactionId = $transaction ? $transaction->id : null;
                            $budgetedValue = $currentBudget ? $currentBudget->budgeted_value : 0;
                            $actualValue   = $currentBudget ? $currentBudget->actual_value : 0;
                            $subCategoryBalance = $budgetedValue - $actualValue;
                            $totalSavings = $subCategory->totalSaving ? $subCategory->totalSaving->value : 0; 
                            // $savingsGoal = $currentBudget ? $currentBudget->savings_goal : 0;
                            $savingsGoal = $subCategory->totalSaving ? $subCategory->totalSaving->goal : 0; 
                            // $percentageCompleted = $savingsGoal > 0 ? ($totalSavings / $savingsGoal) * 100 : 0;
                            $percentageCompleted = $savingsGoal > 0 ? ($totalSavings / $savingsGoal) : 0;
                            $percentageCompleted = $formatter->asPercent($percentageCompleted);
                            
                            echo $this->render('_savings_sub_category_row', compact('subCategory', 'subCategoryBalance', 'actualValue', 'budgetedValue', 'savingsGoal', 'percentageCompleted', 'categoryId', 'budgetId', 'transactionId', 'totalSavings'));
                        }
                    } 
?>
                    </tbody>
                </table> 
            </div> <!-- w1 gridview-->
        </div><!-- SAVING GOALS SECTION END -->
        <?php endif ?>
        <br>
        <div class="cashbook-index">  <!-- TRANSACTIONS -->
            <h2>
                <span>Account</span>
                <?php $account_balance_color = cashBookHelper::balanceColor($accountBalance) ?>
                <span style="color: <?=$account_balance_color?>; font-size: 20px; vertical-align: middle"> Balance: <?= $accountBalance ?></span>
                <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Add').'', ['/transaction/new'], ['class'=>'btn btn-primary grid-button pull-right']) ?>
            </h2>
            <hr/>

    <!-- TRANSACTIONS TABLE -->

            <div id="w1" class="grid-view">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><a href="/budgeter/web/cashbook/index?sort=parent_category_id" data-sort="parent_category_id">Date</a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=budgeted_value" data-sort="budgeted_value">Description</a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=value" data-sort="value">Category</a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=value" data-sort="value">Value</a></th>
                        </tr>
                    </thead>
                    <tbody>
<?php 
                    foreach($transactions as $transaction) {
                        $category = $transaction->category;
                        $category_desc = $category->desc_category;
                        $parent_category_desc = $category->getParent() ? $category->getParent()->one()->desc_category : NULL;
                        echo $this->render('_transaction_row', compact('transaction', 'category_desc', 'parent_category_desc'));
                    } 
?>
                    </tbody>
                </table> <!-- END TRANSACTIONS TABLE -->
            </div> <!-- w1 gridview transactions-->
        </div><!-- end transactions div-->
    </div><!-- col-sm-12 -->
</div> <!-- row -->



    
<?php 
/*   OLD GRID CODE FOR REFERENCE TODO:
GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class'=>'table table-striped table-hover'],
        'emptyText'    => '</br><p class="text-danger">'.Yii::t('app', 'No entries found!').'</p>',
        'summary'      =>  '',
        'showFooter'   => true,
        'showOnEmpty'  => false,
        'footerRowOptions'=>['style'=>'font-weight:bold;'],
        'rowOptions'   => function ($model, $index, $widget, $grid) {
                return [
                    'id' => $model['id'], 
                    'onclick' => 'location.href="'
                        . Yii::$app->urlManager->createUrl('cashbook/') 
                        . '/"+(this.id);',
                    'style' => "cursor: pointer",
                ];
        },        
        'columns'    => [
            [
            'attribute' => 'date',
            'enableSorting' => true,
            'value' => function ($model) {                      
                    return $model->date <> '' ? Yii::$app->formatter->asDate($model->date, 'short') : Yii::$app->formatter->asDate($model->date, 'short');
                    },
            // 'contentOptions'=>['style'=>'width: 15%;text-align:left'],
            'contentOptions'=>['style'=>'text-align:left'],
            'footer' => 'Total',
            ],
            // Parent Category
            [
            'attribute' => 'parent_category_id',
            'format' => 'raw',
            'enableSorting' => true,
            'value' => function ($model) {                      
                    return '<span style="color:'.$model->category->hexcolor_category.'">'.$model->getParentCategory()->desc_category.'</span>';
                    },
            // 'contentOptions'=>['style'=>'width: 20%;text-align:left'],
            'contentOptions'=>['style'=>'text-align:left'],
            'footerOptions' => ['style'=>'text-align:left'],                  
            ],
            //sub category
            [
            'attribute' => 'category_id',
            'format' => 'raw',
            'enableSorting' => true,
            'value' => function ($model) {                      
                    return '<span style="color:'.$model->category->hexcolor_category.'">'.$model->category->desc_category.'</span>';
                    },
            // 'contentOptions'=>['style'=>'width: 20%;text-align:left'],
            'contentOptions'=>['style'=>'text-align:left'],
            'footerOptions' => ['style'=>'text-align:left'],                  
            ],
            [
            'attribute' => 'description',
            'format' => 'raw',
            'enableSorting' => true,
            'value' => function ($model) {                      
                           return $model->description; 
                    },
            // 'contentOptions'=>['style'=>'width: 20%;text-align:left'],
            'contentOptions'=>['style'=>'text-align:left'],
            'footerOptions' => ['style'=>'text-align:left'],                  
            ],
            [
            'label' => 'Budgeted Value',
            'attribute' => 'budgeted_value',
            'format' => 'raw',
            'enableSorting' => true,
            'value' => function ($model) {                      
                        return $model->budgeted_value;
                    },
            // 'contentOptions'=>['style'=>'width: 20%;text-align:left'],
            'contentOptions'=>['style'=>'text-align:left'],
             'footer' => Cashbook::pageTotal($dataProvider->models,'budgeted_value'),
            'footerOptions' => ['style'=>'text-align:left'],                  
            ],
            [
             'label' => 'Actual Value',
             'attribute' => 'value',
             'format' => 'raw',
             'value' => function ($model) {  
                    return $model->is_pending === 0 ? $model->value : '<span class="glyphicon glyphicon-flag" style="color:orange" aria-hidden="true"></span> <strong style="color:'.$model->type->hexcolor_type.'">'.' '.$model->value.'</strong>';
                    },
             'enableSorting' => true,
            'contentOptions'=>['style'=>'text-align:left'],
             // 'contentOptions'=>['style'=>'width: 20%;text-align:left'],
             'footer' => Cashbook::pageTotal($dataProvider->models,'value'),
             'footerOptions' => ['style'=>'text-align:left'],
            ],
            [
             'label' => 'Balance',
             'attribute' => 'value',
             'format' => 'raw',
             'value' => function ($cash_book) {  
                        if ($cash_book->type_id == 1 ) // income
                            $value = $cash_book->value - $cash_book->budgeted_value;
                        else // expense
                            $value = $cash_book->budgeted_value - $cash_book->value;
                        $color = Cashbook::footerColor($value);
                        return "<strong style='color: $color'>" . $value . '</strong>';
                    },
             'enableSorting' => true,
             // 'contentOptions'=>['style'=>'width: 20%;text-align:left'],
            'contentOptions'=>['style'=>'text-align:left'],
             'footer' => Cashbook::pageTotal($dataProvider->models,'budgeted_value') - Cashbook::pageTotal($dataProvider->models,'value'),
             'footerOptions' => ['style'=>'text-align:left;color:' . Cashbook::footerColor(Cashbook::pageTotal($dataProvider->models,'budgeted_value') - Cashbook::pageTotal($dataProvider->models,'value'))],
            ],
        ],
    ]);
     ?>
     <hr/>
     <div class="pull-left">
          <?php
          use kartik\export\ExportMenu;
              $gridColumns = [
                  ['attribute'=>'date','format'=>['date'], 'hAlign'=>'right', 'width'=>'110px'],  
              [
                  'attribute'=>'category_id',
                  'label'=> Yii::t('app', 'Category'),
                  'vAlign'=>'middle',
                  'width'=>'190px',
                  'value'=>function ($model, $key, $index, $widget) { 
                      return Html::a($model->category->desc_category, '#', []);
                  },
                  'format'=>'raw'
              ],                    
                  ['attribute'=>'value','format'=>['decimal',2], 'hAlign'=>'right', 'width'=>'110px'],
              ];
              echo ExportMenu::widget([
              'dataProvider' => $dataProvider,
              'columns' => $gridColumns,
              'fontAwesome' => true,
              'emptyText' => Yii::t('app', 'No entries found!'),
              'showColumnSelector' => true,
              'asDropdown' => true,
              'target' => ExportMenu::TARGET_BLANK,
              'showConfirmAlert' => false,
              'exportConfig' => [
                ExportMenu::FORMAT_HTML => false,
                ExportMenu::FORMAT_PDF => false
            ],
            'columnSelectorOptions' => [
              'class' => 'btn btn-primary btn-sm',
            ],
            'dropdownOptions' => [
              'label' => Yii::t('app', 'Export Data'),
              'class' => 'btn btn-primary btn-sm',
            ],
            ]);
          ?>    
     </div>
    </div>
    </div>
</div>
 */

