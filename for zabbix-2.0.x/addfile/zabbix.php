<?php

require_once dirname(__FILE__).'/include/config.inc.php';


$page['title'] = _('Configuration of proxies');
$page['file'] = 'proxies.php';
$page['hist_arg'] = array('');

require_once dirname(__FILE__).'/include/perm.inc.php';

if(isset($_COOKIE['zbx_sessionid'])){
	    $cUser=new CUser();
	    $date=$cUser->checkAuthentication($_COOKIE['zbx_sessionid']);

        if($date['alias']=='guest'){
        	header ( "Location:/zabbix/" );
        }else{
            require_once dirname(__FILE__).'/zatree/zabbix.php';
        }               
}else{
        header ( "Location:/zabbix/" );
}

