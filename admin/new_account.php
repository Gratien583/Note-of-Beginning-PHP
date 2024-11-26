<?php
include '../config/config.php';

$errors = []; // 各フィールドのエラーメッセージを格納

// フォーム送信時の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_username = $_POST['username'] ?? '';
    $input_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm-password'] ?? '';

    // ユーザー名のチェック
    if (empty($input_username)) {
        $errors['username'] = 'ユーザー名を入力してください。';
    } else {
        // ユーザー名の重複確認
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM accounts WHERE username = ?");
        $stmt->execute([$input_username]);
        if ($stmt->fetchColumn() > 0) {
            $errors['username'] = 'このユーザー名は既に使用されています。';
        }
    }

    // パスワードのチェック
    if (empty($input_password)) {
        $errors['password'] = 'パスワードを入力してください。';
    } elseif ($input_password !== $confirm_password) {
        $errors['confirm-password'] = 'パスワードが一致しません。';
    }

    if (empty($errors)) {
        // パスワードをハッシュ化
        $hashed_password = password_hash($input_password, PASSWORD_BCRYPT);

        // アカウント情報をデータベースに保存
        $stmt = $pdo->prepare("INSERT INTO accounts (username, password_hash) VALUES (?, ?)");
        if ($stmt->execute([$input_username, $hashed_password])) {
            // アカウント作成成功時にリダイレクト
            header("Location: dashboard.php");
            exit;
        } else {
            $errors['general'] = 'アカウント作成中にエラーが発生しました。';
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
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/new_account.css">
</head>
<body>
<?php include './common/sidenav.php'; ?>

<div class="content-container">
    <form id="signup-form" method="POST" action="new_account.php">
        <label for="username">ユーザー名：</label>
        <input type="text" id="username" name="username" 
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
               class="<?= isset($errors['username']) ? 'error' : '' ?>" required>
        <?php if (isset($errors['username'])): ?>
            <p class="error-message"><?= htmlspecialchars($errors['username']) ?></p>
        <?php endif; ?>

        <label for="password">パスワード：</label>
        <input type="password" id="password" name="password" 
               class="<?= isset($errors['password']) ? 'error' : '' ?>" required>
        <?php if (isset($errors['password'])): ?>
            <p class="error-message"><?= htmlspecialchars($errors['password']) ?></p>
        <?php endif; ?>

        <label for="confirm-password">パスワード確認：</label>
        <input type="password" id="confirm-password" name="confirm-password" 
               class="<?= isset($errors['confirm-password']) ? 'error' : '' ?>" required>
        <?php if (isset($errors['confirm-password'])): ?>
            <p class="error-message"><?= htmlspecialchars($errors['confirm-password']) ?></p>
        <?php endif; ?>

        <button type="submit">アカウント作成</button>
    </form>
</div>

<?php include './common/footer.php'; ?>
</body>
</html>
