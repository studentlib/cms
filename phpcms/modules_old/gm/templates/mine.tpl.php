<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">

function checkUpate(data)
{
	$('#check').attr('disabled','');
}
$(document).ready(function(){

	$("#userid").change(function(){
		var th=this;
		$('#res  input').val('');
		$('#records').html('');
		$('#error').html('');
	     $.getJSON("?m=gm&c=mine&a=getMineInfo&id="+$(this).val(),function(data){
		      if(Object.keys(data).length >0)
		      {
	               $.each(data,function(k,v){
		               if(k!='detail'&&k!='record')
		               {
    	                    $('#'+k).val(v);
		               }
	                });
	                
	                if(Object.keys(data['detail']).length>0)
	                {
                	   $.each(data['detail'],function(k1,v1){
                           for(var x in v1)
                           {
                               $('#'+x+'_'+k1).val(v1[x]);
                           }
                       });
	                } 
	                if(Object.keys(data['record']).length >0)
	                {
                	   $.each(data['record'],function(kr,vr){
                		   var records= '<div style="float: auto;">'
                    	   +'<fieldset style="vertical-align: baseline;">'
                    	   +'<legend >Log thủ'+kr+'</legend>'
                    	   +'<label>Kẻ cướp đoạt ID:</label>'
                    	   +'<input type="text" name="auid" id="auid" value="'+vr['auid']+'"/>'
                    	   +'<label>Mã số mỏ:</label>'
                    	   +'<input type="text" name="mid" id="mid" value="'+vr['mid']+'"/>'
                    	   +'<label>Kết quả cướp:</label>'
                    	   +'<input type="text" name="result" id="result" value="'+vr['result']+'"/>'
                    	   +'<label>Thời gian cướp:</label>'
                    	   +'<input type="text" name="rtime" id="rtime" value="'+vr['rtime']+'"/><br/>'
                    	   +'<label>Quân đoàn kẻ cướp:</label>'
                    	   +'<input type="text" name="lname" id="lname" value="'+vr['lname']+'"/>'
                    	   +'<label>Tên kẻ cướp:</label>'
                    	   +'<input type="text" name="aname" id="aname" value="'+vr['aname']+'"/>'
                    	   +'<label>Tiền của kẻ cướp đoạt được:</label>'
                    	   +'<input type="text" name="currency" id="currency" value="'+vr['currency']+'"/>'
                    	   +'<label>V.phẩm kẻ cướp đoạt được:</label>'
                    	   +'<input type="text" name="item" id="item" value="'+vr['item']+'"/>'
                    	   +'</fieldset>'
                    	   +'</div>';
                    	   $('#records').append(records);
    	               });
		     	 }
			     	 return;
		      }
		      $('#error').html('N.vật này chưa tạo mỏ');
		    	
		 });
     });
     $('#search').click(function(){
    	 $('#error').html("");
 	    if($('#suid').val()!='')
 	    {
	    	 $('#res  input').val('');
	    	 $('#records').html('');
	    	 $('#error').html('');
	    	 $.getJSON("?m=gm&c=mine&a=getMineInfo&id="+$('#suid').val(),function(data){
	    		 if(Object.keys(data).length >0)
			      {
		               $.each(data,function(k,v){
			               if(k!='detail'&&k!='record')
			               {
	    	                    $('#'+k).val(v);
			               }
		                });
		                
		                if(Object.keys(data['detail']).length>0)
		                {
	                	   $.each(data['detail'],function(k1,v1){
	                           for(var x in v1)
	                           {
	                               $('#'+x+'_'+k1).val(v1[x]);
	                           }
	                       });
		                } 
		                if(Object.keys(data['record']).length >0)
		                {
	                	   $.each(data['record'],function(kr,vr){
	                		   var records= '<div style="float: auto;">'
	                    	   +'<fieldset style="vertical-align: baseline;">'
	                    	   +'<legend >Log thủ'+kr+'</legend>'
	                    	   +'<label>Kẻ cướp đoạt ID:</label>'
	                    	   +'<input type="text" name="auid" id="auid" value="'+vr['auid']+'"/>'
	                    	   +'<label>Mã số mỏ:</label>'
	                    	   +'<input type="text" name="mid" id="mid" value="'+vr['mid']+'"/>'
	                    	   +'<label>Kết quả cướp:</label>'
	                    	   +'<input type="text" name="result" id="result" value="'+vr['result']+'"/>'
	                    	   +'<label>Thời gian cướp:</label>'
	                    	   +'<input type="text" name="rtime" id="rtime" value="'+vr['rtime']+'"/><br/>'
	                    	   +'<label>Quân đoàn kẻ cướp:</label>'
	                    	   +'<input type="text" name="lname" id="lname" value="'+vr['lname']+'"/>'
	                    	   +'<label>Tên kẻ cướp:</label>'
	                    	   +'<input type="text" name="aname" id="aname" value="'+vr['aname']+'"/>'
	                    	   +'<label>Tiền của kẻ cướp đoạt được:</label>'
	                    	   +'<input type="text" name="currency" id="currency" value="'+vr['currency']+'"/>'
	                    	   +'<label>V.phẩm kẻ cướp đoạt được:</label>'
	                    	   +'<input type="text" name="item" id="item" value="'+vr['item']+'"/>'
	                    	   +'</fieldset>'
	                    	   +'</div>';
	                    	   $('#records').append(records);
	    	               });
			     	 }
				     	 return;
			      }
			      $('#error').html('N.vật này chưa tạo mỏ');
             });
 	 	    
 	    }else{
 	 	    $('#error').html("Nhập n.vật này");
 	    }
      });
     
     $('#servers').change(function(){
 	    $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
  	       location.reload();
 	 	});
      });
      
});

