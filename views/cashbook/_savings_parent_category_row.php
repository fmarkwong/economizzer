<?php use app\helpers\CashBookHelper; ?>
                            <tr id="6" style="border: solid thin;cursor: pointer;background-color: #ffbf00" >
                                <td style="text-align:left"><span style="color:"><?= Yii::t('app', $parentSavingsCategoryName) ?></span></td>
                                <td style="text-align:left;padding-left: 3.25em"><?=$savingsParentCategory['budgeted_total']?></td>
                                <td style="text-align:left;padding-left: 2.5em"><?=$savingsParentCategory['actual_total']?></td>
                                <td style="text-align:left;padding-left: 1.5em"><strong style='color:<?=CashBookHelper::color($category_balance)?>'><?=$category_balance?></strong></td>
                                <td style="text-align:left;padding-left: 3em"><strong><?=$SavingsTotal?></strong></td>
                                <td style="text-align:left;padding-left: 3em"><strong><?="$SavingsGoal / $totalPercentageCompleted"?></strong></td>
                            </tr>
