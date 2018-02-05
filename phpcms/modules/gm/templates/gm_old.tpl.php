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
		    console.log(data);  
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
		            	var carbon='<legend>关卡信息</legend>';
		            	
		            	var i=1;
	            	    $.each(data['carbon'],function(k,v){
	            	    	carbon+='<div><label>关卡'+i+'</label><input type="text" line="'+k+'" value="'+v['chapter_id']+'-'+v['scene_id']+'" /></div>';
	            	    	i++;
		            	});
	            	    if(i>1)
	            	    {
	            	    	carbon+='<input type="button" id="check" value="更新" disabled="disabled"/>';
	            	    	
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
	            	    var skill='<legend>技能信息</legend>';
	            	    i=1;
	            	    $.each(data['skill']['skills'],function(k,v){ 
	            	        skill+='<div><label>技能'+i+'</label><input value="'+v['pos']+'/'+v['level']+'"/></div>';
	            	        i++;
		            	}); 
	            	    
	            	    $('#skill').append(skill);
	            	    i=1;
	            	    $('#equiped').contents().remove();
                        var equiped='<legend>正在使用的技能</legend>';
	            	    $.each(data['skill']['equiped'],function(k,v){
	            	    	equiped+='<div><label>装备技能'+i+'</label><input value="'+v['sid']+'"/></div>';
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
                     var carbon='<legend>关卡信息</legend>';
                     
                     var i=1;
                     $.each(data['carbon'],function(k,v){
                         carbon+='<div><label>关卡'+i+'</label><input line="'+k+'" type="text" value="'+v['chapter_id']+'-'+v['scene_id']+'" /></div>';
                         i++;
                     });
                     if(i>1)
                     {
                         carbon+='<input type="button" id="check" value="更新" disabled="disabled"/>';
                         
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
                     var skill='<legend>技能信息</legend>';
                     i=1;
                     $.each(data['skill']['skills'],function(k,v){ 
                         skill+='<div><label>技能'+i+'</label><input value="'+v['pos']+'/'+v['level']+'"/></div>';
                         i++;
                     }); 
                     
                     $('#skill').append(skill);
                     i=1;
                     $('#equiped').contents().remove();
                     var equiped='<legend>正在使用的技能</legend>';
                     $.each(data['skill']['equiped'],function(k,v){
                         equiped+='<div><label>装备技能'+i+'</label><input value="'+v['sid']+'"/></div>';
                         i++;
                     });
                     $('#equiped').append(equiped);
                     
                     
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
<legend>查找</legend>
<label>账号ID:</label>
<input type="text"  name="suid" id="suid" style="width: 120px;float: left;"/>
<input type="button" name="search" id="search" value="查找" style="width: 50px;float: right;"/>
<label id="error"  style="font-size: medium;color: red;"></label>
</fieldset>
<fieldset style="width: 200;"><legend>区服列表</legend>
<select name="servers" id="servers" style="width: 150">
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].'区 '.$value['text'].'</option>';
}
?>
</select>
</fieldset>
<fieldset style="width: 200; float:none;"><legend>角色ID列表</legend>
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
<legend >角色信息</legend>
<input type="hidden" name="uid" id="uid"/>
<label>账号名称:</label>
<input type="text" name="acount" id="acount" disabled='disabled'/>
<label>角色名称:</label>
<input type="text" name="name" id="name" disabled='disabled'/>
<label>头像:</label>
<input type="text" name="pic_id" id="pic_id" disabled='disabled'/>
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
<input type="text" name="logout_time" id="logout_time" disabled='disabled'/>
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
<label>功勋:</label>
<input type="text" name="gong" id="gong"/>
<label>声望:</label>
<input type="text" name="honor" id="honor"/>
<label>秘境id:</label>
<input type="text" name="mapid" id="mapid"/><br/>
<label>种族:</label>
<input type="text" name="race" id="race"/>
<label>创建时间:</label>
<input type="text" name="create_time" id="create_time" disabled='disabled'/>
<label>当前装备卡片:</label>
<input type="text" name="card" id="card"/>
<label>gm等级:</label>
<input type="text" name="gmlevel" id="gmlevel"/><br/>
<label>魂玉:</label>
<input type="text" name="jade" id="jade"/>
<label>上午整点恢复时间点:</label>
<input type="text" name="firstapretime" id="firstapretime" disabled='disabled'/>
<label>下午整点恢复时间点:</label>
<input type="text" name="secondapretime" id="secondapretime" disabled='disabled'/>
<label>耐力上午整点恢复时间点:</label>
<input type="text" name="firststaminaretime" id="firststaminaretime" disabled='disabled'/><br/>
<label>耐力下午整点恢复时间点:</label>
<input type="text" name="secondstaminaretime" id="secondstaminaretime" disabled='disabled'/>
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
<label>阅历:</label>
<input type="text" name="exploit" id="exploit"/>
<label>玩家复活令:</label>
<input type="text" name="relive" id="relive"/>
<br/>
<label>战斗力:</label>
<input type="text" name="battlepower" id="battlepower" disabled='disabled'/>
<label>饲料:</label>
<input type="text" name="fodder" id="fodder"/>
<label>国贡:</label>
<input type="text" name="nationcontribute" id="nationcontribute"/>
<label>军团ID:</label>
<input type="text" name="leagueid" id="leagueid" disabled='disabled'/>
<br/>
<label>阵营ID:</label>
<input type="text" name="nationid" id="nationid" disabled='disabled'/>
<label>阵营贡献:</label>
<input type="text" name="score" id="score"/>
<label>阵营粮车:</label>
<input type="text" name="car" id="car"/>
<?php
$admin_group=array(1,8);
$priv=in_array($_SESSION['roleid'],$admin_group);
if(isset($priv)&&!empty($priv)){?>
<input type="button" value="保存" name="save" id="save"/>
<input type="button" value="踢下线" name="kick" id="kick"/>
<?php }?>
</fieldset>
</form>
<label id="msg" style="font-size: medium;color: red;"></label>

<form action="">
<fieldset>
<legend>竞技场信息</legend>
<label>赛区:</label>
<input type="text"  id="zone" readonly="readonly"/>
<label>排名:</label>
<input type="text"  id="rank" readonly="readonly"/>
<label>今日挑战次数:</label>
<input type="text"  id="arcount" readonly="readonly"/>
</fieldset>
<fieldset>
<legend>试炼信息</legend>
<label>关卡进度:</label>
<input type="text" name="unCurrentPoint" id="unCurrentPoint" readonly="readonly"/>
<label>子关卡进度:</label>
<input type="text" name="unProcessPointCount" id="unProcessPointCount" readonly="readonly"/>
</fieldset>
<fieldset id="carbon">
<legend>关卡信息</legend>
</fieldset>

<fieldset id="maxtrix" style="float:auto;">
<legend>阵型信息</legend>
<div>
<label>阵位1:</label><input id="form1" value="" readonly="readonly"/> 
<label>阵位2:</label><input id="form2" value="" readonly="readonly"/>
</div>
<div>
<label>阵位3:</label><input id="form3" value="" readonly="readonly"/> 
<label>阵位4:</label><input id="form4" value="" readonly="readonly"/> 
</div>
<div>
<label>阵位5:</label><input id="form5" value="" readonly="readonly"/> 
<label>阵位6:</label><input id="form6" value="" readonly="readonly"/> 
</div>
</fieldset>
<fieldset id="mount">
<legend>坐骑信息</legend>
<div>
<label>模板ID:</label><input id="mtid" value="" readonly="readonly"/> 
<label>坐骑成长:</label><input id="mgrow" value="" readonly="readonly"/>
</div>
<div>
<label>坐骑装备1:</label><input id="me1" value="" readonly="readonly"/> 
<label>坐骑装备2:</label><input id="me2" value="" readonly="readonly"/>
<label>坐骑装备3:</label><input id="me3" value="" readonly="readonly"/> 
<label>坐骑装备4:</label><input id="me4" value="" readonly="readonly"/>
<label>坐骑装备5:</label><input id="me5" value="" readonly="readonly"/> 
<label>坐骑装备6:</label><input id="me6" value="" readonly="readonly"/>
</div>
</fieldset>

<fieldset id="skill" style="float:auto;">
<legend>技能信息</legend>

</fieldset>
<fieldset id="equiped" style="float:auto;">
<legend>正在使用的技能</legend>

</fieldset>

</form>

</div>
</body>
</html>

