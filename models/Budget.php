<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "budget".
 *
 * @property integer $id
 * @property double $budgeted_value
 * @property double $actual_value
 * @property string $date
 * @property integer $category_id
 */
class Budget extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'budget';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['budgeted_value', 'actual_value'], 'number'],
            [['date'], 'safe'],
            [['category_id'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id_category']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'budgeted_value' => Yii::t('app', 'Budgeted Value'),
            'actual_value' => Yii::t('app', 'Actual Value'),
            'date' => Yii::t('app', 'Date'),
            'category_id' => Yii::t('app', 'Category'),
        ];
    }

    public function incrementBudgetedValue($value)
    {
        //TODO: use updateCounters instead: see http://www.yiiframework.com/doc-2.0/guide-db-active-record.html#updating-counters
        $this->budgeted_value += $value;
        return $this;
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id_category' => 'category_id']);
    }

    public function getTransaction()
    {
        return $this->hasOne(Transaction::className(), ['id' => 'transaction_id']);
    }
    
}
