<?php
/* @var $this yii\web\View */
// $this->registerJsFile('/js/jquery.accrue.js', ['depends' => [\yii\web\YiiAsset::class]]);
$this->registerJs($this->render('js/jquery.accrue.js.php'), yii\web\View::POS_READY);
$this->registerCssFile('/css/loan-calculator.css');
$this->registerJs('$(".calculator-loan").accrue()');

?>
<h1>Debt Calculator</h1>


<div class="block grey-lighter">
    <div class="wrap">

        <h2>Basic Loan Calculation</h2>

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
