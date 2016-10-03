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
        $budget = $this->newBudget();
        $budget->category_id = Yii::$app->request->get()['category_id'];
        $category = Category::findOne($budget->category_id);

        return $this->render('_form', [
            'title' => $category->desc_category,
            'budget'  => $budget, 
            'action' => 'create',
            'filterCategories'  => ['-Income', '-Savings'],
            'showSavingsGoalField' => false,
            'showCategoryField' => false,
        ]);
    }

    public function actionNewSavings()
    {
        return $this->render('new', [
            'title'   => Yii::t('app', 'New Savings Entry', [ 'modelClass' => 'Budget', ]),
            'budget'  => $this->newBudget(),
            'filterCategories'  => ['+Savings'],
            'savings' => true
        ]);
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

    public function actionUpdateBudgetedValueForm()
    {
        $id = Yii::$app->request->get()['id'];
        if ((int)$id === 0) return $this->actionNew();
        
        $budget = Budget::findOne($id);
        $category = $budget->category->desc_category;

        return $this->render('_form', [
            'title'   => $category, 
            'budget'  => $budget,
            'action'  => 'update',
            'filterCategories'  => null, 
            'showCategoryField'    => false,
            'showSavingsGoalField' => false,
        ]);
    }

    public function actionUpdateSavingsForm()
    {
        $id = Yii::$app->request->get()['id'];
        if ((int)$id === 0) return $this->actionNewSavings();
        
        $budget = Budget::findOne($id);
        $category = $budget->category->desc_category;

        return $this->render('update', [
            'title'   => $category, 
            'budget'  => $budget,
            'action'  => 'update',
            'filterCategories'  => null, 
            'showCategoryField'    => false,
            'showSavingsGoalField' => false,
        ]);
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
