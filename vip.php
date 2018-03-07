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
define('MYSQL_DB','GM_gd');
define('SERVER_LIST','http://gdga.sl.sg2.dianjianggame.com/ServerList_gdga.xml');//服务器目录地址
define('TABLE_LOGIN','Game_login');//玩家登录信息表
define('TABLE_ACCOUNT','Game_account');//玩家信息表
define('TABLE_RANK','Game_vipRank');//玩家信息表


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
__log('开始执行时间：'.$time1,'.');
$count=array();
$i='';
foreach($server as $k1=>$v1){
	$sql=sprintf('select `uid` from %s where `serverid`=%s',TABLE_ACCOUNT,$v1);
	$arr=getList($sql);
	if(!$arr)continue;
	$str=array();
	foreach($arr as $k2=>$v2){
		$sql2=sprintf('SELECT `uid`, MAX(viplevel) as viplevel FROM %s WHERE `uid`="%s" GROUP BY `uid`  HAVING MAX(viplevel)',TABLE_LOGIN,$v2['uid']);	
		$arr2=getList($sql2);
		if(@$arr2[0]==NULL){$arr2[0]['viplevel']=0;}
		@$i=$arr2[0]['viplevel'];
		if(!isset($str[$i])){
			$str[$i]=0;
		}
		@$str[$i]=$str[$i]+1;
		//var_dump($count);exit;
	}
	//var_dump($str);
	foreach($str as $k2=>$v2){
		$count[$v1][$k2]=$v2;		
	}
}
//var_dump($count);
foreach($count as $kk=>$vv){
	$levels="'".$kk."',";
	for($j=0;$j<22;$j++){
	   if(isset($vv[$j])){
		$levels.="'".$vv[$j]."',";
	   }else{
	   $levels.="'0',";
	   }
	}
	$levels.="'".date('Y-m-d H:i:s')."'";
	$sql3=sprintf('select * from  %s where server="%s"',TABLE_RANK,$kk);
	@$string='';
	if(getList($sql3)){
		$string.='server='.$kk;
		$string.=' '.',time="'.date('Y-m-d H:i:s').'"';
		foreach($vv as $kk1=>$vv1){
			$string.=' ,'.$kk1.'level='.$vv1;
		}
		$sql4=sprintf('update %s set %s',TABLE_RANK,$string);
	}else{
		$sql4=sprintf('INSERT INTO %s VALUE (%s)',TABLE_RANK,$levels);
	}
        query($sql4);
	__log('sql',$sql4);
}
__log('统计结束','.');
$time2=time();
__log('执行结束时间：'.$time2,'.');
$time=$time2-$time1;
__log('执行时间：'.$time,'.');











?>
