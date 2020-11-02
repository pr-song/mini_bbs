<?php
    session_start();
    require("../database/connection.php");

    if (!empty($_POST))
    {
        if ($_POST["name"] == "")
        {   
            $error["name"] = "blank";
        }
        if ($_POST["email"] == "")
        {
            $error["email"] = "blank";
        }
        if ($_POST["password"] == "")
        {
            $error["password"] = "blank";
        }
        if (strlen($_POST["password"]) < 4)
        {
            $error["password"] = "length";
        }

        $fileName = $_FILES["image"]["name"];
        if (!empty($fileName))
        {
            $ext = substr($fileName, -3);
            if ($ext != "jpg" && $ext != "gif")
            {
                $error["image"] = "type";
            }
        }

        if (empty($error))
        {
            $image = date("YmdHis") . $fileName;
            $filePath = "../member_picture/". $image;
            move_uploaded_file($_FILES["image"]["tmp_name"], $filePath);
            $_SESSION["join"] = $_POST;
            $_SESSION["join"]["image"] = $image;

            $statement = $db->prepare('SELECT COUNT(*) AS number_of_accounts FROM members WHERE email=?');
            $statement->execute([$_POST["email"]]);
            $record = $statement->fetch();

            if ($record["number_of_accounts"] > 0)
            {
                $error["email"] = "duplicate";
            }

            header("Location: check.php");
            exit();
        }
    }

    if (!empty($_REQUEST["action"]) && $_REQUEST["action"] == "rewrite")
    {
        $_POST = $_SESSION["join"];
        $error["rewrite"] = true;
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録</title>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <dl>
        <dt>ニックネーム<span class="required">必須</span></dt>
        <dd><input type="text" name="name" size="35" maxlength="255" value="<?php echo empty($_POST["name"])?"":htmlspecialchars($_POST["name"], ENT_QUOTES); ?>"></dd>
        <?php if (!empty($error["name"])): ?>
            <p class="error">＊　ニックネムを入力してください</p>
        <?php endif; ?>

        <dt>メールアドレス<span class="required">必須</span></dt>
        <dd><input type="email" name="email" size="35" maxlength="255" value="<?php echo empty($_POST["email"])?"":htmlspecialchars($_POST["email"], ENT_QUOTES); ?>"></dd>
        <?php if (!empty($error["email"]) && $error["email"] == "blank"): ?>
            <p class="error">＊　メールアドレスを入力してください</p>
        <?php endif; ?>
        <?php if (!empty($error["email"]) && $error["email"] == "duplicate"): ?>
            <p class="error">＊ 指定されたメールアドレスはすでに登録されています</p>
        <?php endif; ?>

        <dt>パスワード<span class="required">必須</span></dt>
        <dd><input type="password" name="password" size="35" maxlength="100" value="<?php echo empty($_POST["password"])?"":htmlspecialchars($_POST["password"], ENT_QUOTES); ?>"></dd>
        <?php if (!empty($error["password"]) && $error["password"] == "blank"): ?>
            <p class="error">＊　パスワードを入力してください</p>
        <?php endif; ?>
        <?php if (!empty($error["password"]) && $error["password"] == "length"): ?>
            <p class="error">＊　パスワードを４文字以上入力してください</p>
        <?php endif; ?>

        <dt>写真など</dt>
        <dd><input type="file" name="image" size="35"></dd>
        <?php if (!empty($error["image"])) : ?>
            <p class="error">* 写真などは「.gif」または「.jpg」の画像を指定してください</p>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p class="error">* 恐れ入りますが、画像を改めて指定してください</p>
        <?php endif; ?>
        </dl>
        <div><input type="submit" value="入力内容を確認する"></div>
    </form>
</body>
</html>