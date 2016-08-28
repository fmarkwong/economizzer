<?php

use yii\db\Migration;

/**
 * Handles the creation of table `transaction`.
 * Has foreign keys to the tables:
 *
 * - `account`
 * - `category`
 */
class m160819_205956_create_transactions_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('transaction', [
            'id' => $this->primaryKey(),
            'description' => $this->string(),
            'date' => $this->date(),
            'income' => $this->float(),
            'expense' => $this->float(),
            'balance' => $this->float(),
            'account_id' => $this->integer(),
            'category_id' => $this->integer(),
        ]);

        // creates index for column `account_id`
        $this->createIndex(
            'idx-transaction-account_id',
            'transaction',
            'account_id'
        );

        // add foreign key for table `account`
        $this->addForeignKey(
            'fk-transaction-account_id',
            'transaction',
            'account_id',
            'account',
            'id',
            'CASCADE'
        );

        // creates index for column `category_id`
        $this->createIndex(
            'idx-transaction-category_id',
            'transaction',
            'category_id'
        );

        // add foreign key for table `category`
        $this->addForeignKey(
            'fk-transaction-category_id',
            'transaction',
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
            'fk-transaction-account_id',
            'transaction'
        );

        // drops index for column `account_id`
        $this->dropIndex(
            'idx-transaction-account_id',
            'transaction'
        );

        // drops foreign key for table `category`
        $this->dropForeignKey(
            'fk-transaction-category_id',
            'transaction'
        );

        // drops index for column `category_id`
        $this->dropIndex(
            'idx-transaction-category_id',
            'transaction'
        );

        $this->dropTable('transaction');
    }
}
