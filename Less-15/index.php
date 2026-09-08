<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-15：POST 型（单引号，布尔盲注）=====
// 后端 SQL：SELECT * FROM users WHERE username='$uname' AND password='$passwd' LIMIT 0,1
// 不报错、不回显数据 → 只能布尔盲注（逐字符猜）

function l15_form() {
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

    // 只给两态，不报错、不回显数据
    if ($row) {
        echo "You are in...........";
    }
    // 否则什么都不显示
} else {
    l15_form();
    echo "<br>布尔盲注（单引号，无报错）：<br>";
    echo "uname 填 <code>admin' AND substr(database(),1,1)='s' --+</code>（页面有/无\"You are in\"判真假）<br>";
    echo "uname 填 <code>admin' AND length(database())>5 --+</code>";
}
?>
