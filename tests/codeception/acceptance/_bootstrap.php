<?php
new yii\web\Application(require(dirname(__DIR__) . '/config/acceptance.php'));

$output = exec('php ../yii migrate/redo all --interactive=0');

echo $output;
