<html>
<head>
<title>功能管理-系统邮件</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="statics/config/config.js"></script>
<style type="text/css">
body{font-family:tahoma Verdana;font-size:14px;}
*{
    font-size: 12px;
    font: sans-serif;
}
* {
 margin:0;
 padding:1px;
}
table {
 font-family:Verdana, Arial, Helvetica, sans-serif;
 font-size:14px;
 border-collapse:collapse;
}
td{
	text-align:center;
}
</style>
</head>
<body >
<div style="width:300px;margin-top: 20px; margin-bottom: 0px; margin-left: 20px; margin-right: 0px;float:left;">
<table border="2px" bordercolor="green" align="center">
<tr><td>道具ID</td><td>道具名</td><td>道具类型</td></tr>
<?php
foreach ($items as $k=>$v){
    echo "<tr id='item".$k."'>";
    foreach ($v as $x=>$y){
            echo "<td>".$y."</td>";
    }
    echo "</tr>";
}
?>
</table>

</div>
<div style="margin-left:100px;margin-top:20px;float:left;">
<form method="post" enctype="multipart/form-data" action="/index.php?m=operation&c=operation&a=file_upload" id="uploadload" target="iframelogo">
        <input type="file" class="uploadxml" name="upload" /><br/>
        <input type="file" class="uploadxml2" name="upload2" /><br/>
        <input type="submit" class="put" value="上传" />
</form>
</div>
</body>
</html>
