<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	   $('#servers').change(function(){
	        $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
	           location.reload();
	        });
	     });
	     $('#mails a').click(function(data){
		     $.post('?m=gm&c=mail_gift&a=remove',{'sid':$('#servers').val(),'mid':$(this).attr('mid')},function(data){
		    	  if(data.code==0) 
		    	  {
		    		  location.reload();
		    	  }else{
			    	  alert(data.msg);
		    	  }
			},'json');
		 });
});
</script>
<style type="text/css">
label{vertical-align:left;width: 180px;float:left;}
input{vertical-align:left;width: 150px;float:left;}
body{font-family:tahoma Verdana;font-size:12px;}
table {
 font-family:Verdana, Arial, Helvetica, sans-serif;
 font-size:14px;
 margin:20px;
 border-collapse:collapse;
}
</style>
</head>
<body>
<?php 
//global $keys;
?>
<div style="width:250px;float:left;">
<form action="">
<fieldset style="width: 550px;"><legend>区服列表</legend>

<select name="servers" id="servers"  style="width: 200px;float: left;">
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].'区 '.$value['text'].'</option>';
}
?>
</select>
</fieldset>
<table border="2px" bordercolor="green" id="mails">
<tr>
<th nowrap="nowrap">编号</th>
<th nowrap="nowrap">最小等级</th>
<th nowrap="nowrap">最大等级</th>
<th nowrap="nowrap">领取时间</th>
<th nowrap="nowrap">注册时间</th>
<th nowrap="nowrap">VIP范围</th>
<th nowrap="nowrap">邮件标题</th>
<th nowrap="nowrap">内容</th>
<th nowrap="nowrap">附件</th>
<th nowrap="nowrap">操作</th>
</tr>
<?php foreach ($gMails as $k=>$v):
$v['start']=date('Y-m-d H:i:s',$v['start']);
$v['end']=date('Y-m-d H:i:s',$v['end']);
if($v['rstart'])
{
    $v['rstart']=date('Y-m-d H:i:s',$v['rstart']);
}
if($v['rstart'])
{
    $v['rend']=date('Y-m-d H:i:s',$v['rend']);
}
$items=array();
if(count($v['items']))
{
    foreach ($v['items'] as $value) 
    {
            if(isset($config[$value['id']]))
            {
                $items[]=$config[$value['id']]['name'].'*'.$value['count'];
            }
    }
}
$items_str=join(',',$items);

?>
<tr>

<td nowrap="nowrap"><?php echo $v['id']?></td>
<td nowrap="nowrap"><?php echo $v['minlv']?></td>
<td nowrap="nowrap"><?php echo $v['maxlv']?></td>
<td nowrap="nowrap"><?php echo $v['start'].'~'.$v['end']?></td>
<td nowrap="nowrap"><?php echo $v['rstart'].'~'.$v['rend']?></td>
<td nowrap="nowrap"><?php echo $v['svip'].'~'.$v['evip']?></td>
<td nowrap="nowrap"><?php echo $v['title']?></td>
<td nowrap="nowrap"><?php echo $v['msg']?></td>
<td nowrap="nowrap"><?php echo $items_str?></td>
<td nowrap="nowrap"><a href="javascript:void(0);" mid="<?php echo $v['id']?>">删除</a></td>
</tr>
<?php endforeach;?>
</table>
</form>
</div>
</html>

