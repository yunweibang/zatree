<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">   
        <title>zatree图形列表</title>
        <script type="text/javascript" src="static/jquery-2.0.3.min.js"></script>
        <script language="javascript" type="text/javascript" src="My97DatePicker/WdatePicker.js"></script>
        <style>
         html, body, div, ul {
	margin: 0;
	padding: 0;
        }   
        </style>
         <?php
         $changeType=(isset($_REQUEST["changeType"]) && $_REQUEST["changeType"] ) ? $_REQUEST["changeType"] : '1days'; 
        ?>
        <script type="text/javascript">
            $(document).ready(function() {
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
                     
                       document.getElementById("searchForm").submit();
                      	
                    }
                });
            }
        </script>
        <link href="static/page.css" rel="stylesheet" type="text/css"/>
    </head>
    <body style="text-align:center;" >
         <?php
        date_default_timezone_set('PRC');
        include_once("page.class.php");
        include_once("zabbix_config.php");
        include_once("ZabbixApi.class.php");
        include_once dirname(dirname(__FILE__)) . '/include/items.inc.php';
        
        $curtime = time(); //当前时间
        $stime = (isset($_GET["stime"]) && $_GET["stime"] != '' ) ? $_GET["stime"] : date("Y-m-d H:i:s", $curtime - 3600 * 24);
        $endtime = (isset($_GET["endtime"]) && $_GET["endtime"] != '' ) ? $_GET["endtime"] : date("Y-m-d H:i:s", $curtime);
        
        $period = strtotime($endtime) - strtotime($stime);
        $fortime = date("YmdHis", strtotime($stime));
        $width = 286; //图形宽度
        global $zabbix_api_config;
        $graphid=$_GET["graphid"] ;

        $url_http = dirname(dirname('http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]));

        $zabbixApi = new ZabbixApi($url_http . '/' . trim($zabbix_api_config['api_url']), trim($zabbix_api_config['user']), trim($zabbix_api_config['passowrd']));
        
         $zoom_x = 3; //点击小图看大图，放大三倍
                    $zoom_width = $width * $zoom_x;
                    $zoom_height = 70 * $zoom_x;
                    $big_graph = "../zabbix_chart.php?graphid=" .$graphid . "&width=" . $zoom_width . "&height=" . $zoom_height . "&stime=" . $fortime . "&period=" . $period;
        ?>
        <div style=" height: 380px;" >
       
             <form method="get" id="searchForm" >
                <input type="hidden" name="graphid" value="<?php echo $graphid; ?>" id="graphid" />
                <input type="hidden"   value="<?php echo $stime; ?>"  width="100px;"   id="stime"  name="stime"/> 
                <input type="hidden" id="endtime"  value="<?php echo $endtime; ?>" width="100px;"  name="endtime"/>
                <input type="hidden" name="changeType" value="<?php echo $changeType; ?>" id="changeType" />
   
                <div style=" margin: 0px 0px 2px 0px;font-size: 14px;" id="quickdiv" ><a href="javascript:void(0);" onclick="changeTime('1hour');">1小时</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="changeTime('2hour');">2小时</a>&nbsp;&nbsp;<a href="javascript:void(0);"  onclick="changeTime('1days');">1天</a>&nbsp;&nbsp;<a href="javascript:void(0);"  onclick="changeTime('2days');">2天</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="changeTime('7days');">7天</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="changeTime('30days');">30天</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="changeTime('1years');">1年</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="changeTime('2years');">2年</a></div>
                <div style=" height: 350px;">
                        <img  src="<?php echo $big_graph; ?>"  height="350px"  /> 
                </div>  
              </form>

   

        </div>     
    </body>
