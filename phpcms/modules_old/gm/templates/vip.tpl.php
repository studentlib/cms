<html>
<head>
<title>Xem tài khoản</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('#servers').change(function(){
        $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
           location.reload();
        });
     });
    $('#buy').click(function(){
      
        if($('#userid').val()==null)
        {
        	return $('#userid').focus();
        }
        if($('#itemid').val()==null)
        {
            return $('#itemid').focus();
        }
        var pdata={};
        pdata.sid=$('#servers').val();
        pdata.uid=$('#userid').val();
        pdata.itemid=$('#itemid').val();
        $.post('?m=gm&c=vip&a=buy',pdata,function(data){
            alert(data['msg']); 
        },'json');
     });

    $('#search').click(function(){
        if($('#acc').val()=='')
        {
            return $('#acc').focus();
        }
        $.getJSON('?m=gm&c=gm&a=getInfo&id='+$('#acc').val(),function(data){
           if(data['msg']==undefined)
           {
               $('#userid').contents().remove();
               $('#userid').append('<option value="'+data['uid']+'">'+data['uid']+'</option>');
               $('#userid').attr('size',$('#userid').attr('size')+1);
           }else{
               alert(data['msg']);
           }
        });
    });
});
</script>
<style type="text/css">
div{
	margin:0px;
	padding:0px;
};
</style>
</head>
<body>
<div>
<div style="width:300px;float:left;" align="left">
<form action="">
<fieldset><legend>Nhập nhân vật ID</legend>
<input name="acc" id="acc" type="text" />
<input name="search" id="search" type="button" value="Xem" >
</fieldset>
<fieldset style="width: 200px;"><legend>Danh sách server</legend>
<select name="servers" id="servers" style="width: 150px;">
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].'server'.$value['text'].'</option>';
}
?>
</select>
</fieldset>
</form>
</div>

<div style="width:300px;float:left;clear:left;" align="left">
<form action="">
<fieldset style="width: 200px; float:left;"><legend>Nhân vật ID Danh sách</legend>
<select name="userid" id="userid" size="<?php echo count($keys)>0?count($keys)+1:2?>" style="width: 200px;height: 800px">
<?php
foreach ($keys as $value) {
    $new=substr($value, strpos($value, ':')+1);
    echo "<option value=".$new.">$new</option>";
}
?>
</select>
</fieldset>
</form>

</div>
<div  align="left">
<fieldset><legend>Mua VIP Vật phẩm</legend>
<select name="itemid" id="itemid"  size="<?php echo count($keys)>0?count($keys)+1:1?>" style="width: 200px;height: 100%;">
<?php
foreach ($pays as $k=>$value) {
    echo "<option value=".$k.'>'.$value['Des']."</option>";
}
?>
</select>
<input type="button" id="buy" value="Mua" />
</fieldset>
</div>
</div>
</body>
</html>
