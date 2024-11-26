<?php
include '../config/config.php';

// IDの取得と検証
if (!isset($_GET['id'])) {
    die("IDが指定されていません。");
}
$blogId = (int)$_GET['id'];

// 現在の公開状態を取得
$stmt = $pdo->prepare("SELECT published FROM blogs WHERE id = :id");
$stmt->execute([':id' => $blogId]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
    die("記事が見つかりません。");
}

// 公開状態の切り替え
$newStatus = $blog['published'] ? 0 : 1;

$stmt = $pdo->prepare("UPDATE blogs SET published = :published WHERE id = :id");
$stmt->execute([':published' => $newStatus, ':id' => $blogId]);

// リダイレクト
header("Location: list.php");
exit;
