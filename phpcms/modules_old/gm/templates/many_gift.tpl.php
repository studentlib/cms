<html>
<head>
<title>功能管理-系统邮件</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="statics/js/calendar/calendar.js"></script>
<script type="text/javascript" src="statics/js/calendar/lang/en.js"></script>
<script type="text/javascript" src="statics/config/config.js?<?php echo time()?>"></script>
<link rel="stylesheet" href="statics/css/mails-2.css" rel="stylesheet" type="text/css"/>

<script>

function doSendUsers()
{
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
	post.title=$('#title').val();
	post.content=$('#content').val();
	post.items=[];
	$('#send_items').children().each(function(k, v) {
        post.items.push($(v).attr('value'));
    });
	
	$.getJSON('?m=gm&c=mail_gift&a=many_mails',post,function(data){
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
	 $('#eviplv').val(15);
     $('#servers').change(function(){
         $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
             $('#users').contents().remove();
//            location.reload();
        	 
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
			   $('#ids').append('<option value="'+data['uid']+'">'+data['uid']+'</option>');
		   }else{
			   alert(data['msg']);
		   }
	    });
    });
	$('#send').click(function(){
    		doSendUsers(); 	
    });

});
</script>
</head>
<body style="background-color:#FFFFFF; margin-top: 0px; margin-bottom: 0px; margin-left: 0px; margin-right: 0px;">
<div style="margin-left:500px;margin-top:20px;">
<form method="post" enctype="multipart/form-data" action="/index.php?m=gm&c=mail_gift&a=file_upload" id="uploadload" target="iframelogo">
        <input type="file" class="uploadtxt" name="upload" /><input type="submit" value="上传" />
</form>
</div>
<form>
<input type="hidden" id="sender" value="CharUI1000004" /><!-- 系统 -->
    <div class="active" style="margin-top:20px;margin-left:30px">
      <fieldset>
        <legend> server服列表</legend>
        <select name="servers" size="1" id="servers">
        <?php 
        foreach ($servers as $value) {
            $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
            echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].' server '.$value['text'].'</option>';
        }
        ?>
		</select>
        
      </fieldset>
    </div>
<div class="__01" style="margin-top: 60px;margin-left: 30px;">
    <div class="active029">邮件内容</div>
    <div class="active031">
        <div style="font-size:15px; margin-top:0px; top:30px;">邮件标题:</div>
        <div style="font-size:15px;margin-top:30px;">邮件正文:</div>
    </div>
    <div class="active032" style="width: 800px;">
		<input type="text" name="title" id="title" >
		<span name="warn" id="warn" style="margin-left:15px;color:red;font-size: 15;">*标题不能为空</span>
    </div>
    <div class="active035" style="width:800px;">
      <label for="content"></label>
      <textarea name="content" id="content" cols="47" rows="10"></textarea>
      <span name="con_warn" id="con_warn" style="margin-left:15px;color:red;font-size: 15;">*内容不能为空</span>
    </div>
    <div class="active036">道具发放</div>
    <div class="active036-1">选择道具类型:</div>
    <div class="active037">
        <div style="font-size:17px;margin-top:3px;height:52px;">双击添加道具&nbsp&nbsp&nbsp数量:</div>
    </div>
    <div class="active038">
    <select name="count" id="count" style="width:40px;padding-top:5px;"></select>
    </div>
    <div class="active039">
        <div style="margin-left:60px;">道具列表:双击删除道具</div>
    </div>
    <div class="active041">
    <select name="type" id="type" size="23" style="width:93%"></select>
    </div>
    <div class="active043">
   <select name="items" id="items" size="23" style="width:93%"></select>
    </div>
    <div class="active045">
   <select name="send_items" id="send_items" size="23" style="width:93%"></select>
    </div>
    <div class="active051">
      <input type="button" name="send" id="send" style="width:117px;height:40px;" value="发送">
    </div>

</div>
</form>

</body>
</html>
