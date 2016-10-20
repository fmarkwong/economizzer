<?php

namespace app\controllers;

use Yii;

class LanguageController extends \yii\web\Controller
{
    public function actionSelect()
    {
        $language = Yii::$app->request->get()['language'];

        if (in_array($language, ['en', 'mm'])) {
            Yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name' => 'language',
                'value' => $language,
            ]));
        }
        return $this->redirect(Yii::$app->request->referrer);
    }
}
