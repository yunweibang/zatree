<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">   
<title>zabbix实例</title>
<link href="static/ztreedemo.css" rel="stylesheet" type="text/css"/>
<link href="static/zTreeStyle.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="static/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="static/jquery.ztree.core-3.5.js"></script>


<style>
.ztree *{
	font-family:sans-serif!important;
}
</style>
</head>


<body>
<ul id="treeDemo" class="ztree" style="height:100%;"></ul>

<script type="text/javascript">
	
	var zTree;
	
	var setting = {
		view: {
			dblClickExpand: false
		},
		async: { //异步加载请求数据
			enable: true,
			url:"zabbix_ajax.php",
			autoParam:["id=groupid"], //请求的参数即groupid=nodeid
			otherParam:{"otherParam":"zTreeAsyncTest"},
			type: "get"
		},
		callback: {
		
		}
	};
	

	$(document).ready(function(){

		$.fn.zTree.init($("#treeDemo"), setting);
		//右键菜单
		zTree = $.fn.zTree.getZTreeObj("treeDemo");

	 
       /*页面刷新注释
		var iTime = setInterval(function() {
			//获取选择的节点
			var nodes=zTree.getSelectedNodes(); 
			var selectNode=nodes[0];
			
			if(selectNode){
				//obj="#"+selectNode.tId+"_a";
				//$(obj).click();
				window.parent.frames["rightFrame"].location.reload(); 
			}
			
			  }, 60000*3);
		  */

		
	});

</script>


</body>
