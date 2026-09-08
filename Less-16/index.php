<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-16：POST 型（双引号 + 括号，布尔盲注）=====
// 后端 SQL：SELECT * FROM users WHERE username=("$uname") AND password=("$passwd") LIMIT 0,1
// 不报错、不回显 → 布尔盲注，闭合用 ")

function l16_form() {
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

    $sql = "SELECT * FROM users WHERE username=(\"$uname\") AND password=(\"$passwd\") LIMIT 0,1";
    echo "<pre style='color:#c0392b'>DEBUG SQL: $sql</pre>";

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);

    if ($row) {
        echo "You are in...........";
    }
    // 不报错、不回显，强制布尔盲注
} else {
    l16_form();
    echo "<br>布尔盲注（闭合 \") ）：<br>";
    echo "uname 填 <code>admin\") AND substr(database(),1,1)='s' --+</code>";
}
?>
