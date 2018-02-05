<html>
<head>
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
 padding:2px;
}
table {
// font-family:Verdana, Arial, Helvetica, sans-serif;
 font-size:10px;
 margin:20px;
 border-collapse:collapse;
}
.ltv_content{margin:0px auto;text-align:center}

</style>
</style>
<script type="text/javascript">


$(document).ready(function(){
	 Calendar.setup({
        weekNumbers: '0',
        inputField : 'st',
        trigger    : 'st',
        dateFormat: '%Y-%m-%d',
        showTime:  'true',
        minuteStep: 1,
        onSelect   : function() {this.hide();}
        });
    Calendar.setup({
        weekNumbers: '0',
        inputField : 'ed',
        trigger    : 'ed',
        dateFormat: '%Y-%m-%d',
        showTime:  'true',
        minuteStep: 1,
        onSelect   : function() {this.hide();}
        });


    $.post('?m=order&c=order&a=loadChannels',{},function(data){
        config=data;
        for(var x in config)
        {
            var ch=config[x];
            $('#ch').append('<option value="'+x+'">'+ch['name']+'</option>');
        }
     },'json');

     $('#query').click(function(){
     	var post={};
     	post.st=$('#st').val();
     	post.ed=$('#ed').val();
     	post.ch=$('#ch').val();
     	post.sid=$('#sid').val();	
     	$.post('?m=account&c=account&a=findbase',post,function(data){
     		if(data.code==0)
            {
                alert(data.msg);
            }
		$('#basetime').attr('value', data.basetime);
     		$('#header ~ tr').remove();//console.log(data.content);
     		for (var k in data.content ) {
     			var tr='<tr>';
     			var ltvTr=data.content[k];
     			for(v in ltvTr){
     				if(ltvTr[v]==null){ltvTr[v]='';}
     					tr+='<th>'+ltvTr[v]+'</th>';
     			}
     			tr+='</tr>';
     			$('#ltv').append(tr);
     		}
     	},'json');

     });
     $('#ch').change(function(){
       	var post={};
       	post.st=$('#st').val();
       	post.ed=$('#ed').val();
       	post.ch=$('#ch').val();
       	post.sid=$('#sid').val();	
       	$.post('?m=account&c=account&a=findbase',post,function(data){
       	      if(data.code==0)
              {
                  alert(data.msg);
              }
		$('#basetime').attr('value', data.basetime);
       		$('#header ~ tr').remove();
       		for (var k in data.content ) {
       			var tr='<tr>';
       			var ltvTr=data.content[k];
       			for(v in ltvTr){
       				if(ltvTr[v]==null){ltvTr[v]='';}
       					tr+='<th>'+ltvTr[v]+'</th>';
       			}
       			tr+='</tr>';
       			$('#ltv').append(tr);
       		}
       	},'json');

       });
     $('#sid').change(function(){
     	var post={};
     	post.st=$('#st').val();
     	post.ed=$('#ed').val();
     	post.ch=$('#ch').val();
     	post.sid=$('#sid').val();	
     	$.post('?m=account&c=account&a=findbase',post,function(data){
     		if(data.code==0)
            	{
                	alert(data.msg);
            	}
		$('#basetime').attr('value', data.basetime);
     		$('#header ~ tr').remove();
     		for (var k in data.content ) {
     			var tr='<tr>';
     			var ltvTr=data.content[k];
     			for(v in ltvTr){
     				if(ltvTr[v]==null){ltvTr[v]='';}
     					tr+='<th>'+ltvTr[v]+'</th>';
     			}
     			tr+='</tr>';
     			$('#ltv').append(tr);
     		}
     	},'json');

     });
     $('#export').click(function(){
	var url='?m=account&c=account&a=exportLtv&st='+$('#st').val()+'&ed='+$('#ed').val()+'&file=base'+'&ch='+$('#ch').val()+'&sid='+$('#sid').val();
        window.open(url);
     });
});

</script>
</head>
<body>

<fieldset align="center">
<legend>base数据</legend>
<label>base更新时间:</label>
<input type="text" disabled id="basetime" value="<?php echo $basetime;?>" style="width: 123px;"/>
<label>按时间查询:</label>
<input type="text" name="st" id="st" value="<?php echo $st;?>" style="width: 140px;"/>
<input type="text" name="ed" id="ed" value="<?php echo $ed;?>" style="width: 140px;"/>
<label>渠道:</label>

<select id="ch">
<option value="">所有渠道</option>
<?php
  if($_SESSION['roleid']==1){
    echo "<option value='ALL'>渠道和</option>";
  }
?>
</select>
<label>区服:</label>
<select id="sid">
<option value="">所有</option>
<option value="all">总共</option>
<?php
foreach($server as $k=>$v){
    echo '<option value="'.$v['ServerType'].'">'.$v['ServerType'].'服-'.$v['text'].'</option>';
}
?>
</select>
<input type="button" value="查询" id="query" />
<div align="center">
	<table border="2" id="ltv" bordercolor="green" >
	<tr id="header" style="font-size: 16px;">
	<?php
		foreach ($servers as $key => $value) {
			echo '<th >'.$value.'</th>';
		}
	?>	
	</tr>
	<?php
	foreach ($result as $key => $value) {

	echo '<tr>';
		foreach ($value as $k => $v) {
			echo '<th id='.$k.'>'.$v.'</th>';
		}
	echo '</tr>';
	}
	?>
	</table>
</div>
</fieldset>
<!--<select name="sservers" id="sservers" multiple="multiple" size="<?php echo count($servers); ?>"  style="width: 150px">
<option value="0">请选择</option>
<?php 
foreach ($servers as $k=>$v) {
    echo "<option value=\"".$k.'" >'.$v.'</option>';
}
?>
</select>-->
<input type="button" id="export" class="export" value="导出">
</body>
</html>

