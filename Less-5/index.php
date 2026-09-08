<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-5：报错注入（不回显数据，只回显 SQL 错误 / "You are in"）=====
// 后端 SQL：SELECT * FROM users WHERE id='$id' LIMIT 0,1
// 特点：页面不显示 username/password，只显示"是否登录成功"或数据库报错
// 用途：用 extractvalue() / updatexml() 等报错函数把数据"吐"在报错信息里

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_select_db($con, "security");

    $sql = "SELECT * FROM users WHERE id='$id' LIMIT 0,1";
    echo "<pre style='color:#c0392b'>DEBUG SQL: $sql</pre>";

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);

    if ($row) {
        echo "You are in...........";   // 不输出数据，只告诉你"查到了"
    } else {
        print_r(mysqli_error($con));    // 把报错打出来 —— 注入数据的出口
    }
} else {
    echo "请在 URL 后加 ?id=1；<br>";
    echo "本关看不到数据，要靠报错注入，例如：<br>";
    echo "?id=1' AND extractvalue(1,concat(0x7e,(SELECT database()),0x7e)) --+";
}
?>
