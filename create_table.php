<?php
$host = 'mysql'; // Docker Composeで指定されたMySQLサービス名
$db   = 'cafe';
$user = 'root';
$pass = 'root'; // docker-compose.ymlで設定したMYSQL_ROOT_PASSWORD
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected to the database successfully!<br>";

    // データベースの作成
    $pdo->exec("CREATE DATABASE IF NOT EXISTS cafe");
    echo "Database 'cafe' created or already exists.<br>";

    // データベースを選択
    $pdo->exec("USE cafe");

    // テーブルの作成
    $sql = "
    CREATE TABLE IF NOT EXISTS contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        kana VARCHAR(50) NOT NULL,
        tel VARCHAR(11),
        email VARCHAR(100) NOT NULL,
        body TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Table 'contacts' created successfully!";
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>