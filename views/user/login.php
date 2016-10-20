<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="user-default-login">
	<div class="container">
		<div class="row">
		<div class="col-sm-6 col-md-4 col-md-offset-4">
		<h1 class="text-center login-title"><?php echo Yii::t('app', 'Login to access Micro Empire Budgeter');?></h1>
		<div class="account-wall">
		<img class="profile-img" src="<?php echo Yii::$app->request->baseUrl;?>/images/logo-profile.png" alt="">
			<?php $form = ActiveForm::begin([
				'id' => 'login-form',
				'options' => ['class' => 'form-signin'],
			]); ?>

			<?= $form->field($model, 'username')->label(false,['class'=>'label-class'])->textInput(array('placeholder' => Yii::t('app', 'E-mail or Username'))) ?>
			<?= $form->field($model, 'password')->label(false,['class'=>'label-class'])->passwordInput(array('placeholder' => Yii::t('app', 'Password'))) ?>
			<?php echo $form->field($model, 'rememberMe', [
				'template' => "{label}<div class=\"checkbox pull-left\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
			])->checkbox(['label' => Yii::t('app', 'Remember Me')]) ?>

					<?= Html::submitButton(Yii::t('app', 'Login'), ['class' => 'btn btn-me btn-primary btn-block']) ?>
		            <?= Html::a(Yii::t('app', 'Forgot password') . "?", ["/user/forgot"], array('class' => 'text-center new-account')) ?>

			<?php ActiveForm::end(); ?>
            <?= Html::tag('p', Yii::t('app', 'Log in with your Facebook Account'),array('class' => 'text-center new-account')) ?>

		    <?php if (Yii::$app->get("authClientCollection", false) && true): //TODO: ?>
			<!-- <div class="col-lg-offset-2"> -->
		            <?= yii\authclient\widgets\AuthChoice::widget([
		                'baseAuthUrl' => ['/user/auth/login'],
						'options' => ['class'=>'auth-flex']
		            ]) ?>
			<!--</div>-->
		    <?php endif; ?>
		</div>
		</div>
		</div>
	</div> 
</div>
