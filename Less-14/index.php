<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-14：POST 型（双引号，报错注入）=====
// 后端 SQL：SELECT * FROM users WHERE username="$uname" AND password="$passwd" LIMIT 0,1
// 双引号包裹、不回显数据 → 报错注入

function l14_form() {
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

    $sql = "SELECT * FROM users WHERE username=\"$uname\" AND password=\"$passwd\" LIMIT 0,1";
    echo "<pre style='color:#c0392b'>DEBUG SQL: $sql</pre>";

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);

    if ($row) {
        echo "You are in...........";
    } else {
        print_r(mysqli_error($con));
    }
} else {
    l14_form();
    echo "<br>闭合用 \" ，报错带数据：<br>";
    echo "uname 填 <code>\" AND extractvalue(1,concat(0x7e,database(),0x7e)) --+</code>";
}
?>
