
Zatree for zabbix 2.4.5 安装
==================================

1：下载文件

git clone https://github.com/spide4k/zatree.git zatree

2：为了减少编辑文件带来的误操作，以后zatree只提供和zabbix整合好的包

php需要支持php-xml、php-gd、php-mysql

先备份当前zabbix web目录并挪走，然后解压zatree-zabbix-2.4.5.tar.gz，然后修改以下两个文件

3：zabbix数据库
拷贝源目录的conf/zabbix.conf.php到新目录覆盖

如果原来有添加字体也顺手拷贝过来

4：支持web interface,修改配置文件 
zatree/zabbix_config.php

'user'=>'xxx', //web登陆的用户名

'password'=>'xxx', //web登陆的密码

'http_user'=>'xxx', //httpsweb登陆的用户名

'http_password'=>'xxx', //httpsweb登陆的密码


定制开发zatree或zabbix
==================================

请发email到zhedou#163.com


交流
==================================

QQ讨论群：271659981

微信订阅号:yunweibang

运维帮,一个技术分享订阅号,扫描我,给我们力量

![image](https://raw.github.com/spide4k/zatree/master/zabbix-2.0.x/screenshots/yunweibang-weixin.jpg)


常见问题
==================================

1：如何排错？

可以打开php的显示错误，看看什么原因

vi /etc/php.ini

display_errors = On

重启web server,然后监控web日志

2：Fatal error: Call to undefined function json_encode() in /var/www/html/zabbix/zatree/ZabbixApiAbstract.class.php on line 220

需要php encode支持

yum install php-pecl-json

如果上面这个方法不行，找不到php-pecl-json，试试下面这个方法

yum install php-pear

pecl install json

echo "extension=json.so" > /etc/php.d/json.ini

3：如果右侧显示一行2个图，说明你分辨率不够，叫老板给你换个机器，或者修改graph.php文件这行的width值

    181 <img  src="<?php echo $small_graph; ?>" width="357" height="211" style="float:left;padding-top:4px;padding-left:4px;"  /> </a>

4:报以下错误

Warning: array_key_exists() expects parameter 2 to be array, null given in zatree/ZabbixApiAbstract.class.php on line 255

Notice: Trying to get property of non-object in zatree/ZabbixApiAbstract.class.php on line 262

Warning: Invalid argument supplied for foreach() in zatree/graph.php online 130

内存溢出，修改php.ini调整大小为XXX
memory_limit = XXXM

5:是否支持搜索多个关键字？

支持，关键字用逗号分隔

6:搜索选项的差值是什么意思？

在一段时间里，最大值减去最小值得到一个结果，然后用这个结果排序，这个选项对一段时间内的突发增长查看非常有用

7: 如果你的主机名都是ip，并且向排序显示，解决方法： 编辑zabbix_ajax.php 

   43行代码注释44打开，不支持ip排序，43行代码打开44行注释，支持ip排序
          43  $new_list[ip2long($each_host->host)]=$each_host;
          44  //$new_list[] = $each_host;

8: 如果zabbix是2.2.1版本，有可能会报

Call to undefined method CMacrosResolverHelper::resolveItemNames() in zabbix/include/classes/api/CLineGraphDraw_Zabbix.php on line 107
解决方法：升级zabbix > 2.2.1


小额捐款
==================================

如果你觉得zatree插件对你有帮助, 可以对作者进行小额捐款

![image](https://raw.github.com/spide4k/zatree/master/zabbix-2.0.x/screenshots/IMG_7649.JPG)![image](https://raw.github.com/spide4k/zatree/master/zabbix-2.0.x/screenshots/IMG_7650.JPG)

