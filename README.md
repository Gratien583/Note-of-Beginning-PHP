# Note-of-Beginning-PHP

## accounts テーブル

| カラム名      | データ型     | NULL 許可 | キー | デフォルト値        | その他         |
| ------------- | ------------ | --------- | ---- | ------------------- | -------------- |
| id            | int(11)      | NO        | PRI  | NULL                | auto_increment |
| username      | varchar(50)  | NO        | UNI  | NULL                |                |
| password_hash | varchar(255) | NO        |      | NULL                |                |
| created_at    | timestamp    | NO        |      | current_timestamp() |                |

## blog_categories テーブル

| カラム名      | データ型     | NULL 許可 | キー | デフォルト値 | その他         |
| ------------- | ------------ | --------- | ---- | ------------ | -------------- |
| id            | int(11)      | NO        | PRI  | NULL         | auto_increment |
| blog_id       | int(11)      | NO        | MUL  | NULL         |                |
| category_name | varchar(255) | NO        |      | NULL         |                |

## blogs テーブル

| カラム名   | データ型     | NULL 許可 | キー | デフォルト値        | その他         |
| ---------- | ------------ | --------- | ---- | ------------------- | -------------- |
| id         | int(11)      | NO        | PRI  | NULL                | auto_increment |
| title      | varchar(255) | NO        |      | NULL                |                |
| thumbnail  | text         | YES       |      | NULL                |                |
| published  | tinyint(1)   | YES       |      | 0                   |                |
| created_at | timestamp    | NO        |      | current_timestamp() |                |
| content    | text         | YES       |      | NULL                |                |
