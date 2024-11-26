<?php
include '../config/config.php';

// GETパラメータから記事IDを取得
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("無効なリクエストです");
}

$blogId = (int)$_GET['id'];

try {
    // 記事を削除
    $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
    $stmt->execute([$blogId]);

    echo '<script>
        alert("記事が削除されました！");
        window.location.href = "list.php";
    </script>';
} catch (PDOException $e) {
    die("削除中にエラーが発生しました: " . $e->getMessage());
}
?>
