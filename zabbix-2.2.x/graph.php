<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">   
        <title>zatree图形列表</title>
        <script type="text/javascript" src="static/jquery-2.0.3.min.js"></script>
        <script language="javascript" type="text/javascript" src="My97DatePicker/WdatePicker.js"></script>
        <script type="text/javascript" src="static/jquery.fancybox.min.js"></script>
        <?php
         $changeType=(isset($_REQUEST["changeType"]) && $_REQUEST["changeType"] ) ? $_REQUEST["changeType"] : '1days'; 
        ?>
        <script type="text/javascript">
            $(document).ready(function() {
                $(".fancybox").fancybox({
                    'width' : '984px',
                    'height' : '500px',
                  //  'autoScale' : true,  //如果为true，fancybox可以自适应浏览器窗口大小
                    'transitionIn' : 'none', //是否显示动画效果
                    'transitionOut' : 'none',
                    'type' : 'iframe',
                    'scrolling':'no',
                   // 'autoDimensions':true,//在内联文本和ajax中，设置是否动态调整元素的尺寸，如果为true，请确保你已经为元素设置了尺寸大小
                    'showNavArrows':'', //如果为true，则显示上一张下一张导航箭头
                    'overflow':'hidden'
                });
                var list=Array();
                list['1hour']='1小时';
                list['2hour']='2小时';
                list['1days']='1天';
                list['2days']='2天';
                list['7days']='7天';
                list['30days']='30天';
                list['1years']='1年';
                list['2years']='2年';
                $("#quickdiv a").each(function(obj){
                   if(list['<?php echo $changeType;?>'] == $(this).text() ){
                        $(this).attr("style","text-decoration:none;color:red;");
                        $(this).removeAttr("onclick");
                        return false;
                   }
                });  
            });

            function changeOrderType() {
                var order_key = $("select[name='order_key']").val();
                if (order_key === '' || typeof(order_key) === 'undefined') {
                    $("select[name='order_type']").val('');
                } else {
                    $("select[name='order_type']").val('desc');
                }
            }


            function changeTime(changetype) {
                var url = "zabbix_ajax.php";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        'changeType': changetype
                    },
                    success: function(result) {
                       var tmp = eval("(" + result + ")");
                       $("#stime").val(tmp.beginTime);
                       $("#endtime").val(tmp.endTime);
                       $("#changeType").val(changetype);
                       
                       var host=$("#hostid").val();
                       var group=$("#group_class").val();
                       if(host >0 || group >0 ){
                            document.getElementById("searchForm").submit();
                       }	
                    }
                });
            }




        </script>
        <link href="static/page.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="static/jquery.fancybox.min.css" type="text/css" media="screen" />
    </head>
    <body style="text-align:center;" >

        <?php
        date_default_timezone_set('PRC');
        include_once("page.class.php");
        include_once("zabbix_config.php");
        include_once("ZabbixApi.class.php");
        include_once dirname(dirname(__FILE__)) . '/include/items.inc.php';

        $hostid = (isset($_REQUEST["hostid"]) && $_REQUEST["hostid"] > 0) ? $_REQUEST["hostid"] : ''; //主机id
        $group_class = (isset($_REQUEST["group_class"]) && $_REQUEST["group_class"] != '') ? $_REQUEST["group_class"] : ''; //分组
        $page = (isset($_REQUEST["page"]) && $_REQUEST["page"] != '') ? $_REQUEST["page"] : 1;
        $url = 'graph.php?' . ($hostid > 0 ? 'hostid=' . $hostid : 'group_class=' . $group_class);
        $curtime = time(); //当前时间

        /* 条件不从cookie里面获取 */
        if (!isset($_GET["itemkey"]) && !isset($_GET["stime"]) && !isset($_GET["endtime"]) &&
                !isset($_GET["order_key"]) && !isset($_GET["order_type"])) {
            if (isset($_COOKIE['stime'])) {
                $stime = $_COOKIE['stime'];
                $endtime = $_COOKIE['endtime'];
                $itemkey = isset($_COOKIE['itemkey']) ? $_COOKIE['itemkey'] : '';
                $orderkey = isset($_COOKIE['order_key']) ? $_COOKIE['order_key'] : '';
                $ordertype = isset($_COOKIE['order_type']) ? $_COOKIE['order_type'] : '';
            } else {
                $itemkey = isset($_GET["itemkey"]) ? $_GET["itemkey"] : '';
                $stime = (isset($_GET["stime"]) && $_GET["stime"] != '' ) ? $_GET["stime"] : date("Y-m-d H:i:s", $curtime - 3600 * 24);
                $endtime = (isset($_GET["endtime"]) && $_GET["endtime"] != '' ) ? $_GET["endtime"] : date("Y-m-d H:i:s", $curtime);
                $orderkey = (isset($_GET["order_key"]) && $_GET["order_key"] != '') ? $_GET["order_key"] : '';
                $ordertype = (isset($_GET["order_type"]) && $_GET["order_type"] != '') ? $_GET["order_type"] : '';
                //然后把数据存入到cookie里面
                SetCookie("itemkey", $itemkey, $curtime + 3600);
                SetCookie("stime", $stime, $curtime + 3600);
                SetCookie("endtime", $endtime, $curtime + 3600);
                SetCookie("order_key", $orderkey, $curtime + 3600);
                SetCookie("order_type", $ordertype, $curtime + 3600);
            }
        } else {
            $itemkey = isset($_GET["itemkey"]) ? $_GET["itemkey"] : '';
            $stime = (isset($_GET["stime"]) && $_GET["stime"] != '' ) ? $_GET["stime"] : date("Y-m-d H:i:s", $curtime - 3600 * 24);
            $endtime = (isset($_GET["endtime"]) && $_GET["endtime"] != '' ) ? $_GET["endtime"] : date("Y-m-d H:i:s", $curtime);
            $orderkey = (isset($_GET["order_key"]) && $_GET["order_key"] != '') ? $_GET["order_key"] : '';
            $ordertype = (isset($_GET["order_type"]) && $_GET["order_type"] != '') ? $_GET["order_type"] : '';
            //然后把数据存入到cookie里面
            SetCookie("itemkey", $itemkey, $curtime + 3600);
            SetCookie("stime", $stime, $curtime + 3600);
            SetCookie("endtime", $endtime, $curtime + 3600);
            SetCookie("order_key", $orderkey, $curtime + 3600);
            SetCookie("order_type", $ordertype, $curtime + 3600);
        }


        $period = strtotime($endtime) - strtotime($stime);
        $fortime = date("YmdHis", strtotime($stime));
        $width = 286; //图形宽度
        global $zabbix_api_config;

        if (!empty($zabbix_api_config['http_user']) && !empty($zabbix_api_config['http_password'])) {
                $url_http = dirname(dirname('http://' . trim($zabbix_api_config['http_user']) . ':' . trim($zabbix_api_config['http_password']) . '@' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]));
        } else {
                $url_http = dirname(dirname('http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]));
        }
        $zabbixApi = new ZabbixApi($url_http . '/' . trim($zabbix_api_config['api_url']), trim($zabbix_api_config['user']), trim($zabbix_api_config['passowrd']));
        
         //域名下传主机名
        if(isset($_REQUEST["host"]) && $_REQUEST["host"] ){
            $host_info_by=$zabbixApi->hostGet(array( "output" => "extend","filter"=>array('host'=>$_REQUEST["host"])));
            if(isset($host_info_by[0])){
                $hostid=$host_info_by[0]->hostid;
            }
        }
        
        
        ?>
        <form method="get" style="font-size:8px;text-align:left;padding-left:10px;" id="searchForm" >
            <div>
                <input type="hidden" name="hostid" value="<?php echo $hostid; ?>" id="hostid" />
                <input type="hidden" name="group_class" value="<?php echo $group_class; ?>" id="group_class" />
                <input type="hidden" name="changeType" value="<?php echo $changeType; ?>" id="changeType" />
                开始时间：<input type="text"   value="<?php echo $stime; ?>"  width="100px;" class="Wdate" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd HH:mm:ss'})"  id="stime"  name="stime"/> &nbsp;
                结束时间：<input type="text" id="endtime"  value="<?php echo $endtime; ?>" width="100px;" class="Wdate" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd HH:mm:ss'})"  name="endtime"/>&nbsp;
                关键字:<input type="text" style="width:130px;" id="itemkey" name="itemkey" value="<?php echo $itemkey; ?>" />&nbsp;
                排序：<select id="order_key" name="order_key" onchange="changeOrderType();" >
                    <option value=''>默认</option>
                    <option value="lastvalue" <?php
                    if ($orderkey == 'lastvalue') {
                        echo 'selected="selected"';
                    };
                    ?>>最新</option>
                    <option value="max" <?php
                    if ($orderkey == 'max') {
                        echo 'selected="selected"';
                    };
                    ?>>最大</option>
                    <option value="min" <?php
                            if ($orderkey == 'min') {
                                echo 'selected="selected"';
                            };
                            ?>>最小</option>
                    <option value="avg" <?php
                    if ($orderkey == 'avg') {
                        echo 'selected="selected"';
                    };
                            ?>>平均</option>
                    <option value="chazhi" <?php
                    if ($orderkey == 'chazhi') {
                        echo 'selected="selected"';
                    };
                            ?>>差值</option>
                </select>&nbsp;
                <select id="order_type" name="order_type">
                    <option value=''>默认</option>
                    <option value="asc" <?php
                            if ($ordertype == 'asc') {
                                echo 'selected="selected"';
                            };
                            ?>>升序</option>
                    <option value="desc" <?php
        if ($ordertype == 'desc') {
            echo 'selected="selected"';
        };
        ?>>降序</option>
                </select>
                &nbsp;<input type="button" value="搜索" onclick="onCheckSubmit();"/>
                &nbsp;<input type="button" value="清除" onclick="clearCookie();"/>
                <input type ="button" onclick="javascript:window.parent.location.href = '<?php echo $url_http; ?>'" value="回到首頁" />
            </div>
            <div style=" margin: 10px 0px 0px 5px;font-size: 14px; " id="quickdiv"><a href="javascript:void(0);" onclick="changeTime('1hour');" >1小时</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="changeTime('2hour');">2小时</a>&nbsp;&nbsp;<a href="javascript:void(0);"  onclick="changeTime('1days');">1天</a>&nbsp;&nbsp;<a href="javascript:void(0);"  onclick="changeTime('2days');">2天</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="changeTime('7days');">7天</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="changeTime('30days');">30天</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="changeTime('1years');">1年</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="changeTime('2years');">2年</a></div>
        </form>

        <p></p>
        <?php
        $order_list_result = $order_list_result_page = array(); //记录结果信息数组
        if ((isset($_REQUEST["hostid"]) && $_REQUEST["hostid"] > 0) || ($group_class != '' && $group_class > 0) || $hostid ) {
            //根据主机id查询当前主机下的图形
            if ($group_class == '') {
                $graphs = $zabbixApi->graphGet(array("hostids" => array($hostid), "output" => "extend", "sortfield" => "name"));

                foreach ($graphs as &$each) {
                    $graphids[] = $each->graphid;
                }
                $items_list = $zabbixApi->graphitemGet(array("graphids" => $graphids, "output" => "extend"));
            } else {
                //查询分组里面的所有机器
                $host_ids = array();
                $hosts = $zabbixApi->hostGet(array("output" => "extend", "monitored_hosts" => true, "groupids" => array($group_class)));
                foreach ($hosts as $each_host) {
                    $host_ids[] = $each_host->hostid;
                }
                //查询分组下的所有机器的所有图形
                $graphs = $zabbixApi->graphGet(array("hostids" => $host_ids, "output" => "extend", "sortfield" => "name"));
                foreach ($graphs as &$each) {
                    $graphids[] = $each->graphid;
                }
                $items_list = $zabbixApi->graphitemGet(array("graphids" => $graphids, "output" => "extend"));
            }

            $list = array('list_item' => $items_list, 'parame' => array('stime' => strtotime($stime), 'period' => $period, 'sizeX' => $width, 'item_name_search' => $itemkey));
            $format_list = $zabbixApi->getItemListFormat($list, '');
            $format_list = (array) $format_list;
            foreach ($format_list as &$format) {
                $format = (array) $format;
                if (is_array($format)) {
                    foreach ($format as $key_obj => &$value_obj) {
                        // print_r($value_obj);
                        if (is_object($value_obj)) {
                            $value_obj = (array) $value_obj;
                        }
                    }
                }
            }
            $order_list_result = (array) $format_list;
            //对结果进行排序
            // print_r($order_list_result);
            if ($orderkey != '' && $ordertype != '') {

                if (empty($itemkey)) {
                    $sort_key = $orderkey == '' ? 'hostname' : $orderkey;
                    $arr = array_map(create_function('$sort', 'return $sort["' . $sort_key . '"];'), $order_list_result);
                    if ($ordertype == "asc") {
                        array_multisort($arr, SORT_ASC, $order_list_result);
                    } else {
                        array_multisort($arr, SORT_DESC, $order_list_result);
                    }
                } else {
                    $search_key_list = explode(",", $itemkey);
                    $sort_new_list = array();
                    foreach ($search_key_list as $each_search_key) {
                        $each_search_key = trim($each_search_key);
                        $order_list_result_search = isset($order_list_result[$each_search_key]) ? $order_list_result[$each_search_key] : array();
                        $sort_key = $orderkey == '' ? 'hostname' : $orderkey;
                        $arr[$each_search_key] = array_map(create_function('$sort', 'return $sort["' . $sort_key . '"];'), $order_list_result_search);

                        if ($ordertype == "asc") {
                            array_multisort($arr[$each_search_key], SORT_ASC, $order_list_result_search);
                        } else {
                            array_multisort($arr[$each_search_key], SORT_DESC, $order_list_result_search);
                        }
                        if (count($order_list_result_search)) {
                            $sort_new_list[$each_search_key] = $order_list_result_search;
                        }
                    }
                    $order_list_result = $sort_new_list;
                }
            }
            
            //print_r($order_list_result);
            
            //获取当前页的数据
            if (count($order_list_result) > 0) {

                if (empty($itemkey)) {
                    $page = new page($order_list_result, array('total' => count($order_list_result), 'url' => $url, 'nowindex' => $page, 'searchkey' => $itemkey));
                } else {
                    $search_list = array();
                    $search_key_list = explode(",", $itemkey);
                    foreach ($search_key_list as $eachkey => $each_search) {
                        $each_search = trim($each_search);
                        if (isset($order_list_result[$each_search])) {
                            $list = $order_list_result[$each_search];
                            $search_list = array_merge($search_list, $list);
                        }
                    }
                    $page = new page(array_values($search_list), array('total' => count($search_list), 'url' => $url, 'nowindex' => $page, 'searchkey' => $itemkey));
                }



                $page_link = $page->show(1);
                $order_list_result_page = $page->_get_result();
                // print_r($order_list_result_page);
            }
            if (isset($page_link)) {
                ?>

                <div class="page" ><?php echo $page_link; ?></div>
                <?php
            }

            // print_r($order_list_result_page);
            //循环输出                                                                       
            foreach ($order_list_result_page as $result) {
                ?>

                    <?php
                    $small_graph = "../zabbix_chart.php?graphid=" . $result['graphid'] . "&width=" . $width . "&height=70&stime=" . $fortime . "&period=" . $period . "&box=box.jpg";
                    $big_graph_change = "big_graph.php?graphid=" . $result['graphid'] . "&stime=" . $stime ;
                    ?>
                <div style="width:357px;float: left; text-align: left;margin: 5px 3px 0px 0px;">
                <?php if ($orderkey == 'chazhi') { ?>
                        <span style=" padding-left: 6px;font-size: 12px; ">差值:<?php echo $result['chazhi']; ?></span>
                <?php } ?>
                    <a class="fancybox"  href="<?php echo $big_graph_change; ?>" >
                        <img  src="<?php echo $small_graph; ?>" width="357" height="211" style="float:left;padding-top:4px;padding-left:4px;"  /> </a>
                </div>  

                <?php
            }
        }

        if (isset($page_link)) {
            ?>
            <p></p>
            <div class="page" style="clear:both;padding-top:20px;"><?php echo $page_link; ?></div>
    <?php
}
?>

        <script type="text/javascript">
            function clearCookie() {
                var myDate = new Date();
                var endtime = myDate.Format("yyyy-MM-dd hh:mm:ss");
                myDate.setDate(myDate.getDate() - 1);
                var stime = myDate.Format("yyyy-MM-dd") + " " + myDate.Format("hh:mm:ss");
                $("#stime").attr("value", stime);
                $("#endtime").attr("value", endtime);
                $("#itemkey").attr("value", '');
                $("#order_key").attr("value", '');
                $("#order_type").attr("value", '');
                var url = "zabbix_ajax.php";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        'clearstatus': 1
                    },
                    success: function(result) {
                        window.parent.frames["rightFrame"].location.reload();
                    }
                });
            }

            function onCheckSubmit() {
                var orderkey = $("select[name='order_key']").val();
                var ordertype = $("select[name='order_type']").val();
                var itemkey = $("#itemkey").val();

                if ((orderkey == '' && ordertype != '') || (orderkey != '' && ordertype == '')) {
                    alert('排序对象、排序方式要同时选择或同时默认');
                    return false;
                }

                $("#searchForm").attr("action", 'graph.php');
                $("#searchForm").submit();
            }

            $(document).ready(function() {
                /**
                 ** 日期格式化函数
                 */
                Date.prototype.Format = function(fmt) {
                    var o = {
                        "M+": this.getMonth() + 1, //月份
                        "d+": this.getDate(), //日
                        "h+": this.getHours(), //小时
                        "m+": this.getMinutes(), //分
                        "s+": this.getSeconds(), //秒
                        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
                        "S": this.getMilliseconds()             //毫秒
                    };
                    if (/(y+)/.test(fmt))
                        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
                    for (var k in o)
                        if (new RegExp("(" + k + ")").test(fmt))
                            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
                    return fmt;
                }
            })
        </script>

        <div align="center" style='font-size:12px;'>
            <a href="https://github.com/spide4k/zatree" target="_blank">Zatree</a> version 1.0.1 for zabbix 2.2.x, 技术支持：
            <a href="http://weibo.com/spider4k" target="_blank">@南非蜘蛛</a>
            <a href="http://weibo.com/chinahanna" target="_blank">@hanna</a>
            <a href="http://weibo.com/678236656" target="_blank">@lijian</a> ，
            <a href="http://me.alipay.com/spider4k">点击这里</a>可以对作者进行小额捐款
        </div>
        </br></br></br></br></br>
    </body>
