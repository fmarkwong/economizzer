<?php
use yii\helpers\Html;
use app\helpers\CashBookHelper;

$budgetUrl = $budgetId ? "/budget/update-budgeted-value-form" : "/budget/new";
// if ($categoryId == 1050) eval(\Psy\sh());
$updateBudgetedValueLink = Html::a($budgetedValue, [$budgetUrl, 'id' => $budgetId, 'category-id' => $categoryId], ['id' => "debt-budgeted-value-category-id-$categoryId"]);
$updatePrincipalLink = Html::a($principal, [$budgetUrl, 'id' => $budgetId, 'category-id' => $categoryId, 'show-debt-total-field' => 'true'], ['id' => "debt-total-category-id-$categoryId"]);
$transactionUrl = $transactionId ? "/transaction/update-actual-value-form" : "/transaction/new";
$updateActualValueLink = Html::a($actualValue, [$transactionUrl, 'id' => $transactionId, 'category-id' => $categoryId], ['id' => "actual-value-category-id-$categoryId"]);
?>
<tr class="shift" style="cursor: pointer" data-key="<?=$budgetId?>">
    <td style="text-align:left;padding-left: 2em"><span style="color:"><?=$subCategory->desc_category?></span></td>
    <td style="text-align:left;padding-left: 3.25em"><?=$updateBudgetedValueLink?></td>
    <td style="text-align:left;padding-left: 2.5em"><?=$updateActualValueLink?></td>
    <td style="text-align:left;padding-left: 1.5em"><strong id="balance-category-id-<?=$subCategory->id_category?>" style='color: <?= CashbookHelper::color($subCategoryBalance)?>'><?=$subCategoryBalance?></strong></td>
    <td style="text-align:left;padding-left: 3em"><strong style='color: <?= 0 ?>'><?=$currentDebt?></strong></td>
    <td style="text-align:left;padding-left: 4em"><strong style='color: <?= 0 ?>'><?="$updatePrincipalLink / $percentageCompleted"?></strong></td>
</tr>
