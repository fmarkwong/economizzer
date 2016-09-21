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
        $session = Yii::$app->session;

        if ($session->has('monthIndex') && $session->has('year')) {
            $date = "{$session['year']}-{$session['monthIndex']}-01";
        } else {
            $date = date('Y-m-d');
        }

        $transaction->date = $date; 
        return $this->render('new', [
            'transaction' => $transaction,
        ]);
    }

    public function actionCreate()
    {
        $transaction = new Transaction;
        $transaction->user_id = Yii::$app->user->id;
        // default to cash account for now
        $transaction->account_id = \app\models\Account::findOne(['name' => 'cash', 'user_id' => YII::$app->user->id])->id;
        $transaction->load(Yii::$app->request->post());

        $date = new \DateTime($transaction->date);
        $month = (int)$date->format('m');
        $year = $date->format('Y');
        $currentBudget = $transaction->category->getBudget($month, $year);
        $currentBudget = $currentBudget ? $currentBudget : $transaction->category->nullBudget($transaction->date);

        if ($transaction->category->type->desc_type === 'Revenue') {
            $transaction->account->balance += $transaction->value;
            $transaction->account->to_be_budgeted += $transaction->value;
        } elseif ($transaction->category->type->desc_type === 'Expense') {
            $transaction->account->balance -= $transaction->value;
            $currentBudget->actual_value += (float)$transaction->value;
        } else {
            throw new Exception("Category Type Error");
        }

        $transaction->account->save();
        $currentBudget->save();

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
