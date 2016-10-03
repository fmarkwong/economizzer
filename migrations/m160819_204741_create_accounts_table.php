<?php

use yii\db\Migration;

/**
 * Handles the creation of table `account`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m160819_204741_create_accounts_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('account', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'user_id' => $this->integer(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-account-user_id',
            'account',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-account-user_id',
            'account',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-account-user_id',
            'account'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-account-user_id',
            'account'
        );

        $this->dropTable('account');
    }
}
