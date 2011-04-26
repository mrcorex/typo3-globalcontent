<?php
if(!strlen($_GET['url'])) die;
//require_once('/var/www/thor.dev.linkfactory.dk/http/t3lib/class.t3lib_div.php');
$url = $_GET['url'];
if(strpos('http',$url)===false){
	
	//$url = 'http://'.$url;
}
preg_match('/(http:\/\/[^\/]+)/',$url,$matches);
//print_r($_GET);
//print_r($matches);
//t3lib_div::_GP('url');
//print(t3lib_div::getUrl($url.'&callback='.urlencode($_GET['callback']).'&no_cache=1&site='.urlencode(urlencode($matches[1]))));
print(file_get_contents($url.'&callback='.urlencode($_GET['callback']).'&no_cache=1&site='.urlencode(urlencode($matches[1]))));
?>