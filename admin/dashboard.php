<?php
include '../config/config.php';

// ログイン状態の確認
session_start();
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

// ブログ記事の取得
try {
    $stmt = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC");
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ブログ記事の取得に失敗しました: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者メインページ</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
<?php include './common/sidenav.php'; ?>

    <!-- コンテンツ -->
    <div class="content-container">
    <div id="blog-list" class="blog-container">
    <?php if (!empty($blogs)): ?>
        <?php foreach ($blogs as $blog): ?>
            <a href="edit.php?id=<?= $blog['id'] ?>" class="blog-box-link">
                <div class="blog-box" style="border-bottom: 15px solid <?= $blog['published'] ? 'lightgreen' : 'red' ?>;">
                    <?php if (!empty($blog['thumbnail'])): ?>
                        <div class="thumbnail-container">
                            <img src="<?= htmlspecialchars($blog['thumbnail']) ?>" alt="サムネイル" class="blog-thumbnail">
                        </div>
                    <?php endif; ?>
                    <h2><?= htmlspecialchars($blog['title']) ?></h2>
                </div>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <p>　</p>
    <?php endif; ?>
</div>

        </div>
    </div>
    <?php include './common/footer.php'; ?>
</body>

</html>
