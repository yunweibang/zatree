
Zatree for zabbix 2.0.x 安装
==================================

1：下载文件

git clone https://github.com/spide4k/zatree.git zatree

2：复制相关文件

假如zabbix web目录位置在/var/www/zabbix,定义zabbix目录

ZABBIX_PATH=/var/www/zabbix

复制相关文件和目录

cp -rf zatree/zabbix-2.0.x $ZABBIX_PATH/zatree

cd $ZABBIX_PATH/zatree/addfile

cp class.cchart_zabbix.php class.cgraphdraw_zabbix.php class.cimagetexttable_zabbix.php $ZABBIX_PATH/include/classes/

cp zabbix.php zabbix_chart.php $ZABBIX_PATH/

cp CItemValue.php $ZABBIX_PATH/api/classes/

3：支持web interface,修改配置文件

vi $ZABBIX_PATH/zatree/zabbix_config.php

'user'=>'xxx', //web登陆的用户名

'passowrd'=>'xxx', //web登陆的密码

4：导航增加Zatree入口,修改menu.inc.php，main.js

vi $ZABBIX_PATH/include/menu.inc.php

添加285行到294行内容

    285         'zatree'=>array(
    286                 'label' => _('Zatree'),
    287                 'user_type'                             => USER_TYPE_ZABBIX_USER,
    288                 'default_page_id'       => 0,
    289                 'force_disable_all_nodes' => true,
    290                 'pages' =>array(
    291                         array('url' => 'zabbix.php','label' => _('Zatree'),)
    292                         )
    293         
    294         ),      
    295         
    296         'login' => array(                               
    297                 'label'                                 => _('Login'),
    298                 'user_type'                             => 0,
    299                 'default_page_id'               => 0,

vi $ZABBIX_PATH/js/main.js

替换106行

menus:                  {'empty': 0, 'view': 0, 'cm': 0, 'reports': 0, 'config': 0, 'admin': 0, 'zatree':0},

6：增加封装的api类


vi $ZABBIX_PATH/include/classes/api/API.php

在74行下添加75行'itemvalue'=>'CItemValue',

     74                 'usermedia' => 'CUserMedia',
     75                 'itemvalue'=>'CItemValue',
     76                 'webcheck' => 'CWebCheck'
     77         ); 

7：登陆zabbix，在导航里可以看到一个Zatree的菜单，使用方法是傻瓜的


8: 如果你的主机名都是ip，并且向排序显示，解决方法： 编辑zabbix_ajax.php 
   43行代码注释44打开，不支持ip排序，43行代码打开44行注释，支持ip排序
          43  $new_list[ip2long($each_host->host)]=$each_host;
          44  //$new_list[] = $each_host;


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

4：如果想在小图里显示时间段，编辑文件include/classes/class.cchart_zabbix.php，打开2363行

     2363                 //      $this->drawDate();

5:报以下错误

Warning: array_key_exists() expects parameter 2 to be array, null given in zatree/ZabbixApiAbstract.class.php on line 255

Notice: Trying to get property of non-object in zatree/ZabbixApiAbstract.class.php on line 262

Warning: Invalid argument supplied for foreach() in zatree/graph.php online 130

内存溢出，修改php.ini调整大小为XXX
memory_limit = XXXM

6:是否支持搜索多个关键字？
支持，关键字用逗号分隔

7:搜索选项的差值是什么意思？
在一段时间里，最大值减去最小值得到一个结果，然后用这个结果排序，这个选项对一段时间内的突发增长查看非常有用

8: 解决分组只显示一个ip的问题，因为我们单位的主机名都写的是ip，你们的主机名是字符串，所以就显示不出来了
解决方法：
编辑zabbix_ajax.php，注释以下几行，并且保证画圈的变量名一致
![image](https://raw.github.com/spide4k/zatree/master/zabbix-2.2.x/screenshots/3.jpg)


技术支持
==================================

http://weibo.com/spider4k

http://weibo.com/chinahanna

http://weibo.com/678236656


小额捐款
==================================

如果你觉得zatree插件对你有帮助, <a href="http://me.alipay.com/spider4k">点击这里</a>可以对作者进行小额捐款

祝玩的愉快
