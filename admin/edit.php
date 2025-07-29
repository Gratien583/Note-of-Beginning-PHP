<?php
include '../config/db.php';

// ブログIDを取得
$blogId = $_GET['id'] ?? null;

if ($blogId) {
    // 指定されたブログ記事を取得
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->execute([$blogId]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$blog) {
        die("記事が見つかりません。");
    }
} else {
    die("ブログIDが指定されていません。");
}

// フォーム送信時の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $categories = $_POST['selectedCategories'] ?? [];
    $thumbnailPath = $_POST['currentThumbnail'] ?? ''; // もともとの画像パスを保持

    // サムネイルが新しくアップロードされた場合
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $fileName = basename($_FILES['thumbnail']['name']);
        $uniqueName = uniqid() . '_' . $fileName;
        $targetPath = $uploadDir . $uniqueName;

        // アップロード先フォルダがなければ作成
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetPath)) {
            $thumbnailPath = $targetPath;
        } else {
            echo '<script>alert("画像のアップロードに失敗しました");</script>';
            exit;
        }
    }

    // ブログ記事の更新
    $stmt = $pdo->prepare("UPDATE blogs SET title = ?, content = ?, thumbnail = ? WHERE id = ?");
    $stmt->execute([$title, $content, $thumbnailPath, $blogId]);

    // カテゴリの更新（全削除→再追加）
    $pdo->prepare("DELETE FROM blog_categories WHERE blog_id = ?")->execute([$blogId]);
    foreach ($categories as $category) {
        $pdo->prepare("INSERT INTO blog_categories (blog_id, category_name) VALUES (?, ?)")
            ->execute([$blogId, $category]);
    }

    header("Location: dashboard.php");
    exit;
}

// カテゴリ一覧を取得
$categoriesStmt = $pdo->query("SELECT DISTINCT category_name FROM blog_categories");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);

// 現在のブログ記事に関連付けられているカテゴリを取得
$selectedCategoriesStmt = $pdo->prepare("SELECT category_name FROM blog_categories WHERE blog_id = ?");
$selectedCategoriesStmt->execute([$blogId]);
$selectedCategories = $selectedCategoriesStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事の編集</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/edit.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
</head>

<body>
    <?php include './common/sidenav.php'; ?>

    <div class="form-container">
        <h1>記事を編集</h1>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="blogId" value="<?= htmlspecialchars($blogId) ?>">

            <label for="title">タイトル:</label>
            <input type="text" id="title" name="title" style="width: 100%;" value="<?= htmlspecialchars($blog['title']) ?>" required>

            <label for="content">本文:</label>
            <div id="content"></div>
            <textarea id="hiddenContent" name="content" style="display:none;"></textarea>

            <label for="newCategory">カテゴリ:</label>
            <input type="text" id="newCategory">
            <button type="button" id="addCategoryButton">追加</button>

            <div id="categoryCheckboxes">
                <?php foreach ($categories as $category): ?>
                    <div>
                        <input type="checkbox" name="selectedCategories[]" value="<?= htmlspecialchars($category) ?>"
                            <?= in_array($category, $selectedCategories) ? 'checked' : '' ?>>
                        <label><?= htmlspecialchars($category) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

            <label for="thumbnail">サムネイル画像:</label>
                <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
                <input type="hidden" name="currentThumbnail" value="<?= htmlspecialchars($blog['thumbnail']) ?>">

                <div id="thumbnailPreview">
                <?php if (!empty($blog['thumbnail'])): ?>
                    <img src="<?= htmlspecialchars($blog['thumbnail']) ?>" alt="Thumbnail Preview" style="max-width: 300px;">
                <?php endif; ?>
                </div>
            <button type="submit">記事を保存</button>
        </form>
    </div>

    <?php include './common/footer.php'; ?>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    // Quillエディタの設定（既存コード）
    var quill = new Quill('#content', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['link']
            ]
        }
    });

    // 初期値を設定
    quill.root.innerHTML = <?= json_encode($blog['content']) ?>;
    console.log("content to insert:", <?= json_encode($blog['content']) ?>);


    // フォーム送信時にQuillの内容をhidden textareaに設定
    document.querySelector('form').addEventListener('submit', function () {
        document.getElementById('hiddenContent').value = quill.root.innerHTML;
    });

    // サムネイルプレビュー更新
   document.getElementById('thumbnail').addEventListener('change', function (event) {
    const file = event.target.files[0];
    const preview = document.getElementById('thumbnailPreview');
    preview.innerHTML = ''; // 前のプレビューをクリア

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = 'サムネイルプレビュー';
            img.style.maxWidth = '300px';
            img.style.marginTop = '10px';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    } else {
        preview.textContent = '画像ファイルを選択してください。';
    }
});

    // カテゴリ追加ボタンの処理
    var addCategoryButton = document.getElementById('addCategoryButton');
    var categoryInput = document.getElementById('newCategory');
    var categoryContainer = document.getElementById('categoryCheckboxes');

    addCategoryButton.addEventListener('click', function () {
        var categoryName = categoryInput.value.trim();

        // 入力が空の場合は処理を終了
        if (!categoryName) {
            alert('カテゴリ名を入力してください。');
            return;
        }

        // 重複チェック
        var existingCategories = Array.from(categoryContainer.querySelectorAll('input[type="checkbox"]'))
            .map(function (checkbox) {
                return checkbox.value;
            });

        if (existingCategories.includes(categoryName)) {
            alert('このカテゴリは既に追加されています。');
            return;
        }

        // 新しいカテゴリを追加
        var checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.name = 'selectedCategories[]';
        checkbox.value = categoryName;
        checkbox.checked = true;

        var label = document.createElement('label');
        label.textContent = categoryName;

        var container = document.createElement('div');
        container.appendChild(checkbox);
        container.appendChild(label);

        categoryContainer.appendChild(container);

        // 入力フィールドをクリア
        categoryInput.value = '';
    });
});

    </script>
</body>
</html>

