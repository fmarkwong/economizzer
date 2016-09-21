<?php

namespace app\controllers;

use Yii;
use app\models\Budget;
use app\models\Category;
use app\models\Account;


class BudgetController extends \yii\web\Controller
{
    public function actionNew()
    {
        return $this->render('create', [
            'model' => $this->newBudget()
        ]);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    // actually create or update
    public function actionCreate()
    {
        $budget = new Budget; 

        if ($budget->load(Yii::$app->request->post())) {
            $account = Account::currentAccount();
            $account->to_be_budgeted -= $budget->budgeted_value;
            $date = new \DateTime($budget->date);
            $month = (int)$date->format('m');
            $year = $date->format('Y');
            $budgetCategory = Category::findOne($budget->category_id);
            $existingBudget = Category::findOne($budget->category_id)->getBudget($month, $year);

            // if existingBudget, then update, else create new budget
            $budget = $existingBudget ? $existingBudget->incrementBudgetedValue($budget->budgeted_value) : $budget;
            if ($budget->save() && $account->save()) {
                Yii::$app->session->setFlash("Entry-success", Yii::t("app", "Entry successfully included"));
                return $this->redirect(['/cashbook/index']);
            } else {
                throw new ErrorException("Error saving Budget");
            }
        } 
    }

    public function newBudget()
    {
        $session = Yii::$app->session;

        if ($session->has('monthIndex') && $session->has('year')) {
            $date = "{$session['year']}-{$session['monthIndex']}-01";
        } else {
            $date = date('Y-m-d');
        }
        return new Budget(['date' => $date]);
    }
    
}
