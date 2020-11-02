<?php
$host = "localhost:8889";
$dbname = "mini_bbs";
$username = "root";
$password = "root";
try 
{
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
}
catch (PDOException $e)
{
    print("Error while accessing database: " . $e->getMessage()) . "<br>";
}
?>