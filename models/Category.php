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
            [['budgeted_value', 'actual_value'], 'number'],
            [['desc_category', 'is_active'], 'required'],
            [['is_active','user_id','parent_id'], 'integer'],
            [['desc_category', 'hexcolor_category'], 'string', 'max' => 45]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_category' => Yii::t('app', 'ID'),
            'budgeted_value' => Yii::t('app', 'Budgeted Value'),
            'budgeted_total' => Yii::t('app', 'Budgeted Total'),
            'actual_value' => Yii::t('app', 'Actual Value'),
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

    public static function topCategoryDescriptionArray()
    {
        $top_category_desc_array = [];
        $top_categories = self::findBySql('SELECT id_category, desc_category from category WHERE parent_id IS NULL AND user_id=:user_id', [':user_id' => Yii::$app->user->id])->asArray()->all();

        foreach($top_categories as $tp) {
            $top_category_desc_array[$tp['id_category']] = $tp['desc_category']; 
        }

        return $top_category_desc_array;
    }

    public static function categories()
    {
        // this will only show parent categories that have subcategories (because of the GROUP BY clause
        return self::findBySql('SELECT *, sum(budgeted_value) as budgeted_total, sum(actual_value) as actual_total FROM category WHERE user_id=:user_id GROUP BY parent_id HAVING parent_id IS NOT NULL', [':user_id' => Yii::$app->user->id])->asArray()->all();
        
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
    
    
    
     
    
}
