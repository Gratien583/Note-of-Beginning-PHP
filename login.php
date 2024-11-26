<?php
include 'config/config.php';

// ログインフォームの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    // ユーザー名の確認
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE username = ?");
    $stmt->execute([$input_username]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($account) {
        // パスワードの確認
        if (password_verify($input_password, $account['password_hash'])) {
            // ログイン成功
            session_start();
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $input_username;
            header("Location: admin/dashboard.php");
            exit;
        } else {
            $error_message = 'パスワードが正しくありません。';
        }
    } else {
        $error_message = 'ユーザー名が見つかりません。';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログイン</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/login.css">
</head>
<body>
<?php include 'header.php'; ?>
  <form id="login-form" action="login.php" method="POST">
    <?php if (!empty($error_message)): ?>
      <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <label for="username">ユーザー名：</label>
    <input type="text" id="username" name="username" required>

    <label for="password">パスワード：</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">ログイン</button>
  </form>

  <div class="footer">
    &copy; Note of Beginning
</div>
</body>
</html>
