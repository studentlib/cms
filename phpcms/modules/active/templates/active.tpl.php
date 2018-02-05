<html>
<head>
<title>活动管理</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
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
.hidden{
	display: none;
}
</style>

<script type="text/javascript">
$(document).ready(function(){
    $('#servers').change(function(){
        $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
           location.reload();
        });
     });

    <?php 
    	foreach ($xml->row as $k=>$v)
    	{
    	    $attr=$v->attributes();
?>
        Calendar.setup({
            weekNumbers: '0',
            inputField : 'OpenTime_'+<?php echo $attr;?>,
            trigger    : 'OpenTime_'+<?php echo $attr;?>,
            dateFormat: '%Y%m%d',
            showTime:  'true',
            minuteStep: 1,
            onSelect   : function() {this.hide();}
            });
        Calendar.setup({
            weekNumbers: '0',
            inputField : 'CloseTime_'+<?php echo $attr;?>,
            trigger    : 'CloseTime_'+<?php echo $attr;?>,
            dateFormat: '%Y%m%d',
            showTime:  'true',
            minuteStep: 1,
            onSelect   : function() {this.hide();}
            });

	<?php  
	}
	?>
	$('.Time').keydown(function(e){
		e.stopPropagation();
		e.preventDefault();
		return false;
	});
	$('#save').click(function(){
		var post={};
		var content={}; 
		$("#edit :input").each(function(k,v){
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
	    post.sid=$('#servers').val();
	    post.content=content;
	    post.comment=$('#comment').val();
	    $.post('index.php?m=active&c=active&a=update',post,function(data){
	        alert(data.msg);
	    },'json');
    }); 

	$('#save_all').click(function(){
	   var post={};
	   var sers1='';//同步成功的区服
	   var sers0='';//同步失败的区服
	   post.sid=$('#servers').val();
	   post.servers=[]; 
	   $('#sservers').children('option:selected').each(function(k,v){
            var server=$(v).val();//要同步到的区服
            var activeEndTime=<?php echo $activeEndTime;?>;
	          var activeEndTime2=activeEndTime[post.sid];//模板的活动结束时间
             for(var key in activeEndTime){  
                if(key==server){
                 var activeCount=activeEndTime[server];
                 for(var k in activeCount){
		                 if(activeCount[k]>=<?php echo time();?>&&activeCount[k]!=activeEndTime2[k])
                     {  
                         var r=confirm(server+'区有活动开启,点击确定覆盖,取消忽略');
                         if(r==false){
                             return true;
                         }else{
                    			   var r=confirm(server+'区确定覆盖？');
                    			   if(r==false){
                              return true;
                             }
                  			 }
                  			break;
                     }
                 }
                 break;
                }
             }
		          if($(v).val()!=0)
              {
                post.servers.push($(v).val());
              } 
	   });
           $.post('index.php?m=active&c=active&a=syncAll',post,function(data){
                   alert(data.msg);
		               location.reload();
           },'json');
/*
           var activeEndTimeChild1=activeEndTime[server];//被同步区服活动时间
	   var activeEndTimeChild2=activeEndTime[post.sid];//模板活动时间
           for(var i in activeEndTimeChild1){
               if(activeEndTimeChild1[i]!=activeEndTimeChild2[i] && activeEndTimeChild1[i]>'<?php echo time();?>' && activeEndTimeChild2[i]>'<?php echo time();?>' ){
      			sers0+=server+',';
				var alert=0;
				return true;
               } 
		   }
		   if(alert==0)return true;
		   if($(v).val()!=0)
		   {
			   post.servers.push($(v).val());
		   } 
		   sers1+=server+','; 
	   }); 
	   
       if(post.servers.length != 0){
           $.post('index.php?m=active&c=active&a=syncAll',post,function(data){
              if(data.code=='0'||sers0==''){
                   alert(data.msg);
              }else if(sers0!=''||data.code=='0'){
                   alert(sers1+'区'+data.msg+sers0+'区活动冲突，同步失败');
              }else{
                   alert(sers0+'区活动冲突，同步失败');
              }
           },'json');
       }else{
           alert(sers0+'区活动冲突，同步失败');
       }
*/

	});

	$('#export').click(function(){
		 var url='index.php?m=active&c=active&a=exportConfig&sid='+$('#servers').val();
	     window.open(url);
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
<input id="export"  name="export" type="button" value="导出配表"/>
</fieldset>
</form>
</div>
<div align="center">
<form id="edit" action="">
<fieldset>
<legend>活动编辑</legend>
<?php 
foreach ($xml->row as $k=>$v)
{
    $attr=$v->attributes();
    $items=explode(',',$attr->DropItemSave);
    $edit_items=explode(',',$attr->ShowItemType);
    $check_item=in_array(1, $items)?'checked="checked" ':'';
    $check_equip=in_array(2, $items)?'checked="checked" ':'';
    $check_stone=in_array(3, $items)?'checked="checked" ':'';
    $check_mount=in_array(4, $items)?'checked="checked" ':'';
    $check_frag=in_array(5, $items)?'checked="checked" ':'';
    
    $edit_item=!in_array(1, $edit_items)?'disabled="disabled"':'';
    $edit_equip=!in_array(2, $edit_items)?'disabled="disabled"':'';
    $edit_stone=!in_array(3, $edit_items)?'disabled="disabled"':'';
    $edit_mount=!in_array(4, $edit_items)?'disabled="disabled"':'';
    $edit_frag=!in_array(5, $edit_items)?'disabled="disabled"':'';

    $tool_show=$attr->ToolsShow==0?'display:none;':'';
?>
<div style="margin: 20px; border-style:solid; border-width:1px;border-color: gray;<?php echo $tool_show;?>"> 
<input type="hidden" id="ID_<?php echo $attr->ID;?>" value="<?php echo $attr->ID;?>"/>
<input type="hidden" id="Key_<?php echo $attr->ID;?>" value="<?php echo $attr->Key;?>"/>

<label>开始时间:</label>
<input type="text"  name="OpenTime_<?php echo $attr->ID;?>" id="OpenTime_<?php echo $attr->ID;?>"  value="<?php echo $attr->OpenTime;?>" class="Time"  style="width: 80px;"/>
<label>开始时间秒数:</label>
<input type="text" name="OpenSecond_<?php echo $attr->ID;?>" id="OpenSecond_<?php echo $attr->ID;?>" value="<?php echo $attr->OpenSecond;?>" style="width: 80px;"/>
<label>结束时间:</label>
<input type="text" name="CloseTime_<?php echo $attr->ID;?>" id="CloseTime_<?php echo $attr->ID;?>"  value="<?php echo $attr->CloseTime;?>" class="Time" style="width: 80px;" />
<label>结束时间秒数:</label>
<?php 
//开启时间保护  内网不需要
//if($_SESSION['sid']!='999'&&strtotime($attr->CloseTime)+$attr->CloseSecond>time()){echo 'disabled="disabled"';}
?> 
<input type="text" name="CloseSecond_<?php echo $attr->ID;?>" id="CloseSecond_<?php echo $attr->ID;?>" value="<?php echo $attr->CloseSecond;?> " style="width: 80px;" />


<input id="Item_<?php echo $attr->ID;?>" type="checkbox" value="1" <?php echo $check_item,$edit_item; ?> /><label>道具</label>
<input id="Equip_<?php echo $attr->ID;?>" type="checkbox" value="2" <?php echo $check_equip,$edit_equip;?> /><label>装备材料</label>
<input id="Stone_<?php echo $attr->ID;?>" type="checkbox" value="3" <?php echo $check_stone,$edit_stone;?> /><label>宝石</label>
<input id="Mount_<?php echo $attr->ID;?>" type="checkbox" value="4" <?php echo $check_mount,$edit_mount;?> /><label>坐骑装备</label>
<input id="Frag_<?php echo $attr->ID;?>" type="checkbox" value="5" <?php echo $check_frag,$edit_frag;?> /><label>武将碎片</label>
<label>倍率:</label>
<select id="Rate_<?php echo $attr->ID;?>" disabled="disabled">
<?php foreach (range(1,3) as $i) {
$check='';
if($i==$attr->Rate)
{
       $check='selected="selected"'; 
}
?>
<option value="<?php echo $i;?>" <?php echo $check;?> ><?php echo $i;?></option>
<?php }?>
</select>
<label>是否开启:</label>
<select id="State_<?php echo $attr->ID;?>">
<?php foreach (range(0,1) as $i) {
$check='';
if($i==$attr->State)
{
       $check='selected="selected"'; 
}
?>
<option value="<?php echo $i;?>" <?php echo $check;?> ><?php echo $i;?></option>
<?php }?>
</select>
<label>描述</label>
<input id="Talk_<?php echo $attr->ID;?>" type="hidden" value="<?php echo $attr->Talk;?>"/>
<textarea id="Talk_<?php echo $attr->ID;?>" rows="4" cols="50">
<?php 
if(!empty($langCN_config["$attr->Talk"])){
    echo $langCN_config["$attr->Talk"];
}else{echo $attr->Talk;}
?>
</textarea>
<label>标题</label>
<input id="Des_<?php echo $attr->ID;?>" type="text" value="<?php 
if(!empty($langCN_config["$attr->Des"])){
    echo $langCN_config["$attr->Des"];
}else{echo $attr->Des;}
?>"/>
<input id="ToolsShow_<?php echo $attr->ID;?>"  type="hidden" value="<?php echo $attr->ToolsShow;?>"/>
<input id="ShowItemType_<?php echo $attr->ID;?>"  type="hidden" value="<?php echo $attr->ShowItemType;?>"/>
<input id="RemainOpenTime_<?php echo $attr->ID;?>"  type="hidden" value="<?php echo $attr->RemainOpenTime;?>"/>

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
<input id="save" type="button" value="保存" style="width:50px;height:20px"/>
<input id="save_all" type="button" value="同步所选服务器" style="width:120px;height:20px"/>
</fieldset>
</form>


</body>
</html>
