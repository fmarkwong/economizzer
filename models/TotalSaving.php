<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "total_saving".
 *
 * @property integer $id
 * @property double $value
 * @property integer $account_id
 * @property integer $budget_id
 */
class TotalSaving extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'total_saving';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value', 'goal'], 'number'],
            [['account_id', 'category_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'value' => Yii::t('app', 'Value'),
            'account_id' => Yii::t('app', 'Account ID'),
            'budget_id' => Yii::t('app', 'Budget ID'),
        ];
    }

    public static function byCategory($categoryId)
    {
        return self::find()->where(['category_id' => $categoryId])->one(); 
    }
    

    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id_category' => 'category_id']);
    }

}
