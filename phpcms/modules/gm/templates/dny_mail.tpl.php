<html>
<head>
<title>功能管理-系统邮件</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="statics/js/calendar/calendar.js"></script>
<script type="text/javascript" src="statics/js/calendar/lang/en.js"></script>
<script type="text/javascript" src="statics/config/config.js?<?php echo time()?>"></script>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/jscal2.css"/>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/border-radius.css"/>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/win2k.css"/>
<link rel="stylesheet" href="statics/css/mails.css" rel="stylesheet" type="text/css"/>

<script>


function doSendAll()
{
	if($('#st').val()=='')
	{
		return $('#st').focus();
	}
	if($('#ed').val()=='')
	{
		return $('#ed').focus();
	}
	if($('#title').val()=='')
	{
		return $('#title').focus();
	}
	if($('#content').val()=='')
	{
		return $('#content').focus();
	}

	if($('#regTime').attr('checked'))
	{
	   if($('#rstart').val()=='')
	    {
	        return $('#rstart').focus();
	    }
	    if($('#rend').val()=='')
	    {
	        return $('#rend').focus();
	    }
	}
	var post={};
	post.sender=$('#sender').val();
	post.s=$('#servers').val();
	//post.EMailID=$('#EMailID').val();
	post.st=$('#st').val();
	post.ed=$('#ed').val();
	var minlv=0;
	var maxlv=0;
	minlv=$('#minlv').val();
	maxlv=$('#maxlv').val();
	post.minlv=minlv;
	post.maxlv=maxlv;
	post.title=$('#mail_title').val();
	post.content=$('#mail_content').val();
	console.log(post.content);
	post.items=[];
	post.rstart=0;
	post.rend=0;
	if($('#regTime').attr('checked'))
    {
	    post.rstart=$('#rstart').val();
	    post.rend=$('#rend').val();
    }
	post.sviplv=$('#sviplv').val();
	post.eviplv=$('#eviplv').val();
	if($('#send_items').children().length==0)
	{
		if(!window.confirm("确定不发送附件么"))
		{
			return;
		}
	}
	$('#send_items').children().each(function(k, v) {
        post.items.push($(v).attr('value'));
    });
	$.post('?m=gm&c=dny_mail&a=sendAllUsersMail',post,function(data){
		alert(data.msg);
    },'json');
	
}

function doSendUsers()
{
	if($('#users').children().length==0)
	{ 
		return $('#users').focus();
	}
	if($('#title').val()=='')
	{
		return $('#title').focus();
	}
	if($('#content').val()=='')
	{
		return $('#content').focus();
	}

   if($('#send_items').children().length==0)
    {
        if(!window.confirm("确定不发送附件么"))
        {
            return;
        }
    }
	var post={};
	post.sender=$('#sender').val();
	post.s=$('#servers').val();
	post.title=$('#mail_title').val();
	post.content=$('#mail_content').val();
	post.uids=[];

	$('#users').children().each(function(k, v) {
        post.uids.push($(v).attr('value'));
    });
	
	post.items=[];
	$('#send_items').children().each(function(k, v) {
        post.items.push($(v).attr('value'));
    });
	
	$.getJSON('?m=gm&c=mail_gift&a=sendUsersMail',post,function(data){
		alert(data.msg);
    });
	
}


function changeItems()
{
	$('#items').contents().remove();
	var tp=$('#type').val();
	console.log(tp);
	for(var it in items)
	{
		var item=items[it];
		if(item['type']==tp)
		{
			$('#items').append('<option value="'+item['id']+'">'+item['name']+'</option>');
		}
	}
}

function addItems()
{
    var id=$(this).val();
	var name=$(this).find("option:selected").text();
	var count=$('#count').val();
	var idc=id+":"+count;
	$('#send_items').append('<option value="'+idc+'">'+name+'*'+count+'</option>');
}

function addUser()
{
    var id=$(this).val();
	var ret=0;
	$('#users').children().each(function(k,v){
		if($(v).val()==id){
			return ret=1;
		}		
	});
	if(ret)return;
    $('#users').append('<option value="'+id+'">'+id+'</option>');
}


function removeItems()
{
	var name=$(this).find("option:selected").remove();
}

