<?php
include '../../config/config.php';

try {
    // データベース接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続に失敗しました: " . $e->getMessage());
}

// ブログ記事の取得
$blogs = [];
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

try {
    $sql = "SELECT * FROM blogs WHERE published = 1";
    $params = [];

    // 検索キーワードが設定されている場合
    if ($searchKeyword) {
        $sql .= " AND title LIKE :search";
        $params[':search'] = "%$searchKeyword%";
    }

    // カテゴリが選択されている場合
    if ($category) {
        $sql .= " AND :category IN (SELECT category_name FROM blog_categories WHERE blog_id = blogs.id)";
        $params[':category'] = $category;
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ブログ記事の取得に失敗しました: " . $e->getMessage());
}

// カテゴリ一覧の取得
$categories = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT category_name FROM blog_categories");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("カテゴリ一覧の取得に失敗しました: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>トップページ</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://unpkg.com/@fortawesome/fontawesome-free@5.15.3/css/all.css">
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

<div class="content-container" style="padding-top: 50px">
    <h1>記事一覧</h1>

    <!-- カテゴリ選択フォーム -->
    <div class="search-container">
        <label for="category-select">カテゴリ:</label>
        <select id="category-select" onchange="updateCategory()">
            <option value="">すべて</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- 検索フォーム -->
    <div class="search-container">
        <form method="GET" action="index.php">
            <input type="text" name="search" id="search-input" placeholder="検索キーワードを入力" value="<?= htmlspecialchars($searchKeyword) ?>">
            <button id="search-button" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- ブログ記事のリスト -->
    <div id="blog-list" class="blog-container">
    <?php if (!empty($blogs)): ?>
    <?php foreach ($blogs as $blog): ?>
        <a href="view.php?id=<?= $blog['id'] ?>" class="blog-link">
            <div class="blog-box">
                <?php if (!empty($blog['thumbnail'])): ?>
                    <div class="thumbnail-container">
                        <img src="<?= htmlspecialchars($blog['thumbnail']) ?>" alt="サムネイル" class="blog-thumbnail">
                    </div>
                <?php endif; ?>
                <h2 class="blog-title"><?= htmlspecialchars($blog['title']) ?></h2>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>

    </div>
</div>
<script>
    function logout() {
        if (confirm('本当にログアウトしますか？')) {
            window.location.href = '../logout.php';
        }
    }
</script>

<?php include '../common/footer.php'; ?>
</body>
</html>
