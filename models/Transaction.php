<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transactions".
 *
 * @property integer $id
 * @property string $description
 * @property string $date
 * @property string $category
 * @property double $income
 * @property double $expense
 * @property double $balance
 * @property integer $account_id
 * @property integer $category_id
 *
 * @property Category $category0
 * @property Accounts $account
 */
class Transaction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transaction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['date'], 'required'],
            [['income', 'expense', 'balance', 'value'], 'number'],
            [['account_id', 'category_id'], 'integer'],
            [['description'], 'string', 'max' => 255],
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
            'description' => Yii::t('app', 'Description'),
            'date' => Yii::t('app', 'Date'),
            'category_id' => Yii::t('app', 'Category'),
            'income' => Yii::t('app', 'Income'),
            'value' => Yii::t('app', 'Value'),
            'expense' => Yii::t('app', 'Expense'),
            'balance' => Yii::t('app', 'Balance'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id_category' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }

    public static function all()
    {
        return self::findAll(['user_id' => YII::$app->user->id]);
    }
    
}
