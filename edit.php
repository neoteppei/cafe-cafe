<?php
session_start();

// データベース接続情報
$host = 'mysql';
$dbname = 'cafe';
$user = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続失敗: " . $e->getMessage());
}

$id = $_GET['id'] ?? null;
$contact = null;

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        $contact = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$contact) {
            die("指定されたデータが見つかりません。");
        }
    } catch (PDOException $e) {
        die("データ取得エラー: " . $e->getMessage());
    }
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $kana = htmlspecialchars(trim($_POST['kana'] ?? ''), ENT_QUOTES, 'UTF-8');
    $tell = htmlspecialchars(trim($_POST['tell'] ?? ''), ENT_QUOTES, 'UTF-8');
    $mail = htmlspecialchars(trim($_POST['mail'] ?? ''), ENT_QUOTES, 'UTF-8');
    $otoi = htmlspecialchars(trim($_POST['otoi'] ?? ''), ENT_QUOTES, 'UTF-8');

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

    // エラーがない場合はデータベースを更新
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE contacts SET name = ?, kana = ?, tell = ?, mail = ?, otoi = ? WHERE id = ?");
            $stmt->execute([$name, $kana, $tell, $mail, $otoi, $id]);

            header("Location: contact_form.php");
            exit;
        } catch (PDOException $e) {
            $errors['db'] = "更新エラー: " . $e->getMessage();
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
</head>
<body>
    <div class="contact-box">
        <h2>お問い合わせ編集</h2>
        <?php if (!empty($errors['db'])) : ?>
            <p class="error"><?php echo $errors['db']; ?></p>
        <?php endif; ?>
        <form action="edit.php?id=<?php echo $contact['id']; ?>" method="post">
            <dl>
                <dt>氏名</dt>
                <dd>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($contact['name'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if (!empty($errors['name'])) : ?>
                        <p class="error"><?php echo $errors['name']; ?></p>
                    <?php endif; ?>
                </dd>
                <dt>フリガナ</dt>
                <dd>
                    <input type="text" name="kana" value="<?php echo htmlspecialchars($contact['kana'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if (!empty($errors['kana'])) : ?>
                        <p class="error"><?php echo $errors['kana']; ?></p>
                    <?php endif; ?>
                </dd>
                <dt>電話番号</dt>
                <dd>
                    <input type="text" name="tell" value="<?php echo htmlspecialchars($contact['tell'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if (!empty($errors['tell'])) : ?>
                        <p class="error"><?php echo $errors['tell']; ?></p>
                    <?php endif; ?>
                </dd>
                <dt>メールアドレス</dt>
                <dd>
                    <input type="text" name="mail" value="<?php echo htmlspecialchars($contact['mail'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if (!empty($errors['mail'])) : ?>
                        <p class="error"><?php echo $errors['mail']; ?></p>
                    <?php endif; ?>
                </dd>
            </dl>
            <h3>お問い合わせ内容</h3>
            <dl class="otoi">
                <dd>
                    <textarea name="otoi"><?php echo htmlspecialchars($contact['otoi'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                    <?php if (!empty($errors['otoi'])) : ?>
                        <p class="error"><?php echo $errors['otoi']; ?></p>
                    <?php endif; ?>
                </dd>
                <dd>
                    <button type="submit">更新</button>
                </dd>
            </dl>
        </form>
        <p><a href="contact_form.php">戻る</a></p>
    </div>
</body>
</html>