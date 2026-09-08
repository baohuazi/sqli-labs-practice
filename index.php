<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<title>SQLi-Labs 本地复刻版 · Less-1~5</title>
<style>
  body { font-family: "Microsoft YaHei", sans-serif; background:#1e1e2e; color:#ddd; margin:40px; }
  h1 { color:#7ee787; }
  .card { background:#282838; padding:16px 20px; border-radius:10px; margin:12px 0; max-width:640px; }
  a { color:#58a6ff; text-decoration:none; font-weight:bold; }
  a:hover { text-decoration:underline; }
  code { background:#0d1117; padding:2px 6px; border-radius:4px; color:#ffa657; }
  .tip { color:#ff7b72; }
</style>
</head>
<body>
<h1>SQLi-Labs 本地复刻版</h1>
<p>关卡逻辑与经典 Audi-1/sqli-labs 的 Less-1~5 完全一致，专为今天「五步手工通关」练习准备。</p>

<div class="card">
  <b>第 0 步 · 先初始化数据库（必做，只需一次）</b><br>
  <a href="sql-connections/setup-db.php">▶ 点击这里初始化 security 库 / users 表</a>
  <div class="tip">如果看到「数据库初始化完成 ✅」再开始打靶。</div>
</div>

<div class="card">
  <b>Less-1</b> · 字符型（单引号闭合）<br>
  <a href="Less-1/?id=1">Less-1 入口</a> &nbsp; 闭合：<code>'</code>
</div>
<div class="card">
  <b>Less-2</b> · 整型（无引号）<br>
  <a href="Less-2/?id=1">Less-2 入口</a> &nbsp; 闭合：<code>（直接数字）</code>
</div>
<div class="card">
  <b>Less-3</b> · 字符型 + 括号（<code>('$id')</code>）<br>
  <a href="Less-3/?id=1">Less-3 入口</a> &nbsp; 闭合：<code>')</code>
</div>
<div class="card">
  <b>Less-4</b> · 字符型 + 双引号括号（<code>("$id")</code>）<br>
  <a href="Less-4/?id=1">Less-4 入口</a> &nbsp; 闭合：<code>")</code>
</div>
<div class="card">
  <b>Less-5</b> · 报错注入（不回显数据，靠报错吐数据）<br>
  <a href="Less-5/?id=1">Less-5 入口</a> &nbsp; 闭合：<code>'</code> + extractvalue/updatexml
</div>

<p style="margin-top:24px;color:#8b949e;">
  每个关卡页面顶部都有 <span style="color:#ff7b72">DEBUG SQL</span> 显示真实拼接后的 SQL，方便你对照「闭合 → 注入」的过程。
</p>
</body>
</html>
