<html>
<head>
<title>在线人数</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="statics/js/highchart/highcharts.js"></script>
<script type="text/javascript" src="statics/js/highchart/highcharts-3d.js"></script>
<script type="text/javascript" src="statics/js/highchart/modules/exporting.js"></script>
<script type="text/javascript" src="statics/js/calendar/calendar.js"></script>
<script type="text/javascript" src="statics/js/calendar/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/jscal2.css"/>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/border-radius.css"/>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/win2k.css"/>
<style type="text/css">
body{font-family:tahoma Verdana;font-size:20px;}
*{
    font-size: 20px;
    font: sans-serif;
}
* {
 margin:0;
 padding:0;
}
table {

 font-size:20px;
 margin:0px;
 border-collapse:collapse;
}
th{width:120px;height:20px;}
td{width:120px;height:15px;}
</style>

<script type="text/javascript">
$(document).ready(function(){
    Calendar.setup({
        weekNumbers: '0',
        inputField : 'st',
        trigger    : 'st',
        dateFormat: '%Y%m%d%H%M',
        showTime:  'true',
        minuteStep: 1,
        onSelect   : function() {this.hide();}
        });
    Calendar.setup({
        weekNumbers: '0',
        inputField : 'ed',
        trigger    : 'ed',
        dateFormat: '%Y%m%d%H%M',
        showTime:  'true',
        minuteStep: 1,
        onSelect   : function() {this.hide();}
        });
    $('#query').click(function(){
        $.post('?m=account&c=account&a=updateSessionTime',{'st':$('#st').val(),'ed':$('#ed').val()},function(){
            location.reload();
         },'json');
    });
//     $('#search').click(function(){
//     	//alert($('#st').val());
//         $.ajax({ 
//                 type:"POST",
//                 url: "?m=account&c=account&a=tab", 
//                 context:{'st':$('#st').val()}, 
//                 success: function(data){
//                     //alert(data);
//             	$('#district').val(data);
            	
//         		}
//     		});
//     });
    
}); 
</script>
</head>
<body>
<div align="center">
<form action="">
<h1>在线查询</h1>
<label>按时间查询:</label>
<input type="text" name="st" id="st" value="<?php echo $st;?>" style="width: 140px;"/>
<input type="text" name="ed" id="ed" value="<?php echo $ed;?>" style="width: 140px;"/>
<input type="button" value="查询" id="query" />
<input type="button" value="全区人数" id="search" />
<input type="text" name="district" id="district" value="<?php echo $number;?>" style="width: 140px;"/>
</form>
</div>
<div id="container" align="center"">

<table border="1" align="top">
<tr>
<th>采集时间</th>
<th>服务器</th>
<th>最多人数</th>
<th>平均人数</th>

</tr>
<?php 
foreach($data as $k=>$v){
    //var_dump($v);echo "<br/>";
    if(empty($v)){continue;}
?>
<table border="1" align="top">
<tr>
<td><?php echo $range?></td>
    
        <?php 
//var_dump($v);echo "<br/>";
        $unit=array();
        if(isset($unit)){$unit=array();}
             foreach($v as $k1=>$v1){
                 $unit[]=$v1['OnlinePlayers'];
             }
        ?>
   <td><?php echo $v1['ServerType'].'区-'.$v1['server'];?> </td>
   <td><?php echo max($unit);?> </td>
    <td>
        <?php 
         if(!$num=0){$num=0;};
         foreach($unit as $k2=>$v2){
             $n=$v2;
             $num=$num+$n;
         }
          echo ceil($num/count($unit));//进一取整
        ?>
    </td>
</tr>
</table>
<?php }?>
</table>

</div>
</body>
</html>