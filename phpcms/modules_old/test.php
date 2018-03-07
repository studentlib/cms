<?php
function getUrl($curlPost,$postUrl){
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
	return $data;
}
$key='8ujonfkWytc0kGwG84k4S0gWG';
$secret='cEB507fdB6da2B7c55CeC61a548fC06f';
$curlPost['key']= $key;
$curlPost['salt']= rand(1,10000);
$curlPost['ts']=time() ;
$curlPost['times']=0;
$curlPost['pkg']='com.zyhy.txyx.nearme.gamecenter';
$curlPost['sign']= md5('signature:'.$curlPost['pkg'].'/'.$key.'/'.$secret.'/'.$curlPost['salt'].'/'.$curlPost['ts'].'/'.$curlPost['times']);
$url='http://i.open.game.oppomobile.com/gameopen/download/v1/pkg?sign='.$curlPost['sign'].'&ts='.$curlPost['ts'].'&key='.$curlPost['key'].'&salt='.$curlPost['salt'].'&times='.$curlPost['times'].'&pkg='.$curlPost['pkg'];
$test=getUrl($curlPost,$url);
echo $url;
echo $test;








?>
