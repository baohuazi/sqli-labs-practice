<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-6：字符型注入（双引号闭合）=====
// 后端 SQL：SELECT * FROM users WHERE id="$id" LIMIT 0,1
// 双引号包裹 → 输入双引号破坏语法；页面不回显数据，靠报错注入/布尔盲注

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_select_db($con, "security");

    $sql = "SELECT * FROM users WHERE id=\"$id\" LIMIT 0,1";
    echo "<pre style='color:#c0392b'>DEBUG SQL: $sql</pre>";

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);

    if ($row) {
        echo "You are in...........";   // 不输出数据，只告诉你"查到了"
    } else {
        print_r(mysqli_error($con));    // 报错出口
    }
} else {
    echo "请在 URL 后加 ?id=1；<br>";
    echo "本关是双引号闭合，试试 ?id=1\" 看报什么错<br>";
    echo "报错注入：?id=1\" AND extractvalue(1,concat(0x7e,(SELECT database()),0x7e)) --+<br>";
    echo "或布尔盲注：?id=1\" AND substr(database(),1,1)='s' --+";
}
?>
