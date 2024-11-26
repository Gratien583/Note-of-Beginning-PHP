<?php
include '../config/config.php';

// ログイン状態の確認
session_start();
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

// ブログ記事の取得
$stmt = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC");
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事一覧</title>
    <link rel="stylesheet" href="./css/list.css">
</head>

<body>
<?php include './common/sidenav.php'; ?>

    <div class="content-container" style="padding-top: 20px;">
        <table id="blog-list" class="blog-table">
            <thead>
                <tr>
                    <th class="title">タイトル</th>
                    <th class="thumbnail-container">サムネイル</th>
                    <th class="status-cell">状態</th>
                    <th class="switching">操作</th>
                    <th class="edit-cell">編集</th>
                    <th class="delete-cell">削除</th>
                </tr>
            </thead>
            <tbody>
    <?php foreach ($blogs as $blog): ?>
        <tr>
            <td><?= htmlspecialchars($blog['title']) ?></td>
            <td>
                <div class="thumbnail-container">
                    <img src="<?= htmlspecialchars($blog['thumbnail']) ?>" alt="Thumbnail" class="blog-thumbnail">
                </div>
            </td>
            <td class="status-cell <?= $blog['published'] ? 'status-published' : 'status-unpublished' ?>">
                <?= $blog['published'] ? '公開中' : '非公開' ?>
            </td>
            <td>
                <button onclick="togglePublication(<?= $blog['id'] ?>)">
                    <?= $blog['published'] ? '非公開にする' : '公開する' ?>
                </button>
            </td>
            <td><button onclick="window.location.href='edit.php?id=<?= $blog['id'] ?>'">編集</button></td>
            <td class="delete-cell">
                <button class="delete-button" onclick="deleteBlog(<?= $blog['id'] ?>)">削除</button>
            </td>

        </tr>
    <?php endforeach; ?>
</tbody>

        </table>
    </div>
    <?php include './common/footer.php'; ?>
    <script>
        function togglePublication(blogId) {
            if (confirm('公開/非公開を変更しますか？')) {
                window.location.href = 'toggle_publication.php?id=' + blogId;
            }
        }

        function deleteBlog(blogId) {
            if (confirm('この記事を削除しますか？')) {
                window.location.href = 'delete_blog.php?id=' + blogId;
            }
        }


        function logout() {
            localStorage.setItem('loggedIn', 'false');
            window.location.href = '../index.php';
        }
    </script>
</body>

</html>
