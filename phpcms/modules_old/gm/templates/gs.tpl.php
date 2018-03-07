<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('#update').click(function(){
          if($('#config').val())
          {
              $.getJSON("?m=gm&c=gs&a=update&sid="+$('#servers').val()+'&file='+$('#config').val(),function(data){
                  if(data&&data['msg']!=undefined)
                  { 
                      $('#msg').html(data['msg']);
                      return;
                  } 
              });
          }
     });
    $('#updateAll').click(function(){
          $.getJSON("?m=gm&c=gs&a=updateAll&sid="+$('#servers').val(),function(data){
              if(data&&data['msg']!=undefined)
              { 
                  $('#msg').html(data['msg']);
                  return;
              } 
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
<?php 
//global $keys;
?>
<div style="width:250px;float:left;">
<form action="">
<fieldset style="width: 550px;"><legend> server服列表</legend>
<select name="servers" id="servers" style="width: 100px;float: left;">
<?php 
foreach ($servers as $value) {
     echo '<option value="'.$value['ServerType'].'">'.$value['id'].' server '.$value['text'].'</option>';
}
?>
</select>
<input type="button" name="update" id="update" value="更新" style="width: 50px;float: auto;"/>
<input type="button" name="updateAll" id="updateAll" value="更新所有" style="width: 80px;float: auto;"/>
</fieldset>
<fieldset style="width: 150px;"><legend>配置文件列表</legend>
<select name="config" id="config" size="<?php echo count($configs);?>" style="width: 150">
<?php 
foreach ($configs as $value) {
     echo '<option value="'.$value.'">'.$value.'</option>';
}
?>
</select>
<label id="msg" style="font-size: medium;color: red;"></label>
</fieldset>
 
</form>
</div>
</html>

