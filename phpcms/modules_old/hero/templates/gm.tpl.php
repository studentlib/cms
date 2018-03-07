<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("#userid").change(function(){
	     $.getJSON("?m=gm&c=gm&a=getInfo&id="+$(this).val(),function(data){
	    	    $.each(data,function(k,v){
	    	    	$('#'+k).val(v);
		    	});
		 });
     });
    $('#save').click(function(){
        if($('#uid').val()!='')
        {
       	    $('#msg').html();
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
             });
 	 	    
 	    }else{
 	 	    $('#error').html("请输入角色ID");
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
</style>
</head>
<body>
<div style="float:left;" >
<form action="" >
<fieldset style="width: 225;"><legend>查找</legend>
<label>账号ID:</label>
<input type="text" name="suid" id="suid" style="width: 120px;float: left;"/>
<input type="button" name="search" id="search" value="查找" style="width: 50px;float: right;"/>
<label id="error"  style="font-size: medium;color: red;"></label>
<fieldset style="width: 200;"><legend> server服列表</legend>
<select name="servers" id="servers" style="width: 150">
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].' server '.$value['text'].'</option>';
}
?>
</select>
</fieldset>
<fieldset style="width: 200; float:none;"><legend>角色ID列表</legend>
<select name="userid" id="userid" size="<?php echo count($keys);?>" style="width: 200px;height: 800px">
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
<legend >编辑表单</legend>
<input type="hidden"" name="uid" id="uid"/>
<label>账号名称:</label>
<input type="text" name="acount" id="acount"/>
<label>角色名称:</label>
<input type="text" name="name" id="name"/>
<label>头像:</label>
<input type="text" name="pic_id" id="pic_id"/>
<label>性别:</label>
<input type="text" name="sex" id="sex"/><br/>
<label>当前经验:</label>
<input type="text" name="exp" id="exp"/>
<label>等级:</label>
<input type="text" name="level" id="level"/>
<label>当前vip经验:</label>
<input type="text" name="vipexp" id="vipexp"/>
<label>VIP等级:</label>
<input type="text" name="viplevel" id="viplevel"/><br/>
<label>下线时间点:</label>
<input type="text" name="logout_time" id="logout_time"/>
<label>金币:</label>
<input type="text" name="gold" id="gold"/>
<label>元宝:</label>
<input type="text" name="diamond" id="diamond"/>
<label>当前体力:</label>
<input type="text" name="ap" id="ap"/><br/>
<label>体力药剂数量:</label>
<input type="text" name="apnum" id="apnum"/>
<label>当天购买体力已使用次数:</label>
<input type="text" name="apbuynum" id="apbuynum"/>
<label>体力上限:</label>
<input type="text" name="apmax" id="apmax"/>
<label>耐力:</label>
<input type="text" name="stamina" id="stamina"/><br/>
<label>耐力上限:</label>
<input type="text" name="stamina_max" id="stamina_max"/>
<label>帮贡:</label>
<input type="text" name="gong" id="gong"/>
<label>声望:</label>
<input type="text" name="honor" id="honor"/>
<label>秘境id:</label>
<input type="text" name="mapid" id="mapid"/><br/>
<label>种族:</label>
<input type="text" name="race" id="race"/>
<label>创建时间:</label>
<input type="text" name="create_time" id="create_time"/>
<label>当前装备卡片:</label>
<input type="text" name="card" id="card"/>
<label>gm等级:</label>
<input type="text" name="gmlevel" id="gmlevel"/><br/>
<label>魂玉:</label>
<input type="text" name="jade" id="jade"/>
<label>上午整点恢复时间点:</label>
<input type="text" name="firstapretime" id="firstapretime"/>
<label>下午整点恢复时间点:</label>
<input type="text" name="secondapretime" id="secondapretime"/>
<label>耐力上午整点恢复时间点:</label>
<input type="text" name="firststaminaretime" id="firststaminaretime"/><br/>
<label>耐力下午整点恢复时间点:</label>
<input type="text" name="secondstaminaretime" id="secondstaminaretime"/>
<label>体力丸回复的体力值    :</label>
<input type="text" name="apfrompillvalue" id="apfrompillvalue"/>
<label>耐力丸回复的耐力值:</label>
<input type="text" name="staminafrompillvalue" id="staminafrompillvalue"/>
<label>将魂:</label>
<input type="text" name="soul" id="soul"/><br/>
<label>好友系统恢复的体力:</label>
<input type="text" name="apfriend" id="apfriend"/>
<label>玩家最后聊天时间:</label>
<input type="text" name="lastchattime" id="lastchattime"/>
<label>玩家功勋:</label>
<input type="text" name="exploit" id="exploit"/>
<label>玩家复活令:</label>
<input type="text" name="relive" id="relive"/>
<br/>
<label>战斗力:</label>
<input type="text" name="battlepower" id="battlepower"/>
<label>饲料:</label>
<input type="text" name="fodder" id="fodder"/>
<label>国贡:</label>
<input type="text" name="nationcontribute" id="nationcontribute"/>
<label>军团ID:</label>
<input type="text" name="leagueid" id="leagueid"/>
<br/>
<label>阵营ID:</label>
<input type="text" name="nationid" id="nationid"/>
<label>阵营贡献:</label>
<input type="text" name="score" id="score"/>
<label>阵营粮车:</label>
<input type="text" name="car" id="car"/>
<input type="button" value="Lưu" name="save" id="save"/>
<input type="button" value="踢下线" name="kick" id="kick"/>
</fieldset>
</form>
<label id="msg" style="font-size: medium;color: red;"></label>
</div>
</body>
</html>

