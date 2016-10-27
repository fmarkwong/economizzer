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

function Tip($text) {
     return Html::tag('i', '', [
                'title'=> $text,
                'data-toggle'=>'tooltip',
                'style'=>'cursor:pointer;',
                'class'=>'fa fa-question-circle',
            ]);
}

$tip = Tip(Yii::t('app', "Instructions"));

$budgetedValueTooltip = Tip(Yii::t('app', "Enter how much you plan to spend in each category for the month"));
$actualValueTooltip = Tip(Yii::t('app', "Each time you spend money in a category, record it here"));

$savingsBudgetedValueTip = Tip(Yii::t('app', "Enter how much you plan to save this month for each category"));
$savingsActualValueTip = Tip(Yii::t('app', "Each time you set aside money for a goal, record it here"));
$savingsGoalTip = Tip(Yii::t('app', "Enter your amounts for each saving goal here"));

$debtBudgetedValueTip = Tip(Yii::t('app', "Enter how much you plan to pay down debt this month for each category"));
$debtActualValueTip = Tip(Yii::t('app', "Each time you pay down debt for a category, record it here"));
$debtPrincipalTip = Tip(Yii::t('app', "Enter the total principal for each debt here"));
?>

<div class="row">
    <div class="col-sm-4 col-sm-offset-4 month-picker">
        <?= Html::a(null, ['/cashbook/previous-month'], ['class' => 'glyphicon glyphicon-circle-arrow-left']) ?>
        <h2 class="month-picker"><?= Yii::t('app', $month) . " $year"?></h2>
        <?= Html::a(null, ['/cashbook/next-month'], ['class' => 'glyphicon glyphicon-circle-arrow-right']) ?>
    </div>
</div> 

<div class="row">
    <div class="col-sm-16">

        <div class="cashbook-index"> <!-- BUDGETS SECTION -->
            <h2>
              <span><?= Html::encode($this->title) ?></span>
              <?php $color = CashBookHelper::balanceColor($totalLeftToBudget) ?>
              <span style="color: <?=CashBookHelper::balanceColor($totalLeftToBudget)?>; font-size: 20px; vertical-align: middle"> <?= Yii::t('app', 'Left to budget') . ": $totalLeftToBudget" ?></span>
                <?php //echo Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Create').'', ['/budget/new'], ['class'=>'btn btn-primary grid-button pull-right']) ?>
            </h2>
            <div style='float:right; font-size: 1.1em'><?= Yii::t('app', 'Add or update values by clicking on the value') ?>.</div>
            <div style="clear: both"></div>
            <div style='float:right; font-size: 1.1em'><?= Yii::t('app', 'Hover over question marks') . " $tip " . Yii::t('app', 'to see instructions')  ?>.</div>
            <br>
            <hr>

            <?php ViewHelper::displayAllFlashes() ?>

            <div id="w1" class="grid-view">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><?= Yii::t('app', 'Category') ?></th>
                            <th><?= Yii::t('app', 'Budgeted Value') .' '.$budgetedValueTooltip ?></th>
                            <th><?= Yii::t('app', 'Actual Value') .' '. $actualValueTooltip ?></th>
                            <th><?= Yii::t('app', 'Balance') ?></th>
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
        <div class="cashbook-index"> <!-- SAVING GOALS SECTION -->
            <div id="w1" class="grid-view">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><?= Yii::t('app', 'Category') ?></th>
                            <th><?= Yii::t('app', 'Budgeted Value') . ' ' . $savingsBudgetedValueTip ?></th>
                            <th><?= Yii::t('app', 'Actual Value') . ' ' . $savingsActualValueTip ?></th>
                            <th><?= Yii::t('app', 'Balance') ?></th>
                            <th><?= Yii::t('app', 'Current Savings') ?></th>
                            <th><?= Yii::t('app', 'Goal ' . $savingsGoalTip . ' / Completed') ?></th>
                        </tr>
                    </thead>
                    <tbody>
<?php 
                        $parentSavingsCategoryName = $savingsParentCategory['desc_category']; 
                        $SavingsTotal = app\models\Account::getTotalSavingTotal(); 
                        $SavingsGoal = app\models\Account::getTotalSavingGoal();  
                        $category_balance = $savingsParentCategory['budgeted_total'] - $savingsParentCategory['actual_total'];
                        $totalPercentageCompleted = ($SavingsGoal > 0 ? ($SavingsTotal / $SavingsGoal) : 0);
                        $formatter = Yii::$app->formatter;
                        $totalPercentageCompleted = $formatter->asPercent($totalPercentageCompleted);
                        echo $this->render('_savings_parent_category_row', compact('parentSavingsCategoryName', 'savingsParentCategory', 'category_balance', 'SavingsGoal', 'totalPercentageCompleted', 'SavingsTotal'));

                        $sub_categories = app\models\Category::subCategories($savingsParentCategory['id_category']); 
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
                            $savingsGoal = $subCategory->totalSaving ? $subCategory->totalSaving->goal : 0; 
                            $percentageCompleted = $savingsGoal > 0 ? ($totalSavings / $savingsGoal) : 0;
                            $percentageCompleted = $formatter->asPercent($percentageCompleted);
                            
                            echo $this->render('_savings_sub_category_row', compact('subCategory', 'subCategoryBalance', 'actualValue', 'budgetedValue', 'savingsGoal', 'percentageCompleted', 'categoryId', 'budgetId', 'transactionId', 'totalSavings'));
                        }
