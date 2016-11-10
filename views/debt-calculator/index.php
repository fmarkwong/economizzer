<?php
/* @var $this yii\web\View */

// $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js', ['depends' => [\yii\web\YiiAsset::class]]);
// $this->registerJsFile('/js/jquery-dateFormat.min.js', ['depends' => [\yii\web\YiiAsset::class]]);
$this->registerJs($this->render('js/jquery.accrue.js.php'), yii\web\View::POS_READY);
$this->registerJs($this->render('js/loan-calculator.js.blade.php'), yii\web\View::POS_READY);
$this->registerCssFile('/css/loan-calculator.css');

?>
<br>
<h2><?= Yii::t('app', 'Debt Calculator') ?></h2>
<br>
<hr>

<div id="container">

<div class="block grey-lighter">
    <div style="padding-left: 260px" class="wrap">
        <div class="calculator-loan">
            <div style="margin-top: 25px" class="thirty form">
            </div>

            <div style="margin-left: 40px; width: 300px" class="thirty">
                <label style="margin-bottom: 20px">Results:</label>
                <div style="border: 1px solid #dce4ec;" class="results"></div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
</div>

