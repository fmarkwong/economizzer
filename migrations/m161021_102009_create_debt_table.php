<?php

use yii\db\Migration;

/**
 * Handles the creation of table `debt`.
 * Has foreign keys to the tables:
 *
 * - `account`
 * - `category`
 */
class m161021_102009_create_debt_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('debt', [
            'id' => $this->primaryKey(),
            'current_value' => $this->float()->defaultValue(0),
            'principal' => $this->float()->defaultValue(0),
            'account_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `account_id`
        $this->createIndex(
            'idx-debt-account_id',
            'debt',
            'account_id'
        );

        // add foreign key for table `account`
        $this->addForeignKey(
            'fk-debt-account_id',
            'debt',
            'account_id',
            'account',
            'id',
            'CASCADE'
        );

        // creates index for column `category_id`
        $this->createIndex(
            'idx-debt-category_id',
            'debt',
            'category_id'
        );

        // add foreign key for table `category`
        $this->addForeignKey(
            'fk-debt-category_id',
            'debt',
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
        // drops foreign key for table `account`
        $this->dropForeignKey(
            'fk-debt-account_id',
            'debt'
        );

        // drops index for column `account_id`
        $this->dropIndex(
            'idx-debt-account_id',
            'debt'
        );

        // drops foreign key for table `category`
        $this->dropForeignKey(
            'fk-debt-category_id',
            'debt'
        );

        // drops index for column `category_id`
        $this->dropIndex(
            'idx-debt-category_id',
            'debt'
        );

        $this->dropTable('debt');
    }
}
