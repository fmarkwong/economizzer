<?php
/* @var $this yii\web\View */

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js', ['depends' => [\yii\web\YiiAsset::class]]);
$this->registerJsFile('/js/jquery-dateFormat.min.js', ['depends' => [\yii\web\YiiAsset::class]]);
$this->registerJs($this->render('js/jquery.accrue.js.php'), yii\web\View::POS_READY);
$this->registerJs($this->render('js/loan-calculator.js.php'), yii\web\View::POS_READY);
$this->registerCssFile('/css/loan-calculator.css');

?>
<h1>Debt Calculator</h1>

<div id="container">

<div class="block grey-lighter">
    <div class="wrap">

        <!-- <h2>Basic Loan Calculation</h2> -->

        <div class="calculator-loan">
            <div class="thirty form">

            </div>

            <div class="thirty">
                <p><label>Results:</label></p>
                <div class="results"></div>
            </div>
        </div>

        <div class="clear"></div>

    </div>
</div>
<br>

<div class="block grey-lighter">
    <div class="wrap">

        <h2>Amortization Schedule Calculation</h2>

        <div class="calculator-amortization">
            <div class="thirty form">

            </div>

            <div class="seventy">
                <p><label>Results:</label></p>
                <div class="results"></div>
            </div>

            <div class="clear"></div>
        </div>


    </div>
</div>
</div>

