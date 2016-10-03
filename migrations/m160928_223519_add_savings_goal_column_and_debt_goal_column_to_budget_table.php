<?php

use yii\db\Migration;

/**
 * Handles adding savings_goal_column_and_debt_goal to table `budget`.
 */
class m160928_223519_add_savings_goal_column_and_debt_goal_column_to_budget_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('budget', 'savings_goal', $this->float()->defaultValue(0));
        $this->addColumn('budget', 'debt_goal', $this->float());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('budget', 'savings_goal');
        $this->dropColumn('budget', 'debt_goal');
    }
}
