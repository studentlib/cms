<?php
//统计各服vip数量
set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');
ini_set('output_buffering','off');
define('MYSQL_CHARSET','UTF-8');
define('MYSQL_IP','127.0.0.1');
define('MYSQL_USER','sg');
define('MYSQL_PWD','f3%DEc*io7');
define('MYSQL_PORT','3306');
define('MYSQL_DB','GM_kr');
define('SERVER_LIST','http://kr.sl.sg2.dianjianggame.com/ServerList_kr.xml');//服务器目录地址
define('TABLE_LOGIN','Game_login');//玩家登录信息表
define('TABLE_ACCOUNT','Game_account');//玩家信息表
define('TABLE_RANK','Game_vipRank');//玩家信息表
define('TABLE_RANK1','Game_vipRank1');//玩家信息表

$link=NULL;

function GetMySql()
{
    global $link;
    if($link==NULL||!mysql_ping($link))
    {
        $link=mysql_connect(MYSQL_IP.':'.MYSQL_PORT,MYSQL_USER,MYSQL_PWD,TRUE);
        mysql_set_charset(MYSQL_CHARSET);
        mysql_select_db(MYSQL_DB);
    }
    return $link;
}

function __log($str,$arr)
{
    echo date('Y-m-d H:i:s').': '.$str.json_encode($arr,JSON_UNESCAPED_UNICODE)."\n";
}
function ping()
{
    global $link;
    mysql_ping($link);
}


function query($sql)
{
    global $link;
    GetMySql();
    return mysql_query($sql,$link);
}
function free($res)
{
    if(is_resource($res))
    {
        return mysql_free_result($res);
    }
}


function getList($sql)
{
    global $link;
    $res=query($sql);
    $ret=array();
    if($res)
    {
        while($row=mysql_fetch_assoc($res))
        {
            $ret[]=$row;
        }
        free($res);
    }else{
        echo mysql_errno($link),mysql_error($link),"\n";
    }
    return $ret;
}

function get_server_config()
{
         $opts = array(
        'http'=>array(
        'method'=>"GET",
        'timeout'=>10,
        )
        );
        $option=stream_context_create($opts);
        $file = file_get_contents(SERVER_LIST, null, $option);
        $servers=simplexml_load_string($file);
        $ret=array();
        foreach ($servers->row as $v)
        {
             foreach ($v->attributes() as $k1=>$v1) 
             {
                $arr[(string)$k1]=(string)$v1;
             }
             $ret[$arr['ServerType']]=$arr;
	}
        return $ret;
}

$servers=get_server_config();
//__log('servers',$servers);

foreach ($servers as $k=>$v){
	$server[]=$v['ServerType'];
}
$link=NULL;
$time1=time();
__log('执行开始时间：'.$time1,'.');
$count=array();
$i='';
$sql1=sprintf('SELECT `uid` ,MAX(`viplevel`) as viplevel  FROM %s GROUP BY `uid` HAVING MAX(viplevel);',TABLE_LOGIN);
$arr=getList($sql1);
foreach($arr as $k=>$v){
	$sql2=sprintf('select `serverid` from %s where `uid`="%s" limit 1',TABLE_ACCOUNT,$v['uid']);
	$arr2=getList($sql2);
	$sql3=sprintf('select * from %s where `uid`="%s"',TABLE_RANK1,$v['uid']);
	if(getList($sql3)){
		@$string='server='.$arr2[0]['serverid'].', uid='.$v['uid'].', viplevel='.$v['viplevel'];
                $sql4=sprintf('update %s set %s where `uid`=%s',TABLE_RANK1,$string,$v['uid']);
	}else{
		$sql4=sprintf('insert into %s (server,uid,viplevel) values (%s,%s,%s)',TABLE_RANK1,$arr2[0]['serverid'],$v['uid'],$v['viplevel']);
	}
	//__log('插入VIP等级：',$sql4);
	query($sql4);
}
//query('DELETE FROM `Game_vipRank1`');
//query("INSERT INTO `Game_vipRank1` SELECT DISTINCT t1.`uid`,t2.`serverid`,t1.`viplevel` FROM `Game_login` AS t1 LEFT JOIN `Game_account` AS t2 ON t2.`uid` = t1.`uid` WHERE t1.`viplevel`!='0'");
$time2=time();
__log('执行结束时间：'.$time2,'.');
$time=$time2-$time1;
__log('执行间隔时间：'.$time,'.');








?>
