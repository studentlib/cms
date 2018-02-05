<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
    <script type="text/javascript" src="statics/js/highchart/highcharts.js"></script>
    <script type="text/javascript" src="statics/js/highchart/highcharts-3d.js"></script>
    <script type="text/javascript" src="statics/js/highchart/modules/exporting.js"></script>
    <script type="text/javascript" src="statics/js/calendar/calendar.js"></script>
    <script type="text/javascript" src="statics/js/calendar/lang/en.js"></script>
    <link rel="stylesheet" type="text/css" href="statics/js/calendar/jscal2.css"/>
    <link rel="stylesheet" type="text/css" href="statics/js/calendar/border-radius.css"/>
    <link rel="stylesheet" type="text/css" href="statics/js/calendar/win2k.css"/>
    <style type="text/css">

        body {
            font-family: tahoma Verdana;
            font-size: 14px;
        }

        * {
            font-size: 12px;
            font: sans-serif;
        }

        * {
            margin: 0;
            padding: 2px;
        }

        table {
        / / font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size: 10px;
            margin: 20px;
            border-collapse: collapse;
        }

        .ltv_content {
            margin: 0px auto;
            text-align: center
        }

    </style>
    <script type="text/javascript">


        $(document).ready(function () {
            Calendar.setup({
                weekNumbers: '0',
                inputField: 'st',
                trigger: 'st',
                dateFormat: '%Y-%m-%d',
                showTime: 'true',
                minuteStep: 1,
                onSelect: function () {
                    this.hide();
                }
            });
            Calendar.setup({
                weekNumbers: '0',
                inputField: 'ed',
                trigger: 'ed',
                dateFormat: '%Y-%m-%d',
                showTime: 'true',
                minuteStep: 1,
                onSelect: function () {
                    this.hide();
                }
            });

            $.post('?m=order&c=order&a=loadChannels',{},function(data){
                config=data;
                for(var x in config)
                {
                    var ch=config[x];
                    $('#ch').append('<option value="'+x+'">'+ch['name']+'</option>');
                }
            },'json');

            $('#query').click(function () {
                var post = {};
                post.st = $('#st').val();
                post.ed = $('#ed').val();
                post.ch = $('#ch').val();
                $.post('?m=account&c=account&a=findLtv', post, function (data) {
                    if (data.code == 0) {
                        alert(data.msg);
                    }
                    $('#ltvtime').attr('value', data.ltvtime);
                    $('#header ~ tr').remove();//console.log(data.content);
                    for (var k in data.content) {
                        var tr = '<tr>';
                        var ltvTr = data.content[k];
                        for (v in ltvTr) {
                            if (ltvTr[v] == null) {
                                ltvTr[v] = '';
                            }
                            if (v.indexOf('amount') > 0) {
                                tr += '<th>' + ltvTr[v] + '</th>';
                            } else {
                                tr += '<th>' + ltvTr[v] + '</th>';
                            }
                        }
                        tr += '</tr>';
                        $('#ltv').append(tr);
                    }
                }, 'json');

            });
            $('#ch').change(function () {
                var post = {};
                post.st = $('#st').val();
                post.ed = $('#ed').val();
                post.ch = $('#ch').val();
                $.post('?m=account&c=account&a=findLtv', post, function (data) {
                    if (data.code == 0) {
                        alert(data.msg);
                    }
                    $('#ltvtime').attr('value', data.ltvtime);
                    $('#header ~ tr').remove();
                    for (var k in data.content) {
                        var tr = '<tr>';
                        var ltvTr = data.content[k];
                        for (v in ltvTr) {
                            if (ltvTr[v] == null) {
                                ltvTr[v] = '';
                            }
                            if (v.indexOf('amount') > 0) {
                                tr += '<th>' + ltvTr[v] + '</th>';
                            } else {
                                tr += '<th>' + ltvTr[v] + '</th>';
                            }
                        }
                        tr += '</tr>';
                        $('#ltv').append(tr);
                    }
                }, 'json');

            });
            $('#export').click(function () {
                var url = '?m=account&c=account&a=exportLtv&st=' + $('#st').val() + '&ed=' + $('#ed').val() + '&ch=' + $('#ch').val() + '&file=ltv';
                window.open(url);
            });
        });

    </script>
</head>
<body>

<fieldset align="center">
    <legend>ltv数据</legend>
    <label>ltv更新时间:</label>
    <input type="text" disabled id="ltvtime" class="ltvtime" value="<?php echo $ltvtime; ?>" style="width: 123px;"/>
    <label>按时间查询:</label>
    <input type="text" name="st" id="st" value="<?php echo $st; ?>" style="width: 120px;"/>
    <input type="text" name="ed" id="ed" value="<?php echo $ed; ?>" style="width: 120px;"/>
    <label>渠道:</label>
    <select id="ch">
        <option value=''>所有平台</option>
        <?php
        if($_SESSION['roleid']==1){
            echo "<option value='ALL'>渠道和</option>";
        }
        ?>
    </select>
    <input type="button" value="查询" id="query"/>
    <div align="center">
        <table border="2" id="ltv" bordercolor="green">
            <tr id="header" style="font-size: 16px;">
                <?php
                foreach ($servers as $key => $value) {
                    echo '<th >' . $value . '</th>';
                }
                ?>
            </tr>
            <?php
            foreach ($result as $key => $value) {

                echo '<tr>';
                foreach ($value as $k => $v) {
                    echo '<th id=' . $k . '>' . $v . '</th>';
                }
                echo '</tr>';
            }
            ?>
        </table>
    </div>
</fieldset>
<!--<select name="sservers" id="sservers" multiple="multiple" size="<?php echo count($servers); ?>"  style="width: 150px">
<option value="0">请选择</option>
<?php
foreach ($servers as $k => $v) {
    echo "<option value=\"" . $k . '" >' . $v . '</option>';
}
?>
</select>-->
<input type="button" id="export" class="export" value="导出">
</body>
</html>

