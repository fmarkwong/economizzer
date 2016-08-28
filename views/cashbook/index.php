<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Cashbook;

$this->title = Yii::t('app', 'Budget');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-3">
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                  <strong><?php echo Yii::t('app', 'Filters');?>
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFilter" aria-expanded="true" aria-controls="collapseFilter">
                      <span class="glyphicon glyphicon-resize-small pull-right" aria-hidden="true"></span>
                    </a>
                  </strong>
                </div>
                <div id="collapseFilter" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">
                        <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">

        <div class="cashbook-index">
            <h2>
              <span><?= Html::encode($this->title) ?></span>
              <?php $total_budgeted_value = (app\models\Cashbook::findBySql('SELECT SUM(budgeted_value) as total_budgeted_value FROM cashbook WHERE user_id = :user_id', [':user_id' => Yii::$app->user->id])->asArray()->one())?>
              <?php $account_balance = app\models\Account::findOne(['user_id' => YII::$app->user->id, 'name' => 'cash'])->balance ?>
              <?php $total_left_to_budget = $account_balance - $total_budgeted_value['total_budgeted_value'] ?>
              <?php $color = Cashbook::footerColor($total_left_to_budget) ?>
              <span style="color: <?=$color?>; font-size: 20px; vertical-align: middle"> Left to budget: <?= $total_left_to_budget?></span>
                <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Create').'', ['/cashbook/create'], ['class'=>'btn btn-primary grid-button pull-right']) ?>
            </h2>
            <hr/>

            <?php foreach (Yii::$app->session->getAllFlashes() as $key=>$message):?>
                <?php $alertClass = substr($key,strpos($key,'-')+1); ?>
                <div class="alert alert-dismissible alert-<?=$alertClass?>" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <p><?=$message?></p>
                </div>
            <?php endforeach ?>

    <!-- BUDGET TABLE -->
            <?php
                // $top_categories = app\models\Category::findBySql('SELECT id_category, desc_category from category WHERE parent_id IS NULL')->asArray()->all();
                $top_categories = app\models\Category::findBySql('SELECT id_category, desc_category from category WHERE parent_id IS NULL AND user_id=:user_id', [':user_id' => Yii::$app->user->id])->asArray()->all();

                $top_category_array = [];
                foreach($top_categories as $tp) {
                    $top_category_desc_array[$tp['id_category']] = $tp['desc_category']; 
                }
                // this will only show parent categories that have subcategories (because of the GROUP BY clause
                $categories = app\models\Category::findBySql('SELECT *, sum(budgeted_value) as budgeted_total, sum(actual_value) as actual_total FROM category WHERE user_id=:user_id GROUP BY parent_id HAVING parent_id IS NOT NULL', [':user_id' => Yii::$app->user->id])->asArray()->all();
                $all_categories = app\models\Category::find()->all();
                $category_budgeted_value_total = app\models\Cashbook::pageTotal($all_categories, 'budgeted_value');
                $category_actual_value_total = app\models\Cashbook::pageTotal($all_categories, 'actual_value');
                $total_budget_balance = $category_budgeted_value_total - $category_actual_value_total;
                $color = app\models\CashBook::footerColor($total_budget_balance);
            ?>

            <div id="w1" class="grid-view">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><a href="/budgeter/web/cashbook/index?sort=parent_category_id" data-sort="parent_category_id">Category</a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=budgeted_value" data-sort="budgeted_value">Budgeted Value</a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=value" data-sort="value">Actual Value</a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=value" data-sort="value">Balance</a></th>
                        </tr>
                    </thead>
                    <!-- <tfoot> -->
                    <!--     <tr style="font&#45;weight:bold;"> -->
                    <!--         <td style="text&#45;align:left;">Total</td> -->
                    <!--         <td style="text&#45;align:left;padding&#45;left: 2em"><?=$category_budgeted_value_total?></td> -->
                    <!--         <td style="text&#45;align:left;padding&#45;left: 2em"><?=$category_actual_value_total?></td> -->
                    <!--         <td style="text&#45;align:left;color:<?=$color?>;padding&#45;left: 2em"><?=$total_budget_balance?></td> -->
                    <!--     </tr> -->
                    <!-- </tfoot> -->
                    <tbody>
                        <?php foreach($categories as $category): ?>
                        <?php $parent_category = $top_category_desc_array[$category['parent_id']] ?>
                        <?php if ($parent_category === 'Income') continue?>
                        <?php $color = Cashbook::footerColor($category_balance = $category['budgeted_total'] - $category['actual_total']) ?>
                            <tr id="6" onclick="location.href=&quot;/budgeter/web/cashbook/&quot;+(this.id);" style="border: solid thin;cursor: pointer;background-color: #ffbf00" data-key="6">
                                <td style="text-align:left"><span style="color:"><?= $parent_category ?></span></td>
                                <td style="text-align:left;padding-left: 2em"><?=$category['budgeted_total']?></td>
                                <td style="text-align:left;padding-left: 2em"><?=$category['actual_total']?></td>
                                <td style="text-align:left;padding-left: 2em"><strong style='color:<?=$color?>'><?=$category_balance?></strong></td>
                            </tr>
                            <?php $sub_categories = app\models\Category::find()->where(['parent_id' => $category['parent_id']])->all(); ?>
                            <?php foreach($sub_categories as $sub_category): ?>
                                <?php $color = Cashbook::footerColor($sub_category_balance = $sub_category->budgeted_value - $sub_category->actual_value) ?>
                                <tr id="6" onclick="location.href=&quot;/budgeter/web/cashbook/&quot;+(this.id);" style="cursor: pointer" data-key="6">
                                    <td style="text-align:left;padding-left: 2em"><span style="color:"><?=$sub_category->desc_category?></span></td>
                                    <td style="text-align:left;padding-left: 2em"><?=$sub_category->budgeted_value?></td>
                                    <td style="text-align:left;padding-left: 2em"><?=$sub_category->actual_value?></td>
                                    <td style="text-align:left;padding-left: 2em"><strong style='color: <?=$color?>'><?=$sub_category_balance?></strong></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endforeach ?>
                    </tbody>
                </table> <!-- END BUDGET TABLE -->
            </div> <!-- w1 gridview-->
        </div><!-- cashbook index Budget-->
        <br>
        <div class="cashbook-index">  <!-- TRANSACTIONS -->
            <h2>
                <span>Account</span>
                <?php $account_balance = app\models\Account::findOne(['user_id' => YII::$app->user->id, 'name' => 'cash'])->balance ?>
                <?php $color = app\models\Cashbook::footerColor($account_balance) ?>
                <span style="color: <?=$color?>; font-size: 20px; vertical-align: middle"> Balance: <?= $account_balance ?></span>
                <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Add').'', ['/transaction/new'], ['class'=>'btn btn-primary grid-button pull-right']) ?>
            </h2>
            <hr/>
