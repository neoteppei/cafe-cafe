<?php
require 'common.php';

$errors = initializeErrors();
$name = '';
$kana = '';
$tel = '';
$email = '';
$body = '';
$sqlErrorMessage = ''; // SQLエラーメッセージ用の変数

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
        $_SESSION['form_data'] = $_POST;
        header("Location: confirm.php");
        exit;
    }
}

$pdo = getPdoConnection();
$contacts = $pdo->query("SELECT * FROM contacts")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>お問い合わせ</title>
    <link rel="stylesheet" type="text/css" href="contact.css">
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="contact-box">
        <h2>お問い合わせ</h2>
        <form action="contact_form.php" method="post">
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

                <dd><button type="submit">送信</button></dd>
            </dl>
        </form>

        <h2>お問い合わせ一覧</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>氏名</th>
                    <th>フリガナ</th>
                    <th>電話番号</th>
                    <th>メールアドレス</th>
                    <th>お問い合わせ内容</th>
                    <th>作成日時</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($contacts)) : ?>
                    <?php foreach ($contacts as $contact) : ?>
                        <tr>
                            <td><?php echo sanitize($contact['name']); ?></td>
                            <td><?php echo sanitize($contact['kana']); ?></td>
                            <td><?php echo sanitize($contact['tel']); ?></td>
                            <td><?php echo sanitize($contact['email']); ?></td>
                            <td><?php echo sanitize($contact['body']); ?></td>
                            <td><?php echo sanitize($contact['created_at']); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo sanitize($contact['id']); ?>">編集</a>
                                <a href="delete.php?id=<?php echo sanitize($contact['id']); ?>" onclick="return confirm('本当に削除しますか？');">削除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7">お問い合わせはありません。</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>