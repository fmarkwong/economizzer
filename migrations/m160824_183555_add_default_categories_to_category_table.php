<?php

use yii\db\Migration;

class m160824_183555_add_default_categories_to_category_table extends Migration
{
    public $categories = ['Immediate Obligations' => 
                            ['type' => 2,
                            'sub_categories' => ['Rent/Mortgage', 'Groceries', 'Electric', 'Water', 'Phone', 'Transportation', 'Interest & Fees']
                            ],
                          'True Expenses' =>
                            ['type' => 2,
                            'sub_categories' => ['Auto Maintenance', 'Home Maintenance', 'Insurance', 'Medical', 'Clothing', 'Gifts', 'Giving', 'Stuff I forgot to budget for'],
                            ],
                          'Other' => 
                            ['type' => 2,
                            'sub_categories' => []
                            ],
                          'Income' =>
                            ['type' => 1,
                            'sub_categories' => ['All income']
                            ],
                      ];          


    public function safeUp()
    {
        $this->addColumn('category', 'type_id', $this->integer());

        $users = app\models\User::find()->all();

        foreach($users as $user) {
            foreach($this->categories as $parent_category => $value) {
                $new_parent_category = new app\models\Category;
                $new_parent_category->desc_category = $parent_category; 
                $new_parent_category->is_active = 1;
                $new_parent_category->user_id = $user->id;
                $new_parent_category->type_id = $value['type'];
                $new_parent_category->save();
                foreach($value['sub_categories'] as $sub_category) {
                    $new_sub_category = new app\models\Category;
                    $new_sub_category->desc_category = $sub_category; 
                    $new_sub_category->parent_id = $new_parent_category->id_category; 
                    $new_sub_category->is_active = 1;
                    $new_sub_category->user_id = $user->id;
                    $new_sub_category->type_id = $value['type'];
                    $new_sub_category->save();
                }
            }
        }
    }

    public function safeDown()
    {
        $categories_to_delete= array_merge($this->array_flatten($this->categories), array_keys($this->categories));
        // $categories_to_delete= array_keys($this->categories);
        app\models\Category::deleteAll(['desc_category' => $categories_to_delete]);
        $this->dropColumn('category', 'type_id');
    }

    // http://www.cowburn.info/2012/03/17/flattening-a-multidimensional-array-in-php/
    private function array_flatten($arr) {
        return iterator_to_array(new RecursiveIteratorIterator(
            new RecursiveArrayIterator($arr)), FALSE);
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
