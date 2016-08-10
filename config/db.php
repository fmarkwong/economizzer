<?php

$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS'); 

return [
    'class' => 'yii\db\Connection',
    'dsn' => "mysql:host=127.0.0.1;dbname=$db_name",
    'username' => $db_user,
    'password' => $db_pass,
    'charset' => 'utf8',
    //'tablePrefix' => 'tb_',
    'enableSchemaCache' => true,
];
