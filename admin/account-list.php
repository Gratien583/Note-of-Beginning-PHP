<?php
include '../config/config.php';

try {
    // データベース接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続に失敗しました: " . $e->getMessage());
}

// ログイン状態の確認
session_start();
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

// アカウント削除の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account_id'])) {
    $accountId = $_POST['delete_account_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM accounts WHERE id = ?");
        $stmt->execute([$accountId]);
        $message = "アカウントが削除されました。";
    } catch (PDOException $e) {
        $message = "アカウント削除に失敗しました: " . $e->getMessage();
    }
}

// アカウント一覧の取得
try {
    $stmt = $pdo->query("SELECT * FROM accounts ORDER BY id ASC");
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("アカウント一覧の取得に失敗しました: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アカウント一覧</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="./css/account-list.css">
</head>
<body>
    <?php include './common/sidenav.php'; ?>

    <div class="content-container" style="padding-top: 20px;">

        <?php if (!empty($message)): ?>
            <p style="color: green;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ユーザー名</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $account): ?>
                    <tr>
                        <td><?= htmlspecialchars($account['id']) ?></td>
                        <td><?= htmlspecialchars($account['username']) ?></td>
                        <td>
                            <form method="POST" action="account-list.php" onsubmit="return confirm('このアカウントを削除しますか？')">
                                <input type="hidden" name="delete_account_id" value="<?= htmlspecialchars($account['id']) ?>">
                                <button type="submit" class="delete-button">削除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php include './common/footer.php'; ?>
</body>
</html>
