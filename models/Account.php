<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "accounts".
 *
 * @property integer $id
 * @property string $name
 * @property integer $user_id
 *
 * @property Transactions[] $transactions
 */
class Account extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transactions::className(), ['account_id' => 'id']);
    }
}
