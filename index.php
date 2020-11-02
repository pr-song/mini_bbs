<?php
    session_start();
    require("database/connection.php");

    // ログイン状態を保持する
    if (!empty($_SESSION["id"]) && $_SESSION["time"]+3600 > time())
    {
        //ログインしている
        $_SESSION["time"] = time();

        $statement = $db->prepare('SELECT * FROM members WHERE id=?');
        $statement->execute([$_SESSION["id"]]);
        $member = $statement->fetch();
    }
    else
    {
        // ログインしていない
        header("Location: login.php");
        exit();
    }

    // 投稿を記録する
    if (!empty($_POST["message"])) {
        $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_post_id=?, created=NOW()');
        $message->execute([
            $member["id"],
            $_POST['message'],
            !empty($_POST["reply_post_id"])?$_POST["reply_post_id"]:NULL
        ]);
        header("Location: index.php");
        exit();
    }

    //　投稿を取得する
    $posts = $db->query('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id = p.member_id ORDER BY p.created DESC');

    // 返信の場合
    if (isset($_REQUEST["res"]))
    {
        $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id = p.member_id AND p.id=? ORDER BY p.created DESC');
        $response->execute([$_REQUEST["res"]]);

        $table = $response->fetch();
        $rep_message = "@". $table["name"]. " " .$table["message"];
    }

    function hsc($value)
    {
        return htmlspecialchars($value);
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホーム</title>
</head>
<body>
    <h3>Welcome <?php echo hsc($member["name"]); ?></h3>
    <div style="text-align: right"><a href="logout.php">ログアウト</a></div>
    <form action="" method="post" enctype="multipart/form-data">
        <dl>
            <dt>メッセージをどうぞ</dt>
            <dd>
                <textarea name="message" cols="50" rows="5"><?php echo !empty($rep_message)?hsc($rep_message):"";?></textarea>
            </dd>
        </dl>
        <input type="hidden" name="reply_post_id" value="<?php echo !empty($_REQUEST["res"])?hsc($_REQUEST["res"]):""; ?>">
        <div><input type="submit" value="投稿する"></div>
    </form>

    <?php foreach ($posts as $post): ?>
        <div style="background-color: #cfcfcf">
        <img src="member_picture/<?php echo hsc($post["picture"]); ?>" alt="<?php echo hsc($post['name']); ?>img" width="48px" height="48px">
        <p>
            <?php echo hsc($post["message"]); ?>
            <span>(<?php echo hsc($post["name"]); ?>)</span>
            [<a href="index.php?res=<?php echo hsc($post["id"]) ;?>">返信</a>]
        </p>
        <p><a href="view.php?id=<?php echo hsc($post["id"]); ?>"><?php echo hsc($post["created"]); ?></a></p>
        <?php if ($post["reply_post_id"] > 0) : ?>
            <a href="view.php?id=<?php echo hsc($post['reply_post_id']); ?>">返信元のメッセージ</a>
        <?php endif; ?>
        <?php if ($_SESSION["id"] == $post["member_id"]) :?>
            [<a href="delete.php?id=<?php echo hsc($post["id"]); ?>">削除</a>]
        <?php endif; ?>
        </div>
        <hr>
    <?php endforeach; ?>
</body>
</html>
