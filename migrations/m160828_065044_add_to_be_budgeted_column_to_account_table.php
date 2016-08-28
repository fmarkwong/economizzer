<?php

use yii\db\Migration;

/**
 * Handles adding to_be_budgeted to table `account`.
 */
class m160828_065044_add_to_be_budgeted_column_to_account_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('account', 'to_be_budgeted', $this->float()->defaultValue(0));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('account', 'to_be_budgeted');
    }
}
