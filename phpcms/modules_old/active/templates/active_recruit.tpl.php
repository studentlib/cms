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
        var content_ba={};
	var content_rank={};
       if('<?php echo time();?>'< '<?php echo $server;?>' && <?php echo $_SESSION['sid'];?>!='999'){
         alert("Hoạt động chưa kết thúc, không được phép thay đổi");
         return;
       }
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
             if(content[idc[2]]['Reward']==undefined)
             {
                   content[idc[2]]['Reward']=[];
             }    
             var idx=$(v).val();
             var count=$('#Count_'+idc[1]+'_'+idc[2]).val();
             var x={};
             x[idx]=count;
             content[idc[2]]['Reward'].push(x);
        });
        $("#edit_ba :input").each(function(k,v){
              var idc=$(v).attr('id').split('_');
              if(content_ba[idc[1]]==undefined)
               {
                   content_ba[idc[1]]={};
               }
               if(content_ba[idc[1]][idc[0]]==undefined)
               {
                   content_ba[idc[1]][idc[0]]=[];
               }
             content_ba[idc[1]][idc[0]]=$(v).val();
        });
	$("#edit_rank :input").each(function(k,v){
            if($(v).attr('class')=='Award')
            {
                return ;
            }
            if($(v).attr('class')=='count')
            {
                return ;
            }
           var idc=$(v).attr('id').split('_');
           if(content_rank[idc[1]]==undefined)
           {
               content_rank[idc[1]]={};
           }
          if($(v).attr('type')=='checkbox'&&!$(v).attr('checked')){
                return;
          }
           content_rank[idc[1]][idc[0]]=$(v).val();
        });
        $('.Award').each(function(k,v){
             var idc=$(v).attr('id').split('_');
             if(content_rank[idc[2]]==undefined)
               {
                   content_rank[idc[2]]={};
               }
             if(content_rank[idc[2]]['Award']==undefined)
             {
                   content_rank[idc[2]]['Award']=[];
             }    
             var idx=$(v).val();
             var count=$('#Count1_'+idc[1]+'_'+idc[2]).val();
             var x={};
             x[idx]=count;
             content_rank[idc[2]]['Award'].push(x);
        });
            post.sid=$('#servers').val();
            post.content=content;
            post.content_ba=content_ba;
	    post.content_rank=content_rank;
            post.comment=$('#comment').val();
            post.comment_ba=$('#comment_ba').val();
            post.comment_rank=$('#comment_rank').val();
            $.post('index.php?m=active&c=active&a=update_active_recruit',post,function(data){
                alert(data.msg);
                location.reload();
            },'json');
    });
      
	$('#save_all').click(function(){
	   var post={};
	   post.sid=$('#servers').val(); 
	   post.hid=$('#heroid').val();
	   post.servers=[]; 
	   $('#sservers').children('option:selected').each(function(k,v){
         var server=$(v).val();
         var activeEndTime=<?php echo $activeTypeTime;?>;
        if(activeEndTime[server]>='<?php echo time();?>'){
            var r=confirm('Vùng '+ server +' có các hoạt động mở, nhấn OK để ghi đè, hủy bỏ');
          if(r==false){
            return true;
          }
        }
		   if($(v).val()!=0)
		   {
			   post.servers.push($(v).val());
		   }  
	   }); 
       $.post('index.php?m=active&c=active&a=sync_active_recruit',post,function(data){
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
<legend>Mời chọn server</legend>
<select name="servers" id="servers" style="width: 150px">
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].' server '.$value['text'].'</option>';
}
?>
</select>
</fieldset>
</form>

</div> 
<div align="center">
<fieldset>
<legend>Edit sự kiện</legend>
<form id="edit_ba" action="">

<?php
foreach($xml_ba_arr as $v1){
    $attr_ba=$v1;
     if($attr_ba['Key']=='HeroID'){
?>
<label><?php echo $attr_ba['Des'];?>：</label>
<input type="hidden" id="ID_<?php echo $attr_ba['ID'];?>" value="<?php echo $attr_ba['ID'];?>"  style="width: 100px;margin-right:100px"/>
<input type="hidden" id="Key_<?php echo $attr_ba['ID'];?>" value="<?php echo $attr_ba['Key'];?>" style="width: 100px;margin-right:100px"/>
<select id="Value_<?php echo $attr_ba['ID'];?>" name="heroid" style="width: 80px;margin-right:20px">
<?php 
    foreach($heros as $k=>$v)
    {
        $selected='';
        if($v['id']==$attr_ba['Value'])
        {
            $selected='selected="selected"';
        }
?>
    <option value="<?php echo $v['id']?>" <?php echo $selected?>><?php echo $v['name']?></option>

<?php 
    }
?>
</select>
<input type="hidden" id="Des_<?php echo $attr_ba['ID'];?>" value="<?php echo $attr_ba['Des'];?>" style="width: 100px;margin-right:100px"/>
<?php
 continue;
}
?>
<label><?php echo $attr_ba['Des'];?>：</label>
<input type="hidden" id="ID_<?php echo $attr_ba['ID'];?>" value="<?php echo $attr_ba['ID'];?>" style="width: 100px;margin-right:100px"/>
<input type="hidden" id="Key_<?php echo $attr_ba['ID'];?>" value="<?php echo $attr_ba['Key'];?>" style="width: 100px;margin-right:100px"/>
<?php 
if($attr_ba['Key']=='RankNum'){
echo '<select id="Value_'.$attr_ba['ID'].'">';
echo '<option value="0">未选择</option>';
foreach (range(1,10) as $value) {
    $check='';
    if($value==$attr_ba['Value'])
    {
        $check=' selected="selected" ';
    }
    echo '<option value="'.$value.'" '.$check.'>'.$value.'</option>';
}
echo '</select>';
}else{
echo '<input type="text" id="Value_'.$attr_ba['ID'].'" value="'.$attr_ba['Value'].'" style="width: 100px;margin-right:20px"/>';
}
?>
<input type="hidden" id="Des_<?php echo $attr_ba['ID'];?>" value="<?php echo $attr_ba['Des'];?>" style="width: 100px;margin-right:100px"/>
<?php
}
?>

</form>
<form id="edit" action="">
<?php 
foreach ($xml_arr as $k=>$v)
{
    $attr=$v;
    $items=$attr['Reward'];
?>
<div style="margin: 20px; border-style:solid; border-width:1px;border-color: gray;"> 
<label>招募次数</label>
<input type="text" id="ID_<?php echo $attr['ID'];?>" value="<?php echo $attr['ID'];?>" style="width: 80px"/>
<label>碎片数量</label>
<input id="FragTimes_<?php echo $attr['ID'];?>" type="text"  value="<?php echo $attr['FragTimes'];?>" style="width: 80px"/>
<?php 
$i=0;
foreach ($items as $k=>$va) {
$i++;
?>
<label>奖励<?php echo $i;?></label>
<input list="company" type="text" name="" id="Items_<?php echo $i;?>_<?php echo $attr['ID'];?>" class="item" style="width: 80px"
value='<?php echo $config[array_keys($va)[0]]['name'];?>' onblur="isItemEexit('Items_<?php echo $i;?>_<?php echo $attr['ID'];?>')">
<datalist id="company">
<?php  
 foreach ($config as $value) {
    echo '<option value="'.$value['name'].'" >'.$value['id'].'</option>';
}
?>
</datalist>
<input type="text" name="" id="Count_<?php echo $i;?>_<?php echo $attr['ID'];?>" class="count" style="width: 50px";
 value=<?php echo array_values($va)[0];?> >

<?php 
}
?>
</div>
<?php 
}
?>
</fieldset>
</form>

<form id="edit_rank" action="">
<fieldset>
<legend>排行奖品</legend>
<?php
foreach ($xml_rank_arr as $k=>$v)
{
    $c=0;
?>
<div style="margin: 20px; border-style:solid; border-width:1px;border-color: gray;">
<label>名次&nbsp:&nbsp</label>
<input id="Rank_<?php echo $v['Rank'];?>" type="text" value="<?php echo $v['Rank']?>" style="width: 30px;"/>
<?php
    foreach($v['Award'] as $k1=>$v1){
     $c++;
?>
<label>Đạo cụ thưởng&nbsp:&nbsp</label>
<input list="company" type="text" name="" id="Award_<?php echo $c;?>_<?php echo $v['Rank'];?>" style="width: 120px;" class="Award" 
onblur="isItemEexit('Award_<?php echo $c;?>_<?php echo $v['Rank'];?>')" value='<?php echo $config[array_keys($v1)['0']]['name'];?>' >
<datalist id="company">
<?php
 foreach ($config as $value) {
    echo '<option value="'.$value['name'].'" >'.$value['id'].'</option>';
}
?>
 </datalist>
<input id="Count1_<?php echo $c;?>_<?php echo $v['Rank'];?>" name="" class="count" type="text" value="<?php echo array_values($v1)['0'];?>" style="width: 60px";/>
<?php }?>
</div>
<?php }?>
</fieldset>

</form>
</div>
<form action="">
<fieldset>
<legend>Chọn server cần đồng bộ</legend>
<select name="sservers" id="sservers" multiple="multiple" size="<?php echo count($servers); ?>"  style="width: 150px">
<option value="0">Hãy chọn</option>
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].' server '.$value['text'].'</option>';
}
?>
</select>
<input type="hidden" id="comment_rank" value="<?php echo htmlspecialchars($comment_rank);?>"/>
<input type="hidden" id="comment_ba" value="<?php echo htmlspecialchars($comment_ba);?>"/>
<input type="hidden" id="comment" value="<?php echo htmlspecialchars($comment);?>"/>
<input id="save" type="button" value="Lưu" style="width:50px;height:20px"/>
<input id="save_all" type="button" value="Đồng bộ tất cả server" style="width:120px;height:20px"/>
</fieldset>
</form>


</body>
</html>
