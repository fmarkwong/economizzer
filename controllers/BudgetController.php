<?php

namespace app\controllers;

use Yii;
use app\models\Budget;
use app\models\Category;
use app\models\Account;
use app\models\TotalSaving;


class BudgetController extends \yii\web\Controller
{
    public function actionNew()
    {
        $budget = $this->newBudget();
        $budget->category_id = Yii::$app->request->get()['category-id'];
        $category = Category::findOne($budget->category_id);
        $showSavingsGoalField = isset(Yii::$app->request->get()['show-savings-goal-field']);
        $showDebtTotalField = isset(Yii::$app->request->get()['show-debt-total-field']);

        return $this->render('_form', [
            'title' => $category->desc_category,
            'budget'  => $budget, 
            'action' => 'create',
            'filterCategories'  => ['-Income', '-"Savings Goals"'],
            'showSavingsGoalField' => $showSavingsGoalField,
            'showDebtTotalField' => $showDebtTotalField,
            'showCategoryField' => false,
        ]);
    }

    public function actionUpdateBudgetedValueForm()
    {
        $id = Yii::$app->request->get()['id'];
        if ((int)$id === 0) return $this->actionNew();
        
        $budget = Budget::findOne($id);
        $categoryName = $budget->category->desc_category;
        $showSavingsGoalField = isset(Yii::$app->request->get()['show-savings-goal-field']);
        $showDebtTotalField = isset(Yii::$app->request->get()['show-debt-total-field']);
        return $this->render('_form', [
            'title' => $categoryName,
            'budget'  => $budget,
            'action'  => 'update',
            'filterCategories'  => null, 
            'showCategoryField'    => false,
            'showSavingsGoalField' => $showSavingsGoalField, 
            'showDebtTotalField' => $showDebtTotalField,
        ]);
    }

    public function actionCreate()
    {
        $budget = new Budget; 

        if ($budget->load(Yii::$app->request->post())) {
            $account = Account::currentAccount();
            $account->to_be_budgeted -= $budget->budgeted_value;
            $date = new \DateTime($budget->date);
            $month = (int)$date->format('m');
            $year = $date->format('Y');
            $existingBudget = Category::findOne($budget->category_id)->getBudget($month, $year);

            // if existingBudget, then update, else create new budget
            $budget = $existingBudget ? $existingBudget->incrementBudgetedValue($budget->budgeted_value) : $budget;
            if ($budget->save() && $account->save()) {
                $this->saveSavingsGoal($budget, $account);
                $this->saveDebtTotal($budget, $account);
                Yii::$app->session->setFlash("Entry-success", Yii::t("app", "Entry successfully included"));
                return $this->redirect(['/cashbook/index']);
            } else {
                throw new ErrorException("Error saving Budget");
            }
        } 
    }

    public function actionUpdate()
    {
        $budget = Budget::findOne(Yii::$app->request->post()['budget_id']);
        $currentBudgetedValue = $budget->budgeted_value; 

        if ($budget->load(Yii::$app->request->post())) {
            $account = Account::currentAccount();
            $account->to_be_budgeted -= ($budget->budgeted_value - $currentBudgetedValue);
            $date = new \DateTime($budget->date);
            $month = (int)$date->format('m');
            $year = $date->format('Y');

            // $budget->incrementBudgetedValue($budget->budgeted_value);
            if ($budget->save() && $account->save()) {
                $this->saveSavingsGoal($budget, $account);
                $this->saveDebtTotal($budget, $account);
                Yii::$app->session->setFlash("Entry-success", Yii::t("app", "Entry successfully included"));
                return $this->redirect(['/cashbook/index']);
            } else {
                throw new ErrorException("Error saving Budget");
            }
        } 
    }

    private function saveDebtTotal($budget, $account)
    {
        $debtTotalExists = isset(Yii::$app->request->post()['debt-total']);
        if (!$debtTotalExists) return;

        $debtTotal = Yii::$app->request->post()['debt-total'];
        if ($budget->category->Debt) {
            $budget->category->Debt->total = $debtTotal;
            $budget->category->Debt->account_id = $account->id;
            $budget->category->Debt->save();
        } else {
            $totalDebt = new TotalDebt();
            $totalDebt->total = $debtTotal;
            $totalDebt->account_id = $account->id;
            $totalDebt->link('category', $budget->category);
        }
    }
    
    private function saveSavingsGoal($budget, $account)
    {
        $savingsGoalExists = isset(Yii::$app->request->post()['savings-goal']);
        if (!$savingsGoalExists) return;

        $savingsGoal = Yii::$app->request->post()['savings-goal'];
        if ($budget->category->totalSaving) {
            $budget->category->totalSaving->goal = $savingsGoal;
            $budget->category->totalSaving->account_id = $account->id;
            $budget->category->totalSaving->save();
        } else {
            $totalSaving = new TotalSaving();
            $totalSaving->goal = $savingsGoal;
            $totalSaving->account_id = $account->id;
            $totalSaving->link('category', $budget->category);
        }
    }
    
    private function newBudget()
    {
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
        return new Budget(['date' => $date]);
    }
    
}
