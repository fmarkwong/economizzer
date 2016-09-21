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
            [['budgeted_total', 'actual_total'], 'number'],
            [['desc_category', 'is_active'], 'required'],
            [['is_active','user_id','parent_id'], 'integer'],
            [['desc_category', 'hexcolor_category'], 'string', 'max' => 45]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_category' => Yii::t('app', 'ID'),
            'budgeted_total' => Yii::t('app', 'Total Budgeted Value'),
            'actual_total' => Yii::t('app', 'Total Acutal Total'),
            'desc_category' => Yii::t('app', 'Description'),
            'hexcolor_category' => Yii::t('app', 'Color'),
            'parent_id' => Yii::t('app', 'Parent Category'),
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

    public static function getHierarchy($exclude_income = false) {
        $options = [];
         
        $parents = self::find()->where(['parent_id' => null,'user_id' => Yii::$app->user->identity->id, 'is_active' => 1]);

        if ($exclude_income) $parents = $parents->andWhere(['!=', 'desc_category', 'Income']);
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

    public static function categories()
    {
        $session = Yii::$app->session;
        $month = $session['monthIndex'];
        $year  = $session['year'];
        $sql = <<<SQL
        SELECT *, p.budgeted_total, p.actual_total
        FROM 
            (SELECT * FROM category WHERE user_id = :user_id AND parent_id IS NULL) AS c 
        LEFT JOIN 
            (SELECT parent_id, SUM(b.budgeted_value) AS budgeted_total, SUM(b.actual_value) AS actual_total
             FROM category AS c LEFT JOIN budget AS b ON c.id_category = b.category_id  WHERE c.user_id=:user_id AND MONTH(b.date) = :month AND YEAR(b.date) = :year GROUP BY c.parent_id HAVING c.parent_id IS NOT NULL) AS p
        ON c.id_category = p.parent_id
SQL;
        return self::findBySql($sql, [':user_id' => Yii::$app->user->id, ':month' => $month, ':year' => $year])->asArray()->all();
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

    public function nullBudget($date = NULL)
    {
        $nullBudget = new Budget;
        $nullBudget->budgeted_value = 0;
        $nullBudget->actual_value = 0;
        $nullBudget->date = $date ? $date : date('Y-m-d'); 
        $nullBudget->category_id = $this->id_category;

        return $nullBudget;
    }
    
    
     
    
    
    
     
    
}
