<?php

use yii\db\Migration;

/**
 * Handles adding value to table `transaction`.
 */
class m160824_151858_add_value_column_to_transactions_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('transaction', 'value', $this->float());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('transaction', 'value');
    }
}
