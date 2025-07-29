<?php
include '../config/db.php';

// DB接続とカテゴリ一覧の取得（常に実行）
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $allCategoriesStmt = $pdo->query("SELECT DISTINCT category_name FROM blog_categories");
    $allCategories = $allCategoriesStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    echo '<script>alert("データベース接続エラー: ' . htmlspecialchars($e->getMessage()) . '");</script>';
    $allCategories = [];
}

$selectedCategories = $_POST['selectedCategories'] ?? [];

// 記事投稿処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $categories = $_POST['selectedCategories'] ?? [];
    $createdAt = date('Y-m-d H:i:s');
    $thumbnailPath = '';

    // サムネイル画像アップロード処理
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $fileName = basename($_FILES['thumbnail']['name']);
        $uniqueName = uniqid() . '_' . $fileName;
        $targetPath = $uploadDir . $uniqueName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetPath)) {
            $thumbnailPath = $targetPath;
        } else {
            echo '<script>alert("画像のアップロードに失敗しました");</script>';
            exit;
        }
    } else {
        echo '<script>alert("画像が選択されていません");</script>';
        exit;
    }

    try {
        $pdo->beginTransaction();

        // blogs テーブルに保存
        $stmt = $pdo->prepare('INSERT INTO blogs (title, thumbnail, content, created_at) VALUES (?, ?, ?, ?)');
        $stmt->execute([$title, $thumbnailPath, $content, $createdAt]);
        $blogId = $pdo->lastInsertId();

        // カテゴリを保存
        if (!empty($categories)) {
            $stmt = $pdo->prepare('INSERT IGNORE INTO blog_categories (blog_id, category_name) VALUES (?, ?)');
            foreach ($categories as $category) {
                $stmt->execute([$blogId, $category]);
            }
        }

        $pdo->commit();
        echo '<script>alert("記事が保存されました！"); window.location.href="dashboard.php";</script>';
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo '<script>alert("エラー: ' . htmlspecialchars($e->getMessage()) . '");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>新しい記事作成</title>
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/create.css">
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.bubble.css">
  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
</head>

<body>
<?php include './common/sidenav.php'; ?>
  </div>

  <div class="form-container">
    <h1>記事作成</h1>
    <form id="blogForm" method="POST" action="" enctype="multipart/form-data">
      <input type="hidden" id="blogId" name="blogId" value="">
      <label for="title">タイトル</label>
      <input type="text" id="title" style="width: 100%;" name="title" required>
      <label for="content">本文</label>
      <div id="content"></div>
      <input type="hidden" id="hiddenContent" name="content">

      <label for="newCategory">カテゴリ</label>
        <input type="text" id="newCategory" name="newCategory">
        <button id="addCategoryButton" class="CategoryButton" type="button">
          <ion-icon name="add" id="Categoryicon" class="Categoryicon"></ion-icon>
          <span class="button-text">追加</span>
        </button>
        <div id="categoryCheckboxes">
  <?php foreach ($allCategories as $category): ?>
    <div>
      <input 
        type="checkbox" 
        name="selectedCategories[]" 
        value="<?= htmlspecialchars($category) ?>" 
        id="category_<?= htmlspecialchars($category) ?>"
        <?= in_array($category, $selectedCategories) ? 'checked' : '' ?>
      >
      <label for="category_<?= htmlspecialchars($category) ?>">
        <?= htmlspecialchars($category) ?>
      </label>
    </div>
  <?php endforeach; ?>
</div>
        <label for="thumbnail">サムネイル画像</label><br>
        <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
        <div id="thumbnailPreview"></div>
      <button type="submit" class="create-blog-button">記事を作成</button>
    </form>
  </div>

  <?php include './common/footer.php'; ?>

  <script>
    var quillContent = new Quill('#content', {
      theme: 'snow',
      modules: {
        toolbar: [
          [{ 'header': [1, 2, false] }],
          ['bold', 'italic', 'underline', 'strike'],
          [{ 'list': 'ordered' }, { 'list': 'bullet' }],
          ['link']
        ]
      }
    });

    document.getElementById('addCategoryButton').addEventListener('click', function () {
      document.getElementById('hiddenContent').value = quillContent.root.innerHTML;
    var categoryInput = document.getElementById('newCategory');
    var categoryName = categoryInput.value.trim();

    if (categoryName !== '') {
      // チェックボックスとラベルを作成
      var categoryContainer = document.getElementById('categoryCheckboxes');
      var checkbox = document.createElement('input');
      checkbox.type = 'checkbox';
      checkbox.name = 'selectedCategories[]'; // PHPで配列として取得するため
      checkbox.value = categoryName;
      checkbox.id = `category_${categoryName}`;

      var label = document.createElement('label');
      label.htmlFor = `category_${categoryName}`;
      label.textContent = categoryName;

      // カテゴリ削除ボタンの作成
      var deleteButton = document.createElement('button');
      deleteButton.type = 'button';
      deleteButton.textContent = '削除';
      deleteButton.className = 'delete-category-button';
      deleteButton.addEventListener('click', function () {
        checkbox.remove();
        label.remove();
        deleteButton.remove();
      });

      // カテゴリ表示
      categoryContainer.appendChild(checkbox);
      categoryContainer.appendChild(label);
      categoryContainer.appendChild(deleteButton);
      categoryContainer.appendChild(document.createElement('br'));

      // 入力フィールドをクリア
      categoryInput.value = '';
    } else {
      alert('カテゴリ名を入力してください。');
    }
  });

  // 画像プレビュー処理の追加
  document.getElementById('thumbnail').addEventListener('change', function (event) {
    const file = event.target.files[0];
    const preview = document.getElementById('thumbnailPreview');
    preview.innerHTML = ''; // 前の画像をクリア

    if (file && file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = function (e) {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.alt = 'プレビュー';
        img.style.maxWidth = '150px';
        img.style.maxHeight = '150px';
        img.style.border = '1px solid #ccc';
        img.style.marginTop = '10px';
        preview.appendChild(img);
      };
      reader.readAsDataURL(file);
    } else {
      preview.textContent = '画像ファイルを選択してください。';
    }
  });
  </script>
</body>

</html>
