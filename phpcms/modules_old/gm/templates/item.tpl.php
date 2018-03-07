<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">

function change_count()
{
	 $('#del_count').empty();
	 //该死的IE
	 var str='';
	 for(var i=1;i<=$(this).find(':selected').attr('count');++i)
	 { 
		 str+='<option>'+i+'</option>';
	 }
	 $('#del_count').append(str);
}

$(document).ready(function(){
	$("#userid").change(function(){
		  $('#items').empty();
          $('#msg').html("");
		  th=this;
	     $.getJSON("?m=gm&c=gm&a=getItem&id="+$(this).val(),function(data){
                 if(data&&data['msg']!=undefined)
                 { 
                     $('#msg').html(data['msg']);
                     return;
                 } 
                 $('#uid').val($('#search').val()); 
                 $('#items').attr('size',data.length);
                 var str='';
                 $.each(data,function(k,v){
                	 str+='<option value="'+v.tid+'" count="'+v.count+'">'+v.name+'('+v.count+')</option>';
                 });
                 $('#items').append(str);
                 $('#items').change(change_count);
                 $('#add_item').attr('disabled','');
                 $('#del_item').attr('disabled','');
                 $('#add_gift').attr('disabled','');
                 $('#uid').val($(th).val());
		 });
     });
    $('#save').click(function(){
        if($('#uid').val()!='')
        {
       	    $('#msg').html();
    	     $.post('?m=gm&c=gm&a=updateInfo',$('#edit').serialize(),function(data){
    	     if(data&&data['msg'])
    	     { 
        	     $('#msg').html(data['msg']);
    	     }
        	},'json');
         }
       });
     $('#search').click(function(){
    	 $('#error').html("");
 	    if($('#suid').val()!='')
 	    { 
	    	 $('#msg').html("");
	    	 $('#items').empty();
             $('#add_item').attr('disabled','disabled');
             $('#del_item').attr('disabled','disabled');
             $('#add_gift').attr('disabled','disabled');
	    	 $.getJSON("?m=gm&c=gm&a=getItem&id="+$('#suid').val(),function(data){
                 $('#add_item').attr('disabled','');
                 $('#del_item').attr('disabled','');
                 $('#add_gift').attr('disabled','');
                 $('#uid').val($('#suid').val());
		    	 if(data&&data['msg']!=undefined)
		    	 { 
		    		 $('#msg').html(data['msg']);
			    	 return;
		    	 } 
		    	 $('#items').attr('size',data.length);

		    	 var str='';
                 $.each(data,function(k,v){
                	 str+='<option value="'+v.tid+'" count="'+v.count+'">'+v.name+'('+v.count+')</option>';
                 });
                 $('#items').append(str);
                 $('#items').change(change_count);

             });
 	 	    
 	    }else{
 	 	    $('#error').html("Nhập nhân vật ID");
 	    }
      });
     $('#add_item').attr('disabled','disabled');
     $('#del_item').attr('disabled','disabled');
     $('#add_gift').attr('disabled','disabled');
     
     $('#add_item').click(function(){
 	     if($('#uid').val()!='')
 	     {
 	    	 $.getJSON("?m=gm&c=gm&a=addItem&id="+$('#uid').val()+"&tid="+$('#item_list').val()+'&count='+$('#item_count').val()+'&sid='+$('#servers').val(),function(data){
                 if(data&&data['msg']!=undefined)
                 { 
                     $('#msg').html(data['msg']);
                     return;
                 } 
             });
 	     }else{
  	         $('#msg').html("Tìm hoặc chọn nhân vật ID");
 	     }
     });
     $('#add_gift').click(function(){
 	     if($('#uid').val()!='')
 	     {
 	    	 $.getJSON("?m=gm&c=gm&a=addGift&id="+$('#uid').val()+"&tid="+$('#item_list').val()+'&count='+$('#item_count').val()+'&sid='+$('#servers').val(),function(data){
                 if(data&&data['msg']!=undefined)
                 { 
                     $('#msg').html(data['msg']);
                     return;
                 } 
             });
 	     }else{
  	         $('#msg').html("Tìm hoặc chọn nhân vật ID");
 	     }
     });
     $('#del_item').click(function(){
         if($('#uid').val()!='')
         {
             $.getJSON("?m=gm&c=gm&a=delItem&id="+$('#uid').val()+"&tid="+$('#items').val()+'&count='+$('#del_count').val()+'&sid='+$('#servers').val(),function(data){
                 if(data&&data['msg']!=undefined)
                 { 
                     $('#msg').html(data['msg']);
                     return;
                 } 
             });
         }else{
             $('#msg').html("Tìm hoặc chọn nhân vật ID");
         }
    	 
     });
     var str='';
     for(var i=1;i<1000;++i)
     { 
    	 str+=('<option>'+i+'</option>');    
     }
     $('#item_count').append(str);

     $('#servers').change(function(){
         $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
            location.reload();
         });
       });
});


</script>
<style type="text/css">
label{vertical-align:left;width: 180px;float:left;}
input{vertical-align:left;width: 150px;float:left;}
body{font-family:tahoma Verdana;font-size:12px;}
</style>
</head>
<body>
<?php 
//global $keys;
?>
<div style="width:225px;float:left;">
<form action="">
<fieldset><legend>Tìm</legend>
<label>Tài khoản ID:</label>
<input type="text" name="suid" id="suid" style="width: 120px;float: left;"/>
<input type="button" name="search" id="search" value="Tìm" style="width: 50px;float: auto;"/>
<label id="error"  style="font-size: medium;color: red;"></label>
</fieldset>
<fieldset style="width: 200px;"><legend>Danh sách server</legend>
<select name="servers" id="servers" style="width: 150">
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].'Server'.$value['text'].'</option>';
}
?>
</select>
</fieldset>
<fieldset style="width: 200px;"><legend>Nhân vật ID Danh sách</legend>
<select name="userid" id="userid" size="<?php  echo count($keys)>0?count($keys)+1:2?>" style="width: 200px;height: 800px">
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
<div style="float: auto;">
<form action="">
<fieldset><legend>Danh sách đạo cụ</legend>
<select name="item_list" id="item_list"  style="width: 200px;float: left;">
<?php 
 foreach ($config as $value) {
    echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
}
?>
</select>
<select name="item_count" id="item_count"  style="width: 50px;float: left;"> 
</select>

<?php 
$admin_group=array(1,8);
$priv=in_array($_SESSION['roleid'],$admin_group);
if(isset($priv)&&!empty($priv)){?>
<input type="button" id="add_item" name="add_item" value="Thêm vào túi"/>
<input type="button" id="del_item" name="del_item" value="Xóa đạo cụ trong túi"/>
<input type="button" id="add_gift" name=add_gift value="Thêm quà"/>
<?php }?>
</fieldset>
<fieldset><legend>Danh sách đạo cụ trong túi</legend>
<input type="hidden" id="uid" name="uid" value=""/>
<select name="items" id="items"  style="width: 200px;float: left;">
</select>
<select name="del_count" id="del_count"  style="width: 50px;float: auto;"> 
<option>1</option>
</select>
</fieldset>
</form>
<label id="msg" style="font-size: medium;color: red;"></label>
</div>
</body>
</html>

