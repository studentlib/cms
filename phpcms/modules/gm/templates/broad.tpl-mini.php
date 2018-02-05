<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
    <!--百度编辑器插件-->
    <link href="statics/plug-in/baidu_write_mini/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">
    <link href="statics/plug-in/baidu_write_mini/themes/default/css/default.css" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="statics/plug-in/baidu_write_mini/third-party/jquery.min.js"></script>
    <script type="text/javascript" src="statics/plug-in/baidu_write_mini/third-party/template.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="statics/plug-in/baidu_write_mini/umeditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="statics/plug-in/baidu_write_mini/umeditor.min.js"></script>
    <script type="text/javascript" src="statics/plug-in/baidu_write_mini/lang/zh-cn/zh-cn.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var cont = UM.getEditor('myEditor').getContent();
            $('#save').click(function () {
                if (cont.length < 50) {
                    // $('#msg').html('公告内容太短');
                    alert('公告内容太短');
                    return;
                }
                $.post('?m=gm&c=broad&a=save', {content: cont, file: $('#notice').val()}, function (data) {
                    console.log(data);
                    alert(data['msg']);
                }, 'json');
            });
            $('#notice').change(function () {
                var data = {};
                data.file = $('#notice').val();
                $.post('?m=gm&c=broad&a=file_content', data, function (data) {
                    if (data.ret == 0) {
                        alert(data.msg);
                        UM.getEditor('myEditor').setContent('');
                        return;
                    }
                    //向ueditor 编辑器写入内容
                    UM.getEditor('myEditor').setContent(data.content);
                }, 'json');
            });
        });
    </script>
</head>
<body>
<div id="editor" style="width:1024px;height:500px;">
    <b>平台:</b>&nbsp&nbsp<select id='notice' name='notice'>

        <?php
        foreach ($files_name as $k => $v) {
            echo '<option value="' . $k . '">' . $v . '</option>';
        }
        ?>
    </select>
    <form action="">
        <fieldset>
            <legend>公告编辑</legend>
            <!-- <textarea id="content" rows="20" cols="150"></textarea>
             -->
            <!--style给定宽度可以影响编辑器的最终宽度-->
            <script type="text/plain" id="myEditor" style="width:1000px;height:500px;">
                <?php
                /*php转码*/
                //   $charset = mb_detect_encoding($content, array('ASCII','UTF-8','GB2312','GBK','BIG5'));
                //   echo $charset;
                //   if("utf-8" != $charset){
                //       $content = iconv('GB2312',"UTF-8//IGNORE",$content);
                // echo $content;
                //   }else{
                // echo $content;
                //   }
                echo $content;
                ?>
            </script>
            <
            script
            type = "text/javascript" >
            //实例化编辑器
            var um = UM.getEditor('myEditor');

            function getAllHtml() {
                alert(UM.getEditor('myEditor').getAllHtml())
            }
            </script>
            <input type="button" id="save" value="保存公告"/>
            <span id="msg" style="color: red;"></span>
        </fieldset>
    </form>
</div>
</body>
</html>


