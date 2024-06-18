<?php
session_start();

// データベース接続情報
$host = 'mysql';
$dbname = 'cafe';
$user = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続失敗: " . $e->getMessage());
}

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: contact_form.php");
        exit;
    } catch (PDOException $e) {
        $errorMessage = "削除エラー: " . $e->getMessage();
    }
} else {
    header("Location: contact_form.php");
    exit;
}
?>
