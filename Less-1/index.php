<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-1：字符型注入（单引号闭合）=====
// 后端 SQL：SELECT * FROM users WHERE id='$id' LIMIT 0,1
// 单引号包裹 → 输入单引号即破坏语法，触发报错，可注入

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_select_db($con, "security");

    $sql = "SELECT * FROM users WHERE id='$id' LIMIT 0,1";
    echo "<pre style='color:#c0392b'>DEBUG SQL: $sql</pre>";   // 学习用：把真实 SQL 打出来

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
    echo "请在 URL 后加 ?id=1 访问，例如：?id=1<br>";
    echo "试试 ?id=1' 看报什么错（单引号破坏语法）";
}
?>
