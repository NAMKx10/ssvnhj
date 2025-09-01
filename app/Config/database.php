<?php
// app/Config/database.php

$db_host = 'localhost:3307';
$db_name = 'naji_123'; // <-- قم بتغيير هذا
$db_user = 'naji';         // <-- قم بتغيير هذا إذا لزم الأمر
$db_pass = '123456';             // <-- قم بتغيير هذا إذا لزم الأمر
$charset = 'utf8mb4';

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}