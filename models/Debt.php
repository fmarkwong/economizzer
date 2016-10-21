<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "debt".
 *
 * @property integer $id
 * @property double $current_value
 * @property double $principal
 * @property integer $account_id
 * @property integer $category_id
 */
class Debt extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'debt';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['current_value', 'principal'], 'number'],
            [['account_id', 'category_id'], 'required'],
            [['account_id', 'category_id'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id_category']],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'current_value' => Yii::t('app', 'Current Value'),
            'principal' => Yii::t('app', 'Principal'),
            'account_id' => Yii::t('app', ''),
            'category_id' => Yii::t('app', ''),
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