</script>
<style type="text/css">
label{vertical-align:left;width: 200px;float:left;}
input{vertical-align:left;width: 150px;float:left;}
body{font-family:tahoma Verdana;font-size:12px;}
#maxtrix input
{
	vertical-align:left;width: 180px;float:left;
}
#maxtrix label
{
vertical-align:left;width: 180px;float:left;
}
</style>
</head>
<body>

<div style="float:left;" >
<form action="" >
<fieldset style="width: 200;">
<legend>Tìm</legend>
<label>Tài khoản ID:</label>
<input type="text"  name="suid" id="suid" style="width: 120px;float: left;"/>
<input type="button" name="search" id="search" value="Tìm" style="width: 50px;float: right;"/>
<label id="error"  style="font-size: medium;color: red;"></label>
</fieldset>
<fieldset style="width: 200;"><legend>DS server</legend>
<select name="servers" id="servers" style="width: 150">
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].'server'.$value['text'].'</option>';
}
?>
</select>
</fieldset>
<fieldset style="width: 200; float:none;"><legend>Nhân vật ID Danh sách</legend>
<select name="userid" id="userid" size="<?php echo count($keys)>0?count($keys)+3:2?>" style="width: 200px;height: 1080px">
<?php
foreach ($keys as $value) {
    $new=substr($value, strpos($value, ':')+1);
    echo "<option value=".$value.">$new</option>";
}
?>
</select>
</fieldset>

</form>
</div>
<div id="res">
<div style="float: auto;">
<fieldset style="vertical-align: baseline;">
<legend >Thông tin n.vật</legend>
<input type="hidden" name="uid" id="uid"/>
<label>Cấp:</label>
<input type="text" name="lv" id="lv"/>
<label>Tích điểm:</label>
<input type="text" name="score" id="score"/>
<label>Thời gian ghép:</label>
<input type="text" name="match" id="match"/>
<label>Kết thúc ghép:</label>
<input type="text" name="end" id="end"/><br/>
<label>Trạng thái:</label>
<input type="text" name="state" id="state"/>
<label>Tổng số quặng:</label>
<input type="text" name="mines" id="mines"/>
</fieldset>
</div>

