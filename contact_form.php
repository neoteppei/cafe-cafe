<?php
session_start();

$dsn = 'mysql:host=mysql;dbname=cafe;charset=utf8';
$user = 'root';
$password = 'root';

$errors = [];
$name = '';
$kana = '';
$tel = '';
$email = '';
$body = '';
$sqlErrorMessage = ''; // SQLエラーメッセージ用の変数

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

    // エラーがない場合はデータベースに保存
    if (empty($errors)) {
        try {
            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("INSERT INTO contacts (name, kana, tel, email, body) VALUES (:name, :kana, :tel, :email, :body)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':kana', $kana);
            $stmt->bindParam(':tel', $tel);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':body', $body);
            $stmt->execute();

            $_SESSION['success_message'] = 'お問い合わせを送信しました。';
            header("Location: contact_form.php");
            exit;
        } catch (PDOException $e) {
            $sqlErrorMessage = 'データベースエラー：' . $e->getMessage();
        }
    }
}

// データベースからデータを取得
try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM contacts");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'データベース接続失敗：' . $e->getMessage();
}
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

                <dd><button type="submit">送信</button></dd>
            </dl>
        </form>

        <?php if (!empty($sqlErrorMessage)) : ?>
            <p class="error"><?php echo $sqlErrorMessage; ?></p>
        <?php endif; ?>

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
                            <td><?php echo htmlspecialchars($contact['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($contact['kana'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($contact['tel'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($contact['body'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($contact['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo htmlspecialchars($contact['id'], ENT_QUOTES, 'UTF-8'); ?>">編集</a>
                                <a href="delete.php?id=<?php echo htmlspecialchars($contact['id'], ENT_QUOTES, 'UTF-8'); ?>" onclick="return confirm('本当に削除しますか？');">削除</a>
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