$(document).ready(function(e) {
    Calendar.setup({
			weekNumbers: '0',
		    inputField : 'st',
		    trigger    : 'st',
		    dateFormat: '%Y-%m-%d %H:%M:%S',
		    showTime:  'true',
		    minuteStep: 1,
		    onSelect   : function() {this.hide();}
			});
	Calendar.setup({
		weekNumbers: '0',
		inputField : 'ed',
		trigger    : 'ed',
		dateFormat: '%Y-%m-%d %H:%M:%S', 
		showTime:  'true',
		minuteStep: 1,
		onSelect   : function() {this.hide();}
		});
    Calendar.setup({
			weekNumbers: '0',
		    inputField : 'rstart',
		    trigger    : 'rstart',
		    dateFormat: '%Y-%m-%d %H:%M:%S',
		    showTime:  'true',
		    minuteStep: 1,
		    onSelect   : function() {this.hide();}
			});
	Calendar.setup({
		weekNumbers: '0',
		inputField : 'rend',
		trigger    : 'rend',
		dateFormat: '%Y-%m-%d %H:%M:%S', 
		showTime:  'true',
		minuteStep: 1,
		onSelect   : function() {this.hide();}
		});
	for(var tp in types)
	{
		var type=types[tp];
	    $('#type').append('<option value="'+type['type']+'">'+type['name']+'</option>');
	}
	 $('#type').change(changeItems);
	 $('#type').val(1);
	 changeItems();
	 $('#items').dblclick(addItems);
	 $('#ids').dblclick(addUser);
	 $('#send_items').dblclick(removeItems);
	 $('#users').dblclick(removeItems);
	
	 var str='';
     for(var i=1;i<10800;++i)
     { 
         if(i>1000)
         {
             i+=99;
         }
    	 str+=('<option>'+i+'</option>');    
     }
     $('#count').append(str);
	 var str1='';
     for(var i=0;i<=100;++i)
     { 
    	 str1+=('<option>'+i+'</option>');    
     }
     $('#minlv').append(str1);
	 $('#minlv').val(0);
	 $('#maxlv').append(str1);
	 $('#maxlv').val(100);
	 $('#eviplv').val(18);
     $('#servers').change(function(){
         $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
             $('#users').contents().remove();
//            location.reload();
        	 
         });
       }); 
	$('input[name=user]').click(function(e){
	    var op=$(this).val();
	    if(op==1){
			 $('#acc').attr('disabled','disabled');
			 $('#search').attr('disabled','disabled');
			 $('#ids').attr('disabled','disabled');
			 $('#users').attr('disabled','disabled');
			 $('#st').attr('disabled','');
			 $('#ed').attr('disabled','');

		}else{
			 $('#acc').attr('disabled','');
			 $('#search').attr('disabled','');
			 $('#ids').attr('disabled','');
			 $('#users').attr('disabled','');
			 $('#st').attr('disabled','disabled');
			 $('#ed').attr('disabled','disabled');
			 $('#minlv').attr('disabled','disabled');
			 $('#maxlv').attr('disabled','disabled');
		}
     });
	 $('#lv').click(function(){
	 if($('#lv').attr('checked'))
	 {
	 	 $('#minlv').attr('disabled','');
		 $('#maxlv').attr('disabled','');
	 }else{
		 $('#minlv').attr('disabled','disabled');
	 	 $('#maxlv').attr('disabled','disabled');
	 }
     });
	 $('#regTime').click(function(){
	     if($('#regTime').attr('checked'))
	     {
	         $('#rstart').attr('disabled','');
	         $('#rend').attr('disabled','');
	     }else{
	         $('#rstart').attr('disabled','disabled');
	         $('#rend').attr('disabled','disabled');
	     }
	 });
	 $('#vipLv').click(function(){
	     if($('#vipLv').attr('checked'))
	     {
	         $('#sviplv').attr('disabled','');
	         $('#eviplv').attr('disabled','');
	     }else{
	         $('#sviplv').attr('disabled','disabled');
	         $('#eviplv').attr('disabled','disabled');
	     }
	 });
    $('#user2').click();

	$('#search').click(function(){
		if($('#acc').val()=='')
		{
			return $('#acc').focus();
		}
		$.getJSON('?m=gm&c=gm&a=getInfo&id='+$('#acc').val(),function(data){
		   if(data['msg']==undefined)
		   {
			   $('#ids').append('<option value="'+data['uid']+'">'+data['uid']+'</option>');
		   }else{
			   alert(data['msg']);
		   }
	    });
    });
	$('#mail_title').change(function(){
		//$('#EMailID').val($('#mail_title').attr('value'));
		//alert($('#mail_title').attr('value'));
		 $.post("?m=gm&c=dny_mail&a=mail_content",{'content':$('#mail_title').attr('value')},function(data){
				//alert(data.content);
				$('#mail_content').html(data.content);
				$('#EMailID').html(data.EMailID);
		},"json");
	});
	$('#send').click(function(){
        var op=$('input[name=user]:checked').val();
		if(op==1){
			doSendAll();
		}else{
    		doSendUsers(); 
		}
		
    });
});
</script>
</head>
<body style="background-color:#FFFFFF; margin-top: 0px; margin-bottom: 0px; margin-left: 0px; margin-right: 0px;">

