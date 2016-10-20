<?php use app\helpers\CashBookHelper; ?>
                            <tr id="6" onclick="location.href=&quot;/budgeter/web/cashbook/&quot;+(this.id);" style="border: solid thin;cursor: pointer;background-color: #ffbf00" data-key="6">
                                <td style="text-align:left"><span style="color:"><?= Yii::t('app', $parent_category) ?></span></td>
                                <td id="budgeted-value-total-category-id-<?=$category['id_category']?>" style="text-align:left;padding-left: 2em"><?= Yii::t('app', $category['budgeted_total']) ?></td>
                                <td id="actual-value-total-category-id-<?=$category['id_category']?>" style="text-align:left;padding-left: 2em"><?= Yii::t('app', $category['actual_total']) ?></td>
                                <td id="balance-total-category-id-<?=$category['id_category']?>" style="text-align:left;padding-left: 2em"><strong style='color:<?=CashBookHelper::color($category_balance)?>'><?=$category_balance?></strong></td>
                            </tr>
