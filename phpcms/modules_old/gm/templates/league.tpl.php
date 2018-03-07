<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="statics/js/jquery.min.js"></script>
<script type="text/javascript" src="statics/js/jquery.json.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#setLeader').hide(); 
    $('#servers').change(function(){
        $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
           location.reload();
        });
      });
    $('#userid').change(function(e){
        var json=$(this).find("option:selected").attr('info');
        var data=$.parseJSON(json);
        $.each(data,function(k,v){
            $('#'+k).val(v);
        });
        $.post('?m=gm&c=league&a=getMembers',{'id':$(this).val()},function(data){
            $('#members').contents().remove(); 
            $('#setLeader').hide(); 
            var json=$.parseJSON(data);
            $.each(json,function(k,v){  
                $('#members').append('<option info=\''+$.toJSON(v)+'\' value="'+k+'">'+v['mname']+'(lv'+v['mlevel']+')</option>');
            });
        });
        if($('#id').val())
        {
            $('#modify').removeAttr('disabled');
            $('#delete').removeAttr('disabled');
        }else{
            $('#modify').attr('disabled','disabled');
            $('#delete').attr('disabled','disabled');
        }
    });
    $('#members').change(function(){
      	  var json=$(this).find("option:selected").attr('info');
          var data=$.parseJSON(json); 
          $.each(data,function(k,v){
              $('#'+k).html(v);
              
          });
          $('#setLeader').show(); 
    }); 
    $('#setLeader').click(function(){
        $.post('?m=gm&c=league&a=setLeader',{'sid':$('#servers').val(),'lid':$('#id').val(),'mid':$('#mid').html()},function(data){
            alert(data.msg);
        },'json');
    });
    $('#modify').click(function(){
        var post={};
        post.sid=$('#servers').val();
        post.lid=$('#id').val();
        post.content={};
        $('#leagueInfo >input').each(function(k,v){
			post.content[$(v).attr('id')]=$(v).val();
        });
        $('#leagueInfo >select').each(function(k,v){
			post.content[$(v).attr('id')]=$(v).val();
        });
    	$.post('?m=gm&c=league&a=modify',post,function(data){
    		alert(data.msg);
    	},'json');
    });
    $('#delete').click(function(){
    	$.post('?m=gm&c=league&a=delete',{'sid':$('#servers').val(),'lid':$('#id').val()},function(data){
    		alert(data.msg);
    	},'json');
    });
});
</script>
<style type="text/css">
 label{vertical-align:left;width: 100px;float:left;height:50px;} 
 input{vertical-align:left;width: 150px;float:left;} 
 select{vertical-align:left;width: 150px;float:left;} 
body{font-family:tahoma Verdana;font-size:12px;}
</style>
</head>
<body>
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

<fieldset style="width: 200; float:left;"><legend>DS quân đoàn</legend>
<select name="userid" id="userid" size="<?php echo count($keys)>0?count($keys)+1:2?>" style="width: 200px;height: 800px">
<?php
foreach ($leagues as $value) {
    foreach ($value as $k=>&$v)
    {
        if(is_string($v))
        {
            $v=str_replace(array("\r","\n"),"",$v);
            $v=urlencode(trim($v));
        }
    }
    $info=urldecode(json_encode($value));
    echo "<option info='".$info."' value='".$value['id']."'>".urldecode($value['name'])."(".$value['mcount']."Người)</option>";
}
?>
</select>
</fieldset>
<fieldset id="leagueInfo" style="vertical-align: baseline;float: auto;">
<legend>Thông tin quân đoàn</legend>
<label>Mã:</label>
<input id="id" name="id" type="text" disabled="disabled" /> 
<label>Xếp hạng:</label>
<input id="rank" name="rank"  type="text" />
<label>Cấp:</label>
<select id="level" name="level">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
</select>
<label>Exp:</label>
<input id="exp"   name="exp"  type="text" />
<label>Phê duyệt:</label>
<input id="auto" name="auto"  type="text" style="clear:right;" />
<label style="clear:left;">Số thành viên:</label>
<input id="mcount" name="mcount"  type="text" />
<label>Số thành viên tối đa:</label>
<input id="maxcount" name="maxcount"  type="text" disabled="disabled"/>
<label>Hành động:</label>
<input id="money" name="money"  type="text" />
<label>Đoàn trưởng ID:</label>
<input id="leader" name="leader"  type="text" />
<label>Đoàn phó ID:</label>
<input id="tleader" name="tleader"  type="text" disabled="disabled"/>

<label style="clear:left;">Thời gian tạo:</label>
<input id="ctime" name="ctime"  type="text" />
<label>Tên:</label>.
<input id="name" name="name"  type="text" />
<label>Thông báo:</label>
<input id="broad"  name="broad"  type="text" />
<label>Ngày cập nhật:</label>
<input id="date" name="date"  type="text" />
<label>Sôi nổi:</label>
<input id="active" name="active"  type="text" />
<label style="clear:left;">Thời gian Đoàn trưởng đăng xuất:</label>
<input id="logout" name="logout"  type="text" />
<?php
$admin_group=array(1,8);
$priv=in_array($_SESSION['roleid'],$admin_group);
if(isset($priv)&&!empty($priv)){?>
<input id="modify" name="modify"  type="button"  value="Sửa" disabled="disabled"/>
<input id="delete" name="delete"  type="button" value="Xóa" disabled="disabled"/>
<?php }?>
</fieldset>
<fieldset style="float:none;clear:none;">
<legend>Thông tin thành viên</legend>
<select name="members" id="members" size="1" multiple="multiple" style="width: 200px;height: 800px;float: left;margin-right:  10px;">
</select> 
<label>Thành viên ID:</label><label id="mid"></label>
<label>Tên thành viên:</label><label id="mname"></label>
<label>Cấp:</label><label id="mlevel"></label>
<label>VIP Cấp:</label><label id="mviplevel"></label>

<label>Chức vị:</label><label id="mop"></label>
<label>Chiến lực:</label><label id="mbp"></label>
<label>Tổng số lần cống hiến:</label><label id="mtdc"></label>
<label>Exp quan chức:</label><label id="mgexp"></label>
<br/>
<label>Số lần tham gia quân đoàn chiến:</label><label id="mfcont"></label>
<label>Ảnh đại diện ID:</label><label id="mmainid"></label>
<label>Phe ID:</label><label id="mcampid"></label>
<label>Chức vị phe ID:</label><label id="mcpos"></label>
<br/>
<label>Cống hiến quân đoàn hiện tại:</label><label id="mlegong"></label>
<label>Thời gian vào:</label><label id="mjtime"></label>
<label>Thời gian offline:</label><label id="mofftime"></label>
<label>Thời gian offline:</label><label id="mstatus"></label>
<input type="button" id="setLeader"  value="Thời gian offline" />
</fieldset>
</body>
</html>