<?php

use yii\db\Migration;

/**
 * Handles the creation of table `budget`.
 * Has foreign keys to the tables:
 *
 * - `category`
 */
class m160909_183657_create_budget_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('budget', [
            'id' => $this->primaryKey(),
            'budgeted_value' => $this->float()->defaultValue(0),
            'actual_value' => $this->float()->defaultValue(0),
            'date' => $this->date(),
            'category_id' => $this->integer(),
        ]);

        // creates index for column `category_id`
        $this->createIndex(
            'idx-budget-category_id',
            'budget',
            'category_id'
        );

        // add foreign key for table `category`
        $this->addForeignKey(
            'fk-budget-category_id',
            'budget',
            'category_id',
            'category',
            'id_category',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `category`
        $this->dropForeignKey(
            'fk-budget-category_id',
            'budget'
        );

        // drops index for column `category_id`
        $this->dropIndex(
            'idx-budget-category_id',
            'budget'
        );

        $this->dropTable('budget');
    }
}
