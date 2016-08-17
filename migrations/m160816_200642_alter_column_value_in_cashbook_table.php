<?php

use yii\db\Migration;

class m160816_200642_alter_column_value_in_cashbook_table extends Migration
{
    public function up()
    {
        $this->alterColumn('cashbook', 'value', $this->float(), 'NULL');
    }

    public function down()
    {
        $this->alterColumn('cashbook', 'value', $this->flaot(), 'NOT NULL');
    }
}
