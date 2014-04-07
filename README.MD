# 简介
ThinkHaml是[HAML](http://haml-lang.com)的一个PHP实现，目前支持作为ThinkPHP的模板引擎。HAML解析部分主要代码来自[MtHaml](https://github.com/arnaud-lb/MtHaml/)
# 系统要求
ThinkPHP版本 >= 3.2.1
# ThinkHaml语法介绍
作为HAML的PHP实现，ThinkHaml的大多数语法都与标准的HAML语言类似。但由于PHP与Ruby的差异，ThinkHaml与标准的HAML存在局部少量的差异。
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
会被编译成
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
会被编译成
``` html
<!-- [if IE 6] -->
    <link rel="stylesheet" href="/static/css/ie6-fix.css">
<![endif] -->
```
# RoadMap
* 同时支持HTML和HAML
* 支持scss解析
* 简单的、一体化的前端集成工具
** 支持

# 参与开发
ThinkHaml目前作为alpha版本，在API稳定之前仅开放内部团队参与开发，暂不接受pull requests，如你有好的建议或BUG报告，欢迎通过[issues](https://github.com/mr5/ThinkHaml/issues)反馈。