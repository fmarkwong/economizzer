<?php use app\helpers\CashBookHelper; ?>
                            <tr id="6" style="border: solid thin;cursor: pointer;background-color: #ffbf00" >
                                <td style="text-align:left"><span style="color:"><?= Yii::t('app', $parentDebtCategoryName) ?></span></td>
                                <td style="text-align:left;padding-left: 3.25em"><?=$debtParentCategory['budgeted_total']?></td>
                                <td style="text-align:left;padding-left: 2.5em"><?=$debtParentCategory['actual_total']?></td>
                                <td style="text-align:left;padding-left: 1.5em"><strong style='color:<?=CashBookHelper::color($category_balance)?>'><?=$category_balance?></strong></td>
                                <td style="text-align:left;padding-left: 3em"><strong><?=$currentDebtTotal?></strong></td>
                                <td style="text-align:left;padding-left: 1.5em"><strong><?="$principalTotal / $totalPercentageCompleted"?></strong></td>
                            </tr>
