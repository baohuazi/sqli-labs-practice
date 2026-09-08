<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-20：Cookie 注入（报错 / 回显）=====
// 登录后把 username 写进 Cookie；后续请求从 Cookie 取 uname 拼 SQL：
//   SELECT * FROM users WHERE username='$cookee' LIMIT 0,1
// 注入点在 Cookie 的 uname 值

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
        setcookie("uname", $uname, time() + 3600);
        echo "已写入 Cookie: uname=$uname （<a href='index.php'>刷新页面</a>，从 Cookie 读 uname 查询）<br>";
    } else {
        echo "登录失败";
    }
} elseif (isset($_COOKIE['uname'])) {
    $cookee = $_COOKIE['uname'];
    mysqli_select_db($con, "security");

    $sql = "SELECT * FROM users WHERE username='$cookee' LIMIT 0,1";
    echo "<pre style='color:#c0392b'>DEBUG SQL(cookie): $sql</pre>";

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);

    if ($row) {
        echo "Your Login name: " . $row['username'] . "<br>";
        echo "Your Password: "   . $row['password'] . "<br>";
    } else {
        echo "Cookie 查询报错：<br>";
        print_r(mysqli_error($con));
    }
    echo "<br><a href='?logout=1'>清除 Cookie 重新登录</a>";

    if (isset($_GET['logout'])) {
        setcookie("uname", "", time() - 3600);
        echo "<br>已清除 Cookie，<a href='index.php'>重新登录</a>";
    }
} else {
    echo '<form method="POST" style="font-family:monospace">
         Username: <input type="text" name="uname" size="30" value="admin"><br><br>
         Password: <input type="text" name="passwd" size="30" value="admin"><br><br>
         <input type="submit" name="submit" value="Login">
         </form>';
    echo "<br>登录后 Cookie 里 uname 可注入（用 Burp 改 Cookie 值）：<br>";
    echo "<code>admin' AND extractvalue(1,concat(0x7e,database(),0x7e)) --+</code>";
}
?>
