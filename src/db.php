<?php

$config = require __DIR__ . '/config.php';
$db = $config['db'];

$dsn = "pgsql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}";
try {
    $pdo = new PDO($dsn, $db['user'], $db['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Exception $e) {
    die('Connection failed: ' . $e->getMessage());
}

?>

