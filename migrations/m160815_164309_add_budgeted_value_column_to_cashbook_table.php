<?php

use yii\db\Migration;

/**
 * Handles adding budgeted_value to table `cashbook`.
 */
class m160815_164309_add_budgeted_value_column_to_cashbook_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('cashbook', 'budgeted_value', $this->float());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('cashbook', 'budgeted_value');
    }
}
