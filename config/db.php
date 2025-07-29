<?php
// エラーレポート設定
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// config.php から設定読み込み
$config = include(__DIR__ . '/config.php');

$host     = $config['host'];
$dbname   = $config['dbname'];
$username = $config['username'];
$password = $config['password'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続に失敗しました: " . $e->getMessage());
}
