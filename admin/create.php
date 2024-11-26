<?php
// サーバーサイドの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $thumbnail = $_POST['thumbnail'] ?? '';
    $content = $_POST['content'] ?? '';
    $categories = $_POST['selectedCategories'] ?? []; // 選択されたカテゴリ
    $createdAt = date('Y-m-d H:i:s');

    include '../config/config.php';

    try {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // トランザクション開始
        $pdo->beginTransaction();

        // blogsテーブルに記事を保存
        $stmt = $pdo->prepare('INSERT INTO blogs (title, thumbnail, content, created_at) VALUES (?, ?, ?, ?)');
        $stmt->execute([$title, $thumbnail, $content, $createdAt]);
        $blogId = $pdo->lastInsertId(); // 挿入された記事IDを取得

        // blog_categoriesテーブルにカテゴリを保存
        if (!empty($categories)) {
            $stmt = $pdo->prepare('INSERT IGNORE INTO blog_categories (blog_id, category_name) VALUES (?, ?)');
            foreach ($categories as $category) {
                $stmt->execute([$blogId, $category]);
            }
        }

        // トランザクションコミット
        $pdo->commit();

        echo '<script>
        alert("記事が保存されました！");
        window.location.href = "dashboard.php";
    </script>';
    } catch (PDOException $e) {
        // トランザクションをロールバック
        $pdo->rollBack();
        echo '<script>alert("エラーが発生しました: ' . htmlspecialchars($e->getMessage()) . '");</script>';
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
    <form id="blogForm" method="POST" action="">
      <input type="hidden" id="blogId" name="blogId" value="">
      <label for="title">タイトル:</label>
      <input type="text" id="title" style="width: 100%;" name="title" required>
      <label for="content">本文:</label>
      <div id="content"></div>
      <input type="hidden" id="hiddenContent" name="content">

      <label for="newCategory">カテゴリ:</label>
<input type="text" id="newCategory" name="newCategory">
<button id="addCategoryButton" class="CategoryButton" type="button">
  <ion-icon name="add" id="Categoryicon" class="Categoryicon"></ion-icon>
  <span class="button-text">追加</span>
</button>
<div id="categoryCheckboxes"></div>


      <label for="thumbnail">サムネイル URL:</label>
      <input type="text" style="width: 100%;" id="thumbnail" name="thumbnail" required>
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
  </script>
</body>

</html>
