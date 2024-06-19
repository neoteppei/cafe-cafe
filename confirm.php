<?php
require 'common.php';

if (empty($_SESSION['form_data'])) {
    header("Location: contact_form.php");
    exit;
}

$formData = $_SESSION['form_data'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pdo = getPdoConnection();
    try {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, kana, tel, email, body) VALUES (:name, :kana, :tel, :email, :body)");
        $stmt->bindParam(':name', $formData['name']);
        $stmt->bindParam(':kana', $formData['kana']);
        $stmt->bindParam(':tel', $formData['tel']);
        $stmt->bindParam(':email', $formData['email']);
        $stmt->bindParam(':body', $formData['body']);
        $stmt->execute();

        unset($_SESSION['form_data']);
        header("Location: complete.php");
        exit;
    } catch (PDOException $e) {
        echo 'データベースエラー：' . $e->getMessage();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>お問い合わせ確認</title>
    <link rel="stylesheet" type="text/css" href="contact.css">
</head>
<body>
    <div class="contact-box">
        <h2>お問い合わせ内容確認</h2>
        <form action="confirm.php" method="post">
            <dl>
                <dt>氏名</dt>
                <dd><?php echo sanitize($formData['name']); ?></dd>

                <dt>フリガナ</dt>
                <dd><?php echo sanitize($formData['kana']); ?></dd>

                <dt>電話番号</dt>
                <dd><?php echo sanitize($formData['tel']); ?></dd>

                <dt>メールアドレス</dt>
                <dd><?php echo sanitize($formData['email']); ?></dd>

                <dt>お問い合わせ内容</dt>
                <dd><?php echo nl2br(sanitize($formData['body'])); ?></dd>
            </dl>
            <button type="submit">送信</button>
            <button type="button" onclick="history.back();">戻る</button>
        </form>
    </div>
</body>
</html>