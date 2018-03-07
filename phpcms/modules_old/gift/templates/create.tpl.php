<html>
<head>
<title>Tạo gói quà</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="statics/config/config.js?<?php echo time()?>"></script>
<style type="text/css">
body{font-family:tahoma Verdana;font-size:12px;}
</style>
<script type="text/javascript">

function item_func()
{ 
	 $('#gift_list').append('<option count="'+$('#giftcount').val()+'" value="'+$('#item_list').val()+'">'+
	 $('#item_list').find("option:selected").text()+':'+$('#giftcount').val()+'</option>');
}

function gift_func()
{
	$('#gift_list').find("option:selected").remove(); 
}
 
function cannotchange()
{
	$('#error').html("Không thể sửa nội dung quà đã tạo");
}

function changed()
{
    $('#item_list').unbind('dblclick');
    $('#gift_list').unbind('dblclick');
    $('#gift_list').contents().remove();
    $('#error').html('');
    var post={};
    post.batch=$('#batch').val();
    post.channel=$('#channel').val();
    $.getJSON('?m=gift&c=gift&a=getItem',post,function(data){
        if(data.code=='0'&&data.item.item!=undefined)
        {
            var item=data.item.item;
            for(var x in item)
            {
           	  $('#gift_list').append('<option count="'+item[x]['count']+'" value="'+item[x]['id']+'">'+
              item[x]['name']+':'+item[x]['count']+'</option>'); 
            }
            $('#oldcount').val(data.count);
            $('#item_list').dblclick(cannotchange);
            $('#gift_list').dblclick(cannotchange);
            $('#common').attr('disabled','disabled');
        }else{
            $('#item_list').dblclick(item_func);
            $('#gift_list').dblclick(gift_func);
            $('#oldcount').val('0');
            $('#common').attr('disabled','');
        }
    });
}

function changeItems()
{
	$('#item_list').contents().remove();
	var tp=$('#item_class').val();
	console.log(tp);
	console.log(items);
	for(var it in items)
	{
		var item=items[it];
		if(tp ==""){tp=1;}
		if(item['type']==tp)
		{
			$('#item_list').append('<option value="'+item['id']+'">'+item['name']+'</option>');
		}
	}
}
$(document).ready(function(){
	for(var tp in types)
	{
		var type=types[tp];
	    $('#item_class').append('<option value="'+type['type']+'">'+type['name']+'</option>');
	}
	 $('#item_class').change(changeItems);
	 $('#item_class').html(changeItems);
    for(var x=1;x<1000;++x)
    {
    	$('#giftcount').append('<option value="'+x+'">'+x+'</option>');
    }
    for(var x=1;x<=100;++x)
    {
    	$('#codecount').append('<option value="'+x+'">'+x+'</option>');
    }
    $('#gen').click(function(){
        if($('#gift_list').children().length>0)
        {
            var post={};
            var op=$('input[name=common]:checked').val();
            post.repeat=$('#repeat').val();
            post.batch=$('#batch').val();
            post.channel=$('#channel').val();
            post.codecount=$('#codecount').val();
            post.codeunit=$('#codeunit').val(); 
            post.common=op;
            post.usecount=$('#usecount').val();
            var items=[];
            $('#gift_list').children().each(function(k,v){
                items.push($(v).val()+":"+$(v).attr('count'));
            });
            post.items=items;
            $.getJSON('?m=gift&c=gift&a=createCode',post,function(data){
                 if(data.code=='0')
                 {
                 }
            });
            $('#create').hide(1);
            setTimeout("updateState()",500); 
        }else{
            $('#error').html("Click đúp DS vật phẩm thêm vào quà");
        }
    });
    $('#batch').change(changed);
    $('#channel').change(changed);
    $('#channel').change();
    $('#usecount').attr('disabled','disabled');
    $('#common').click(function(){
    	var op=$('input[name=common]:checked').val();
    	if(op)
    	{
    		$('#usecount').attr('disabled','');
    	}else{
    		$('#usecount').attr('disabled','disabled');
    	}
    });
    
});
function updateState()
{ 
	  var post={};
	  post.batch=$('#batch').val();
      post.channel=$('#channel').val();
      post.codecount=$('#codecount').val();
      post.codeunit=$('#codeunit').val(); 
      post.oldcount=$('#oldcount').val(); 
      $.getJSON('?m=gift&c=gift&a=updateState',post,function(data){
          if(data.code=='1')
          {
        	  $('#progress').html('Đang tạo'+data.current+"/"+data.total);
        	  setTimeout("updateState()",500); 
          }else if(data.code=='0'){ 
              $('#create').show(1);
              $('#progress').html('');
              $('#channel').change();
          }
     }); 
      
}
</script>
</head>
<body>
<div id="create">
<form>
<fieldset>
<legend>Tạo giftcode</legend>

<label>Chọn lượt</label>
<select id="batch">
<?php foreach ($batch as $v) {
 ?>
<option value="<?php echo $v?>"><?php echo $v?></option>
<?php 
}
?>
</select>
<label>Chọn kênh</label>
<select id="channel">
<?php foreach ($list as $v) {
 ?>
<option value="<?php echo $v['code']?>"><?php echo $v['name']?></option>
<?php 
}
?>
</select>

<label>Số lượng chọn</label>
<select name="codecount" id="codecount"  size="0">
</select>
<label>Đơn vị chọn</label>
<select name="codeunit" id="codeunit"  size="0">
<option value="1">Cái</option>
<option value="10">Chục</option>
<option value="100">Trăm</option>
<option value="1000">Nghìn</option>
<option value="10000">Vạn</option>
</select>
<input type="checkbox" name="common" id="common" value="1"/>Quà thông dụng
<label>Chọn lượt sử dụng</label>
<select name="usecount" id="usecount"  size="0">
<option value="10">10</option>
<option value="100">100</option>
<option value="1000">1000</option>
<option value="10000">10000</option>
<option value="100000">100000</option>
<option value="500000">500000</option>
</select>
<input type="hidden" id="oldcount" value=""/>
<label>Sử dụng nhiều lần?</label>
<select name="repeat" id="repeat"  size="0">
<option value="0">Không</option>
<option value="1">Có</option>
</select>
<input type="button" id="gen" value="Tạo quà" />
<br/>
<hr/>
<fieldset>
<label>Phân loại</label>
<select name="item_class" id="item_class"  size="20" style="width: 200px;">

</select>
<label>Click đúp chọn v.phẩm</label>
<select name="item_list" id="item_list"  size="20" style="width: 200px;">
 <?php 
 foreach ($items as $value) {
    echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
}
?>
</select>
<label>Chọn số lượng</label>
<select name="giftcount" id="giftcount"  size="0">
</select>
<label>Click đúp xóa v.phẩm</label>
<select name="gift_list" id="gift_list"  size="20" style="width: 200px;float:auto;">
</select>
<span id="error" style="color: red;"></span>
</fieldset>
</fieldset>
</form>
</div>
<div id="state">
<p id="progress"></p>
</div>
</body>
</html>