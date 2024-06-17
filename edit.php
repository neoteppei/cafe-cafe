<?php
session_start();

$dsn = 'mysql:host=mysql;dbname=cafe;charset=utf8';
$user = 'root';
$password = 'root';

$errors = [];
$id = $_GET['id'];
$name = '';
$kana = '';
$tel = '';
$email = '';
$body = '';

// データベースから現在のデータを取得
try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($contact) {
        $name = htmlspecialchars($contact['name'], ENT_QUOTES, 'UTF-8');
        $kana = htmlspecialchars($contact['kana'], ENT_QUOTES, 'UTF-8');
        $tel = htmlspecialchars($contact['tel'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8');
        $body = htmlspecialchars($contact['body'], ENT_QUOTES, 'UTF-8');
    } else {
        echo "データが見つかりません。";
        exit;
    }
} catch (PDOException $e) {
    echo 'データベース接続失敗：' . $e->getMessage();
}

// フォームが送信された場合の処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $kana = htmlspecialchars(trim($_POST['kana']), ENT_QUOTES, 'UTF-8');
    $tel = htmlspecialchars(trim($_POST['tel']), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');
    $body = htmlspecialchars(trim($_POST['body']), ENT_QUOTES, 'UTF-8');

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

    if (!preg_match("/^[0-9]+$/", $tel)) {
        $errors['tel'] = "電話番号は0-9の数字のみでご入力ください。";
    }

    if (empty($email)) {
        $errors['email'] = "メールアドレスは必須入力です。";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "正しいメールアドレス形式で入力してください。";
    }

    if (empty($body)) {
        $errors['body'] = "お問い合わせ内容は必須入力です。";
    }

    // エラーがない場合はデータベースを更新
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE contacts SET name = :name, kana = :kana, tel = :tel, email = :email, body = :body WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':kana', $kana);
            $stmt->bindParam(':tel', $tel);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':body', $body);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['success_message'] = 'お問い合わせを更新しました。';
            header("Location: contact_form.php");
            exit;
        } catch (PDOException $e) {
            echo 'データベースエラー：' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>お問い合わせ編集</title>
    <link rel="stylesheet" type="text/css" href="contact.css">
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="contact-box">
        <h2>お問い合わせ編集</h2>
        <form action="edit.php?id=<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" method="post">
            <dl>
                <dt><label for="name">氏名</label><span class="kome">*</span></dt>
                <dd><input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"></dd>
                <?php if (!empty($errors['name'])) : ?><p class="error"><?php echo $errors['name']; ?></p><?php endif; ?>

                <dt><label for="kana">フリガナ</label><span class="kome">*</span></dt>
                <dd><input type="text" name="kana" id="kana" value="<?php echo htmlspecialchars($kana, ENT_QUOTES, 'UTF-8'); ?>"></dd>
                <?php if (!empty($errors['kana'])) : ?><p class="error"><?php echo $errors['kana']; ?></p><?php endif; ?>

                <dt><label for="tel">電話番号</label><span class="kome">*</span></dt>
                <dd><input type="text" name="tel" id="tel" value="<?php echo htmlspecialchars($tel, ENT_QUOTES, 'UTF-8'); ?>"></dd>
                <?php if (!empty($errors['tel'])) : ?><p class="error"><?php echo $errors['tel']; ?></p><?php endif; ?>

                <dt><label for="email">メールアドレス</label><span class="kome">*</span></dt>
                <dd><input type="text" name="email" id="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"></dd>
                <?php if (!empty($errors['email'])) : ?><p class="error"><?php echo $errors['email']; ?></p><?php endif; ?>

                <dt><label for="body">お問い合わせ内容</label><span class="kome">*</span></dt>
                <dd><textarea name="body" id="body"><?php echo htmlspecialchars($body, ENT_QUOTES, 'UTF-8'); ?></textarea></dd>
                <?php if (!empty($errors['body'])) : ?><p class="error"><?php echo $errors['body']; ?></p><?php endif; ?>

                <dd><button type="submit">更新</button></dd>
            </dl>
        </form>
    </div>
</body>
</html>