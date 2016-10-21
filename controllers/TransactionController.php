<?php

namespace app\controllers;

use Yii;
use app\models\Transaction;
use app\models\Category;
use app\models\TotalSaving;

class TransactionController extends BaseController
{
    public function actionNew()
    {
        // if no category-id param, that means we're coming from Add transaction button 
        // on cashbook/index (instead of clicking on value) so set category_id to null
        // and need to show categoryfield.  Eventually we'll want to set category to income since
        // we'll only be using the add transaction button for income.
        $categoryIdExists = isset(Yii::$app->request->get()['category-id']);
        $categoryId  = $categoryIdExists ? Yii::$app->request->get()['category-id'] : null;
        $session = Yii::$app->session;

        if ($session->has('monthIndex') && $session->has('year')) {
            if ($session['monthIndex'] === (int)date('m') && $session['year'] === date('Y')) {
                $date = date('Y-m-d');
            } else { 
                $date = "{$session['year']}-{$session['monthIndex']}-01";
            }
        } else {
            $date = date('Y-m-d');
        }

        $transaction = new Transaction;
        $transaction->category_id = $categoryId;
        $transaction->date = $date; 
        $title = $categoryIdExists ? $transaction->category->desc_category : '';

        $showCategoryField = !$categoryIdExists;
        return $this->render('new', [
            'transaction' => $transaction,
            'title' => $title, 
            'action' => 'create', 
            'showCategoryField' => $showCategoryField 
        ]);
    }

    public function actionUpdateActualValueForm()
    {
        $id = Yii::$app->request->get()['id'];
        $transaction = Transaction::findOne($id); 

        return $this->render('update', [
            'title' => $transaction->category->desc_category, 
            'transaction' => $transaction,
            'showCategoryField' => false,
            'action' => 'update'
        ]);
    }
    

    public function actionCreate($transaction = null)
    {
        $transaction = $transaction ? $transaction : new Transaction;
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
            if ($currentBudget->category->parent->desc_category === 'Savings Goals') {
                if (!$transaction->category->totalSaving) {
                    $totalSavings = new TotalSaving();
                    $totalSavings->value += (float)$transaction->value;
                    $totalSavings->category_id = $transaction->category->id_category;
                    $totalSavings->link('account', $transaction->account);
                } else {
                    // $transaction->account->totalSaving->value += (float)$transaction->value;
                    // $transaction->account->totalSaving->category_id = $transaction->category->id_category;
                    // $transaction->account->totalSaving->save();
                    $transaction->category->totalSaving->value += (float)$transaction->value;
                    $transaction->category->totalSaving->account_id = $transaction->account->id;
                    // $transaction->category->totalSaving->category_id = $transaction->category->id_category;
                    $transaction->category->totalSaving->save();
                }
            }
        } else {
            throw new Exception("Category Type Error");
        }

        if ($transaction->save()) {
            $transaction->account->save();
            $currentBudget->transaction_id = $transaction->id;
            $currentBudget->save();
            Yii::$app->session->setFlash("transaction-success", Yii::t("app", "Transaction added"));
        } else {
            Yii::$app->session->setFlash("transaction-error", Yii::t("app", "Transaction error"));
        }
        return $this->redirect(['cashbook/index']);

    }

    public function actionUpdate()
    {
        $transaction = Transaction::findOne(Yii::$app->request->post()['transaction_id']);
        return $this->actionCreate($transaction); 
    }
    

    public function actionDelete()
    {
        return $this->render('delete');
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}
