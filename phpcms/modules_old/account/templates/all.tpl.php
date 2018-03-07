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
	            type: 'column'
	        },
	        title: {
	            text: 'Thống kê ccu hiện tại all server'
	        },
	        subtitle: {
	            text: ''
	        },
	        xAxis: {
	            type: 'category',
	            labels: {
	                rotation: -45,
	                style: {
	                    fontSize: '13px',
	                    fontFamily: 'Verdana, sans-serif'
	                }
	            }
	        },
	        yAxis: {
	            min: 0,
	            title: {
	                text: 'Số người'
	            }
	        },
	        legend: {
	            enabled: false
	        },
	        tooltip: {
	            pointFormat: 'CCU<b>{point.y:1f}</b>'
	        },
	        series: [{
	            name: 'Population',
	            data: [
                    <?php echo $online;?>
	            ],
	            dataLabels: {
	                enabled: true,
	                rotation: -90,
	                color: '#FFFFFF',
	                align: 'right',
	                format: '{point.y:1f}Người', 
	                y: 10, // 10 pixels down from the top
	                style: {
	                    fontSize: '13px',
	                    fontFamily: 'Verdana, sans-serif'
	                }
	            }
	        }]
	    });
      
}); 
</script>
</head>
<body>
<div id="container"></div>
</body>
</html>