<div style="float: auto;">
<fieldset style="vertical-align: baseline;">
<legend >Mỏ 1</legend>
<input type="hidden" name="mid_0" id="mid_0"/>
<input type="hidden" name="id_0" id="id_0"/>
<input type="hidden" name="uid_0" id="uid_0"/>
<label>Loại:</label>
<input type="text" name="type_0" id="type_0"/>
<label>Thời gian lấy v.phẩm lần trước:</label>
<input type="text" name="last_item_time_0" id="last_item_time_0"/>
<label>Tăng tốc thời gian kết thúc:</label>
<input type="text" name="speed_end_time_0" id="speed_end_time_0"/>
<label>Thế trận thủ:</label>
<input type="text" name="defend_form_id_0" id="defend_form_id_0"/><br/>
<label>Thiên phú phòng thủ:</label>
<input type="text" name="defend_skill_id_0" id="defend_skill_id_0"/>
<label>Thời gian lấy lần trước:</label>
<input type="text" name="last_reward_time_0" id="last_reward_time_0"/>
<label>Số lần bị cướp v.phẩm:</label>
<input type="text" name="be_rob_item_count_0" id="be_rob_item_count_0"/>
<label>Số lượng bị cướp:</label>
<input type="text" name="be_rob_amount_0" id="be_rob_amount_0"/>
</fieldset>
</div>
<div style="float: auto;">
<fieldset style="vertical-align: baseline;">
<legend >Mỏ 2</legend>
<input type="hidden" name="mid_1" id="mid_1"/>
<input type="hidden" name="id_1" id="id_1"/>
<input type="hidden" name="uid_1" id="uid_1"/>
<label>Loại:</label>
<input type="text" name="type_1" id="type_1"/>
<label>Thời gian lấy v.phẩm lần trước:</label>
<input type="text" name="last_item_time_1" id="last_item_time_1"/>
<label>Tăng tốc thời gian kết thúc:</label>
<input type="text" name="speed_end_time_1" id="speed_end_time_1"/>
<label>Thế trận thủ:</label>
<input type="text" name="defend_form_id_1" id="defend_form_id_1"/><br/>
<label>Thiên phú phòng thủ:</label>
<input type="text" name="defend_skill_id_1" id="defend_skill_id_1"/>
<label>Thời gian lấy lần trước:</label>
<input type="text" name="last_reward_time_1" id="last_reward_time_1"/>
<label>Số lần bị cướp v.phẩm:</label>
<input type="text" name="be_rob_item_count_1" id="be_rob_item_count_1"/>
<label>Số lượng bị cướp:</label>
<input type="text" name="be_rob_amount_1" id="be_rob_amount_1"/>
</fieldset>
</div>
<div style="float: auto;">
<fieldset style="vertical-align: baseline;">
<legend >Mỏ3</legend>
<input type="hidden" name="mid_2" id="mid_2"/>
<input type="hidden" name="id_2" id="id_2"/>
<input type="hidden" name="uid_2" id="uid_2"/>
<label>Loại:</label>
<input type="text" name="type_2" id="type_2"/>
<label>Thời gian lấy v.phẩm lần:</label>
<input type="text" name="last_item_time_2" id="last_item_time_2"/>
<label>Tăng tốc thời gian kết thúc:</label>
<input type="text" name="speed_end_time_2" id="speed_end_time_2"/>
<label>Thiên phú phòng thủ:</label>
<input type="text" name="defend_form_id_2" id="defend_form_id_2"/><br/>
<label>Thiên phú phòng thủ:</label>
<input type="text" name="defend_skill_id_2" id="defend_skill_id_2"/>
<label>Thời gian lấy lần trước:</label>
<input type="text" name="last_reward_time_2" id="last_reward_time_2"/>
<label>Số lần bị cướp v.phẩm:</label>
<input type="text" name="be_rob_item_count_2" id="be_rob_item_count_2"/>
<label>Số lượng bị cướp:</label>
<input type="text" name="be_rob_amount_2" id="be_rob_amount_2"/>
</fieldset>
</div>

<div id="records">
<span style="color: red;" id="error"></span>
</div>


</div>
</body>
</html>

