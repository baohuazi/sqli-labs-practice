<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-9：时间盲注（单引号，无论对错页面都一样）=====
// 后端 SQL：SELECT * FROM users WHERE id='$id' LIMIT 0,1
// 关键：页面"永远"显示 You are in，无法用布尔区分 → 只能靠 sleep 时间差

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_select_db($con, "security");

    $sql = "SELECT * FROM users WHERE id='$id' LIMIT 0,1";
    echo "<pre style='color:#c0392b'>DEBUG SQL: $sql</pre>";

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);

    if ($row) {
        echo "You are in...........";
    } else {
        echo "You are in...........";   // 故意真假都显示一样，逼你用时间盲注
    }
} else {
    echo "本关真假页面一样，只能时间盲注：<br>";
    echo "?id=1' AND if(substr(database(),1,1)='s',sleep(3),0) --+  （真→延迟3秒）<br>";
    echo "?id=1' AND if(length(database())>5,sleep(3),0) --+";
}
?>
