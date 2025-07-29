<?php
include 'config/db.php';

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
<?php include 'header.php'; ?> <!-- ヘッダーのインポート -->

<div class="article-container">
    <!-- タイトルを表示 -->
    <div class="title"><?= htmlspecialchars($blog['title']) ?></div>

    <!-- 作成日を表示 -->
    <div class="date"><?= htmlspecialchars($blog['created_at']) ?></div>

    <!-- サムネイルを表示 -->
    <?php
        $thumbnail = $blog['thumbnail'] ?? '';
        if (!empty($thumbnail)) {
            if (strpos($thumbnail, 'uploads/') === 0) {
                $thumbnail = '../../' . $thumbnail;
            } else {
                $thumbnail = 'uploads/' . $thumbnail;
            }
        } 
    ?>
    <img src="<?= htmlspecialchars($thumbnail) ?>" alt="サムネイル">

   <?php
        // 目次の生成処理
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $blog['content'], LIBXML_NOERROR);
        $headings = $dom->getElementsByTagName('*');

        $tocItems = [];
        foreach ($headings as $heading) {
            if (in_array($heading->nodeName, ['h1', 'h2'])) {
                $id = uniqid('heading_');
                $heading->setAttribute('id', $id);
                $tocItems[] = [
                    'id' => $id,
                    'text' => $heading->nodeValue,
                    'level' => $heading->nodeName === 'h1' ? 1 : 2
                ];
            }
        }
        ?>

        <?php if (!empty($tocItems)): ?>
            <!-- 目次を表示（見出しが1つ以上ある場合） -->
            <div id="tableOfContents">
                <p class="mokuji">目次</p>
                <ul>
                    <?php foreach ($tocItems as $item): ?>
                        <li>
                            <a href="#<?= $item['id'] ?>" style="margin-left: <?= $item['level'] === 2 ? '32px' : '10px' ?>;">
                                <?= htmlspecialchars($item['text']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

    <!-- 本文を表示 -->
    <div id="blogContent"><?= $blog['content'] ?></div>

        <!-- カテゴリを表示 -->
    <div id="categoryContainer">
        <?= !empty($blog['categories']) ? '#' . implode('   #', array_map('htmlspecialchars', $blog['categories'])) : '' ?>
    </div>

</div>

<div class="footer">
    &copy; Note of Beginning
</div>

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
</script>
</body>
</html>
