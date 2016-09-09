<tr id="6" onclick="location.href=&quot;/budgeter/web/cashbook/&quot;+(this.id);" style="cursor: pointer" data-key="6">
    <td style="text-align:left"><span style="color:"><?= $transaction->date ?></span></td>
    <td style="text-align:left"><?=$transaction->description?></td>
    <td style="text-align:left"><?="$parent_category_desc/$category_desc"?></td>
    <td style="text-align:left"><?=$transaction->value?></td>
</tr>

