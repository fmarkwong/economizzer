<?php

use yii\db\Migration;

/**
 * Handles adding budgeted_value_and_actual_value to table `category`.
 */
class m160818_175732_add_budgeted_value_and_actual_value_columns_to_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('category', 'budgeted_value', $this->float());
        $this->addColumn('category', 'actual_value', $this->float());

        $cashBooks = app\models\Cashbook::find()->all();

        foreach ($cashBooks as $cashbook) {
            $category = $cashbook->category; 
            $category->budgeted_value = $cashbook->budgeted_value;
            $category->actual_value = $cashbook->value;
            $category->save();
        }

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('category', 'budgeted_value');
        $this->dropColumn('category', 'actual_value');
    }
}
