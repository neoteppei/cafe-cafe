<?php
session_start();

// データベース接続情報
$dsn = 'mysql:host=mysql;dbname=cafe;charset=utf8';
$user = 'root';
$password = 'root';

// データベース接続の共通関数
function getPdoConnection()
{
    global $dsn, $user, $password;
    try {
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        echo 'データベース接続失敗：' . $e->getMessage();
        exit;
    }
}

// 入力データのサニタイズ関数
function sanitize($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// エラーメッセージの初期化関数
function initializeErrors()
{
    return [
        'name' => '',
        'kana' => '',
        'tel' => '',
        'email' => '',
        'body' => ''
    ];
}
