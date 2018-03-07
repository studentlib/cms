<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#sendNotice').click(function(){
		$('#msg1').html("");
	    if($('#notice').val()=='')
	    {
	    	$('#notice').focus();
	    	$('#msg1').html("Vui lòng nhập nội dung thông báo");
	    }else if($('#notice').val().length<10){
	    	$('#notice').focus();
	    	$('#msg1').html("Số từ không đủ 10");
	    }else{
	    	 $.getJSON("?m=gm&c=gs&a=sendNotice&sid="+$('#servers').val(),{'msg':$('#notice').val()},function(data){
	              if(data&&data['msg']!=undefined)
	              { 
	                  $('#msg1').html(data['msg']);
	                  return;
	              } 
	          });
	    }
    });
     $('#sendShutDown').click(function(){ 
   	    $('#msg2').html("");
        if($('#shutdown').val()=='')
        {
            $('#shutdown').focus();
            $('#msg2').html("Vui lòng nhập nội dung tin nhắn");
        }else if($('#shutdown').val().length<10){
            $('#shutdown').focus();
            $('#msg2').html("Số từ không đủ 10");
        }else{ 
             $.getJSON("?m=gm&c=gs&a=shutDownNotice&sid="+$('#servers').val(),{'msg':$('#notice').val()},function(data){
                  if(data&&data['msg']!=undefined)
                  { 
                      $('#msg2').html(data['msg']);
                      $('#kickAll').attr('disabled','');
                      return;
                  } 
              });
        }
    });
    $('#kickAll').click(function(){

   	    $.getJSON("?m=gm&c=gs&a=kickAll&sid="+$('#servers').val(),function(data){
         if(data&&data['msg']!=undefined)
         { 
             $('#msg3').html(data['msg']);
             $('#kickAll').attr('disabled','disabled');
             return;
         } 
     });
    });
    
    $('#selectall').click(function(){
        if($(this).attr('checked'))
        {
            $('#servers').children('option').attr('selected','selected');
        }else{
            $('#servers').children('option').attr('selected','');
        }
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
<div style="width:250px;float:left;">
<form action="">
<fieldset style="width: 550px;"><legend>Cụm sv</legend>

<select name="servers" id="servers" multiple="multiple" size="<?php echo count($servers)?>" style="width: 200px;float: left;">
<?php 
foreach ($servers as $value) {
     echo '<option value="'.$value['ServerType'].'">'.$value['id'].' Quận '.$value['text'].'</option>';
}
?>
</select>
<input type="checkbox" id="selectall" name="selectall"/><label>Chọn tất cả</label>
</fieldset>

 <fieldset>
 <legend>Thông báo</legend>
 <textarea rows="10" cols="50" name="notice" id="notice" ></textarea>
 <input type="button" id="sendNotice" name="sendNotice" value="Gửi"/>
  <label id="msg1" style="font-size: medium;color: red;"></label>
 </fieldset>
 <fieldset>
 <legend>Đóng thông tin dịch vụ</legend>
 <textarea rows="10" cols="50" name="shutdown" id="shutdown"></textarea>
 <input type="button" id="sendShutDown" name="sendShutDown" value="Gửi"/>
 <input type="button" id="kickAll" name="kickAll"  disabled="disabled" value="Đóng tất cả người chơi"/>
 <label id="msg2" style="font-size: medium;color: red;"></label>
 </fieldset>

</form>
</div>
</html>

