<?php

use yii\db\Migration;

class m160824_183555_add_default_categories_to_category_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('category', 'budgeted_value');
        $this->dropColumn('category', 'actual_value');

        $this->addColumn('category', 'budgeted_total', $this->float()->defaultValue(0));
        $this->addColumn('category', 'actual_total', $this->float()->defaultValue(0));



        $this->addColumn('category', 'type_id', $this->integer());
        $this->execute('ALTER TABLE category ADD parent_or_sub VARCHAR(10) AFTER id_category');

        $users = app\models\User::find()->all();

        foreach($users as $user) {
            \app\models\Category::addDefaultCategories($user->id);
        }
    }

    public function safeDown()
    {
        // $categories_to_delete= array_merge($this->array_flatten($this->categories), array_keys($this->categories));
        // app\models\Category::deleteAll(['desc_category' => $categories_to_delete]);
        app\models\Category::deleteAll();
        
        $this->dropColumn('category', 'parent_or_sub');
        $this->dropColumn('category', 'type_id');
        $this->addColumn('category', 'budgeted_value', $this->float());
        $this->addColumn('category', 'actual_value', $this->float());

        $this->dropColumn('category', 'budgeted_total');
        $this->dropColumn('category', 'actual_total');
    }

    // http://www.cowburn.info/2012/03/17/flattening-a-multidimensional-array-in-php/
    private function array_flatten($arr) {
        return iterator_to_array(new RecursiveIteratorIterator(
            new RecursiveArrayIterator($arr)), FALSE);
    }
}
