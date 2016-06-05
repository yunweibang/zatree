
Zatree for zabbix 3.0.x 安装
==================================
##【目录】
----

[0 zatree3.0.x 快速部署方法](#0)  
[1 zatree3.0.x 实现原理](#1)  
........[1.1 zabbix 菜单上显示zatree ](#1.1)  
........[1.2 点击dash上zatree跳转到zatree界面 ](#1.2)  
[2 zatree3.0.x 常见问题及处理方法](#0)  
[3 版本发布 ](#1)  

##【正文】
----

<h2 name="0">zatree3.0.x 快速部署方法</h2>

部署方法很简单

```
git clone https://github.com/BillWang139967/zatree.git
cd cd zatree/zabbix-3.0.x/
sh start.sh
```
执行过程中需要输入zabbix admin的账号和密码

![image](https://raw.github.com/BillWang139967/zatree/master/zabbix-3.0.x/images/install.jpg)

<h2 name="1">zatree3.0.x 实现原理</h2>

假如zabbix web目录位置在/data/web/zabbix,定义zabbix目录

ZABBIX_PATH=/data/web/zabbix

<h3 name="1.1">1.1 zabbix 菜单上显示zatree</h3>

导航增加Zatree入口,修改menu.inc.php，main.js 

$ZABBIX_PATH/include/menu.inc.php

在302行插入如下内容

```
'zatree'=> [
    	'label' => _('Zatree'),
   		'user_type' => USER_TYPE_ZABBIX_USER,
     	'default_page_id' => 0,
     	//'force_disable_all_nodes' => true,
     	'pages' => [
     	        [
		'url' => 'zabbix_tree.php',
		'label' => _('zatree')
	],
     	]

], 

```
替换$ZABBIX_PATH/js/main.js 104行内容
```
	menus:			{'view': 0, 'cm': 0, 'reports': 0, 'config': 0, 'admin': 0,'zatree':0},
```
<h3 name="1.2">1.2 点击dash上zatree跳转到zatree界面</h3>

在上一步骤zatree指向的文件为$ZABBIX_PATH/zabbix_tree.php 文件

故将zabbix_tree.php文件放到$ZABBIX_PATH目录

<h2 name="2">2 常见问题及处理方法</h2>

1：如何排错？

可以打开php的显示错误，看看什么原因

vi /etc/php.ini
```
display_errors = On
```
重启web server,然后监控web日志

2：如果右侧显示一行2个图，说明你分辨率不够，叫老板给你换个机器，或者修改graph.php文件这行的width值

```
 181 <img  src="<?php echo $small_graph; ?>" width="357" height="211" style="float:left;padding-top:4px;padding-left:4px;"  /> </a>
```
3:报以下错误

Warning: array_key_exists() expects parameter 2 to be array, null given in zatree/ZabbixApiAbstract.class.php on line 255

Notice: Trying to get property of non-object in zatree/ZabbixApiAbstract.class.php on line 262

Warning: Invalid argument supplied for foreach() in zatree/graph.php online 130

内存溢出，修改php.ini调整大小为XXX
```
memory_limit = XXXM
```
4:是否支持搜索多个关键字？

支持，关键字用逗号分隔

5:搜索选项的差值是什么意思？

在一段时间里，最大值减去最小值得到一个结果，然后用这个结果排序，这个选项对一段时间内的突发增长查看非常有用

6: 如果你的主机名都是ip，并且向排序显示，解决方法： 编辑zabbix_ajax.php 

   43行代码注释44打开，不支持ip排序，43行代码打开44行注释，支持ip排序
```
          43  $new_list[ip2long($each_host->host)]=$each_host;
          44  //$new_list[] = $each_host;
```
<h2 name="3">3 版本发布</h2>
----
* v3.0.1，2016-06-05，新增。发布初始版本。