<!--
            <?php foreach (Yii::$app->session->getAllFlashes() as $key=>$message):?>
                <?php $alertClass = substr($key,strpos($key,'-')+1); ?>
                <div class="alert alert-dismissible alert-<?=$alertClass?>" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <p><?=$message?></p>
                </div>
            <?php endforeach ?>
-->

    <!-- TRANSACTIONS TABLE -->
            <?php
                // $top_categories = app\models\Category::findBySql('SELECT id_category, desc_category from category WHERE parent_id IS NULL')->asArray()->all();

                // $top_category_array = [];
                // foreach($top_categories as $tp) {
                //     $top_category_desc_array[$tp['id_category']] = $tp['desc_category']; 
                // }
                // $categories = app\models\Category::findBySql('SELECT *, sum(budgeted_value) as budgeted_total, sum(actual_value) as actual_total FROM category GROUP BY parent_id HAVING parent_id IS NOT NULL')->asArray()->all();
                // $all_categories = app\models\Category::find()->all();
                // $category_budgeted_value_total = app\models\Cashbook::pageTotal($all_categories, 'budgeted_value');
                // $category_actual_value_total = app\models\Cashbook::pageTotal($all_categories, 'actual_value');

                $transactions = app\models\Transaction::findAll(['user_id' => YII::$app->user->id]);
                
            ?>

            <div id="w1" class="grid-view">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><a href="/budgeter/web/cashbook/index?sort=parent_category_id" data-sort="parent_category_id">Date</a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=budgeted_value" data-sort="budgeted_value">Description</a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=value" data-sort="value">Category</a></th>
                            <th><a href="/budgeter/web/cashbook/index?sort=value" data-sort="value">Value</a></th>
                        </tr>
                    </thead>
