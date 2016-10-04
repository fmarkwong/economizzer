<?php use app\helpers\CashBookHelper; ?>
                            <tr id="6" onclick="location.href=&quot;/budgeter/web/cashbook/&quot;+(this.id);" style="border: solid thin;cursor: pointer;background-color: #ffbf00" data-key="6">
                                <td style="text-align:left"><span style="color:"><?= $parent_category ?></span></td>
                                <td style="text-align:left;padding-left: 3.25em"><?=$category['budgeted_total']?></td>
                                <td style="text-align:left;padding-left: 2.5em"><?=$category['actual_total']?></td>
                                <td style="text-align:left;padding-left: 1.5em"><strong style='color:<?=CashBookHelper::color($category_balance)?>'><?=$category_balance?></strong></td>
                                <td style="text-align:left;padding-left: 3em"><strong style='color:<?= 0 ?>'><?=$totalSavingsTotal?></strong></td>
                                <td style="text-align:left;padding-left: 1.5em"><strong style='color:<?= 0 ?>'><?="$totalSavingsGoal / $totalPercentageCompleted"?></strong></td>
                            </tr>
