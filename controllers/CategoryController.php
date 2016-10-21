<?php

namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\CategorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class CategoryController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::classname(),
                'only'  => ['index','create','update','delete','view'],
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
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Category();
        $model->user_id = Yii::$app->user->identity->id;
        $model->is_active = 1;

        if ($model->load(Yii::$app->request->post())) {
            $this->processCategory($model);
            if ($model->save()) { 
                Yii::$app->session->setFlash("Category-success", Yii::t("app", "Category successfully included"));
                return $this->redirect(['index']);
            }
        } 
        //TODO:  need to flash fail to save message
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->user_id != Yii::$app->user->id){
            throw new ErrorException(Yii::t('app', 'Forbidden to change entries of other users'));
        }

        if ($model->load(Yii::$app->request->post())) {
            $this->processCategory($model);
            if ($model->save()) {
                Yii::$app->session->setFlash("Category-success", Yii::t("app", "Category updated"));
                return $this->redirect(['index']);
            }
        } 
        //TODO:  need to flash fail to update message
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    private function processCategory($category)
    {
        if ($category->parent_or_sub == 'parent') { 
            $category->parent_id = null;
            // we're creating a new parent category, can only do make expense type parent categories
            // the only revenue category is income
            $category->type_id = 2; // expense  
        } elseif ($category->parent->desc_category == 'Income') {
            $category->type_id = 1;  // revenue 
        } else {
            $category->type_id = 2;
        }
    }
    


    public function actionDelete($id)
    {
        $model= $this->findModel($id);
        if ($model->user_id != Yii::$app->user->id){
            throw new ErrorException(Yii::t('app', 'Forbidden to change entries of other users'));
        }
        try {
            if ($model->isParent()) {
                $this->deleteSubCategories($model);
            }
             $model->delete();
             Yii::$app->session->setFlash("Category-success", Yii::t("app", "Category successfully deleted"));
             return $this->redirect(['index']);
        } catch(\yii\db\IntegrityException $e) {
             //throw new \yii\web\ForbiddenHttpException('Could not delete this record.');
             Yii::$app->session->setFlash("Category-danger", Yii::t("app", "This category is associated with some record"));
             return $this->redirect(['index']);            
        }        
    }

    private function deleteSubCategories($parentCategory)
    {
        Category::deleteAll([
            'user_id' => Yii::$app->user->id,
            'parent_or_sub' => 'sub',
            'parent_id' => $parentCategory->id_category
        ]);
    }
    

    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null && $model->user_id == Yii::$app->user->id) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t("app", "The page you requested is not available or does not exist."));
        }
    }
}
