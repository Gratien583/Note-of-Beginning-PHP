<?php
include '../../config/config.php';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続に失敗しました: " . $e->getMessage());
}

// 記事のIDをURLパラメータから取得
$blogId = isset($_GET['id']) ? intval($_GET['id']) : null;

// 記事データの取得
$blog = null;
if ($blogId) {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = :id AND published = 1");
    $stmt->execute([':id' => $blogId]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);

    // カテゴリの取得
    if ($blog) {
        $stmtCategories = $pdo->prepare("SELECT category_name FROM blog_categories WHERE blog_id = :id");
        $stmtCategories->execute([':id' => $blogId]);
        $categories = $stmtCategories->fetchAll(PDO::FETCH_COLUMN);
        $blog['categories'] = $categories;
    }
}

// 非公開または存在しない記事の場合
if (!$blog) {
    echo "<p>この記事は非公開です。</p>";
    echo '<meta http-equiv="refresh" content="3; url=index.php">';
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($blog['title']) ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/view.css">
</head>
<body>
<header class="header">
    <a href="../dashboard.php">記事編集</a>
    <a href="../list.php">記事一覧</a>
    <a href="../create.php">記事作成</a>
    <a href="../user_view/index.php">ユーザー表示</a>
    <a href="../new_account.php">アカウント作成</a>
    <a href="../account-list.php">アカウントリスト</a>
    <a href="#" onclick="logout()">ログアウト</a>
  </header>

<div class="article-container">
    <!-- タイトルを表示 -->
    <div class="title"><?= htmlspecialchars($blog['title']) ?></div>

    <!-- 作成日を表示 -->
    <div class="date"><?= htmlspecialchars($blog['created_at']) ?></div>

    <!-- サムネイルを表示 -->
    <?php if (!empty($blog['thumbnail'])): ?>
        <img src="<?= htmlspecialchars($blog['thumbnail']) ?>" alt="サムネイル">
    <?php endif; ?>

    <!-- カテゴリを表示 -->
    <div id="categoryContainer">
        <?= !empty($blog['categories']) ? '#' . implode('   #', array_map('htmlspecialchars', $blog['categories'])) : '' ?>
    </div>

    <!-- 目次 -->
    <div id="tableOfContents">
        <p class="mokuji">目次</p>
        <ul>
            <?php
            // 目次の生成
            $dom = new DOMDocument();
            @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $blog['content'], LIBXML_NOERROR);
            $headings = $dom->getElementsByTagName('*');

            foreach ($headings as $heading) {
                if (in_array($heading->nodeName, ['h1', 'h2'])) {
                    $id = uniqid('heading_');
                    $heading->setAttribute('id', $id);
                    $level = $heading->nodeName === 'h1' ? 1 : 2;
                    echo '<li><a href="#' . $id . '" style="margin-left: ' . ($level === 2 ? '32px' : '10px') . '">' .
                        htmlspecialchars($heading->nodeValue) . '</a></li>';
                }
            }
            ?>
        </ul>
    </div>

    <!-- 本文を表示 -->
    <div id="blogContent"><?= $blog['content'] ?></div>
</div>

<?php include '../common/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 目次リンクのスムーススクロール
        document.querySelectorAll('#tableOfContents a').forEach(function (link) {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                var targetId = this.getAttribute('href').substring(1);
                var target = document.getElementById(targetId);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    });

    function logout() {
        if (confirm('本当にログアウトしますか？')) {
            window.location.href = '../logout.php';
        }
    }

</script>
</body>
</html>
