<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-4：字符型 + 双引号括号闭合（("$id")）=====
// 后端 SQL：SELECT * FROM users WHERE id=("$id") LIMIT 0,1
// 闭合方式：" )  —— 用双引号而非单引号

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_select_db($con, "security");

    $sql = "SELECT * FROM users WHERE id=(\"$id\") LIMIT 0,1";
    echo "<pre style='color:#c0392b'>DEBUG SQL: $sql</pre>";

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);

    if ($row) {
        echo "Your Login name: " . $row['username'] . "<br>";
        echo "Your Password: "   . $row['password'] . "<br>";
    } else {
        echo "查询为空 / 报错：<br>";
        print_r(mysqli_error($con));
    }
} else {
    echo "请在 URL 后加 ?id=1；<br>";
    echo "本关闭合符是 \" ) ，例如 ?id=1\") UNION SELECT ... --+";
}
?>
