<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-8：布尔盲注（单引号，不报错，只两态）=====
// 后端 SQL：SELECT * FROM users WHERE id='$id' LIMIT 0,1
// 页面：有数据→"You are in..."；无数据→什么都不显示
// 不报错 → 不能报错注入，只能布尔盲注（逐字符猜）

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_select_db($con, "security");

    $sql = "SELECT * FROM users WHERE id='$id' LIMIT 0,1";
    echo "<pre style='color:#c0392b'>DEBUG SQL: $sql</pre>";

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);

    if ($row) {
        echo "You are in...........";
    }
    // 注意：不输出 mysqli_error，强制只能布尔盲注
} else {
    echo "本关不报错，只能布尔盲注：<br>";
    echo "?id=1' AND substr(database(),1,1)='s' --+  （页面有/无\"You are in\"区分真假）<br>";
    echo "?id=1' AND length(database())>5 --+";
}
?>
