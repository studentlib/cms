<html>
<head>
<title>活动管理</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="statics/js/calendar/calendar.js"></script>
<script type="text/javascript" src="statics/js/calendar/lang/en.js"></script>
<script type="text/javascript" src="statics/config/config.js"></script>
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
    $('#servers').change(function(){
        $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
           location.reload();
        });
     });
	 
	$('#save').click(function(){
		var post={};
		var content={}; 
		var content_lu={};
		if("<?php echo time();?>"<"<?php echo $closetime;?>" && <?php echo $_SESSION['sid'];?>!='999'){
			alert("活动未结束，禁止修改");
			return;
		}
		$("#edit :input").each(function(k,v){
			var idc=$(v).attr('id').split('_');
			if($(v).attr('class')=='item' || $(v).attr('class')=='count')
			{
				return ;
			}
			if(content[idc[1]]==undefined)
			{
				content[idc[1]]={};
			}
			content[idc[1]][idc[0]]=$(v).val();
    		}); 
		$('.item').each(function(k,v){
	    	 var idc=$(v).attr('id').split('_');
	         if(content[idc[2]]==undefined)
	           {
	               content[idc[2]]={};
	           }
	         if(content[idc[2]]['ItemReward']==undefined)
	         {
	        	   content[idc[2]]['ItemReward']=[];
	         }    
	         var idx=$(v).val();
	         var count=$('#Count_'+idc[2]+'_'+idc[1]).val();
	         var x={};
	         x[idx]=count;
           content[idc[2]]['ItemReward'].push(x);
		});
		 post.sid=$('#servers').val();
		    post.content=content;
		    post.comment=$('#comment').val();
		    $.post('index.php?m=active&c=active&a=update_PaySumDay',post,function(data){
		        alert(data.msg);console.log(data);
			location.reload();
		    },'json');
    }); 
      
	$('#save_all').click(function(){
	   var post={};
	   post.sid=$('#servers').val(); 
	   post.servers=[]; 
	   $('#sservers').children('option:selected').each(function(k,v){
	   		var server=$(v).val();
            var activeEndTime=<?php echo $activeTypeTime;?>;
		    if(activeEndTime[server]>='<?php echo time();?>'){
		        var r=confirm(server+'区本活动开启,点击确定覆盖,取消忽略');
			  	if(r==false){
			      return true;
			    }
		    }
		   if($(v).val()!=0)
		   {
			   post.servers.push($(v).val());
		   }  
	   }); 
       $.post('index.php?m=active&c=active&a=sync_PaySumDay',post,function(data){
            alert(data.msg);
        },'json');
	});
       $('#renovate').click(function(){
	   $.get('index.php?m=active&c=active&a=PaySumDay&renovate=1',function(data){
		$('body').html(data);	
	   });
	});

});
function isItemEexit(id){
	var post={};
	post.item=$('#'+id).val();
	$.post('index.php?m=active&c=active&a=is_item_exit',post,function(data){
		if(data.code==1){alert(data.msg);$('#'+id).val("").focus();}
    },'json');
}
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
<input  type="button" id="renovate" value='使用模板'>
</fieldset>
</form>
</div>

<div  align="center">
<form id="edit" action="">
<fieldset>
<legend>连续充值奖品</legend>
<?php 
foreach ($xml_arr as $k=>$v)
{
    $c=0;
?>
<div style="margin: 20px; border-style:solid; border-width:1px;border-color: gray;"> 
<label>累计天数&nbsp:&nbsp</label>
<input id="Days_<?php echo $v['Days'];?>" type="text" value="<?php echo $v['Days']?>" style="width: 30px;"/>
<?php 
    foreach($v['ItemReward'] as $k1=>$v1){
     $c++;
?>
<label>奖励道具&nbsp:&nbsp</label>
<input list="company" type="text" name="" id="ItemReward_<?php echo $c;?>_<?php echo $v['Days'];?>" style="width: 80px;" class="item" 
onblur="isItemEexit('ItemReward_<?php echo $c;?>_<?php echo $v['Days'];?>')" value='<?php echo $config[array_keys($v1)['0']]['name'];?>' >
<datalist id="company">
<?php  
 foreach ($config as $value) {
    echo '<option value="'.$value['name'].'" >'.$value['id'].'</option>';
}
?>
 </datalist>
<input id="Count_<?php echo $v['Days'];?>_<?php echo $c;?>" name="item_sum" class="count" type="text" value="<?php echo array_values($v1)['0']?>" style="width: 60px";/>
<?php }?>
<label>元宝门槛&nbsp:&nbsp</label>
<input id="PaySumDayLeastGold_<?php echo $v['Days'];?>" type="text" value="<?php echo $v['PaySumDayLeastGold']?>" style="width: 80px;"/>
</div>
<?php }?>
</fieldset>

</form>
</div>
<form action="">
<fieldset>
<legend>请选择要同步的服务器</legend>
<select name="sservers" id="sservers" multiple="multiple" size="<?php echo count($servers); ?>"  style="width: 150px">
<option value="0">请选择</option>
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].'区 '.$value['text'].'</option>';
}
?>
</select>
<input type="hidden" id="comment" value="<?php echo htmlspecialchars($comment);?>"/>
<input type="hidden" id="comment_lu" value="<?php echo htmlspecialchars($comment_lu);?>"/>
<input id="save" type="button" value="保存" style="width:50px;height:20px"/>
<input id="save_all" type="button" value="同步所选服务器" style="width:120px;height:20px"/>
</fieldset>
</form>
</body>
</html>
