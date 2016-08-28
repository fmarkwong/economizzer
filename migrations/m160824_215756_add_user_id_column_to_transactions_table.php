<?php

use yii\db\Migration;

/**
 * Handles adding user_id to table `transaction`.
 */
class m160824_215756_add_user_id_column_to_transactions_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('transaction', 'user_id', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('transaction', 'user_id');
    }
}
