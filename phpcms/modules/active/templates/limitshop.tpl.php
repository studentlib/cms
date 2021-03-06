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
        if('<?php echo time();?>'< '<?php echo $server;?>' && <?php echo $_SESSION['sid'];?>!='999'){
          alert("活动未结束，禁止修改");
          return;
        }
        $("#edit :input").each(function(k,v){
            var idc=$(v).attr('id').split('_');//console.log(idc);
               if(content[idc[1]]==undefined)
               {
                   content[idc[1]]={};
               }
              if($(v).attr('type')=='checkbox'){
                  if($(v).attr('checked')){
                      content[idc[1]][idc[0]]="1";
                      }else{ content[idc[1]][idc[0]]="0";}
                  return;
              }
             content[idc[1]][idc[0]]=$(v).val();
        }); 
            //console.log(content);
            post.sid=$('#servers').val();
            post.content=content;
            post.comment=$('#comment').val();  
            $.post('index.php?m=active&c=active&a=update_limitshop',post,function(data){console.log(data);
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
       $.post('index.php?m=active&c=active&a=sync_limitshop',post,function(data){
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
<div align="center">
<form id="edit" action="">
<fieldset>
<legend>活动编辑</legend>
<?php 
foreach ($xml_arr as $k=>$v)
{
    $attr=$v;//var_dump($attr);
?>
<div style="margin: 20px; border-style:solid; border-width:1px;border-color: gray;"> 
<label>栏位</label>
<input type="text" id="ID_<?php echo $attr['ID'];?>" disabled="disabled" value="<?php echo $attr['ID'];?>" style="width:30px;"/>
<label>物品</label>
<input list="company" type="text" name="item" id="ItemId_<?php echo $attr['ID'];?>" class="item"style="width: 80px"; value="<?php echo $config[(string)$attr['ItemId']]['name'];?>" 
onblur="isItemEexit('ItemId_<?php echo $attr['ID'];?>')" >
<datalist id="company">
 <?php  

  foreach ($limititem as $value) {
     echo '<option value="'.$value['Des'].'" '.$check.'>'.$value['ItemId'].'</option>';
 }
 ?>
 </datalist>
<label style="margin-left:16px;">原价</label>
<input type="text" id="PriceFormal_<?php echo $attr['ID'];?>" value="
<?php 
 foreach ($limititem as $value) {
    if($value['ID']==$attr['ID'])
    {
        echo $attr['PriceFormal'];
    }
}
?>"/>
<label>现价</label>
<input type="text" id="PriceNow_<?php echo $attr['ID'];?>" value="
<?php 
 foreach ($limititem as $value) {
    if($value['ID']==$attr['ID'])
    {
        echo $attr['PriceNow'];
    }
}
?>"/>
</select>
<label>VIP等级限制</label>
<select id="RequiredVip_<?php echo $attr['ID'];?>">
<option value="0">未选择</option>
<?php 
 foreach (range(1,15) as $value) {
    $check='';
    if($value==$attr['RequiredVip'])
    {
        $check=' selected="selected" ';
    }
    echo '<option value="'.$value.'" '.$check.'>'.$value.'</option>';
}
?>
</select>
<label>库存</label>
<input id="ServerNum_<?php echo $attr['ID'];?>" type="text" value="<?php echo $attr['ServerNum'];?>"/>
<label>个人限购</label>
<input id="PersonalNum_<?php echo $attr['ID'];?>" type="text" value="<?php echo $attr['PersonalNum'];?>"/>
<label>热卖</label>
<input id="Hot_<?php echo $attr['ID'];?>" type="checkbox" <?php if($attr['Hot']==1){echo "checked='checked'";};?> class="Hot"/>
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
