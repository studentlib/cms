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
input{
 width:50px;
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
        if("<?php echo time();?>"<"<?php echo $closetime;?>" && <?php echo $_SESSION['sid'];?>!='999'){
          alert("Hoạt động chưa kết thúc, không được phép thay đổi");
          return;
        }
        $("#edit :input").each(function(k,v){
            var idc=$(v).attr('id').split('_');
               if(content[idc[1]]==undefined)
               {
                   content[idc[1]]={};
               }
	       if($(v).attr('class')=='Item')
               {
                    return ;
               }
               if($(v).attr('class')=='count')
               {
                    return ;
               }
              if($(v).attr('type')=='checkbox'){
                  if($(v).attr('checked')){
                      content[idc[1]][idc[0]]="1";
                      }else{ content[idc[1]][idc[0]]="0";}
                  return;
              }
             content[idc[1]][idc[0]]=$(v).val();
        });
	   $('.Item').each(function(k,v){
                 var idc=$(v).attr('id').split('_');
                 if(content[idc[2]]==undefined)
                   {
                       content[idc[2]]={};
                   }
                 if(content[idc[2]]['Value']==undefined)
                 {
                           content[idc[2]]['Value']=[];
                 }
                 var idx=$(v).val();
                 var count=$('#Count_'+idc[1]+'_'+idc[2]).val();
                 var x={};
                 x[idx]=count;
             content[idc[2]]['Value'].push(x);

         });
            post.sid=$('#servers').val();
            post.content=content;
            post.comment=$('#comment').val(); 
            $.post('index.php?m=active&c=active&a=update_comment',post,function(data){
                alert(data.msg);
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
       $.post('index.php?m=active&c=active&a=sync_comment',post,function(data){
            alert(data.msg);
        },'json');
    });

});
function isItemEexit(id){
    var post={};
    post.item=$('#'+id).val();
    $.post('index.php?m=active&c=active&a=is_item_exit',post,function(data){
	if(data.code==1){alert(data.msg);$('#'+id).val('');}
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
<form id="edit" action="">
<fieldset>
<legend>Edit sự kiện</legend>
<?php 
foreach ($xml_arr as $k=>$v)
{
    $attr=$v;//var_dump($attr);
    $items=$attr['Value'];
?>
<div style="margin: 20px; border-style:solid; border-width:1px;border-color: gray;"> 
<?php echo $attr['Des'];?>:
<input type="hidden" id="ID_<?php echo $attr['ID'];?>"  value="<?php echo $attr['ID'];?>" style="width:30px;"/>
<input type="hidden" id="Key_<?php echo $attr['ID'];?>" value="<?php echo $attr['Key'];?>" style="width:120px;"/>
<?php
if(!in_array($attr['Key'],array('CommitAward','LikeAward'))){
?>
<input type="text" id="Value_<?php echo $attr['ID'];?>" value="<?php echo $items;?>" style="width:30px;"/>
<?php
}else{
    $arr1=explode(',',$items);
    $arr=array();
    foreach($arr1 as $k2=>$v2){
          $arr2=explode(':',$v2);
          $arr[$arr2[0]]=$arr2['1'];
    }
    $i=0; 
    foreach($arr as $k3=>$v3){
    $i++
?>
<label>Phần thưởng<?php echo $i;?></label>
<input list="company" type="text" name="Item" id="Value_<?php echo $i;?>_<?php echo $attr['ID'];?>" class="Item" value="<?php echo $config[(string)$k3]['name'];?>" onblur="isItemEexit('Value_<?php echo $i;?>_<?php echo $attr['ID'];?>')" style="width:90px;">
<datalist id="company">
 <?php  
  foreach ($config as $value) {
    echo '<option value="'.$value['name'].'" >'.$value['id'].'</option>';
  }
 ?>
 </datalist>
<input type="text" id="Count_<?php echo $i;?>_<?php echo $attr['ID'];?>" class="count" value="<?php echo $v3;?>" style="width:50px;"/>
<?php
    }
}
?>
<input id="Des_<?php echo $attr['ID'];?>" type="hidden" value="<?php echo $attr['Des'];?>"/>
</div>
<?php 
}
?>

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
<input type="hidden" id="comment" value="<?php echo htmlspecialchars($comment);?>"/>
<input id="save" type="button" value="Lưu" style="width:50px;height:20px"/>
<input id="save_all" type="button" value="Đồng bộ tất cả server" style="width:120px;height:20px"/>
</fieldset>
</form>
</body>
</html>
