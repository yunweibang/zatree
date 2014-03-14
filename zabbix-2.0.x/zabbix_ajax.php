<?php

require_once("ZabbixApi.class.php");
require_once("zabbix_config.php");

//clear cookie
if (isset($_POST['clearstatus'])) {

    SetCookie("itemkey", '', time() - 3600);
    SetCookie("stime", '', time() - 3600);
    SetCookie("endtime", '', time() - 3600);
    SetCookie("order_key", '', time() - 3600);
    SetCookie("order_type", '', time() - 3600);

    return true;
} else {

    //获取传过来的参数 
    if (isset($_POST['changeType'])) {
        $changedayList = array('1hour' => 3600, '2hour' => 7200, '1days' => 3600 * 24, '2days' => 3600 * 48, '7days' => 3600 * 24 * 7, '30days' => 3600 * 24 * 30, '1years' => 3600 * 24 * 365, '2years' => 3600 * 24 * 365 * 2);
        $changeType = $_POST['changeType'];
        $endTime = time();
        $beginTime = $endTime - $changedayList[$changeType];

        echo "{endTime:" . json_encode(date("Y-m-d H:i:s", $endTime)) . ",beginTime:" . json_encode(date("Y-m-d H:i:s", $beginTime)) . "}";
        exit;
    }



    global $zabbix_api_config;

    $url_http = dirname(dirname('http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]));

    $zabbixApi = new ZabbixApi($url_http . '/' . trim($zabbix_api_config['api_url']), trim($zabbix_api_config['user']), trim($zabbix_api_config['passowrd']));
    $groupid = isset($_GET["groupid"]) ? $_GET["groupid"] : 0;

    if ($groupid > 0) {
        //根据分组id查询分组下的机器
        $hosts = $zabbixApi->hostGet(array("output" => "extend", "monitored_hosts" => true, "groupids" => array($groupid), "sortfield" => array("host"), "sortorder" => array("ASC")));
        $new_list = array();
        foreach ($hosts as $each_host) {
            //$new_list[ip2long($each_host->host)]=$each_host;
            $new_list[] = $each_host;
        }
        ksort($new_list);
        foreach ($new_list as &$each_host_new) {
            $each_host_new->target = 'rightFrame';
            $each_host_new->groupids = $groupid;
            $each_host_new->url = 'graph.php?hostid=' . $each_host_new->hostid;
        }
        echo json_encode(array_values($new_list));
    } else {
        //查询所有的分组列表
        $groups = $zabbixApi->hostgroupGet(array("output" => "extend", "monitored_hosts" => true));
        foreach ($groups as &$each) {
            $each->id = $each->groupid;
            $each->isParent = true;
            $each->target = 'rightFrame';
            $each->url = 'graph.php?group_class=' . $each->groupid;

            //查询下面有多少机器
            $hosts = $zabbixApi->hostGet(array("output" => "extend", "monitored_hosts" => true, "groupids" => array($each->groupid)));
            $each->name = $each->name . '(' . count($hosts) . ')';
        }
        echo json_encode($groups);
    }
}
?>