<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-11：POST 型字符注入（单引号闭合）=====
// 后端 SQL：SELECT * FROM users WHERE username='$uname' AND password='$passwd' LIMIT 0,1
// 登录框 uname/passwd 走 POST，单引号包裹 → 用户名处加 ' 即破坏语法

function l11_form() {
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

    $sql = "SELECT * FROM users WHERE username='$uname' AND password='$passwd' LIMIT 0,1";
    echo "<pre style='color:#c0392b'>DEBUG SQL: $sql</pre>";

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);

    if ($row) {
        echo "You are in...........<br>";
        echo "Your Login name: " . $row['username'] . "<br>";
        echo "Your Password: "   . $row['password'] . "<br>";
    } else {
        echo "登录失败 / 报错：<br>";
        print_r(mysqli_error($con));
    }
} else {
    l11_form();
    echo "<br>试试 uname 填 <code>admin' --+</code>（密码判断被注释，恒真登录）<br>";
    echo "或 uname 填 <code>' UNION SELECT 1,version(),database() --+</code> 看回显位";
}
?>
