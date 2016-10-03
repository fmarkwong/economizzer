<?php
use yii\helpers\Html;
$budgetUrl = $budgetId ? "/budget/update-budgeted-value-form" : "/budget/new";
$updateBudgetedValueLink = Html::a($budgetedValue, [$budgetUrl, 'id' => $budgetId, 'category_id' => $categoryId]);
$transactionUrl = $transactionId ? "/transaction/update-actual-value-form" : "/transaction/new";
$updateActualValueLink = Html::a($actualValue, [$transactionUrl, 'id' => $transactionId, 'category_id' => $categoryId]);
?>
<tr class="shift" style="cursor: pointer" data-key="<?=$budgetId?>">
    <td style="text-align:left;padding-left: 2em"><span style="color:"><?=$subCategory->desc_category?></span></td>
    <td id="<?=$budgetId?>"  style="text-align:left;padding-left: 2em"><?=$updateBudgetedValueLink?></td>
    <td style="text-align:left;padding-left: 2em"><?=$updateActualValueLink?></td>
    <td style="text-align:left;padding-left: 2em"><strong style='color: <?=$subCategoryBalanceColor?>'><?=$subCategoryBalance?></strong></td>
</tr>
