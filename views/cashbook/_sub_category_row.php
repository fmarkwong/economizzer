<?php
use yii\helpers\Html;
use app\helpers\CashBookHelper;

$budgetUrl = $budgetId ? "/budget/update-budgeted-value-form" : "/budget/new";
$updateBudgetedValueLink = Html::a($budgetedValue, [$budgetUrl, 'id' => $budgetId, 'category-id' => $categoryId], ['id' => "budgeted-value-category-id-$categoryId"]);
$transactionUrl = $transactionId ? "/transaction/update-actual-value-form" : "/transaction/new";
$updateActualValueLink = Html::a($actualValue, [$transactionUrl, 'id' => $transactionId, 'category-id' => $categoryId], ['id' => "actual-value-category-id-$categoryId"]);
?>
<tr class="shift" style="cursor: pointer" data-key="<?=$budgetId?>">
    <td style="text-align:left;padding-left: 2em"><span style="color:"><?=$subCategory->desc_category?></span></td>
    <td style="text-align:left;padding-left: 2em"><?=$updateBudgetedValueLink?></td>
    <td style="text-align:left;padding-left: 2em"><?=$updateActualValueLink?></td>
    <td style="text-align:left;padding-left: 2em"><strong id="balance-category-id-<?=$subCategory->id_category?>" style='color: <?=CashBookHelper::color($subCategoryBalance)?>'><?=$subCategoryBalance?></strong></td>
</tr>
