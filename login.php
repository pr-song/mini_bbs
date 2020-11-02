<?php
    require("database/connection.php");
    session_start();

    if (!empty($_COOKIE["email"]))
    {
        $_POST["email"] = $_COOKIE["email"];
        $_POST["password"] = $_COOKIE["password"];
        $_POST["save"] = "on";
    }

    if (!empty($_POST))
    {
        //ログインの処理
        if (!empty($_POST["email"]) && !empty($_POST["password"]))
        {
            $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
            $login->execute([
                $_POST["email"],
                sha1($_POST["password"])
            ]);

            $member = $login->fetch();
            
            if ($member)
            {
                //ログイン成功
                $_SESSION["id"] = $member["id"];
                $_SESSION["time"] = time();

                //ログイン情報を記録する
                if ($_POST["save"] === "on")
                {
                    setcookie("email", $_POST["email"], time()+3600);
                    setcookie("password", $_POST["password"], time()+3600);
                }

                header("Location: index.php");
                exit();
            }
            else
            {
                $error["login"] = "failed";
            }
        }
        else
        {
            $error["login"] = "blank";
        }
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
</head>
<body>
    <div>
        <p>メールアドレスとパスワードを記入してログインしてください。</p>
        <p>入会手続きがまだの方はこちらからどうぞ。</p>
        <p>&raquo;<a href="join/">入会手続きをする</a></p>

        <form action="" method="post">
            <dl>
                <dt>メールアドレス</dt>
                <dd>
                    <input type="text" name="email" size="35" maxlength="255" value="<?php echo empty($_POST["email"])?"":htmlspecialchars($_POST["email"], ENT_QUOTES); ?>">
                    <?php if (!empty($error["login"]) && $error["login"] == "blank"): ?>
                        <p class="error">* メールアドレスとパスワードをご記入ください</p>
                    <?php endif; ?>
                    <?php if (!empty($error["login"]) && $error["login"] == "failed"): ?>
                        <p class="error">* ログインに失敗しました。正しくご記入ください。</p>
                    <?php endif; ?>
                </dd>
                <dt>パスワード</dt>
                <dd>
                    <input type="password" name="password" size="35" maxlength="255" value="<?php echo empty($_POST["password"])?"":htmlspecialchars($_POST["password"], ENT_QUOTES); ?>">
                </dd>
                <dt>ログイン情報の記録</dt>
                <dd>
                    <input type="checkbox" name="save" value="on" id="save">
                    <label for="save">次回から自動的にログインする</label>
                </dd>
            </dl>
            <div><input type="submit" value="ログインする"></div>
        </form>
    </div>
</body>
</html>
