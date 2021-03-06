<?php

namespace app\controllers;

use amnah\yii2\user\controllers\DefaultController as BaseUserController;
use Yii;
use app\models\UserKeychain;
use app\models\Category;
use yii\web\Response;
use yii\widgets\ActiveForm;


class UserController extends BaseUserController{

    public function init(){
        parent::init();
        Yii::$app->i18n->translations['user'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/messages',
            //'forceTranslation' => true,
        ];
    }

    // fix bug where if you go to login page when already logged in,
    // get error page
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] ['denyCallback']  = function ($rule, $action) {
            if ($action->actionMethod === 'actionLogin') {
                if (Yii::$app->user->isGuest) {
                    return $this->goHome();
                } else {
                    return $this->redirect(['/cashbook/index']);
                }
            }
        };

        return $behaviors;
    }

    public function actionAuth(){
        return "wrong!";
    }

    /**
     * Confirm email
     */
    public function actionConfirm($key)
    {
        /** @var \amnah\yii2\user\models\UserKey $userKey */
        /** @var \amnah\yii2\user\models\User $user */

        // search for userKey
        $success = false;
        $userKey = Yii::$app->getModule("user")->model("UserKey");
        $userKey = $userKey::findActiveByKey($key, [$userKey::TYPE_EMAIL_ACTIVATE, $userKey::TYPE_EMAIL_CHANGE]);
        if ($userKey) {

            // confirm user
            $user = Yii::$app->getModule("user")->model("User");
            $user = $user::findOne($userKey->user_id);
            $user->confirm();

            // consume userKey and set success
            $userKey->consume();
            $success = $user->email;
            Category::addDefaultCategories($user->id);
        }

        // render
        return $this->render("confirm", [
            "userKey" => $userKey,
            "success" => $success
        ]);
    }

    public function actionAccount()
    {
        /** @var \amnah\yii2\user\models\User $user */
        /** @var \amnah\yii2\user\models\UserKey $userKey */
        // save url for goBack() method
        Yii::$app->getUser()->setReturnUrl(Yii::$app->request->url);

        // set up user and load post data
        $user = Yii::$app->user->identity;

        //$userAuths = $user->userAuths;
        //when user does not have old school account, registered via social networks
        if (empty($user->password)) {
            $user->setScenario("socialonlyaccount");
        }else{
            $user->setScenario("account");
        }
        $loadedPost = $user->load(Yii::$app->request->post());

        // validate for ajax request
        if ($loadedPost && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($user);
        }

        // validate for normal request
        if ($loadedPost && $user->validate()) {

            // generate userKey and send email if user changed his email
            if (Yii::$app->getModule("user")->emailChangeConfirmation && $user->checkAndPrepEmailChange()) {

                $userKey = Yii::$app->getModule("user")->model("UserKey");
                $userKey = $userKey::generate($user->id, $userKey::TYPE_EMAIL_CHANGE);
                if (!$numSent = $user->sendEmailConfirmation($userKey)) {

                    // handle email error
                    //Yii::$app->session->setFlash("Email-error", "Failed to send email");
                }
            }

            // save, set flash, and refresh page
            $user->save(false);
            Yii::$app->session->setFlash("Account-success", Yii::t("user", "Account updated"));
            return $this->refresh();
        }

        $keychainConnects = UserKeychain::getKeychainConnects();

        // render
        return $this->render("account", [
            'user' => $user,
            'keychainConnects' => $keychainConnects
        ]);
    }

    public function actionForgot()
    {
        $model = Yii::$app->getModule("user")->model("ForgotForm");
        if ($model->load(Yii::$app->request->post()) && $model->sendForgotEmail()) {
            Yii::$app->session->setFlash("Forgot-success", Yii::t("user", "Instructions to reset your password have been sent"));
        }
        return $this->render("forgot", [
            "model" => $model,
        ]);
    }
    public function actionCrawler()
    {
        $model = Yii::$app->getModule("user")->model("LoginForm");
        if ($model->load(Yii::$app->request->post(), '') && $model->login(Yii::$app->getModule("user")->loginDuration)) {
            return $this->goBack(Yii::$app->getModule("user")->loginRedirect);
        }
        return $this->render('crawler', compact("model"));
 
    }    
    

    /**
     * Log user out and redirect
     * overriading vendor/amnah/yii2-user/controllers/DefaulController.php
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
