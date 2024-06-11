<?php
session_start();

$errors = [];
$name = '';
$kana = '';
$tell = '';
$mail = '';
$otoi = '';

// 戻るボタンが押された場合、セッションからデータを取得
if (isset($_SESSION['formData'])) {
    $formData = $_SESSION['formData'];
    $name = htmlspecialchars($formData['name'], ENT_QUOTES, 'UTF-8');
    $kana = htmlspecialchars($formData['kana'], ENT_QUOTES, 'UTF-8');
    $tell = htmlspecialchars($formData['tell'], ENT_QUOTES, 'UTF-8');
    $mail = htmlspecialchars($formData['mail'], ENT_QUOTES, 'UTF-8');
    $otoi = htmlspecialchars($formData['otoi'], ENT_QUOTES, 'UTF-8');
}

// フォームが送信された場合の処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $kana = htmlspecialchars(trim($_POST['kana']), ENT_QUOTES, 'UTF-8');
    $tell = htmlspecialchars(trim($_POST['tell']), ENT_QUOTES, 'UTF-8');
    $mail = htmlspecialchars(trim($_POST['mail']), ENT_QUOTES, 'UTF-8');
    $otoi = htmlspecialchars(trim($_POST['otoi']), ENT_QUOTES, 'UTF-8');

    // バリデーション処理
    if (empty($name)) {
        $errors['name'] = "氏名は必須入力です。";
    } elseif (mb_strlen($name) > 10) {
        $errors['name'] = "氏名は10文字以内で入力してください。";
    }

    if (empty($kana)) {
        $errors['kana'] = "フリガナは必須入力です。";
    } elseif (mb_strlen($kana) > 10) {
        $errors['kana'] = "フリガナは10文字以内で入力してください。";
    }

    if (!preg_match("/^[0-9]+$/", $tell)) {
        $errors['tell'] = "電話番号は0-9の数字のみでご入力ください。";
    }

    if (empty($mail)) {
        $errors['mail'] = "メールアドレスは必須入力です。";
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errors['mail'] = "正しいメールアドレス形式で入力してください。";
    }

    if (empty($otoi)) {
        $errors['otoi'] = "お問い合わせ内容は必須入力です。";
    }

    // エラーがない場合は確認画面へリダイレクト
    if (empty($errors)) {
        $_SESSION['formData'] = $_POST;
        header("Location: confirm.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>お問い合わせ</title>
    <link rel="stylesheet" type="text/css" href="contact.css">
    <script src="contact.js"></script>
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="contact-box">
        <h2>お問い合わせ</h2>
        <form action="contact.php" method="post">
            <h3>下記の項目をご記入の上送信ボタンを押してください</h3>
            <p>送信頂いた件につきましては、当社より折り返しご連絡を差し上げます。</p>
            <p>なお、ご連絡までに、お時間を頂く場合もございますので予めご了承ください。</p>
            <p><span class="kome">*</span>は必須項目となります。</p>

            <dl>
                <dt>
                    <label for="name">氏名</label>
                    <span class="kome">*</span>
                    <?php if (!empty($errors['name'])) : ?>
                        <p class="error"><?php echo htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                </dt>
                <dd>
                    <input type="text" name="name" id="name" class="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="name">
                </dd>
                <dt>
                    <label for="kana">フリガナ</label>
                    <span class="kome">*</span>
                    <?php if (!empty($errors['kana'])) : ?>
                        <p class="error"><?php echo htmlspecialchars($errors['kana'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                </dt>
                <dd>
                    <input type="text" name="kana" id="kana" class="kana" value="<?php echo htmlspecialchars($kana, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="additional-name">
                </dd>
                <dt>
                    <label for="tell">電話番号</label>
                    <span class="kome">*</span>
                    <?php if (!empty($errors['tell'])) : ?>
                        <p class="error"><?php echo htmlspecialchars($errors['tell'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                </dt>
                <dd>
                    <input type="text" name="tell" id="tell" class="tell" value="<?php echo htmlspecialchars($tell, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="tel">
                </dd>
                <dt>
                    <label for="mail">メールアドレス</label>
                    <span class="kome">*</span>
                    <?php if (!empty($errors['mail'])) : ?>
                        <p class="error"><?php echo htmlspecialchars($errors['mail'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                </dt>
                <dd>
                    <input type="text" name="mail" id="mail" class="mail" value="<?php echo htmlspecialchars($mail, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="email">
                </dd>
            </dl>
            <h3>
                <label for="otoi">
                    お問い合わせ内容をご記入ください
                    <span class="kome">*</span>
                    <?php if (!empty($errors['otoi'])) : ?>
                        <p class="error"><?php echo htmlspecialchars($errors['otoi'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                </label>
            </h3>
            <dl class="otoi">
                <dd>
                    <textarea name="otoi" id="otoi" class="otoi" autocomplete="off"><?php echo htmlspecialchars($otoi, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </dd>
                <dd>
                    <button type="submit">送信</button>
                </dd>
            </dl>
        </form>
    </div>
</body>
</html>