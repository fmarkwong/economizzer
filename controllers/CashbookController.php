<?php

namespace app\controllers;

use Yii;
use app\models\Cashbook;
use app\models\Account;
use app\models\Category;
use app\models\CashbookSearch;
use app\models\Transaction;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\base\Security;

class CashbookController extends BaseController
{

    public static $month = [ 1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
        7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::classname(),
                'only'  => ['index','create','update','delete','view','target','accomplishment','overview','performance'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $session = Yii::$app->session;
        if (!$session->has('monthIndex')) $session['monthIndex'] = (int)date('m');
        if (!$session->has('year')) $session['year'] = date('Y');

        $month = self::$month[$session['monthIndex']];
        $year  = $session['year'];

        $totalLeftToBudget = Account::getTotalLeftToBudget();
        $categories = Category::categories(null, ['Savings', 'Income']);
        $savingsCategory = Category::categories(['Savings']);
        $accountBalance = Account::balance();
        $transactions = Transaction::getCurrent();

        return $this->render('index', compact(
            'totalLeftToBudget',
            'categories',
            'savingsCategory',
            'accountBalance',
            'transactions',
            'month',
            'year'
        ));
    }
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->getCashBook($id),
        ]);
    }
    public function actionCreate()
    {
        if ($post = Yii::$app->request->post()) {
            $post = $post['Cashbook'];
            $category = Category::findOne($post['category_id']);
            $category->budgeted_value += $post['budgeted_value'];
            $category->save();
            $account = Account::findOne(['user_id' => YII::$app->user->id, 'name' => 'cash']);
            $account->to_be_budgeted -= $post['budgeted_value'];
            $account->save();
        }

        $model = new Cashbook;
        $model->inc_datetime = date("Y-m-d H:i:s"); 
        $model->user_id = Yii::$app->user->identity->id;
        $model->date = date("Y-m-d");
 
        if ($model->load(Yii::$app->request->post())) {
            // process uploaded image file instance
            $file = $model->uploadImage();
 
            if ($model->save()) {
                // upload only if valid uploaded file instance found
                if ($file !== false) {
                    // Create the ID folder 
                    $idfolder = Yii::$app->user->identity->id;
                    //$idfolder = str_pad($idfolder, 6, "0", STR_PAD_LEFT); // add 0000+ID
                    if(!is_dir(Yii::$app->params['uploadUrl'] . $idfolder)){
                    mkdir(Yii::$app->params['uploadUrl'] . $idfolder, 0777, true);
                    }
                    $path = $model->getImageFile();
                    $file->saveAs($path);
                }
                Yii::$app->session->setFlash("Entry-success", Yii::t("app", "Entry successfully included"));
                return $this->redirect(['index']);
            } else {
                // error in saving model
            }
        }// post vars empty, a brand new create
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    public function actionUpdate($id)
    {
        $cash_book = $this->getCashBook($id);
        $oldFile = $cash_book->getImageFile();
        $oldattachment = $cash_book->attachment;
        $oldFileName = $cash_book->filename;
        $cash_book->edit_datetime = date("Y-m-d H:i:s");
 
        if ($cash_book->load(Yii::$app->request->post())) {
            // process uploaded image file instance
            $file = $cash_book->uploadImage();
 
            // revert back if no valid file instance uploaded
            if ($file === false) {
                $cash_book->attachment = $oldattachment;
                $cash_book->filename = $oldFileName;
            }
 
            if ($cash_book->save()) {
                // upload only if valid uploaded file instance found
                if ($file !== false && unlink($oldFile)) { // delete old and overwrite
                    $path = $cash_book->getImageFile();
                    $file->saveAs($path);
                }
                Yii::$app->session->setFlash("Entry-success", Yii::t("app", "Entry updated"));
                return $this->redirect(['index']);
            } else {
                // error in saving cash_book
            }
        }
        return $this->render('update', [
            'model'=>$cash_book,
        ]);
    }
    public function actionDelete($id)
    {
        $cash_book = $this->getCashBook($id);
        // validate deletion and on failure process any exception
        // e.g. display an error message
        if ($cash_book->delete()) {
            if (!$cash_book->deleteImage()) {
                Yii::$app->session->setFlash("Entry-danger", 'Error deleting image');
            }
        }
        Yii::$app->session->setFlash("Entry-success", Yii::t("app", "Entry successfully deleted"));
        return $this->redirect(['index']);
    }
    public function actionTarget()
    {
        $cash_book = new Cashbook();
        return $this->render('target', [
                'model' => $cash_book,
            ]);
    }

    public function actionPreviousMonth()
    {
        $session = Yii::$app->session;

        if ($session['monthIndex'] === 1)  {
            $session['monthIndex'] = 12;
            $session['year'] = $session['year'] - 1;
        } else {
            $session['monthIndex'] = $session['monthIndex'] - 1;
        }
        return $this->redirect(['index']);
    }
    


    public function actionNextMonth()
    {
        $session = Yii::$app->session;

        if ($session['monthIndex'] === 12)  {
            $session['monthIndex'] = 1;
            $session['year'] = $session['year'] + 1;
        } else {
            $session['monthIndex'] = $session['monthIndex'] + 1;
        }
        return $this->redirect(['index']);
    }

    protected function getCashBook($id)
    {
        if (($cash_book = Cashbook::findOne($id)) !== null && $cash_book->user_id == Yii::$app->user->id) {
            return $cash_book;
        } else {
            throw new NotFoundHttpException('The page you requested is not available or does not exist.');
        }
    }
}
