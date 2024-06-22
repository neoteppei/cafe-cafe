<?php
require 'common.php';

if (!isset($_GET['id'])) {
    header("Location: contact_form.php");
    exit;
}

$pdo = getPdoConnection();

$id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
} catch (PDOException $e) {
    echo 'データベースエラー：' . $e->getMessage();
    exit;
}

header("Location: contact_form.php");
exit;
