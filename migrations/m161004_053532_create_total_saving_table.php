<?php

use yii\db\Migration;

/**
 * Handles the creation of table `total_saving`.
 */
class m161004_053532_create_total_saving_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('total_saving', [
            'id' => $this->primaryKey(),
            'value' => $this->float()->defaultValue(0),
            'account_id' => $this->integer(),
            'category_id' => $this->integer(),
            'goal' => $this->float()->defaultValue(0),
        ]);

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('total_saving');
    }
}
