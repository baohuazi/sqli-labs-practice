<?php
// SQLi-Labs 本地复刻版 · 数据库连接配置
// 关卡逻辑与经典 Audi-1/sqli-labs Less-1~5 完全一致
// 注意：$dbpass 必须与 phpstudy 的 MySQL root 密码一致（此处为 230380）
$dbuser = 'root';
$dbpass = '230380';
$dbname = "security";
$host   = '127.0.0.1';

// 连接时不指定库：库由 setup-db.php 先创建，各关卡自己 select_db
$con = @mysqli_connect($host, $dbuser, $dbpass);
if (!$con) {
    // 静默：库未初始化时连接会报 access/unknown，setup 后正常
    // die('Connect Error: ' . mysqli_connect_error());
}
?>
