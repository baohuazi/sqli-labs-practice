<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-13：POST 型（单引号 + 括号，报错注入）=====
// 后端 SQL：SELECT * FROM users WHERE username=('$uname') AND password=('$passwd') LIMIT 0,1
// 不回显数据 → 用 extractvalue / updatexml 报错把信息带出来

function l13_form() {
    echo '<form method="POST" style="font-family:monospace">
         Username: <input type="text" name="uname" size="30"><br><br>
         Password: <input type="text" name="passwd" size="30"><br><br>
         <input type="submit" name="submit" value="Login">
         </form>';
}

if (isset($_POST['uname']) && isset($_POST['passwd'])) {
    $uname  = $_POST['uname'];
    $passwd = $_POST['passwd'];
    mysqli_select_db($con, "security");

    $sql = "SELECT * FROM users WHERE username=('$uname') AND password=('$passwd') LIMIT 0,1";
    echo "<pre style='color:#c0392b'>DEBUG SQL: $sql</pre>";

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);

    if ($row) {
        echo "You are in...........";
    } else {
        // 故意保留报错输出 → 教报错注入
        print_r(mysqli_error($con));
    }
} else {
    l13_form();
    echo "<br>闭合用 ') ，报错带数据：<br>";
    echo "uname 填 <code>') AND extractvalue(1,concat(0x7e,database(),0x7e)) --+</code><br>";
    echo "爆表：') AND extractvalue(1,concat(0x7e,(SELECT group_concat(table_name) FROM information_schema.tables WHERE table_schema=database()),0x7e)) --+";
}
?>
