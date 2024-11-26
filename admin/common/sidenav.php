<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<link rel="stylesheet" href="./css/sidenav.css">
<?php
// 現在のページを取得（$_SERVER['REQUEST_URI'] を使用）
$current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
?>
<div class="sidenav">
    <ul>
        <li class="list <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
            <a href="dashboard.php">
                <span class="icon"><ion-icon name="home-outline"></ion-icon></span>
                <span class="title">記事編集</span>
            </a>
        </li>
        <li class="list <?= $current_page === 'list.php' ? 'active' : '' ?>">
            <a href="list.php">
                <span class="icon"><ion-icon name="list-outline"></ion-icon></span>
                <span class="title">記事一覧</span>
            </a>
        </li>
        <li class="list <?= $current_page === 'create.php' ? 'active' : '' ?>">
            <a href="create.php">
                <span class="icon"><ion-icon name="add-outline"></ion-icon></span>
                <span class="title">記事作成</span>
            </a>
        </li>
        <li class="list <?= $current_page === 'index.php' ? 'active' : '' ?>">
            <a href="user_view/index.php">
                <span class="icon"><ion-icon name="eye-outline"></ion-icon></span>
                <span class="title">ユーザー表示</span>
            </a>
        </li>
        <li class="list <?= $current_page === 'new_account.php' ? 'active' : '' ?>">
            <a href="new_account.php">
                <span class="icon"><ion-icon name="person-add-outline"></ion-icon></span>
                <span class="title">アカウント作成</span>
            </a>
        </li>
        <li class="list <?= $current_page === 'account-list.php' ? 'active' : '' ?>">
            <a href="account-list.php">
                <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
                <span class="title">アカウントリスト</span>
            </a>
        </li>
        <li class="list">
            <a href="#" onclick="logout()">
                <span class="icon"><ion-icon name="log-out-outline"></ion-icon></span>
                <span class="title">ログアウト</span>
            </a>
        </li>
    </ul>
</div>
<script>
    function logout() {
    if (confirm('本当にログアウトしますか？')) {
        window.location.href = 'logout.php';
    }
}
</script>
