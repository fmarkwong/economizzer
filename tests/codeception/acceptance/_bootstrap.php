<?php
new yii\web\Application(require(dirname(__DIR__) . '/config/acceptance.php'));

exec('php ../yii migrate/redo all --interactive=0', $output);
print_r($output);
