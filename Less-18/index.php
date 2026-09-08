<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-18：User-Agent 头部注入（报错）=====
// 后端 SQL：INSERT INTO security.uagents (uagent, ip_address, username) VALUES ('$uagent', '$ip', '$uname')
// 登录成功后把 UA 写进 uagents 表 → 注入点在 User-Agent 头

if (isset($_POST['uname']) && isset($_POST['passwd'])) {
    $uname  = $_POST['uname'];
    $passwd = $_POST['passwd'];
    mysqli_select_db($con, "security");

    $login = "SELECT * FROM users WHERE username='$uname' AND password='$passwd' LIMIT 0,1";
    echo "<pre style='color:#c0392b'>DEBUG SQL(login): $login</pre>";
    $result = mysqli_query($con, $login);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);

    if ($row) {
        echo "You are in...........<br>";
        $uagent = $_SERVER['HTTP_USER_AGENT'] ?? 'NULL';
        $ip     = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // 漏洞点：UA 直接拼进 INSERT
        $insert = "INSERT INTO security.uagents (uagent, ip_address, username) VALUES ('$uagent', '$ip', '$uname')";
        echo "<pre style='color:#c0392b'>DEBUG SQL(insert): $insert</pre>";
        mysqli_query($con, $insert);

        if (mysqli_error($con)) {
            print_r(mysqli_error($con));
        } else {
            echo "UA 已记录。用 Burp/浏览器插件改 User-Agent 头注入：<br>";
            echo "<code>' AND extractvalue(1,concat(0x7e,database(),0x7e)) AND '</code>";
        }
    } else {
        echo "登录失败";
    }
} else {
    echo '<form method="POST" style="font-family:monospace">
         Username: <input type="text" name="uname" size="30" value="admin"><br><br>
         Password: <input type="text" name="passwd" size="30" value="admin"><br><br>
         <input type="submit" name="submit" value="Login">
         </form>';
    echo "<br>先登录成功（admin/admin），再在 User-Agent 头注入：<br>";
    echo "<code>' AND extractvalue(1,concat(0x7e,database(),0x7e)) AND '</code>";
}
?>
