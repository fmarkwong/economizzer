<?php

namespace app\controllers;
use amnah\yii2\user\controllers\AuthController as BaseAuthController;
use amnah\yii2\user\models\UserAuth;
use app\models\UserKeychain;
use yii\helpers\Url;
use yii\web\UnauthorizedHttpException;
use Yii;

class AuthController extends BaseAuthController{

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'connect' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'connectCallback'],
                'successUrl' => Url::to(['/user/account']),
            ],
            'login' => [
                'class' => 'yii\authclient\AuthAction',
                // 'successCallback' => [$this, 'loginRegisterCallback'],
                'successCallback' => [$this, 'loginCallBack'],
                'successUrl' => Url::base(),
            ],
        ];
    }

    // taken from vendor/amnah/yii2-user/controllers/AuthController->loginRegisterCallbac
    public function loginCallBack($client)
    {
        // uncomment this to see which attributes you get back
        //echo "<pre>";print_r($client->getUserAttributes());echo "</pre>";exit;
        // check if user is already logged in. if so, do nothing
        if (!Yii::$app->user->isGuest) {
            return;
        }

        // attempt to log in as an existing user
        if ($this->attemptLogin($client)) {
            return;
        }

         // register a new user
        $userAuth = $this->initUserAuth($client);
        $this->registerAndLoginUser($client, $userAuth);
    }
    
    /**
     * Register a new user using client attributes and then associate userAuth
     *
     * @param \yii\authclient\BaseClient $client
     * @param \amnah\yii2\user\models\UserAuth $userAuth
     */
    protected function registerAndLoginUser($client, $userAuth)
    {
        /** @var \amnah\yii2\user\models\User    $user */
        /** @var \amnah\yii2\user\models\Profile $profile */
        /** @var \amnah\yii2\user\models\Role    $role */
        $role = Yii::$app->getModule("user")->model("Role");

        // set user and profile info
        $attributes = $client->getUserAttributes();
        $function = "setInfo" . ucfirst($client->name); // "setInfoFacebook()"
        list ($user, $profile) = $this->$function($attributes);

        // calculate and double check username (in case it is already taken)
        $fallbackUsername = "{$client->name}_{$userAuth->provider_id}";
        $user = $this->doubleCheckUsername($user, $fallbackUsername);

        // save new models
        $user->setRegisterAttributes($role::ROLE_USER, Yii::$app->request->userIP, $user::STATUS_ACTIVE)->save(false);
        $profile->setUser($user->id)->save(false);
        $userAuth->setUser($user->id)->save(false);

        $this->addDefaultCategories($user->id);
        // log user in
        Yii::$app->user->login($user, Yii::$app->getModule("user")->loginDuration);
    }

    /**
     * Connect social auth to the logged-in user
     * @param \yii\authclient\BaseClient $client
     * @return \yii\web\Response
     * @throws \yii\web\ForbiddenHttpException
     */
    public function connectCallback($client)
    {
        // uncomment this to see which attributes you get back
        //echo "<pre>";print_r($client->getUserAttributes());echo "</pre>";exit;

        // check if user is not logged in. if so, do nothing
        if (Yii::$app->user->isGuest) {
            return;
        }

        // check duplicates by provider_id
        $attributes = $client->getUserAttributes();
        if ($this->checkIsAlreadyConnected($attributes['id'])) {
            // register a new user
            $userAuth = $this->initUserAuth($client);
            $userAuth->setUser(Yii::$app->user->id)->save();
        } else {
            Yii::$app->session->setFlash('Connect-danger', Yii::t('user','This account has already been connected'));
        }
    }

    /**
     * Returns 'true' if $provider_id is not persist into user_auth
     * @param $provider_id
     * @return bool
     */
    protected function checkIsAlreadyConnected($provider_id)
    {
        $count = UserAuth::find()
            ->where(['provider_id'=>$provider_id])
            ->count();
        return !($count > 0);
    }

    /**
     * Disconnect social account by id and go back
     * @param $id
     */
    public function actionDisconnect($id){
        try{
            $result = UserKeychain::disconnect($id);
            if ($result == false){
                \Yii::$app->session->setFlash("Disconnect-danger", \Yii::t('user', 'Account has already been disabled'));
            }else{
                \Yii::$app->session->setFlash("Disconnect-success", \Yii::t('user', 'Account successfully disconnected'));
            }
        }catch (\Exception $e){
            \Yii::$app->session->setFlash("Disconnect-danger", \Yii::t('user', $e->getMessage()));
        }
        $this->goBack();
    }

    private function addDefaultCategories($userId)
    {
        $categories = ['Immediate Obligations' => 
                            ['type' => 2,
                            'sub_categories' => ['Rent/Mortgage', 'Groceries', 'Electric', 'Water', 'Phone', 'Transportation', 'Interest & Fees']
                            ],
                          'True Expenses' =>
                            ['type' => 2,
                            'sub_categories' => ['Auto Maintenance', 'Home Maintenance', 'Insurance', 'Medical', 'Clothing', 'Gifts', 'Giving', 'Stuff I forgot to budget for'],
                            ],
                          'Other' => 
                            ['type' => 2,
                            'sub_categories' => []
                            ],
                          'Income' =>
                            ['type' => 1,
                            'sub_categories' => ['All income']
                            ],
                      ];          

        (new \app\models\Account(['user_id' => $userId, 'name' => 'cash']))->save();

        foreach($categories as $parent_category => $value) {
            $new_parent_category = new \app\models\Category;
            $new_parent_category->desc_category = $parent_category; 
            $new_parent_category->is_active = 1;
            $new_parent_category->user_id = $userId;
            $new_parent_category->type_id = $value['type'];
            $new_parent_category->save();
            foreach($value['sub_categories'] as $sub_category) {
                $new_sub_category = new \app\models\Category;
                $new_sub_category->desc_category = $sub_category; 
                $new_sub_category->parent_id = $new_parent_category->id_category; 
                $new_sub_category->is_active = 1;
                $new_sub_category->user_id = $userId;
                $new_sub_category->type_id = $value['type'];
                $new_sub_category->save();
            }
        }
    }
    
}
