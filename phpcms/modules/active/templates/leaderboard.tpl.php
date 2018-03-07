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
		var content_ti={};
//		if('<?php echo time();?>'< '<?php echo $server;?>'){
//			alert("活动未结束，禁止修改");
//			return;
//		}
		$("#edit :input").each(function(k,v){
			if($(v).attr('class')=='item')
			{
				return ;
			}
			if($(v).attr('class')=='count')
			{
				return ;
			}
		   var idc=$(v).attr('id').split('_');
		   if(content[idc[1]]==undefined)
		   {
			   content[idc[1]]={};
		   }
		  if($(v).attr('type')=='checkbox'&&!$(v).attr('checked')){
			    return;
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
	         var count=$('#Count_'+idc[1]+'_'+idc[2]).val();
	         var x={};
	         x[idx]=count;
           	content[idc[2]]['ItemReward'].push(x);

		});
		$("#edit_lu :input").each(function(k,v){
                        var idc=$(v).attr('id').split('_');
                           if(content_ti[idc[1]]==undefined)
                           {
                                   content_ti[idc[1]]={};
                           }
                         content_ti[idc[1]][idc[0]]=$(v).val();
                });
	    post.sid=$('#servers').val();
	    post.content=content;
	    post.content_ti=content_ti;
	    post.comment=$('#comment').val();
	    post.comment_ti=$('#comment_ti').val();
	    $.post('index.php?m=active&c=active&a=update_leaderboard',post,function(data){
	        alert(data.msg);
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
       $.post('index.php?m=active&c=active&a=sync_leaderboard',post,function(data){
            alert(data.msg);
        },'json');
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
</fieldset>
</form>

</div>

<div  align="center">
<form id="edit_lu" action="">

<fieldset>
<legend>结算时间编辑</legend>
<br/>
<?php
foreach ($xml_arr_ti as $k=>$v)
{
    $str='';
     if($k==2){
	$v['Value']=$v['Value']/86400;
        echo '<span >';
        echo "<input id='ID_".$v['ID']."' type='hidden' value='".$v['ID']."' />";
        echo "<input id='Key_".$v['ID']."' type='hidden' value='".$v['Key']."'/>";
	echo "<span>&nbsp".$v['Des'].$str."&nbsp:&nbsp</span><select id='Value_".$v['ID']."' style='width: 60px;'>";
	foreach(range(1,7) as $i){
            $checked=$i==$v['Value']?' selected = "selected"' : '';
            echo "<option ".$checked.">".$i."</option>";
        }
        echo "</select>天";
        echo "<input id='Des_".$v['ID']."' type='hidden' value='".$v['Des']."'/>";
        echo "</span>";
        }else{
        echo '<span >';
        echo "<input id='ID_".$v['ID']."' type='hidden' value='".$v['ID']."' />";
        echo "<input id='Key_".$v['ID']."' type='hidden' value='".$v['Key']."'/>";
        echo "<span>&nbsp".$v['Des'].$str."&nbsp:&nbsp</span><input id='Value_".$v['ID']."' value='".$v['Value']."' style='width: 60px;'/>";
        echo "<input id='Des_".$v['ID']."' type='hidden' value='".$v['Des']."'/>";
        echo "</span>";
    }
}
?>
</fieldset>
</form>
</div><br/>

<div align="center">
<form id="edit" action="">
<fieldset>
<legend>活动奖励编辑</legend>
<?php 
foreach ($xml_arr as $k=>$v)
{
    $attr=$v;
    
    $items=$attr['ItemReward'];
?>
<div style="margin: 20px; border-style:solid; border-width:1px;border-color: gray;"> 
<label>排名</label>
<input type="text" id="Rank_<?php echo $attr['Rank'];?>" value="<?php echo $attr['Rank']?>" disabled="disabled" style="width:50px;"/>
<?php 
$i=0;
foreach ($items as $k=>$va) {
$i++;
?>
<label>道具奖励<?php echo $i;?></label>
<!-- <input type="text" name="" id="Items_<?php echo $i;?>_<?php echo $attr['Rank'];?>" class="item" style="width: 80px;" 
value='<?php echo $config[$va[0]]['name'];?>' onblur="isItemEexit('Items_<?php echo $i;?>_<?php echo $attr['Rank'];?>')"> -->
<input list="company" type="text" id="Items_<?php echo $i;?>_<?php echo $attr['Rank'];?>" class="item" style="width: 80px;" 
value='<?php echo $config[array_keys($va)['0']]['name'];?>' onblur="isItemEexit('Items_<?php echo $i;?>_<?php echo $attr['Rank'];?>')">
<datalist id="company">
<?php  
 foreach ($config as $value) {
    echo '<option value="'.$value['name'].'" >'.$value['id'].'</option>';
}
?>
 </datalist>
<input type="text" name="" id="Count_<?php echo $i;?>_<?php echo $attr['Rank'];?>" class="count" style="width: 50px";
 value=<?php echo array_values($va)['0'];?> >

<?php 
}
?>
</div>
<?php 
}
?>
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
<input type="hidden" id="comment_ti" value="<?php echo htmlspecialchars($comment_ti);?>"/>
<input id="save" type="button" value="保存" style="width:50px;height:20px"/>
<input id="save_all" type="button" value="同步所选服务器" style="width:120px;height:20px"/>
</fieldset>
</form>


</body>
</html>