<!--
                    <tfoot>
                        <tr style="font-weight:bold;">
                            <td style="text-align:left;">Total</td>
                            <td style="text-align:left;padding-left: 2em"><?=$category_budgeted_value_total?></td>
                            <td style="text-align:left;padding-left: 2em"><?=$category_actual_value_total?></td>
                            <td style="text-align:left;color:#18bc9c;padding-left: 2em"><?=$category_budgeted_value_total - $category_actual_value_total?></td>
                        </tr>
                    </tfoot>
-->
                    <tbody>
                        <?php foreach($transactions as $transaction): ?>
                            <?php //$color = Cashbook::footerColor($category_balance = $category['budgeted_total'] - $category['actual_total']) ?>
                            <?php $category = $transaction->category ?>
                            <?php $category_desc = $category->desc_category; ?>
                            <?php if ($category->getParent()) $parent_category_desc = $category->getParent()->one()->desc_category; ?> 
                            <tr id="6" onclick="location.href=&quot;/budgeter/web/cashbook/&quot;+(this.id);" style="cursor: pointer" data-key="6">
                                <td style="text-align:left"><span style="color:"><?= $transaction->date ?></span></td>
                                <td style="text-align:left"><?=$transaction->description?></td>
                                <td style="text-align:left"><?="$parent_category_desc/$category_desc"?></td>
                                <td style="text-align:left"><?=$transaction->value?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table> <!-- END TRANSACTIONS TABLE -->
            </div> <!-- w1 gridview transactions-->
        </div><!-- end transactions div-->
    </div><!-- col-sm-12 -->
