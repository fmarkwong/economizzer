<?php

namespace app\controllers;

use Yii;
use app\models\Transaction;
use app\models\Category;

class TransactionController extends BaseController
{
    public function actionNew()
    {
        $transaction = new Transaction;
        $transaction->date = date('Y-m-d');
        return $this->render('new', [
            'transaction' => $transaction,
        ]);
    }

    public function actionCreate()
    {
        $transaction = new Transaction;
        $transaction->user_id = Yii::$app->user->id;
        $transaction->account_id = \app\models\Account::findOne(['name' => 'cash', 'user_id' => YII::$app->user->id])->id;
        $transaction->load(Yii::$app->request->post());

        if ($transaction->category->type->desc_type === 'Revenue') {
            // error_log('Revenue');
            // error_log("account balance: " . $transaction->account->balance);
            // error_log("transaction value: " . $transaction->value);
            $transaction->account->balance += $transaction->value;
            $transaction->account->to_be_budgeted += $transaction->value;
        } elseif ($transaction->category->type->desc_type === 'Expense') {
            // error_log('Expense');
            // error_log("account balance:" . $transaction->account->balance);
            // error_log("transaction value:" . $transaction->value);
            $transaction->account->balance -= $transaction->value;
            $current_actual_value = Category::findOne(['id_category' => $transaction->category->id_category,
                                                        'user_id'    => Yii::$app->user->id,
                                    ])->actual_value;
            $transaction->category->actual_value = $current_actual_value + (float)$transaction->value;
        } else {
            throw new Exception("Category Type Error");
        }

        $transaction->account->save();

        $transaction->category->save();

        if ($transaction->save()) {
            Yii::$app->session->setFlash("transaction-success", Yii::t("app", "Transaction added"));
        } else {
            Yii::$app->session->setFlash("transaction-error", Yii::t("app", "Transaction error"));
        }
        return $this->redirect(['cashbook/index']);

    }

    public function actionDelete()
    {
        return $this->render('delete');
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionUpdate()
    {
        return $this->render('update');
    }

}
