<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">

$(document).ready(function(){
	$("#userid").change(function(){
		  $('#heros').empty();
          $('#msg').html("");
		  th=this;
	     $.getJSON("?m=hero&c=hero&a=getHeros&id="+$(this).val(),function(data){console.log(data);
                 if(data&&data['msg']!=undefined)
                 { 
                     $('#msg').html(data['msg']);
                     return;
                 } 
                 $('#uid').val($('#search').val()); 
                var sz=0;
                 var str='';
                 $.each(data,function(k,v){
                	 str+='<option value="'+k+'" >'+v+'</option>';
                	 sz++;
                 });
                 $('#heros').attr('size',sz);
                 $('#heros').append(str);
                 $('#uid').val($(th).val());
                 $('#heros').change(); 
		 });
     });
     $('#search').click(function(){
   	  $('#heros').empty();
      $('#msg').html("");
    	 $.getJSON("?m=hero&c=hero&a=getHeros&id="+$('#suid').val(),function(data){
             if(data&&data['msg']!=undefined)
             { 
                 $('#msg').html(data['msg']);
                 return;
             } 
             $('#uid').val($('#search').val()); 
            var sz=0;
             var str='';
             $.each(data,function(k,v){
                 str+='<option value="'+k+'" >'+v+'</option>';
                 sz++;
             });
             $('#heros').attr('size',sz);
             $('#heros').append(str);
             $('#uid').val($('#suid').val());
             $('#heros').change(); 
          });
      });
     $('#heros').change(function(){
    	 $.getJSON("?m=hero&c=hero&a=getHeroInfo&id="+$('#uid').val()+'&hid='+$(this).val(),function(data){
             if(data&&data['msg']!=undefined)
             { 
                 $('#msg').html(data['msg']); 
                 return;
             }  
             $('#equips').contents().remove();
             $('#props').contents().remove();
             $.each(data,function(k,v){
         	    if(k=='equips')
         	    {
             	    var html='<legend>装备信息</legend>';
                   $.each(v,function(k1,v1){
            	    html+='<div><label>部位:</label><input value="'+v1['part']+'" readonly="readonly"/>';
            	    html+='<label>模板ID:</label><input value="'+v1['tid']+'" readonly="readonly"/>';
            	    html+='<label>名称:</label><input value="'+v1['name']+'" readonly="readonly"/>';
            	    html+='<label>等级:</label><input value="'+v1['level']+'" readonly="readonly"/>';
            	    var i=1;
            	    $.each(v1['qenchs'],function(qk,qv){
            	    	html+='<label>淬火项'+i+'</label><input value="'+qv['id']+'/'+qv['percent']+'" readonly="readonly"/>';
            	    	++i;
                	});
                	i=1; 
            	    $.each(v1['stones'],function(sk,sv){
            	    	html+='<label>宝石槽'+i+'</label><input value="'+sv+'" readonly="readonly"/>';
                	});
            	    html+='</div>';
                  });
                 
                   $('#equips').append(html);
             	    return true;
         	    }
         	    if(k=='props'&&v!=null)
         	    {  
             	    var html='<legend>属性信息</legend>';
                    $.each(v,function(k2,v2){ 
            	    html+='<div><label>属性ID:</label><input value="'+v2['id']+'" readonly="readonly"/>';
            	    html+='<label>属性名称:</label><input value="'+v2['name']+'" readonly="readonly"/>';
            	    html+='<label>值:</label><input value="'+v2['value']+'" readonly="readonly"/>';
            	    html+='</div>';
                    });
                   $('#props').append(html);
             	    return true;
         	    }
         	    
           	    $('#'+k).val(v);
         	    
             });
//             $('#hero').show();
             
         });
     });
     $('#servers').change(function(){
         $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
            location.reload();
         });
       });
});


</script>
<style type="text/css">

label{vertical-align:left;width: 120px;float:auto;}
input{vertical-align:left;width: 120px;float:auto;}
body{font-family:tahoma Verdana;font-size:12px;}
</style>
</head>
<body>
<?php
//global $keys;
?>
<div style="width: 225px; float: left;">
<form action="">
<fieldset><legend>查找</legend> <label>账号ID:</label> 
<input type="text" name="suid" id="suid" style="width: 120px; float: left;" />
 <input type="button" name="search" id="search" value="查找" style="width: 50px; float: auto;" />
  <label id="error" style="font-size: medium; color: red;"></label></fieldset>
<fieldset style="width: 200px;">
<legend>区服列表</legend> <select
	name="servers" id="servers" style="width: 150">
<?php
foreach ($servers as $value) {
    $selected = $value['ServerType'] == $_SESSION['sid'] ? 'selected="selected"' : '';
    echo "<option  $selected value=\"" . $value['ServerType'] .
     '" >' . $value['id'] . '区 ' . $value['text'] . '</option>';
}
?>
</select></fieldset>
<fieldset style="width: 200px;"><legend>角色ID列表</legend> <select
	name="userid" id="userid" size="<?php
echo count($keys)>0?count($keys)+1:2
?>"
	style="width: 200px; height: 800px">
<?php
foreach ($keys as $value) {
    $new = substr($value, strpos($value, ':') + 1);
    echo "<option value=" . $new . ">$new</option>";
}
?>
</select></fieldset>

</form>
</div>
<div style="float: auto;">
<form action="">
<fieldset><legend>武将列表</legend>
 <input type="hidden" id="uid" name="uid"value="" /> 
<select name="heros" id="heros"  style="width: 200px; float: left;">
</select>

</fieldset>
    <fieldset style="width: 1400px"><legend>武将信息</legend>
    <label>模板ID:</label>
    <input id="tid"  value="" readonly="readonly"/>
    <label>经验:</label>
    <input id="exp"  value="" readonly="readonly"/>
    <label>等级:</label>
    <input id="level"  value="" readonly="readonly"/>
    <label>觉醒等级:</label>
    <input id="awakelv"  value="" readonly="readonly"/>
    <label>天赋等级:</label>
    <input id="talentlv"  value="" readonly="readonly"/>
    <label>消耗碎片id:</label>
    <input id="fid"  value="" readonly="readonly"/>
    <label>消耗碎片数量:</label>
    <input id="count"  value="" readonly="readonly"/>
    </fieldset>
    <fieldset style="width: 800px;" id="equips">
    
    </fieldset>
    <fieldset style="width: 800px;" id="props">
    
    </fieldset>
</form>

<label id="msg" style="font-size: medium; color: red;"></label></div>
</body>
</html>

