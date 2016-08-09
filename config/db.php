<?php

if (YII_ENV === 'production') {
    $url = parse_url(getenv("CLEARDB_DATABASE_URL"));
    $host = $url['host'];
    $db = substr($url["path"], 1);

    return [
        'class' => 'yii\db\Connection',
        'dsn' => "mysql:host=$host;dbname=$db",
        'username' => $url['user'], 
        'password' => $url['pass'], 
        'charset' => 'utf8',
        //'tablePrefix' => 'tb_',
        'enableSchemaCache' => true,
    ];
} else {
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=127.0.0.1;dbname=economizzer',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
        //'tablePrefix' => 'tb_',
        'enableSchemaCache' => true,
    ];
}
