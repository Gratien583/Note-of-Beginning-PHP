# Note-of-Beginning

![HTML](https://img.shields.io/badge/HTML-E34F26?style=for-the-badge&logo=html&logoColor=white)
![CSS](https://img.shields.io/badge/CSS-1572B6?style=for-the-badge&logo=css&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

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
