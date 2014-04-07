# 简介
ThinkHaml是[HAML](http://haml-lang.com)的一个PHP实现，目前支持作为ThinkPHP的模板引擎。HAML解析部分主要代码来自[MtHaml](https://github.com/arnaud-lb/MtHaml/)
# 系统要求
ThinkPHP版本 >= 3.2.1
# 语法介绍
作为HAML的PHP实现，ThinkHaml的大多数语法都与标准的HAML语言类似。但由于PHP与Ruby的语言差异，ThinkHaml与标准的HAML存在局部少量的差异。
## 基本语法：
### doctype

``` haml
%ul#users
  - foreach($users as $user)
    %li.user
      = $user->getName()
      邮箱: #{$user->getEmail()}
      %a(href=$user->getUrl()) 首页
```

生成：

``` php
<ul id="users">
  <?php foreach($users as $user) { ?>
    <li class="user">
      <?php echo $user->getName(); ?>
      邮箱: <?php echo $user->getEmail(); ?>
      <a href="<?php echo $user->getUrl(); ?>">首页</a>
    </li>
  <?php } ?>
</ul>
```
### 注释
#### HTML注释
``` haml
#footer
  / 这里是尾部
  %ul
    %li 关于我们
    %li 联系我们
    %li 加入我们
```
会被编译成
``` html
<div id="footer">
    <!-- 这里是尾部 -->
    <ul>
        <li>关于我们</li>
        <li>联系我们</li>
        <li>加入我们</li>
    </ul>
</div>
```
反斜杠「/」还可以注释掉整块缩进的内容
``` haml
/
  %p 这些内容不会被渲染
  %div
    %h1 因为我们被注释了！
```
会被编译成：
``` html
<!--
    <p>这些内容不会被渲染</p>
    <div>
        <h1>因为我们被注释了！</h1>
    </div>
-->
```
#### 条件注释
你也可以使用[IE的条件注释](http://www.quirksmode.org/css/condcom.html)，方括号[]中的内容与普通html代码无区别。
``` haml
/[if IE 6]
  %link(rel="stylesheet" href="/static/css/ie6-fix.css")
```
会被编译成：
``` html
<!--[if IE 6]>
    <link rel="stylesheet" href="/static/css/ie6-fix.css">
<![endif]-->
```
### HAML注释
``` haml
%p foo
-# 这里是单行注释
%p bar
```
会被编译成：
``` html
<p>foo</p>
<p>bar</p>
```
HAML注释同样支持多行，遵守HAML的缩进嵌套规则：
``` haml
%p foo
-#
  这里不会响应到浏览器
    这也不会...
                   这也不会...
%p bar
```
会被编译成：
``` html
<p>foo</p>
<p>bar</p>
```
## 嵌入PHP代码
### 插入PHP变量和表达式
``` haml
%p
  =$doc['subject'].', 作者：'.$doc['author']
  =$doc['body']
  %a(href=U('article/read'.$doc['id'])) 阅读全部
```
会被编译成：
``` php
<p>
  <?php echo $doc['subject'].', 作者：'.$doc['author']; echo $doc['body'] ?>
  <a href="<?php echo U('article/read'.$doc['id']) ?>">阅读全部</a>
</p>
```
### 循环和迭代
``` haml
.users
  - foreach($users AS $user)
    %li=$user['name']
```
会被编译成：
``` php
<div class="users">
  <?php foreach($users AS $user) { ?>
    <li><?php echo $user['name']; ?></li>
  <?php } ?>
</div>
```
### 流程控制语句
``` haml
#login-info
  - if($isLogin)
    %p 欢迎回来，#{$user['username']}
    %a(href=U('account/logout')) 退出
  - else
    %a(href=U('account/login')) 登录
    %a(href=U('account/register')) 注册
```
会被编译成：
``` php
<div id="login-info">
  <?php if($isLogin) { ?>
    <p>欢迎回来，<?php echo $user['username']; ?></p>
    <a href="<?php echo U('account/logout'); ?>">退出</a>
  <?php } else { ?>
    <a href="<?php echo U('account/login'); ?>">登录</a>
    <a href="<?php echo U('account/logout'); ?>">注册</a>
  <?php } ?>
</div>
```
# RoadMap
* 在ThinkPHP中同时支持HTML和HAML作为模板文件
* 支持scss解析
* 简单的、一体化的前端集成工具
  * css文件依赖管理
  * css与js的combo、minify、内联管理

# 参与开发
ThinkHaml目前作为alpha版本，在API稳定之前仅开放内部团队参与开发，暂不接受pull requests，如你有好的建议或BUG报告，欢迎通过[issues](https://github.com/mr5/ThinkHaml/issues)反馈。
