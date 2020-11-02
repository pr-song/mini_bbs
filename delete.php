<?php
    session_start();
    require("database/connection.php");

    if (isset($_SESSION["id"]))
    {
        $id = $_REQUEST["id"];

        // 投稿を検索する
        $messages = $db->prepare('SELECT * FROM posts WHERE id=?');
        $messages->execute([$id]);
        $message = $messages->fetch();

        if ($message["member_id"] == $_SESSION["id"])
        {
            $del = $db->prepare('DELETE FROM posts WHERE id=?');
            $del->execute([$id]);
        }
    }

    header("Location: index.php");
    exit();
?>