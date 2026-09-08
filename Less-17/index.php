<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-17：POST 型 UPDATE 注入（password 字段）=====
// 后端 SQL：UPDATE users SET password='$passwd' WHERE username='$uname'
// 先校验 username 是否存在（只对存在的用户改密码），注入点只在 password
// 报错注入：password 里用 extractvalue / updatexml 报错带出信息

function l17_form() {
    echo '<form method="POST" style="font-family:monospace">
         Username: <input type="text" name="uname" size="30" value="admin"><br><br>
         Password: <input type="text" name="passwd" size="30"><br><br>
         <input type="submit" name="submit" value="Login">
         </form>';
}

if (isset($_POST['uname']) && isset($_POST['passwd'])) {
    $uname  = $_POST['uname'];
    $passwd = $_POST['passwd'];
    mysqli_select_db($con, "security");

    // 校验 uname 是否存在（模拟真实：只对存在的用户改密码）
    $check = "SELECT username FROM users WHERE username='$uname' LIMIT 0,1";
    echo "<pre style='color:#c0392b'>DEBUG SQL(check): $check</pre>";
    $cr = mysqli_query($con, $check);
    $crow = mysqli_fetch_array($cr, MYSQLI_BOTH);

    if ($crow) {
        // 漏洞点：password 直接拼进 UPDATE
        $sql = "UPDATE users SET password='$passwd' WHERE username='$uname'";
        echo "<pre style='color:#c0392b'>DEBUG SQL(update): $sql</pre>";
        mysqli_query($con, $sql);

        if (mysqli_error($con)) {
            echo "报错注入命中：<br>";
            print_r(mysqli_error($con));
        } else {
            echo "密码已更新（可被注入）。试试 password 填报错 payload：<br>";
            echo "<code>' AND extractvalue(1,concat(0x7e,database(),0x7e)) AND '</code>";
        }
    } else {
        echo "用户名不存在：<br>";
        print_r(mysqli_error($con));
    }
} else {
    l17_form();
    echo "<br>username 填已存在的 admin，password 填报错 payload：<br>";
    echo "<code>' AND extractvalue(1,concat(0x7e,database(),0x7e)) AND '</code>";
}
?>
