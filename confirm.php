<?php
session_start();

if (!isset($_SESSION['formData'])) {
    header("Location: contact.php");
    exit;
}

$formData = $_SESSION['formData'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['back'])) {
        header("Location: contact.php");
        exit;
    }

    if (isset($_POST['submit'])) {
        // 実際の処理（例：データベースへの保存、メール送信など）
        // ここにデータベースへの保存やメール送信などの処理を記述することができます。
        
        // 完了画面へリダイレクト
        unset($_SESSION['formData']);
        header("Location: complete.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Confirm</title>
    <link rel="stylesheet" type="text/css" href="confirm.css">
</head>

<body>
    <div class="contact-box">
        <h2>確認画面</h2>
        <p>下記の内容を確認の上送信ボタンを押してください</p>
        <p>内容を訂正する場合は戻るを押してください。</p>
        <form action="confirm.php" method="post">
            <dl>
                <dt>氏名</dt>
                <dd><?php echo htmlspecialchars($formData['name'], ENT_QUOTES, 'UTF-8'); ?></dd>
                <dt>フリガナ</dt>
                <dd><?php echo htmlspecialchars($formData['kana'], ENT_QUOTES, 'UTF-8'); ?></dd>
                <dt>電話番号</dt>
                <dd><?php echo htmlspecialchars($formData['tell'], ENT_QUOTES, 'UTF-8'); ?></dd>
                <dt>メールアドレス</dt>
                <dd><?php echo htmlspecialchars($formData['mail'], ENT_QUOTES, 'UTF-8'); ?></dd>
                <dt>お問い合わせ内容</dt>
                <dd><?php echo nl2br(htmlspecialchars($formData['otoi'], ENT_QUOTES, 'UTF-8')); ?></dd>
            </dl>
            <div class="button">
                <button type="submit" name="back">戻る</button>
                <button type="submit" name="submit">送信</button>
            </div>
        </form>
    </div>
</body>

</html>