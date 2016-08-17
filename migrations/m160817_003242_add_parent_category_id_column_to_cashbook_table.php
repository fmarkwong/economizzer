<?php

use yii\db\Migration;

/**
 * Handles adding parent_category_id to table `cashbook`.
 */
class m160817_003242_add_parent_category_id_column_to_cashbook_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('cashbook', 'parent_category_id', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('cashbook', 'parent_category_id');
    }
}
