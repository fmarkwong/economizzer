<?php
use yii\helpers\Html;
$updateBudgetedValueLink = Html::a($budgetedValue, ["/budget/update-budgeted-value-form", 'id' => $budgetId]);
?>
<tr class="shift">
    <td style="text-align:left;padding-left: 2em"><span style="color:"><?=$subCategory->desc_category?></span></td>
    <td style="text-align:left;padding-left: 2em"><?=$updateBudgetedValueLink?></td>
    <td style="text-align:left;padding-left: 2em"><?=$actualValue?></td>
    <td style="text-align:left;padding-left: 2em"><strong style='color: <?=$subCategoryBalanceColor?>'><?=$subCategoryBalance?></strong></td>
    <td style="text-align:left;padding-left: 3em"><strong style='color: <?=$subCategoryBalanceColor?>'><?=$totalGoal?></strong></td>
    <td style="text-align:left;padding-left: 6em"><strong style='color: <?=$subCategoryBalanceColor?>'><?=$percentageCompleted . '%'?></strong></td>
</tr>
