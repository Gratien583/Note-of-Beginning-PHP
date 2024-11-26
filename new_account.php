<?php
include 'config/config.php';


$error_message = "";

// フォーム送信時の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // パスワード確認
    if ($input_password !== $confirm_password) {
        $error_message = 'パスワードが一致しません。';
    } else {
        // パスワードをハッシュ化
        $hashed_password = password_hash($input_password, PASSWORD_BCRYPT);

        // ユーザー名の重複確認
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM accounts WHERE username = ?");
        $stmt->execute([$input_username]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $error_message = 'このユーザー名は既に使用されています。';
        } else {
            // アカウント情報をデータベースに保存
            $stmt = $pdo->prepare("INSERT INTO accounts (username, password_hash) VALUES (?, ?)");
            if ($stmt->execute([$input_username, $hashed_password])) {
                // アカウント作成成功時にリダイレクト
                header("Location: account_success.php");
                exit;
            } else {
                $error_message = 'アカウント作成中にエラーが発生しました。';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>アカウント作成</title>
  <link rel="stylesheet" href="css/new_account.css">
</head>
<body>
<?php include 'header.php'; ?>

  <div class="content">
    <h1>アカウント作成</h1>
    <?php if (!empty($error_message)): ?>
      <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <form id="signup-form" action="new_account.php" method="POST">
      <label for="username">ユーザー名：</label>
      <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>

      <label for="password">パスワード：</label>
      <input type="password" id="password" name="password" required>

      <label for="confirm-password">パスワード確認：</label>
      <input type="password" id="confirm-password" name="confirm-password" required>

      <button type="submit">アカウント作成</button>
    </form>
  </div>

  <div class="footer">
    &copy; Note of Beginning
</div>
</body>
</html>