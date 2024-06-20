<?php
require 'common.php';

if (!isset($_GET['id'])) {
    header("Location: contact_form.php");
    exit;
}

$pdo = getPdoConnection();

$id = (int)$_GET['id'];
$sqlErrorMessage = '';
$errors = initializeErrors();
$name = '';
$kana = '';
$tel = '';
$email = '';
$body = '';

// データを取得
try {
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$contact) {
        header("Location: contact_form.php");
        exit;
    }

    $name = $contact['name'];
    $kana = $contact['kana'];
    $tel = $contact['tel'];
    $email = $contact['email'];
    $body = $contact['body'];
} catch (PDOException $e) {
    $sqlErrorMessage = 'データベースエラー：' . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize($_POST['name']);
    $kana = sanitize($_POST['kana']);
    $tel = sanitize($_POST['tel']);
    $email = sanitize($_POST['email']);
    $body = sanitize($_POST['body']);

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

    if (empty($tel)) {
        $errors['tel'] = "電話番号は必須入力です。";
    } elseif (!preg_match("/^[0-9]+$/", $tel)) {
        $errors['tel'] = "電話番号は0-9の数字のみでご入力ください。";
    } elseif (strlen($tel) > 20) {
        $errors['tel'] = "電話番号は20文字以内で入力してください。";
    }

    if (empty($email)) {
        $errors['email'] = "メールアドレスは必須入力です。";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "正しいメールアドレス形式で入力してください。";
    }

    if (empty($body)) {
        $errors['body'] = "お問い合わせ内容は必須入力です。";
    }

    if (empty($errors['name']) && empty($errors['kana']) && empty($errors['tel']) && empty($errors['email']) && empty($errors['body'])) {
        try {
            $stmt = $pdo->prepare("UPDATE contacts SET name = :name, kana = :kana, tel = :tel, email = :email, body = :body WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':kana', $kana);
            $stmt->bindParam(':tel', $tel);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':body', $body);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: contact_form.php");
            exit;
        } catch (PDOException $e) {
            $sqlErrorMessage = 'データベースエラー：' . $e->getMessage();
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
        <form action="edit.php?id=<?php echo sanitize($id); ?>" method="post">
            <dl>
                <dt><label for="name">氏名</label><span class="kome">*</span></dt>
                <dd><input type="text" name="name" id="name" value="<?php echo sanitize($name); ?>"></dd>
                <?php if (!empty($errors['name'])) : ?><p class="error"><?php echo $errors['name']; ?></p><?php endif; ?>

                <dt><label for="kana">フリガナ</label><span class="kome">*</span></dt>
                <dd><input type="text" name="kana" id="kana" value="<?php echo sanitize($kana); ?>"></dd>
                <?php if (!empty($errors['kana'])) : ?><p class="error"><?php echo $errors['kana']; ?></p><?php endif; ?>

                <dt><label for="tel">電話番号</label><span class="kome">*</span></dt>
                <dd><input type="text" name="tel" id="tel" value="<?php echo sanitize($tel); ?>"></dd>
                <?php if (!empty($errors['tel'])) : ?><p class="error"><?php echo $errors['tel']; ?></p><?php endif; ?>

                <dt><label for="email">メールアドレス</label><span class="kome">*</span></dt>
                <dd><input type="text" name="email" id="email" value="<?php echo sanitize($email); ?>"></dd>
                <?php if (!empty($errors['email'])) : ?><p class="error"><?php echo $errors['email']; ?></p><?php endif; ?>

                <dt><label for="body">お問い合わせ内容</label><span class="kome">*</span></dt>
                <dd><textarea name="body" id="body"><?php echo sanitize($body); ?></textarea></dd>
                <?php if (!empty($errors['body'])) : ?><p class="error"><?php echo $errors['body']; ?></p><?php endif; ?>

                <dd><button type="submit">更新</button></dd>
            </dl>
        </form>
        <?php if (!empty($sqlErrorMessage)) : ?>
            <p class="error"><?php echo $sqlErrorMessage; ?></p>
        <?php endif; ?>
    </div>
</body>

</html>