document.addEventListener("DOMContentLoaded", function() {
    var form = document.getElementById('contactForm');

    form.addEventListener('submit', function(event) {
        var isValid = true;
        var errors = [];

        // 氏名チェック
        var name = document.getElementById('name').value.trim();
        if (name === "" || name.length > 10) {
            isValid = false;
            errors.push("氏名は必須入力です。10文字以内で入力してください。");
        }

        // フリガナチェック
        var kana = document.getElementById('kana').value.trim();
        if (kana === "" || kana.length > 10) {
            isValid = false;
            errors.push("フリガナは必須入力です。10文字以内で入力してください。");
        }

        // 電話番号チェック
        var tell = document.getElementById('tell').value.trim();
        if (!/^[0-9]+$/.test(tell)) {
            isValid = false;
            errors.push("電話番号は0-9の数字のみでご入力ください。");
        }

        // メールアドレスチェック
        var mail = document.getElementById('mail').value.trim();
        if (mail === "" || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail)) {
            isValid = false;
            errors.push("正しいメールアドレス形式で入力してください。");
        }

        // お問い合わせ内容チェック
        var otoi = document.getElementById('otoi').value.trim();
        if (otoi === "") {
            isValid = false;
            errors.push("お問い合わせ内容は必須入力です。");
        }

        if (!isValid) {
            event.preventDefault(); // フォームの送信を中止

            // エラーメッセージを表示
            alert(errors.join("\n"));
        }
    });
});
