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
    $('#findLegion').click(function(){
	var legionNumber=$('#legionNumber').val();
	var json=$('#userid').find("option[value='"+legionNumber+"']").attr('info');
	if(json==undefined){
	    alert('不存在该军团编号');
	    return;
	}
        var data=$.parseJSON(json);
        $.each(data,function(k,v){
	    $('#'+k).val();
            $('#'+k).val(v);
        });
	$.post('?m=gm&c=league&a=getMembers',{'id':$('#legionNumber').val()},function(data){
            $('#members').contents().remove(); 
            $('#setLeader').hide(); 
            var json=$.parseJSON(data);
            $.each(json,function(k,v){  
                $('#members').append('<option info=\''+$.toJSON(v)+'\' value="'+k+'">'+v['mname']+'(lv'+v['mlevel']+')</option>');
            });
        });
	$('#modify').removeAttr('disabled');
        $('#delete').removeAttr('disabled');
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
 label{vertical-align:left;width: 100px;float:left;} 
 input{vertical-align:left;width: 150px;float:left;} 
 select{vertical-align:left;width: 150px;float:left;} 
body{font-family:tahoma Verdana;font-size:12px;}
</style>
</head>
<body>
<fieldset style="width: 500;"><legend>区服列表</legend>
<select name="servers" id="servers" style="width: 150;margin-right:15px">
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].'区 '.$value['text'].'</option>';
}
?>
</select>
<label><b>军团查找:</b></label>
<input type='text' id='legionNumber' style="width: 100px;margin-right:2px">
<input type="button" id='findLegion' value='查找' style="width: 80px;">
</fieldset>
<fieldset style="width: 200; float:left;"><legend>军团列表</legend>
<select name="userid" id="userid" size="<?php echo count($keys)>0?count($keys)+1:2?>" style="width: 200px;height: 720px">
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
    echo "<option info='".$info."' value='".$value['id']."'>".urldecode($value['name'])."(".$value['mcount']."人)</option>";
}
?>
</select>
</fieldset>
<fieldset id="leagueInfo" style="vertical-align: baseline;float: auto;">
<legend>军团信息</legend>
<label>编号:</label>
<input id="id" name="id" type="text" disabled="disabled" /> 
<label>排名:</label>
<input id="rank" name="rank"  type="text" />
<label>等级:</label>
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
<label>经验:</label>
<input id="exp"   name="exp"  type="text" />
<label>审批:</label>
<input id="auto" name="auto"  type="text" style="clear:right;" />
<label style="clear:left;">成员数:</label>
<input id="mcount" name="mcount"  type="text" />
<label>最大成员数:</label>
<input id="maxcount" name="maxcount"  type="text" disabled="disabled"/>
<label>行动力:</label>
<input id="money" name="money"  type="text" />
<label>团长ID:</label>
<input id="tleader" name="tleader"  type="text" />
<label>团副ID:</label>
<input id="leader" name="leader"  type="text" disabled="disabled"/>

<label style="clear:left;">创建时间:</label>
<input id="ctime" name="ctime"  type="text" />
<label>名称:</label>.
<input id="name" name="name"  type="text" />
<label>公告:</label>
<input id="broad"  name="broad"  type="text" />
<label>更新日期:</label>
<input id="date" name="date"  type="text" />
<label>活跃度:</label>
<input id="active" name="active"  type="text" />
<label style="clear:left;">团长登出时间:</label>
<input id="logout" name="logout"  type="text" />
<?php
$admin_group=array(1,8);
$priv=in_array($_SESSION['roleid'],$admin_group);
if(isset($priv)&&!empty($priv)){?>
<input id="modify" name="modify"  type="button"  value="修改" disabled="disabled"/>
<input id="delete" name="delete"  type="button" value="删除" disabled="disabled"/>
<?php }?>
</fieldset>
<fieldset style="float:none;clear:none;">
<legend>成员信息</legend>
<select name="members" id="members" size="1" multiple="multiple" style="width: 200px;height: 600px;float: left;margin-right:  10px;">
</select> 
<label>成员ID:</label><label id="mid"></label>
<label>成员名:</label><label id="mname"></label>
<label>等级:</label><label id="mlevel"></label>
<label>VIP等级:</label><label id="mviplevel"></label>

<label>职位:</label><label id="mop"></label>
<label>战斗力:</label><label id="mbp"></label>
<label>总捐献次数:</label><label id="mtdc"></label>
<label>官职经验:</label><label id="mgexp"></label>
<br/>
<label>参加团战次数:</label><label id="mfcont"></label>
<label>头像ID:</label><label id="mmainid"></label>
<label>阵营ID:</label><label id="mcampid"></label>
<label>阵营官职ID:</label><label id="mcpos"></label>
<br/>
<label>当前军团贡献:</label><label id="mlegong"></label>
<label>加入时间:</label><label id="mjtime"></label>
<label>离线时间:</label><label id="mofftime"></label>
<label>在线状态:</label><label id="mstatus"></label>
<input type="button" id="setLeader"  value="设为团长" />
</fieldset>
</body>
</html>