?>
                    </tbody>
                </table> 
            </div> <!-- w1 gridview-->
        </div><!-- SAVING GOALS SECTION END -->
        <br>
        <div class="cashbook-index"> <!-- DEBT PAYMENT GOALS SECTION -->
            <div id="w1" class="grid-view">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><?= Yii::t('app', 'Category') ?></th>
                            <th><?= Yii::t('app', 'Budgeted Value') . ' ' . $debtBudgetedValueTip ?></th>
                            <th><?= Yii::t('app', 'Actual Value') . ' ' . $debtActualValueTip ?></th>
                            <th><?= Yii::t('app', 'Balance') ?></th>
                            <th><?= Yii::t('app', 'Current Debt') ?></th>
                            <th><?= Yii::t('app', 'Principal ' . $debtPrincipalTip . ' / Completed') ?></th>
                        </tr>
                    </thead>
                    <tbody>
<?php 
                        $parentDebtCategoryName = $debtParentCategory['desc_category']; 
                        $currentDebtTotal = app\models\Account::getCurrentDebtTotal(); 
                        $principalTotal = app\models\Account::getPrincipalTotal();  
                        $category_balance = $debtParentCategory['budgeted_total'] - $debtParentCategory['actual_total'];
                        // $totalPercentageCompleted = ($principalTotal > 0 ? ($currentDebtTotal / $principalTotal) : 0);
                        $totalPercentageCompleted = ($principalTotal > 0 ? ($principalTotal - $currentDebtTotal) / $principalTotal : 0);
                        $formatter = Yii::$app->formatter;
                        $totalPercentageCompleted = $formatter->asPercent($totalPercentageCompleted);
                        echo $this->render('_debt_parent_category_row', compact('parentDebtCategoryName', 'debtParentCategory', 'category_balance', 'principalTotal', 'totalPercentageCompleted', 'currentDebtTotal'));

                        $sub_categories = app\models\Category::subCategories($debtParentCategory['id_category']); 
                        foreach($sub_categories as $subCategory) {
                            $categoryId    = $subCategory->id_category;
                            $currentBudget = $subCategory->getCurrentBudget(); //TODO: add nullBudget object if not found so we don't have to keep checking for null below
                            $budgetId      = $currentBudget ? $currentBudget->id: 0;
                            $transaction   = $currentBudget ? $currentBudget->transaction : null;
                            $transactionId = $transaction ? $transaction->id : null;
                            $budgetedValue = $currentBudget ? $currentBudget->budgeted_value : 0;
                            $actualValue   = $currentBudget ? $currentBudget->actual_value : 0;
                            $subCategoryBalance = $budgetedValue - $actualValue;
                            $currentDebt = $subCategory->debt ? $subCategory->debt->current_value : 0; 
                            $principal = $subCategory->debt ? $subCategory->debt->principal : 0; 
                            // $percentageCompleted = $principal > 0 ? ($currentDebt / $principal) : 0;
                            $percentageCompleted = $principal > 0 ? ($principal - $currentDebt) / $principal : 0;
                            $percentageCompleted = $formatter->asPercent($percentageCompleted);
                            
                            echo $this->render('_debt_sub_category_row', compact('subCategory', 'subCategoryBalance', 'actualValue', 'budgetedValue', 'principal', 'percentageCompleted', 'categoryId', 'budgetId', 'transactionId', 'currentDebt'));
                        }
?>
                    </tbody>
                </table> 
            </div> <!-- w1 gridview-->
        </div> <!-- END DEBT PAYMENT GOALS SECTION -->
        <br>
        <div class="cashbook-index">  <!-- TRANSACTIONS -->
            <h2>
            <span><?= Yii::t('app', 'Account') ?></span>
                <?php $account_balance_color = cashBookHelper::balanceColor($accountBalance) ?>
                <span style="color: <?=$account_balance_color?>; font-size: 20px; vertical-align: middle"><?= Yii::t('app', 'Balance') ?>: <?= $accountBalance ?></span>
                <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Add Income').'', ['/transaction/new'], ['class'=>'btn btn-primary grid-button pull-right']) ?>
            </h2>
            <hr/>
    <!-- TRANSACTIONS TABLE -->

            <div id="w1" class="grid-view">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><?= Yii::t('app', 'Data') ?></th>
                            <th><?= Yii::t('app', 'Description') ?></th>
                            <th><?= Yii::t('app', 'Category') ?></th>
                            <th><?= Yii::t('app', 'Value') ?></th>
                        </tr>
                    </thead>
                    <tbody>
<?php 
                    foreach($transactions as $transaction) {
                        $category = $transaction->category;
                        $plusMinus = $category->type_id == 1 ? '+' : '-';
                        $category_desc = Yii::t('app', $category->desc_category);
                        $parent_category_desc = $category->getParent() ? $category->getParent()->one()->desc_category : NULL;
                        $parent_category_desc = Yii::t('app', $parent_category_desc); 
                        echo $this->render('_transaction_row', compact('plusMinus', 'transaction', 'category_desc', 'parent_category_desc'));
                    } 
?>
                    </tbody>
                </table> <!-- END TRANSACTIONS TABLE -->
            </div> <!-- w1 gridview transactions-->
        </div><!-- end transactions div-->
    </div><!-- col-sm-12 -->
</div> <!-- row -->