</div> <!-- row -->



    
<?php 
/*   OLD GRID CODE FOR REFERENCE TODO:
GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class'=>'table table-striped table-hover'],
        'emptyText'    => '</br><p class="text-danger">'.Yii::t('app', 'No entries found!').'</p>',
        'summary'      =>  '',
        'showFooter'   => true,
        'showOnEmpty'  => false,
        'footerRowOptions'=>['style'=>'font-weight:bold;'],
        'rowOptions'   => function ($model, $index, $widget, $grid) {
                return [
                    'id' => $model['id'], 
                    'onclick' => 'location.href="'
                        . Yii::$app->urlManager->createUrl('cashbook/') 
                        . '/"+(this.id);',
                    'style' => "cursor: pointer",
                ];
        },        
        'columns'    => [
            [
            'attribute' => 'date',
            'enableSorting' => true,
            'value' => function ($model) {                      
                    return $model->date <> '' ? Yii::$app->formatter->asDate($model->date, 'short') : Yii::$app->formatter->asDate($model->date, 'short');
                    },
            // 'contentOptions'=>['style'=>'width: 15%;text-align:left'],
            'contentOptions'=>['style'=>'text-align:left'],
            'footer' => 'Total',
            ],
            // Parent Category
            [
            'attribute' => 'parent_category_id',
            'format' => 'raw',
            'enableSorting' => true,
            'value' => function ($model) {                      
                    return '<span style="color:'.$model->category->hexcolor_category.'">'.$model->getParentCategory()->desc_category.'</span>';
                    },
            // 'contentOptions'=>['style'=>'width: 20%;text-align:left'],
            'contentOptions'=>['style'=>'text-align:left'],
            'footerOptions' => ['style'=>'text-align:left'],                  
            ],
            //sub category
            [
            'attribute' => 'category_id',
            'format' => 'raw',
            'enableSorting' => true,
            'value' => function ($model) {                      
                    return '<span style="color:'.$model->category->hexcolor_category.'">'.$model->category->desc_category.'</span>';
                    },
            // 'contentOptions'=>['style'=>'width: 20%;text-align:left'],
            'contentOptions'=>['style'=>'text-align:left'],
            'footerOptions' => ['style'=>'text-align:left'],                  
            ],
            [
            'attribute' => 'description',
            'format' => 'raw',
            'enableSorting' => true,
            'value' => function ($model) {                      
                           return $model->description; 
                    },
            // 'contentOptions'=>['style'=>'width: 20%;text-align:left'],
            'contentOptions'=>['style'=>'text-align:left'],
            'footerOptions' => ['style'=>'text-align:left'],                  
            ],
            [
            'label' => 'Budgeted Value',
            'attribute' => 'budgeted_value',
            'format' => 'raw',
            'enableSorting' => true,
            'value' => function ($model) {                      
                        return $model->budgeted_value;
                    },
            // 'contentOptions'=>['style'=>'width: 20%;text-align:left'],
            'contentOptions'=>['style'=>'text-align:left'],
             'footer' => Cashbook::pageTotal($dataProvider->models,'budgeted_value'),
            'footerOptions' => ['style'=>'text-align:left'],                  
            ],
            [
             'label' => 'Actual Value',
             'attribute' => 'value',
             'format' => 'raw',
             'value' => function ($model) {  
                    return $model->is_pending === 0 ? $model->value : '<span class="glyphicon glyphicon-flag" style="color:orange" aria-hidden="true"></span> <strong style="color:'.$model->type->hexcolor_type.'">'.' '.$model->value.'</strong>';
                    },
             'enableSorting' => true,
            'contentOptions'=>['style'=>'text-align:left'],
             // 'contentOptions'=>['style'=>'width: 20%;text-align:left'],
             'footer' => Cashbook::pageTotal($dataProvider->models,'value'),
             'footerOptions' => ['style'=>'text-align:left'],
            ],
            [
             'label' => 'Balance',
             'attribute' => 'value',
             'format' => 'raw',
             'value' => function ($cash_book) {  
                        if ($cash_book->type_id == 1 ) // income
                            $value = $cash_book->value - $cash_book->budgeted_value;
                        else // expense
                            $value = $cash_book->budgeted_value - $cash_book->value;
                        $color = Cashbook::footerColor($value);
                        return "<strong style='color: $color'>" . $value . '</strong>';
                    },
             'enableSorting' => true,
             // 'contentOptions'=>['style'=>'width: 20%;text-align:left'],
            'contentOptions'=>['style'=>'text-align:left'],
             'footer' => Cashbook::pageTotal($dataProvider->models,'budgeted_value') - Cashbook::pageTotal($dataProvider->models,'value'),
             'footerOptions' => ['style'=>'text-align:left;color:' . Cashbook::footerColor(Cashbook::pageTotal($dataProvider->models,'budgeted_value') - Cashbook::pageTotal($dataProvider->models,'value'))],
            ],
        ],
    ]);
     ?>
     <hr/>
     <div class="pull-left">
          <?php
          use kartik\export\ExportMenu;
              $gridColumns = [
                  ['attribute'=>'date','format'=>['date'], 'hAlign'=>'right', 'width'=>'110px'],  
              [
                  'attribute'=>'category_id',
                  'label'=> Yii::t('app', 'Category'),
                  'vAlign'=>'middle',
                  'width'=>'190px',
                  'value'=>function ($model, $key, $index, $widget) { 
                      return Html::a($model->category->desc_category, '#', []);
                  },
                  'format'=>'raw'
              ],                    
                  ['attribute'=>'value','format'=>['decimal',2], 'hAlign'=>'right', 'width'=>'110px'],
              ];
              echo ExportMenu::widget([
              'dataProvider' => $dataProvider,
              'columns' => $gridColumns,
              'fontAwesome' => true,
              'emptyText' => Yii::t('app', 'No entries found!'),
              'showColumnSelector' => true,
              'asDropdown' => true,
              'target' => ExportMenu::TARGET_BLANK,
              'showConfirmAlert' => false,
              'exportConfig' => [
                ExportMenu::FORMAT_HTML => false,
                ExportMenu::FORMAT_PDF => false
            ],
            'columnSelectorOptions' => [
              'class' => 'btn btn-primary btn-sm',
            ],
            'dropdownOptions' => [
              'label' => Yii::t('app', 'Export Data'),
              'class' => 'btn btn-primary btn-sm',
            ],
            ]);
          ?>    
     </div>
    </div>
    </div>
</div>
 */

