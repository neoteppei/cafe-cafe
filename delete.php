<?php
session_start();

$dsn = 'mysql:host=mysql;dbname=cafe;charset=utf8';
$user = 'root';
$password = 'root';

$id = $_GET['id'];

// データベースからデータを削除
try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $_SESSION['success_message'] = 'お問い合わせを削除しました。';
    header("Location: contact_form.php");
    exit;
} catch (PDOException $e) {
    echo 'データベース接続失敗：' . $e->getMessage();
}
?>
