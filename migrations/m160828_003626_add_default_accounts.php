<?php

use yii\db\Migration;

class m160828_003626_add_default_accounts extends Migration
{
    public function up()
    {
        $this->addColumn('account', 'balance', $this->float()->defaultValue(0));

        $users = app\models\User::find()->all();

        foreach($users as $user) {
            $new_account = new app\models\Account;
            $new_account->load(['Account' => ['name' => 'cash',
                                              'user_id' => $user->id
                               ]]);
            $new_account->save();
        }

    }

    public function down()
    {
        app\models\Account::deleteAll(['name' => 'Cash']);
        $this->dropColumn('account', 'balance');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
