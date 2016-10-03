<?php

use yii\db\Migration;

/**
 * Handles adding transaction_id to table `budget`.
 */
class m161003_180957_add_transaction_id_column_to_budget_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('budget', 'transaction_id', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('budget', 'transaction_id');
    }
}
