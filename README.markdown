# 简介
ThinkHaml是[HAML](http://haml-lang.com)的一个PHP实现，HAML来自于Ruby社区。目前支持作为ThinkPHP的模板引擎。HAML解析部分主要代码来自[MtHaml](https://github.com/arnaud-lb/MtHaml/)。
## HAML的优点
* 减少模板文件的代码量，平均可以减少一半
* 强制缩进，源文件结构清晰，极大方便团队协作与后期维护
## HAML的主要特点
* 全部是单标签，不像HTML那样需要大量使用成对的标签来表示嵌套
* 使用缩进来表示嵌套而不是使用标签来嵌套
* 与HTML的知识基本差不多，上手成本不高，所以不用害怕。
# 系统要求
ThinkPHP版本 >= 3.2.1
# 语法介绍
作为HAML的PHP实现，ThinkHaml的大多数语法都与标准的HAML语言类似。但由于PHP与Ruby的语言差异，ThinkHaml与标准的HAML存在局部少量的差异。

## 基本语法：

### doctype
``` haml
!!! 5
```
会被编译成：
``` html
<!DOCTYPE html>
```
### 标签

HAML标签以`%`号打头，后面紧跟HTML标签名：

``` haml
%div
%p
```
会被编译成：

``` html
<div></div>
<p></p>
```
无需关心自闭合类型标签

``` haml
%img
%meta
%br
```
会被编译成：

``` html
<img>
<meta>
<br>
```
### 标签属性

#### 普通属性

为标签设置属性的语法与HTML没有任何差别
``` haml
%a(href="/account/login" target="_blank" id="J_LoginBtn" class="btn btn-primary")
%p(data-article-id="5" class="article-body")
```
会被编译成：
``` html
<a href="/account/login" target="_blank" id="J_LoginBtn"  class="btn btn-primary"></a>
<p data-article-id="5" class="article-body"></p>
```
由于id和class属性使用非常频繁，因此上面的代码可以这样写：
``` haml
%a#J_LoginBtn.btn.btn-primary(href="/account/login" target="_blank")
%p.article-body(data-article-id="5")
```
使用`#`作为ID识别符、`.`作为class识别符是沿用css选择器的语法。

由于div标签的使用非常频繁，因此可以直接省略

``` haml
.panel
#J_Panel
.panel.panel-info#J_Panel2
```
等价于：
``` haml
%div.panel
%div#J_Panel
%div.panel.panel-info#J_Panel2
```
会被编译成：
``` html
<div class="panel"></div>
<div id="J_Panel"></div>
<div class="panel panel-info" id="J_Panel2"></div>
```

### 标签嵌套
由于不像HTML那样有成对的标签，同时由于HAML来自Ruby社区，因此HAML的标签嵌套采用缩进的方式，并且是强制的：

``` haml
.panel
  .panel-heading
  .panel-body
```
会被编译成：
``` html
<div class="panel">
  <div class="panel-heading"></div>
  <div class="panel-body"></div>
</div>
```
缩进即嵌套，无缩进则不嵌套：
``` haml
.panel
.panel-heading
.panel-body
```
会被编译成：
``` html
<div class="panel"></div>
<div class="panel-heading"></div>
<div class="panel-body"></div>
```
****
注意不同缩进生成的不同HTML代码。
****
### 标签内容
HAML标签的内容与标签在同一行，使用空格区隔
``` haml
%h1.subject 我是文章标题
%p 我是文章内容
```
会被编译成：
``` html
<h1 class="subject">我是文章标题</h1>
<p>我是文章内容</p>
```
如果标签中有普通文本，并且需要嵌套其他标签时：
``` haml
%h1.subject
  我是文章标题
  %span.author 作者：xxx
%p 我是文章内容
```
会被编译成：
``` html
<h1 class="subject">
  我是文章标题
  <span class="author">
    作者：xxx
  </span>
</h1>
```
****
注意：当一个标签内既包含普通文本和HAML标签时，标签内的内容必须另起一行，以下的HAML代码是不合法的
****
``` haml
%h1.subject 我是文章标题
  %span.author 作者：xxx
%p 我是文章内容
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
HAML代码：
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
# ThinkPHP配置（注意：ThinkPHP的版本需要 >= 3.2.1）
在 Application目录下建立Lib/gallery目录，创建完后看起来大概是这个样子
``` shell
 ├── Application                … 应用代码目录
    ├── Common                  … 公共目录（作用于所有模块）
        ├── Common              …
        └── Conf                … 公共配置目录
            ├── config.php      … 通用配置(ThinkHaml的配置写入到这里)
    └── Lib                     … 项目类库目录
        └── gallery             … 第三方类库
    ...(其他目录)
```
将以下配置写入`/Application/Common/Conf/config.php` (如果不需要全局启用，请写入到相应的模块配置文件中)
``` php
  'TMPL_TEMPLATE_SUFFIX'  =>  '.haml',     // 默认模板文件后缀
  
  'AUTOLOAD_NAMESPACE' => array(
      'gallery'     => APP_PATH.'Lib/gallery'
  ),
  'TMPL_ENGINE_TYPE'=>'gallery\ThinkHaml\ThinkPHPBundler'
```
然后模板文件改用.haml后缀即可。控制器中`display`方法与使用ThinkPHP内置模板引擎时无任何差别。
目前仅支持include语法，extend和layout均不支持。include的用法如下：
``` haml
%include(file="Common:header")
```
等价于：
``` html
<include file="Common:header" />
```

# RoadMap（将来支持）
* 在ThinkPHP中同时支持HTML和HAML作为模板文件（.html后缀文件作为html渲染，.haml后缀文件作为haml渲染）
* 与THinkPHP一致的extend、layout
* 清晰明朗的HTML转义机制
* 支持scss解析
* 简单、一体化的前端集成工具
  * css文件依赖管理
  * css与js的combo、minify、内联管理

# 参与开发
ThinkHaml目前作为alpha版本，在API稳定之前仅开放内部团队参与开发，暂不接受pull requests，如你有好的建议或BUG报告，欢迎通过[issues](https://github.com/mr5/ThinkHaml/issues)反馈。
