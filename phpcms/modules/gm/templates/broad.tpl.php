<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#save').click(function () {
                if ($('#content').val() == '') {
                    return $('#content').focus();
                }
                if ($('#content').val().length < 50) {
                    $('#msg').html('公告内容太短');
                    return;
                }
                $.post('?m=gm&c=broad&a=save', {
                    content: $('#content').val(),
                    file: $('#notice').val()
                }, function (data) {
                    alert(data['msg']);
                }, 'json');
            });
            $('#notice').change(function () {
                var data = {};
                data.file = $('#notice').val();
                $.post('?m=gm&c=broad&a=file_content', data, function (data) {
                    if (data.ret == 0) {
                        alert(data.msg);
                        location.reload();
                    }
                    //$('#content').html('');
                    //$('#content').append(data.content);
                    $('#content').html(data.content);
                }, 'json');
            });
        });
    </script>
</head>
<body>
<div id="editor" style="width:1024px;height:500px;">
    <b>平台:</b>&nbsp&nbsp<select id='notice' name='notice'>
        <?php
        foreach ($file_name as $k => $v) {
            echo '<option value="' . $k . '">' . $v . '</option>';
        }
        ?>
    </select>
    <form action="">
        <fieldset>
            <legend>公告编辑</legend>
            <textarea id="content" rows="20" cols="150"><?php echo $content; ?></textarea>
            <input type="button" id="save" value="保存公告"/>
            <span id="msg" style="color: red;"></span>
        </fieldset>
    </form>
</div>
</body>
</html>
