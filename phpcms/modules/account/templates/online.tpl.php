<html>
<head>
<title>活动管理</title>
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
body{font-family:tahoma Verdana;font-size:14px;}
*{
    font-size: 12px;
    font: sans-serif;
}
* {
 margin:0;
 padding:0;
}
table {
 font-family:Verdana, Arial, Helvetica, sans-serif;
 font-size:14px;
 margin:20px;
 border-collapse:collapse;
}
</style>

<script type="text/javascript">
$(document).ready(function(){
	 $('#container').highcharts({
	        chart: {
	            type: 'areaspline'
	        },
	        title: {
	            text: "<?php echo $range;?>"+'在线人数统计'
	        },
	        legend: {
	            layout: 'vertical',
	            align: 'left',
	            verticalAlign: 'top',
	            x: 150,
	            y: 100, 
	            floating: true,
	            borderWidth: 1,
	            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
	        }, 
	        xAxis: {
	            categories: [
		         <?php echo $date;?> 	            
	            ],
	            plotBands: [{ // visualize the weekend
	                from: 4.5,
	                to: 6.5,
	                color: 'rgba(68, 170, 213, .2)'
	            }]
	        },
	        yAxis: { 
	            title: {
	                text: '人数'
	            }
	        },
	        tooltip: {
	            shared: true,
	            valueSuffix: ' 人'
	        },
	        credits: {
	            enabled: false
	        },
	        plotOptions: {
	            areaspline: {
	                fillOpacity: 0.5
	            }
	        },
	        series: [{
	            name: "<?php echo $sid;?>"+'区',
	            data: [ <?php echo $online;?>]
	        }]
	    });

	    $('#servers').change(function(){
	        $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
	           location.reload();
	        });
	     });
//	    $('#servers').change();
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
}); 
</script>
</head>
<body>
<div align="center">
<form action="">
<fieldset>
<legend>请选择服务器</legend>
<select name="servers" id="servers" style="width: 150px">
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].'区 '.$value['text'].'</option>';
}
?>
</select>
</fieldset>
<label>起始时间:</label>
<input type="text" name="st" id="st" value="<?php echo $st;?>" style="width: 120px;"/>
<label>结束时间:</label>
<input type="text" name="ed" id="ed" value="<?php echo $ed;?>" style="width: 120px;"/>
<input type="button" value="查询" id="query" />
</form>
</div>
<div id="container"></div>

</body>
</html>