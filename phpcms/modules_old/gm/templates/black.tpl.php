<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">

function CurentTime(now)
{ 
   
    var year = now.getFullYear();       //年
    var month = now.getMonth() + 1;     //月
    var day = now.getDate();            //日
   
    var hh = now.getHours();            //时
    var mm = now.getMinutes();          //分
    var ss = now.getSeconds();          //秒
   
    var clock = year + "-";
   
    if(month < 10)
        clock += "0";
   
    clock += month + "-";
   
    if(day < 10)
        clock += "0";
       
    clock += day + " ";
   
    if(hh < 10)
        clock += "0";
       
    clock += hh + ":";
    if (mm < 10) clock += '0'; 
    clock += mm+":"; 
    if (ss < 10) ss += '0'; 
    clock += ss; 
    return(clock); 
} 
$(document).ready(function(){
	$('#add_black').click(function(){
		if($('#userid').val()&&$('#servers').val())
		{
		 var option=$('#black_List').find("option[value='"+$('#userid').val()+"']");
		 if(option.length==0)
	 	 {console.log('seshi');
    	          $.getJSON('index.php?m=gm&c=black&a=forbbiden',{'uid':$('#userid').val(),'sid':$('#servers').val(),'ch':0xFFFF},function(data){
		     if(data['error']==0)
            	      {
               	    	 $('#black_List').append('<option value='+$('#userid').val()+'>'+$('#userid').val()+'</option>');
                	     $('#black_List').attr('size',$('#black_List').attr('size')+1);
            	      }
    		  });
		}
	       }
    });
	$('#del_black').click(function(){
		if($('#black_List').val()&&$('#servers').val())
		{
			 var option=$('#black_List').find("option[value='"+$('#black_List').val()+"']");
			 if(option.length>0) {
    		 $.getJSON('index.php?m=gm&c=black&a=forbbiden',{'uid':$('#black_List').val(),'sid':$('#servers').val(),'ch':0x0000},function(data){
    			   if(data['error']==0)
                   {
 				       console.log(option[0]);
					   option[0].remove();
                   }
    	        });
    		 } 
		 }
    });
	$('#add_ban').click(function(){ 
		if($('#userid').val()&&$('#servers').val())
		{
			 var option=$('#ban_List').find("option[value='"+$('#userid').val()+"']");
		     if(option.length==0)
			  {
    	          $.getJSON('index.php?m=gm&c=black&a=ban',{'uid':$('#userid').val(),'sid':$('#servers').val(),'ch':0xFFFF,'time':$('#time').val()},function(data){
            	      if(data['error']==0)
            	      {
            	    	    var date=new Date();
            	    	    date.setTime(parseInt(date.getTime()+1000*$('#time').val()));
            	    	    var dtstr=CurentTime(date);
               	    	 $('#ban_List').append('<option value='+$('#userid').val()+'>'+$('#userid').val()+' '+dtstr+'</option>');
                	     $('#ban_List').attr('size',$('#ban_List').attr('size')+1);
            	      }
    		       });
			  }
		} 
    }); 
	$('#del_ban').click(function(){
		if($('#ban_List').val()&&$('#servers').val())
		{
			 var option=$('#ban_List').find("option[value='"+$('#ban_List').val()+"']");
			 
			 if(option.length>0) {  
    		 $.getJSON('index.php?m=gm&c=black&a=delban',{'uid':$(option).attr('value'),'sid':$('#servers').val(),'ch':0x0000,'time':$('#time').val()},function(data){
    			   if(data['error']==0)
                   {  
					   option[0].remove();
                   }
    	        });
    		 } 
		 } 
    });
    
	$('#servers').change(function(){
	    $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
	       location.reload();
	    });
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
<fieldset style="width: 200px;"><legend>DS server</legend>
<select name="servers" id="servers" style="width: 150px;">
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].'server'.$value['text'].'</option>';
}
?>
</select>

</fieldset>
<fieldset><legend>Tìm user</legend>
<input name="acc" id="acc" type="text" />
<input name="search" id="search" type="button" value="Tìm" >
</fieldset>
<fieldset style="width: 200px;"><legend>Nhân vật ID Danh sách</legend>
<select name="userid" id="userid" size="<?php echo count($keys)>0?count($keys)+1:2?>" style="width: 200px;">
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
<fieldset><legend>Thao tác cấm chat</legend>
<?php 
$admin_group=array(1,8,17,16);
$priv=in_array($_SESSION['roleid'],$admin_group);
if(isset($priv)&&!empty($priv)){?>
<input type="button" id="add_black"  value="Thêm vào DS đen"/>
<input type="button" id="del_black"  value="Xóa DS đen"/>
<select id="time" name="time">
<option value="3600">1 Giờ</option>
<option value="86400">1 Ngày</option>
<option value="604800">1 Tuần</option>
<option value="2592000">1 Tháng</option>
<option value="31536000">1 Năm</option>
</select>
<input type="button" id="add_ban"  value="Cấm đăng nhập"/>
<input type="button" id="del_ban"  value="Xóa đăng nhập"/>
<?php }?>
</fieldset>
<fieldset><legend>Cấm chat&&DS phong hiệu</legend>
<label>DS cấm chat</label>
<input type="hidden" id="uid" name="uid" value=""/>
<select name="black_List" id="black_List"  size="<?php echo count($black)+1;?>" style="width: 200px;float: left;">
<?php
foreach ($black as $key=>$value) {
    echo "<option value=".$key.">$key</option>";
}
?>
</select>
<label>DS phong hiệu</label>
<select name="ban_List" id="ban_List"  size="<?php echo count($ban)+1;?>" style="width: 500px;float: left;">
<?php
foreach ($ban as $key=>$value) {
   $ed=date('Y-m-d h:i:s',$value['ed']);
    echo "<option value=".$key.">$key $ed</option>";
}
?>
</select>
</fieldset>
</form>
<label id="msg" style="font-size: medium;color: red;"></label>
</div>
</body>
</html>

