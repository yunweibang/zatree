#########################################################################
# File Name: start.sh
# Author: Bill
# mail: XXXXXXX@qq.com
# Created Time: 2016-06-05 10:44:20
#########################################################################
#!/bin/bash
WEB_DIR=`find / -name zabbix | grep web`
if [[ -z ${WEB_DIR} ]]
then
    echo "not find the zabbix web dir"
    exit 1
fi
cp -rf ${WEB_DIR} ${WEB_DIR}_bak
tar -zxf zatree.tar.gz -C ${WEB_DIR}
# 导航增加Zatree入口
cp ${WEB_DIR}/zatree/addfile/menu.inc.php ${WEB_DIR}/include/menu.inc.php
cp ${WEB_DIR}/zatree/addfile/main.js ${WEB_DIR}/js/main.js

cp ${WEB_DIR}/zatree/addfile/zabbix_tree.php ${WEB_DIR}/

cp ${WEB_DIR}/zatree/addfile/API.php ${WEB_DIR}/include/classes/api/
cp ${WEB_DIR}/zatree/addfile/CApiServiceFactory.php ${WEB_DIR}/include/classes/api/
cp ${WEB_DIR}/zatree/addfile/CItemValue.php ${WEB_DIR}/include/classes/api/services/

read -p  "please input zabbix admin_name(default:admin) ?:" g_ZABBIX_USER
g_ZABBIX_USER=${g_ZABBIX_USER:-admin}
read -p  "please input zabbix admin_password(default:zabbix) ?:" g_ZABBIX_PASS
g_ZABBIX_PASS=${g_ZABBIX_PASS:-zabbix}
sed -i "s/ZABBIX_USER/${g_ZABBIX_USER}/g" ${WEB_DIR}/zatree/zabbix_config.php
sed -i "s/ZABBIX_PASS/${g_ZABBIX_PASS}/g" ${WEB_DIR}/zatree/zabbix_config.php
