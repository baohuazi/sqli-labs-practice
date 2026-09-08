<?php
// 初始化 SQLi-Labs 数据库：创建 security 库 + users 表 + 填充测试数据
// 访问方式：浏览器打开 http://127.0.0.1/sqli-labs/sql-connections/setup-db.php
$dbuser = 'root';
$dbpass = '230380';
$host   = '127.0.0.1';

$con = mysqli_connect($host, $dbuser, $dbpass);
if (!$con) {
    die("数据库连接失败：" . mysqli_connect_error());
}

// 删除旧库再重建（显式指定 utf8_general_ci 与 information_schema 对齐，避免 UNION 报 Illegal mix of collations）
@mysqli_query($con, "DROP DATABASE IF EXISTS security");
if (!@mysqli_query($con, "CREATE DATABASE security CHARACTER SET utf8 COLLATE utf8_general_ci")) {
    die("创建数据库失败：" . mysqli_error($con));
}
mysqli_select_db($con, "security");
mysqli_query($con, "SET NAMES utf8");

// 创建 users 表（经典字段）
$sql_create = "CREATE TABLE users (
    id INT(3) NOT NULL AUTO_INCREMENT,
    username VARCHAR(20) NOT NULL,
    password VARCHAR(20) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
if (!@mysqli_query($con, $sql_create)) {
    die("创建表失败：" . mysqli_error($con));
}

// 头部注入用表：uagents（User-Agent / Referer 记录）
$uagents_create = "CREATE TABLE IF NOT EXISTS uagents (
    id INT(3) NOT NULL AUTO_INCREMENT,
    uagent VARCHAR(512) NOT NULL,
    ip_address VARCHAR(64) NOT NULL,
    username VARCHAR(20) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
@mysqli_query($con, $uagents_create);

// 头部注入用表：referers（Referer 记录）
$referers_create = "CREATE TABLE IF NOT EXISTS referers (
    id INT(3) NOT NULL AUTO_INCREMENT,
    referer VARCHAR(512) NOT NULL,
    ip_address VARCHAR(64) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
@mysqli_query($con, $referers_create);

// 经典测试数据（与官方靶场一致）
$users = [
    [1, 'Dumb',      'Dumb'],
    [2, 'Angelina',  'I-kill-you'],
    [3, 'Dummy',     'p@ssword'],
    [4, 'secure',    'crappy'],
    [5, 'stupid',    'stupidity'],
    [6, 'superman',  'genious'],
    [7, 'batman',    'mob!le'],
    [8, 'admin',     'admin'],
    [9, 'admin1',    'admin1'],
    [10,'admin2',    'admin2'],
    [11,'admin3',    'admin3'],
    [12,'dhakkan',   'dumbo'],
    [13,'admin4',    'admin4'],
];

foreach ($users as $u) {
    $id = $u[0];
    $name = mysqli_real_escape_string($con, $u[1]);
    $pass = mysqli_real_escape_string($con, $u[2]);
    mysqli_query($con, "INSERT INTO users (id, username, password) VALUES ($id, '$name', '$pass')");
}

echo "<h2>SQLi-Labs 数据库初始化完成 ✅</h2>";
echo "<p>已创建数据库 <b>security</b> 与表 <b>users</b>（共 " . count($users) . " 条测试数据）。</p>";
echo "<p>现在可以回到 <a href='../../index.php'>靶场首页</a> 开始 Less-1~5。</p>";
mysqli_close($con);
?>
