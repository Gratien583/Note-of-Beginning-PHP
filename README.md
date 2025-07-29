# Note-of-Beginning-PHP
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![jQuery](https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white)
![Quill](https://img.shields.io/badge/Quill-0084FF?style=for-the-badge&logo=quill&logoColor=white)
<br>
**Note-of-Beginning** は、私が初めて企画から設計・開発までを手がけたブログ兼メモアプリです。

## 📖 名前の由来

**Note-of-Beginning** という名前には、それぞれ以下の意味が込められています。

- **Note** → 「メモ」や「記録」
- **of** → 「～の」
- **Beginning** → 「始まり」

つまり、「始まりの記録」や「最初の一歩を刻むノート」という意味を持ち、私自身のエンジニア（プログラマー）としての第一歩を象徴するプロジェクトです。今回はそれのPHP+MySQL版


## トップページ
<img width="1908" height="918" alt="スクリーンショット 2025-07-29 173001" src="https://github.com/user-attachments/assets/c1e301bd-5d50-4e76-a1c6-5eaa93a5c967" />


最初は何も表示されませんが、記事が投稿されると、上記のようにサムネイル付きで一覧表示されます。  
右上のカテゴリや検索ワードによる絞り込みも可能です。
記事詳細ページは最後の方にある**記事詳細ページ**に記載

## アカウント作成画面
<img width="1907" height="912" alt="スクリーンショット 2025-07-29 191305" src="https://github.com/user-attachments/assets/dfee32fe-a742-4dca-ae57-3430c9b94bba" />

- ユーザー名、パスワード、パスワード確認の入力フォームが表示されます。
- 入力後「アカウント作成」ボタンでユーザー登録を行えます。
- 作成されたアカウントでログイン可能になります。

## ログイン画面
<img width="1904" height="914" alt="スクリーンショット 2025-07-29 191233" src="https://github.com/user-attachments/assets/8b294341-abb2-414d-9fc8-ed88a9a2a980" />

- 登録済みのユーザー名とパスワードでログインできます。
- 成功するとトップページに遷移します。
- セッション管理によりログイン状態が維持されます。

## 管理者トップページ
<img width="1906" height="921" alt="スクリーンショット 2025-07-29 200236" src="https://github.com/user-attachments/assets/bcb7c4f0-7aeb-46bf-9b8b-b28132973262" />


- ログイン後に表示される管理画面です。
- サイドバーから以下の管理機能にアクセスできます：
  - 📝 記事一覧の表示・編集
  - ➕ 新規記事の作成
  - 👤 ユーザー情報の表示
  - 👥 アカウントの作成・一覧表示
  - 🔓 ログアウト
- メインエリアには、作成した記事のサムネイルとタイトルがカード形式で表示されます。
- 各カードをクリックすると、該当記事の編集ページに遷移できます。

### ✅ 公開状態の識別

- カード下部の色で、記事の**公開状態**を視覚的に識別できます：
  - 🟢 緑：公開中
  - 🔴 赤：非公開
- この判定によって、管理者はどの記事がユーザーに表示されているかを一目で確認できます。

## 記事作成ページ
<img width="1889" height="923" alt="スクリーンショット 2025-07-29 191413" src="https://github.com/user-attachments/assets/c990cf55-162c-46ea-ad33-f27d0255498c" />

- 新しい記事を作成するための画面です。
- 入力項目：
  - **タイトル**：記事の見出しを入力
  - **本文**：WYSIWYGエディタにより、装飾付きのリッチテキスト入力が可能
  - **カテゴリ**：新しいカテゴリ名を入力し、［＋追加］で登録
  - **サムネイル画像**：記事のカードに表示されるアイキャッチ画像をアップロード
- ［記事を作成］ボタンを押すと、入力内容が投稿として登録されます。

### 作成した記事の反映について
<img width="1906" height="925" alt="スクリーンショット 2025-07-29 200359" src="https://github.com/user-attachments/assets/c5ccc11e-0a23-410e-b3fe-17a2ea63e3d7" />

- 記事作成ページで投稿された記事は、**管理者トップページ**にカード形式で表示されます。
- ただし、作成直後は「**非公開**」状態であり、**記事一覧ページ**から「公開」に切り替えない限り、一般ユーザーには表示されません。


## 記事一覧ページ

記事作成後の投稿は、この「記事一覧ページ」に一覧表示されます。
<img width="1915" height="917" alt="スクリーンショット 2025-07-29 194333" src="https://github.com/user-attachments/assets/3c3adff1-db80-4ed2-892a-b31997da43b4" />

- 各記事は1行ごとのテーブル形式で表示され、以下の情報が確認できます：
  - **タイトル**：投稿された記事のタイトル
  - **サムネイル**：アップロードされた画像（アイキャッチ）
  - **投稿日時**：作成された日時
  - **状態**：初期状態では「非公開」（赤背景）として表示される
  - **操作**：「公開する」ボタンで公開状態に変更可能
  - **編集**：「編集」ボタンで記事編集画面に移動
  - **削除**：「削除」ボタンで記事を削除可能

> 記事は作成直後は「非公開」状態であり、ここで「公開する」ボタンを押さないと一般ユーザーには表示されません。

## 記事編集ページ
<img width="1909" height="919" alt="スクリーンショット 2025-07-29 194749" src="https://github.com/user-attachments/assets/8b1b4af3-58db-426e-b857-ec27d23b566a" />

- 「記事一覧ページ」の各記事カードにある **［編集］ボタン** をクリックすると、この「記事編集ページ」に遷移します。
- すでに投稿された記事の内容がフォームに反映されており、以下の項目を自由に修正できます：

  - **タイトル**：記事の見出しを変更可能
  - **本文**：WYSIWYGエディタでリッチテキストの編集が可能
  - **カテゴリ**：既存のカテゴリにチェックを入れたり、新しいカテゴリを追加可能
  - **サムネイル画像**：任意で新しい画像に差し替え可能

- 編集完了後、画面下部のボタンから記事を更新できます。

> 🔁 記事を編集しても、公開状態は変わりません。公開設定を変更したい場合は「記事一覧ページ」に戻って操作してください。

## ユーザー表示ページ
<img width="1904" height="923" alt="スクリーンショット 2025-07-29 194349" src="https://github.com/user-attachments/assets/e23670b5-ee89-4d20-a99a-2e5500c7d4cf" />

このページは、**ユーザー側にどのように記事が見えるかを管理者が確認するためのページ**です。
- 記事はカード形式で表示され、サムネイルとタイトルが確認できます。
- 表示されるのは**公開・非公開の記事の両方が**表示されます。
このページにより、**ユーザーにはどこまで記事が公開されているのか**を確認できます。

## 記事詳細ページ
<img width="1889" height="924" alt="スクリーンショット 2025-07-29 194358" src="https://github.com/user-attachments/assets/c52f18cd-f98b-40b0-8983-598a1a80e86c" />
<img width="1887" height="921" alt="スクリーンショット 2025-07-29 194416" src="https://github.com/user-attachments/assets/57a23de0-15eb-40ed-90f5-8dad7d69b8e4" />


- ユーザーに実際に公開される記事の詳細ページです。
- **タイトル・投稿日・サムネイル画像・本文**が表示されます。
- 本文は **Quillエディタで記述されたリッチテキスト**がそのまま反映され、表示されます。
- **QuillのHeading 1 → HTMLの`<h1>`（大見出し）**
- **QuillのHeading 2 → HTMLの`<h2>`（小見出し）**
- 見出し構造に応じて、**自動で目次が生成され、ページ上部に表示**されます。
- アップロードした**サムネイル画像**は記事本文の上部に表示されます。

> このページは、**記事の公開状態が「公開」になっている場合のみ、ユーザーに表示されます**。  
> 管理者は非公開の記事でも閲覧することができます。

## 今後の展望
> もっとデザインをよくしたい。
> 所々雑な部分があるのでそれを修正したい。

## 🔗 関連プロジェクト

- 📄 ローカルストレージ版  
https://github.com/Gratien583/Note-of-Beginning-js

- ⚛️ React + Supabase (β)：フロントエンドを React、バックエンドに Supabase を使用  
  [https://github.com/Gratien583/Note-of-Beginning-React-Beta](https://github.com/Gratien583/Note-of-Beginning-React-Beta)  

- 🐘 PHP + MySQL 版：サーバーサイドを PHP、データ保存に MySQL を使用  （このリポジトリ） 

- 🐳 Docker 対応版 (β)  
  [https://github.com/Gratien583/Note-of-Beginning-Docker](https://github.com/Gratien583/Note-of-Beginning-Docker)
