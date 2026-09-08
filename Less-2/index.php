<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-2：整型注入（无引号包裹）=====
// 后端 SQL：SELECT * FROM users WHERE id=$id LIMIT 0,1
// 数字不加引号 → 直接写数字，单引号不再管用了，靠注释/逻辑注入

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_select_db($con, "security");

    $sql = "SELECT * FROM users WHERE id=$id LIMIT 0,1";
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
    echo "请在 URL 后加 ?id=1 访问；<br>";
    echo "本关 id 是数字，无需引号，直接 ?id=-1 UNION SELECT ... 即可";
}
?>