<form>
<input type="hidden" id="sender" value="系统" />
<div class="__01">
    <div class="active">
      <fieldset>
        <legend>区服列表</legend>
        <select name="servers" size="1" id="servers">
        <?php 
        foreach ($servers as $value) {
            $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
            echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].'区 '.$value['text'].'</option>';
        }
        ?>
        </select>
        
      </fieldset>
    </div>
    <div class="active002">
        <img src="statics/images/mails/active-02.gif" width="1123" height="90" alt="">
    </div>
    <div class="active003">
        <fieldset><legend>添加玩家</legend>
        <input name="acc" id="acc" type="text" />
         <input name="search" id="search" type="button" value="查找" >
        </fieldset>
    </div>
    <div class="active004">
        <img src="statics/images/mails/active-04.gif" width="37" height="1189" alt="">
    </div>
    <div class="active005">
	  <input name="user" id="user1" type="radio" value="1">
    </div>
    <div class="active006">
        <img src="statics/images/mails/active-06.gif" width="77" height="27" alt="">
    </div>
    <div class="active007">

      <input type="radio" name="user" id="user2" value="2">

    </div>
    <div class="active008">
        <img src="statics/images/mails/active-08.gif" width="974" height="27" alt="">
    </div>
    <div class="active010">
        <img src="statics/images/mails/active-10.gif" width="17" height="8" alt="">
    </div>
    <div class="active011" >
        <select name="users" id="users" size="10" style="width:93%">
      </select>
    </div>
    <div class="active012">
        <img src="statics/images/mails/active-12.gif" width="684" height="244" alt="">
    </div>
    <div class="active013">
    <fieldset><legend>查找结果双击添加到发送列表</legend>
    
    <select name="ids" id="ids" size="68" style="width:80%" multiple>
    
    </select>
    
    </fieldset>
    </div>
    <div class="active014">
        <img src="statics/images/mails/active-14.gif" width="402" height="66" alt="">
    </div>
    <div class="active015">
        <img src="statics/images/mails/active-15.gif" width="102" height="5" alt="">
    </div>
    <div class="active016">
      <input type="text" name="st"  style="width:150px;height: 100%;" id="st">
    </div>
    <div class="active017">
        <img src="statics/images/mails/active-17.gif" width="31" height="141" alt="">
    </div>
    <div class="active018">
      <input type="text" name="ed"  style="width:159px;height: 100%;" id="ed">
    </div>
    <div class="active019">
        <img src="statics/images/mails/active-19.gif" width="644" height="432" alt="">
    </div>
    <div class="active020"></div>
    <div class="active021">
        <img src="statics/images/mails/active-21.gif" width="84" height="136" alt="">
    </div>
    <div class="active022">
        <img src="statics/images/mails/active-22.gif" width="18" height="17" alt="">
    </div>
    <div class="active023">
        <img src="statics/images/mails/active-23.gif" width="150" height="7" alt="">
    </div>
    <div class="active024">
        <img src="statics/images/mails/active-24.gif" width="159" height="7" alt="">
    </div>
    <div class="active025">
      <select name="minlv" disabled="disabled"  style="width: 150px; height: 100%;"  id="minlv">
      </select>
    </div>
    <div class="active026">
      <select name="maxlv"   disabled="disabled"  style="width:159px;height: 100%;"  id="maxlv">
      </select>
    </div>
    
     <div class="active054">
     <input type="checkbox" id="regTime">
     <label>注册时间</label>
     <input type="text" name="rstart"  disabled="disabled" style="width:120px;height: 100%;" id="rstart">
    </div>
    <div class="active055">
      <label>至</label>
      <input type="text" name="rend"  disabled="disabled" style="width:120px;height: 100%;" id="rend">
    </div>
    
    <div class="active056">
    <input type="checkbox" id="vipLv">
       <label>VIP区间 </label>
       <select name="sviplv" disabled="disabled" style="width:130px;height: 100%;"  id="sviplv">
       <option value="0">0</option>
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
       <option value="11">11</option>
       <option value="12">12</option>
       <option value="13">13</option>
       <option value="14">14</option>
       <option value="15">15</option>
       <option value="16">16</option>
       <option value="17">17</option>
       <option value="18">18</option>
      </select>
    </div>
    <div class="active057">
     <label>至</label>
       <select name="eviplv" disabled="disabled" style="width:120px;height: 100%;"  id="eviplv">
       <option value="0">0</option>
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
       <option value="11">11</option>
       <option value="12">12</option>
       <option value="13">13</option>
       <option value="14">14</option>
       <option value="15">15</option>
       <option value="16">16</option>
       <option value="17">17</option>
       <option value="18">18</option>
      </select>
    </div>
    
    <div class="active027">
    <input name="lv" id="lv" type="checkbox" value="1"></div>
    <div class="active028">
        <img src="statics/images/mails/active-28.gif" width="18" height="412" alt="">
    </div>
    <div class="active029">
        <img src="statics/images/mails/active-29.gif" width="150" height="83" alt="">
    </div>
    <div class="active030">
        <img src="statics/images/mails/active-30.gif" width="159" height="83" alt="">
    </div>
    <div class="active031">
        <img src="statics/images/mails/active-31.gif" width="60" height="323" alt="">
    </div>
    <div class="active032">
		<select id='mail_title'>
		<option>请选择</option>
		<?php 
		foreach($email as $k1=>$v1){
		        echo "<option id='mail_title' value=".'title'.$k1.'>'.$lang_config[$v1]."</option>";
		 }
		 echo "</select>";
		 //echo "<input id='EMailID' type='text' value=''>"
		 ?>
		
		
      
    </div>
    <div class="active033">
        <img src="statics/images/mails/active-33.gif" width="14" height="323" alt="">
    </div>
    <div class="active034">
        <img src="statics/images/mails/active-34.gif" width="350" height="21" alt="">
    </div>
    <div class="active035">
      <label for="content"></label>
      <textarea name="mail_content" id="mail_content" cols="100" rows="6"></textarea>
    </div>
    <div class="active036">
        <img src="statics/images/mails/active-36.gif" width="350" height="99" alt="">
    </div>
    <div class="active037">
        <img src="statics/images/mails/active-37.gif" width="37" height="32" alt="">
    </div>
    <div class="active038">
    <select name="count" id="count" style="width:75px"></select>
    </div>
    <div class="active039">
        <img src="statics/images/mails/active-39.gif" width="536" height="32" alt="">
    </div>
    <div class="active040">
        <img src="statics/images/mails/active-40.gif" width="71" height="8" alt="">
    </div>
    <div class="active041">
    <select name="type" id="type" size="23" style="width:93%"></select>
    </div>
    <div class="active042">
        <img src="statics/images/mails/active-42.gif" width="59" height="454" alt="">
    </div>
    <div class="active043">
   <select name="items" id="items" size="23" style="width:93%"></select>
    </div>
    <div class="active044">
        <img src="statics/images/mails/active-44.gif" width="64" height="454" alt="">
    </div>
    <div class="active045">
   <select name="send_items" id="send_items" size="23" style="width:86%"></select>
    </div>
    <div class="active046">
        <img src="statics/images/mails/active-46.gif" width="231" height="454" alt="">
    </div>
    <div class="active047">
        <img src="statics/images/mails/active-47.gif" width="252" height="16" alt="">
    </div>
    <div class="active048">
        <img src="statics/images/mails/active-48.gif" width="239" height="86" alt="">
    </div>
    <div class="active049">
        <img src="statics/images/mails/active-49.gif" width="241" height="86" alt="">
    </div>
    <div class="active050">
        <img src="statics/images/mails/active-50.gif" width="8" height="70" alt="">
    </div>
    <div class="active051">
      <input type="button" name="send" id="send" style="width:117px;height:40px;" value="发送">
    </div>
    <div class="active052">
        <img src="statics/images/mails/active-52.gif" width="127" height="70" alt="">
    </div>
    <div class="active053">
        <img src="statics/images/mails/active-53.gif" width="117" height="30" alt="">
    </div>
</div>
</form>
</body>
</html>
