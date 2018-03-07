<html>
<head>
<title>生成礼包</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<style type="text/css">
body{font-family:tahoma Verdana;font-size:12px;}
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
</style>

<script type="text/javascript">
$(document).ready(function(){
	 $('td a').click(function(data){
	        var post={};
	        post.batch=$(this).parent().parent().find('td:nth-child(1)').html();
	        post.channel=$(this).parent().parent().find('td:nth-child(2)').html();
	        $.getJSON('?m=gift&c=gift&a=delCode',post,function(data){
	            if(data.code=='0')
	            {
		            location.reload();
	            }
			});
	});
});

</script>
</head>
<body>
<div align="center">
<table border="1" >
<tr>
<th>Lượt</th>
<th>Kênh</th>
<th>Tên kênh</th>
<th>Nội dung quà</th>
<th>Số lần sử dụng tối đa</th>
<th>Thao tác</th>
</tr>
<?php 
foreach ($items as $k=>$v)
{
?>
<tr>
<td><?php echo $v['no']?></td>
<td><?php echo $v['channel']?></td>
<td><?php echo $v['cname']?></td>
<td><?php echo $v['item']?></td>
<td><?php echo $v['total_used']?></td>
<td><a href="javascript:void(0);">Xóa</a></td>
</tr>
<?php
}
?>
</table>
</div>
</body>
</html>