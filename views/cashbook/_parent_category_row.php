                            <tr id="6" onclick="location.href=&quot;/budgeter/web/cashbook/&quot;+(this.id);" style="border: solid thin;cursor: pointer;background-color: #ffbf00" data-key="6">
                                <td style="text-align:left"><span style="color:"><?= $parent_category ?></span></td>
                                <td style="text-align:left;padding-left: 2em"><?=$category['budgeted_total']?></td>
                                <td style="text-align:left;padding-left: 2em"><?=$category['actual_total']?></td>
                                <td style="text-align:left;padding-left: 2em"><strong style='color:<?=$left_to_budget_color?>'><?=$category_balance?></strong></td>
                            </tr>
