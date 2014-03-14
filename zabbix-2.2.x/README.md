
Zatree for zabbix 2.2.x 安装
==================================

1：下载文件

git clone https://github.com/spide4k/zatree.git zatree

2：复制相关文件

假如zabbix web目录位置在/var/www/zabbix,定义zabbix目录

ZABBIX_PATH=/var/www/zabbix

复制相关文件和目录

cp -rf zatree/zabbix-2.2.x $ZABBIX_PATH/zatree

cd $ZABBIX_PATH/zatree/addfile

cp -f CLineGraphDraw_Zabbix.php CGraphDraw_Zabbix.php CImageTextTable_Zabbix.php $ZABBIX_PATH/include/classes/graphdraw/
cp -f zabbix.php zabbix_chart.php $ZABBIX_PATH/
cp -f CItemValue.php $ZABBIX_PATH/api/classes/
cp -f menu.inc.php $ZABBIX_PATH/include/
cp -f main.js $ZABBIX_PATH/js/
cp -f API.php $ZABBIX_PATH/include/classes/api/

3：支持web interface,修改配置文件

vi $ZABBIX_PATH/zatree/zabbix_config.php

'user'=>'xxx', //web登陆的用户名

'passowrd'=>'xxx', //web登陆的密码



交流
==================================

QQ讨论群：216490997

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

技术支持
==================================

http://weibo.com/spider4k

http://weibo.com/chinahanna

http://weibo.com/678236656


小额捐款
==================================

如果你觉得zatree插件对你有帮助, <a href="http://me.alipay.com/spider4k">点击这里</a>可以对作者进行小额捐款

祝玩的愉快
