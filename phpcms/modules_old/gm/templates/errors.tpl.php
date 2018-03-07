<html>
<head>
<title>
</title>
<style type="text/css">
*{
	font-size: 4px;
	font: sans-serif;
}
* {
 margin:0;
 padding:0;
}
table {
 font-family:Verdana, Arial, Helvetica, sans-serif;
 font-size:11px;
 margin:20px;
 border-collapse:collapse;
}

.jqpager ul.pages {
display:block;
border:none;
text-transform:uppercase;
margin:2px 0 15px 2px;
padding:0;
}

.jqpager ul.pages li {
list-style:none;
float:left;
border:1px solid #ccc;
text-decoration:none;
margin:0 5px 0 0;
padding:5px;
}

.jqpager ul.pages li:hover {
border:1px solid #003f7e;
}

.jqpager ul.pages li.pgEmpty {
border:1px solid #aaa;
color:#aaa;
cursor:default;
}

.jqpager ul.pages li.pgText 
{
    border:none;
    cursor:default;
}

.jqpager ul.pages li.page-number
{
    min-width:15px;
    text-align:center;
}

.jqpager ul.pages li.pgCurrent {
border:1px solid #003f7e;
color:#000;
font-weight:700;
background-color:#eee;
}
 
.jqpager input.iptGo
{
    width:30px;
    border:1px solid #ccc;
    margin:-2px 0px 0px 0px;
    height:18px;
    vertical-align:middle;
}
.jqpager span.spGo
{
    cursor:pointer;
    margin-left:2px;
}
td{
    	
}
</style> 

<script type="text/javascript" src="statics/js/jquery.min.js"></script>
<script type="text/javascript" src="statics/js/jquery.pager.js"></script>
<script type="text/javascript">
 
function Pager()
{
	
}
$(document).ready(function(){
	 $("#pager").pager({'pagenumber':0,'recordcount':10,'pagesize':10,'buttonClickCallback':Pager,
		 'firsttext': '首页',
		 'prevtext': '前一页',
		 'nexttext': '下一页',
		 'lasttext': '尾页', 
		 'recordtext': '共{0}页，{1}条记录'
		 });   
	 $('td a').click(function(data){
		var data=$(this).siblings().attr('data');
		var html=$(this).siblings().html();
		$(this).siblings().html(data);
		$(this).siblings().attr('data',html);
      });
	 $('td input').click(function(data){
		 var th=this;
		  $.getJSON("?m=gm&c=index&a=delete&id="+$(this).attr('no'),function(data){
              if(data&&data['code']=='0')
              {  
                  $(th).parent().parent().remove();
                  return;
              } 
          });
      });
	 
});
</script>
</head>
<body>
<div align="center">
<div align="center" >
<p style="font-size:20px;font-family: sans-serif;">
<!--<table id="pager" class="jqpager"></table>-->
<?php echo $pages?>
</p>
<?php if(isset($playerid)&&!empty($playerid)){?>
<input type="button" onclick="window.location='index.php?m=gm&c=index&a=errors'" value="查看所有错误"/>
<?php }?>
</div>
<table border="1" >
<tr>
<th>时间</th>
<th>版本号</th>
<th>区号</th>
<th>账号</th>
<th>角色ID</th>
<th>系统代码</th>
<th>设备ID</th>
<th>系统</th>
<th>错误内容</th>
<th>操作</th>
</tr>
<?php 
foreach ($list as $k=>$v)
{
?>
<tr>
<td nowrap="nowrap"><?php echo $v['logtime']?></td>
<td><?php echo $v['version']?></td>
<td><?php echo $v['area']?></td>
<td><?php echo $v['account']?></td>
<td><a href="index.php?m=gm&c=index&a=errors&playerid=<?php echo $v['playerid']?>"><?php echo $v['playerid']?></a></td>
<td><?php echo $v['platform']?></td>
<td><?php echo $v['device']?></td>
<td><?php echo $v['system']?></td>
<td><pre data="<?php echo $v['err']?>"><?php echo substr($v['err'],0,224)?>...</pre><a href="javascript:void(0);" >more</a>
</td>
<td><input type="button" value="删除" no="<?php echo $v['ID']?>" /></td> 
</tr>
<?php
}
?>
</table>

</div>
</body>
</html>