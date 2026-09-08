<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-10：时间盲注（双引号）=====
// 后端 SQL：SELECT * FROM users WHERE id="$id" LIMIT 0,1

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_select_db($con, "security");

    $sql = "SELECT * FROM users WHERE id=\"$id\" LIMIT 0,1";
    echo "<pre style='color:#c0392b'>DEBUG SQL: $sql</pre>";

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);

    if ($row) {
        echo "You are in...........";
    } else {
        echo "You are in...........";
    }
} else {
    echo "本关双引号 + 时间盲注：<br>";
    echo "?id=1\" AND if(substr(database(),1,1)='s',sleep(3),0) --+";
}
?>
