<?php

namespace app\models;

use Yii;

class Category extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'category';
    }

    public function rules()
    {
        return [
            [['budgeted_total', 'actual_total', 'type_id'], 'number'],
            [['desc_category'], 'required'],
            [['is_active','user_id','parent_id'], 'integer'],
            [['desc_category', 'hexcolor_category', 'parent_or_sub'], 'string', 'max' => 45]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_category' => Yii::t('app', 'ID'),
            'type_id' => Yii::t('app', 'Type'),
            'budgeted_total' => Yii::t('app', 'Total Budgeted Value'),
            'actual_total' => Yii::t('app', 'Total Actual Total'),
            'desc_category' => Yii::t('app', 'Name'),
            'hexcolor_category' => Yii::t('app', 'Color'),
            'parent_id' => Yii::t('app', ''),
            'is_active' => Yii::t('app', 'Active'),
        ];
    }

    public function getCashbooks()
    {
        return $this->hasMany(Cashbook::className(), ['category_id' => 'id_category']);
    }

    public function getUser() 
    { 
       return $this->hasOne(User::className(), ['id' => 'user_id']); 
    } 

    public function getTotalSaving() 
    { 
       return $this->hasOne(TotalSaving::className(), ['category_id' => 'id_category']); 
    } 

    // public static function getHierarchy($exclude_income = false) {
    public static function getHierarchy($categories = null) {
        $options = [];
         
        $parents = self::find()->where(['parent_id' => null,'user_id' => Yii::$app->user->identity->id, 'is_active' => 1]);

        // if ($exclude_income) $parents = $parents->andWhere(['!=', 'desc_category', 'Income']);
        self::createFilters($parents, $categories); 
        $parents = $parents->all();

        foreach($parents as $id_category => $p) {
            $children = self::find()->where("parent_id=:parent_id", [":parent_id"=>$p->id_category])->all();
            $child_options = [];
            foreach($children as $child) {
                $child_options[$child->id_category] = $child->desc_category;
            }
            $options[$p->desc_category] = $child_options;
        }
        return $options;
    }      

    private static function createFilters($parents, $categories)
    {
        if (!$categories) return;
        foreach ($categories as $category) {
            if ($category[0] === '+') $compareOperator = '=';
            if ($category[0] === '-') $compareOperator = '!=';
            $category = substr($category, 1);
            $parents->andWhere([$compareOperator, 'desc_category', $category]);
        }
    }
    

    public function getDescription()
    {
        return $this->desc_category;
    }

    public function getType()
    {
        return $this->hasOne(Type::className(), ['id_type' => 'type_id']);
    }
    
    
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id_category' => 'parent_id']);
    }      

    public static function categories($selectFilters = null, $excludeFilters = null)
    {
        $session = Yii::$app->session;
        $month = $session['monthIndex'];
        $year  = $session['year'];

        $selectFilter = self::createFiltersQuery('=', $selectFilters); 
        $excludeFilter = self::createFiltersQuery('!=', $excludeFilters); 

        $sql = <<<SQL
        SELECT *, p.budgeted_total, p.actual_total
        FROM 
            (SELECT * FROM category WHERE user_id = :user_id AND parent_id IS NULL $selectFilter $excludeFilter) AS c 
        LEFT JOIN 
            (SELECT parent_id, SUM(b.budgeted_value) AS budgeted_total, SUM(b.actual_value) AS actual_total
             FROM category AS c LEFT JOIN budget AS b ON c.id_category = b.category_id
             WHERE c.user_id=:user_id
                AND MONTH(b.date) = :month
                AND YEAR(b.date) = :year
             GROUP BY c.parent_id HAVING c.parent_id IS NOT NULL) AS p
                ON c.id_category = p.parent_id
SQL;
        return self::findBySql($sql, [':user_id' => Yii::$app->user->id, ':month' => $month, ':year' => $year])->asArray()->all();
    }

    private static function createFiltersQuery($compareOperator, $filters)
    {
        if (!$filters) return null;
        $queryString = '';
        foreach ($filters as $filter) {
            $queryString .= "AND desc_category$compareOperator'$filter' ";
        }
        return $queryString; 
    }
    

    public static function categoryTotal($value_type)
    {
        $all_categories = self::find()->all();
        return Cashbook::pageTotal($all_categories, $value_type);
    }

    public static function subCategories($parent_id)
    {
        return self::find()->where(['parent_id' => $parent_id])->all();
    }

    public static function getBudgets()
    {
        return $this->hasMany(Budget::className(), ['category_id' => 'id_category']);
    }

    public function getBudget($month, $year)
    {
        return Budget::findBySql('SELECT * FROM budget AS b WHERE category_id = :category_id AND MONTH(b.date) = :month AND YEAR(b.date) = :year', ['category_id' => $this->id_category, ':month' => $month, ':year' => $year])->one(); 
    }

    public function getCurrentBudget()
    {
        $session = Yii::$app->session;
        $month = $session['monthIndex'];
        $year  = $session['year'];
        return $this->getBudget($month, $year);
    }

    public function isParent()
    {
        return $this->parent_or_sub == 'parent';
    }

    public function isSub()
    {
        return $this->parent_or_sub == 'sub';
    }

    public function nullBudget($date = NULL)
    {
        $nullBudget = new Budget;
        $nullBudget->budgeted_value = 0;
        $nullBudget->actual_value = 0;
        $nullBudget->date = $date ? $date : date('Y-m-d'); 
        $nullBudget->category_id = $this->id_category;

        return $nullBudget;
    }
    
    
    //actually add default categoires and account for $userId
    public static function addDefaultCategories($userId)
    {
        $categories = [
                          'Immediate Obligations' => 
                              [
                                  'type' => 2,
                                  'sub_categories' => ['Rent/Mortgage', 'Groceries', 'Electric', 'Water', 'Phone', 'Transportation', 'Interest & Fees'],
                              ],
                          'Other Expenses' =>
                              [
                                  'type' => 2,
                                  'sub_categories' => ['Auto Maintenance', 'Home Maintenance', 'Insurance', 'Medical', 'Clothing', 'Gifts', 'Giving', 'Stuff I forgot to budget for'],
                              ],
                          'Savings Goals' =>
                              [
                                  'type' => 2,
                                  'sub_categories' => ['House Down Payment', 'Refrigerator'],
                              ],
                          // 'Other' => 
                          //     [
                          //         'type' => 2,
                          //         'sub_categories' => []
                          //     ],
                          'Income' =>
                              [
                                  'type' => 1,
                                  'sub_categories' => ['General income']
                              ],
                      ];          

        (new \app\models\Account(['user_id' => $userId, 'name' => 'cash']))->save();

        foreach($categories as $parent_category => $value) {
            $new_parent_category = new \app\models\Category([
                'desc_category' => $parent_category,
                'is_active'     => 1,
                'user_id'       => $userId,
                'type_id'       => $value['type'],
                'parent_or_sub' => 'parent', 
            ]);
            // $new_parent_category->desc_category = $parent_category; 
            // $new_parent_category->is_active = 1;
            // $new_parent_category->user_id = $userId;
            // $new_parent_category->type_id = $value['type'];
            $new_parent_category->save();
            foreach($value['sub_categories'] as $sub_category) {
                $new_sub_category = new \app\models\Category([
                    'desc_category' => $sub_category,
                    'parent_id'     => $new_parent_category->id_category,
                    'is_active'     => 1,
                    'user_id'       => $userId,
                    'type_id'       => $value['type'],
                    'parent_or_sub' => 'sub', 
                ]);
                // $new_sub_category->desc_category = $sub_category; 
                // $new_sub_category->parent_id = $new_parent_category->id_category; 
                // $new_sub_category->is_active = 1;
                // $new_sub_category->user_id = $userId;
                // $new_sub_category->type_id = $value['type'];
                $new_sub_category->save();
            }
        }
    }
}
