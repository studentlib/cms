<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">

function checkUpate(data)
{
	$('#check').attr('disabled','');
}
function updateCarbon()
{   
	var data={};
	$('#carbon').find('input[type="text"]').each(function(k,v){
		data[$(v).attr('line')]=({'line':$(v).attr('line'),'chapter':$(v).val()});
	}); 
	$.getJSON("?m=gm&c=gm&a=updateCarbon&id="+$('#uid').val()+'&sid='+$('#servers').val(),{'data':data},function(dt){
	    console.log(dt);  
    });
}
$(document).ready(function(){

	$("#userid").change(function(){
		var th=this;
	     $.getJSON("?m=gm&c=gm&a=getInfo&id="+$(this).val(),function(data){
		      if(data)
		      {
	               $.each(data,function(k,v){
	                    $('#'+k).val(v);
	                });
	               $.getJSON("?m=gm&c=gm&a=getExtraInfo&id="+$(th).val()+'&sid='+$('#servers').val(),function(data){
	            	    $.each(data['trial'],function(k,v){
	            	    	$('#'+k).val(v);
		            	});
	            	    $('#carbon').contents().remove();
		            	var carbon='<legend>Thông tin ải</legend>';
		            	
		            	var i=1;
	            	    $.each(data['carbon'],function(k,v){
	            	    	carbon+='<div><label>Ải'+i+'</label><input type="text" line="'+k+'" value="'+v['chapter_id']+'-'+v['scene_id']+'" /></div>';
	            	    	i++;
		            	});
	            	    if(i>1)
	            	    {
	            	    	carbon+='<input type="button" id="check" value="Cập nhật" disabled="disabled"/>';
	            	    	
	            	    }
	            	    $('#carbon').append(carbon);
	            	    $('#carbon').find('input').keyup(checkUpate);
	            	    $('#check').click(updateCarbon);
	            	    $('#zone').val(data['arena']['zone']);
	            	    $('#rank').val(data['arena']['rank']);
	            	    $('#arcount').val(data['arena']['count']);
	            	    
	            	    $.each(data['form'],function(fk,fv){
	            	        i=1;
		            	    $.each(fv,function(mk,mv){
		            	        $('#form'+mk).val(mv);
			            	});
	            	    });
	            	    $('#mtid').val(data['mount']['tid']);
	            	    $('#mgrow').val(data['mount']['grow']);
	            	    $('#me1').val(data['mount']['1']);
	            	    $('#me2').val(data['mount']['2']);
	            	    $('#me3').val(data['mount']['3']);
	            	    $('#me4').val(data['mount']['4']);
	            	    $('#me5').val(data['mount']['5']);
	            	    $('#me6').val(data['mount']['6']);
	            	    $('#skill').contents().remove();
	            	    var skill='<legend>Thông tin kĩ năng</legend>';
	            	    i=1;
	            	    $.each(data['skill']['skills'],function(k,v){ 
	            	        skill+='<div><label>Kĩ năng'+i+'</label><input value="'+v['pos']+'/'+v['level']+'"/></div>';
	            	        i++;
		            	}); 
	            	    
	            	    $('#skill').append(skill);
	            	    i=1;
	            	    $('#equiped').contents().remove();
                        var equiped='<legend>Kĩ năng đang dùng</legend>';
	            	    $.each(data['skill']['equiped'],function(k,v){
	            	    	equiped+='<div><label>Kĩ năng trang bị'+i+'</label><input value="'+v['sid']+'"/></div>';
	            	    	i++;
	                    });
	            	    $('#equiped').append(equiped);
	                    
	                    
		           });
		      }

		    	
		 });
     });
    $('#save').click(function(){
        if($('#uid').val()!='')
        {
       	    $('#msg').html();
	    if($('#level').val()>120){alert('等级不能超过就120级');exit;}
            if($('#viplevel').val()>21){alert('VIP等级不超过21级');exit;}
            if($('#gold').val()>2147483648){alert('金币不能超过2147483648');exit;}
    	     $.post('?m=gm&c=gm&a=updateInfo&sid='+$('#servers').val(),$('#edit').serialize(),function(data){
    	     if(data&&data['msg'])
    	     { 
        	     $('#msg').html(data['msg']);
    	     }
        	},'json');
         }
       });
    $('#kick').click(function(){
        if($('#uid').val()!='')
        { 
       	    $('#msg').html();
    	     $.post('?m=gm&c=gm&a=kickOut',{'uid':$('#uid').val(),'sid':$('#servers').val()},function(data){
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
	    	 $.getJSON("?m=gm&c=gm&a=getInfo&id="+$('#suid').val(),function(data){
		    	 if(data&&data['msg']!=undefined)
		    	 { 
		    		 $('#msg').html(data['msg']);
			    	 return;
		    	 }
                 $.each(data,function(k,v){
                     $('#'+k).val(v);
                 });
                 $.getJSON("?m=gm&c=gm&a=getExtraInfo&id="+$('#suid').val()+'&sid='+$('#servers').val(),function(data){
                     $.each(data['trial'],function(k,v){
                         $('#'+k).val(v);
                     });
                     $('#carbon').contents().remove();
                     var carbon='<legend>Thông tin ải</legend>';
                     
                     var i=1;
                     $.each(data['carbon'],function(k,v){
                         carbon+='<div><label>Ải'+i+'</label><input line="'+k+'" type="text" value="'+v['chapter_id']+'-'+v['scene_id']+'" /></div>';
                         i++;
                     });
                     if(i>1)
                     {
                         carbon+='<input type="button" id="check" value="Cập nhật" disabled="disabled"/>';
                         
                     }
                     $('#carbon').append(carbon);
                     $('#check').click(updateCarbon);
                     $('#carbon').find('input').keyup(checkUpate);

                     $('#zone').val(data['arena']['zone']);
                     $('#rank').val(data['arena']['rank']);
                     $('#arcount').val(data['arena']['count']);
                     
                     $.each(data['form'],function(fk,fv){
                         i=1;
                         $.each(fv,function(mk,mv){
                             $('#form'+mk).val(mv);
                         });
                     });
                     $('#mtid').val(data['mount']['tid']);
                     $('#mgrow').val(data['mount']['grow']);
                     $('#me1').val(data['mount']['1']);
                     $('#me2').val(data['mount']['2']);
                     $('#me3').val(data['mount']['3']);
                     $('#me4').val(data['mount']['4']);
                     $('#me5').val(data['mount']['5']);
                     $('#me6').val(data['mount']['6']);
                     $('#skill').contents().remove();
                     var skill='<legend>Thông tin kĩ năng</legend>';
                     i=1;
                     $.each(data['skill']['skills'],function(k,v){ 
                         skill+='<div><label>Kĩ năng'+i+'</label><input value="'+v['pos']+'/'+v['level']+'"/></div>';
                         i++;
                     }); 
                     
                     $('#skill').append(skill);
                     i=1;
                     $('#equiped').contents().remove();
                     var equiped='<legend>Kĩ năng đang dùng</legend>';
                     $.each(data['skill']['equiped'],function(k,v){
                         equiped+='<div><label>Kĩ năng trang bị'+i+'</label><input value="'+v['sid']+'"/></div>';
                         i++;
                     });
                     $('#equiped').append(equiped);
                     
                     
                });
             });
 	 	    
 	    }else{
 	 	    $('#error').html("Nhập nhân vật ID");
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
label{vertical-align:left;width: 180px;float:left;}
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
<fieldset style="width: 225;">
<legend>Tìm</legend>
<label>Tài khoản ID:</label>
<input type="text"  name="suid" id="suid" style="width: 120px;float: left;"/>
<input type="button" name="search" id="search" value="Tìm" style="width: 50px;float: right;"/>
<label id="error"  style="font-size: medium;color: red;"></label>
</fieldset>
<fieldset style="width: 200;"><legend>Danh sách server</legend>
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
<select name="userid" id="userid" size="<?php echo count($keys)>0?count($keys)+1:2?>" style="width: 200px;height: 800px">
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
<div style="float: auto;">
<form action="" id="edit" name="edit">
<fieldset style="vertical-align: baseline;">
<legend >Thông tin n.vật</legend>
<input type="hidden"" name="uid" id="uid"/>
<label>Tên tài khoản:</label>
<input type="text" name="acount" id="acount"/>
<label>Tên n.vật:</label>
<input type="text" name="name" id="name"/>
<label>Ảnh đại diện:</label>
<input type="text" name="pic_id" id="pic_id"/>
<label>Giới tính:</label>
<input type="text" name="sex" id="sex"/><br/>
<label>Exp hiện tại:</label>
<input type="text" name="exp" id="exp"/>
<label>Cấp:</label>
<input type="text" name="level" id="level"/>
<label>Hiện tại vip Exp :</label>
<input type="text" name="vipexp" id="vipexp"/>
<label>VIP Cấp:</label>
<input type="text" name="viplevel" id="viplevel"/><br/>
<label>Thời gian offline:</label>
<input type="text" name="logout_time" id="logout_time"/>
<label>Vàng:</label>
<input type="text" name="gold" id="gold"/>
<label>KNB:</label>
<input type="text" name="diamond" id="diamond"/>
<label>Thể lực hiện tại:</label>
<input type="text" name="ap" id="ap"/><br/>
<label>Số lượng thuốc thể lực:</label>
<input type="text" name="apnum" id="apnum"/>
<label>Số lần đã dùng để mua thể lực:</label>
<input type="text" name="apbuynum" id="apbuynum"/>
<label>Thể lực tối đa:</label>
<input type="text" name="apmax" id="apmax"/>
<label>Sức bền:</label>
<input type="text" name="stamina" id="stamina"/><br/>
<label>Sức bền tối đa:</label>
<input type="text" name="stamina_max" id="stamina_max"/>
<label>Công Huân:</label>
<input type="text" name="gong" id="gong"/>
<label>Danh vọng:</label>
<input type="text" name="honor" id="honor"/>
<label>Bí Cảnh id:</label>
<input type="text" name="mapid" id="mapid"/><br/>
<label>Chủng tộc:</label>
<input type="text" name="race" id="race"/>
<label>Thời gian tạo:</label>
<input type="text" name="create_time" id="create_time"/>
<label>Mảnh t.bị hiện tại:</label>
<input type="text" name="card" id="card"/>
<label>gm Cấp:</label>
<input type="text" name="gmlevel" id="gmlevel"/><br/>
<label>Hồn Ngọc:</label>
<input type="text" name="jade" id="jade"/>
<label>Thời gian hồi buổi sáng:</label>
<input type="text" name="firstapretime" id="firstapretime"/>
<label>Thời gian hồi buổi chiều:</label>
<input type="text" name="secondapretime" id="secondapretime"/>
<label>T.gian hồi sức bền sáng:</label>
<input type="text" name="firststaminaretime" id="firststaminaretime"/><br/>
<label>T.gian hồi sức bền chiều:</label>
<input type="text" name="secondstaminaretime" id="secondstaminaretime"/>
<label>Đan thể lực hồi:</label>
<input type="text" name="apfrompillvalue" id="apfrompillvalue"/>
<label>Đan bền hồi:</label>
<input type="text" name="staminafrompillvalue" id="staminafrompillvalue"/>
<label>Hồn tướng:</label>
<input type="text" name="soul" id="soul"/><br/>
<label>Thể lực hồi phục từ bạn bè:</label>
<input type="text" name="apfriend" id="apfriend"/>
<label>Thời gian chat sau cùng:</label>
<input type="text" name="lastchattime" id="lastchattime"/>
<label>Kinh nghiệm:</label>
<input type="text" name="exploit" id="exploit"/>
<label>Lệnh hồi sinh:</label>
<input type="text" name="relive" id="relive"/>
<br/>
<label>Chiến lực:</label>
<input type="text" name="battlepower" id="battlepower"/>
<label>Nguyên liệu:</label>
<input type="text" name="fodder" id="fodder"/>
<label>Cống hiến:</label>
<input type="text" name="nationcontribute" id="nationcontribute"/>
<label>Quân đoàn ID:</label>
<input type="text" name="leagueid" id="leagueid"/>
<br/>
<label>Phe ID:</label>
<input type="text" name="nationid" id="nationid"/>
<label>Cống hiến phe:</label>
<input type="text" name="score" id="score"/>
<label>Xe lương phe:</label>
<input type="text" name="car" id="car"/>
<?php
$admin_group=array(1,8);
$priv=in_array($_SESSION['roleid'],$admin_group);
if(isset($priv)&&!empty($priv)){?>
<input type="button" value="Lưu" name="save" id="save"/>
<input type="button" value="Đá offline" name="kick" id="kick"/>
<?php }?>
</fieldset>
</form>
<label id="msg" style="font-size: medium;color: red;"></label>

<form action="">
<fieldset>
<legend>Thông tin đấu trường</legend>
<label>Khu đấu:</label>
<input type="text"  id="zone" readonly="readonly"/>
<label>Xếp hạng:</label>
<input type="text"  id="rank" readonly="readonly"/>
<label>Số lần thách đấu hôm nay:</label>
<input type="text"  id="arcount" readonly="readonly"/>
</fieldset>
<fieldset>
<legend>Thông tin thí luyện</legend>
<label>Tiến độ ải:</label>
<input type="text" name="unCurrentPoint" id="unCurrentPoint" readonly="readonly"/>
<label>Tiến độ ải phụ:</label>
<input type="text" name="unProcessPointCount" id="unProcessPointCount" readonly="readonly"/>
</fieldset>
<fieldset id="carbon">
<legend>Thông tin ải</legend>
</fieldset>

<fieldset id="maxtrix" style="float:auto;">
<legend>Thông tin thế trận</legend>
<div>
<label>Vị trí1:</label><input id="form1" value="" readonly="readonly"/> 
<label>Vị trí2:</label><input id="form2" value="" readonly="readonly"/>
</div>
<div>
<label>Vị trí3:</label><input id="form3" value="" readonly="readonly"/> 
<label>Vị trí4:</label><input id="form4" value="" readonly="readonly"/> 
</div>
<div>
<label>Vị trí5:</label><input id="form5" value="" readonly="readonly"/> 
<label>Vị trí6:</label><input id="form6" value="" readonly="readonly"/> 
</div>
</fieldset>
<fieldset id="mount">
<legend>Thông tin thú cưỡi</legend>
<div>
<label>Module ID:</label><input id="mtid" value="" readonly="readonly"/> 
<label>Trưởng thành thú cưỡi:</label><input id="mgrow" value="" readonly="readonly"/>
</div>
<div>
<label>Trang bị thú cưỡi1:</label><input id="me1" value="" readonly="readonly"/> 
<label>Trang bị thú cưỡi2:</label><input id="me2" value="" readonly="readonly"/>
<label>Trang bị thú cưỡi3:</label><input id="me3" value="" readonly="readonly"/> 
<label>Trang bị thú cưỡi4:</label><input id="me4" value="" readonly="readonly"/>
<label>Trang bị thú cưỡi5:</label><input id="me5" value="" readonly="readonly"/> 
<label>Trang bị thú cưỡi6:</label><input id="me6" value="" readonly="readonly"/>
</div>
</fieldset>

<fieldset id="skill" style="float:auto;">
<legend>Thông tin kĩ năng</legend>

</fieldset>
<fieldset id="equiped" style="float:auto;">
<legend>Kĩ năng đang sử dụng</legend>

</fieldset>

</form>

</div>
</body>
</html>

