<?php


$app = new Illuminate\Foundation\Application(
			realpath(__DIR__.'/../')
	);

$obj = new Illuminate\Cache\Cache;

$obj->registerCacheHandle('Memcache', function(){
	
	$cache   = new Illuminate\Cache\Memcache\MemcacheStore();
	
	$connect = new Illuminate\Cache\Memcache\MemcacheConnect;
	$servers = array(
			array(
					'host' => '127.0.0.1',
					'port' => 11211,
					'persistent' => NULL,
					'weight'=> 1
			)
	);
	$memcache = $connect->connect($servers);
	$cache->setConnectId($memcache);
		
	return $cache;
});
$cache = $obj->get('Memcache');
$bool = $cache->set('aaaaa','aaaaaaaaaaaaaaaaaa',0);
$content = $cache->get('aaaaa');


var_dump($content);
//http
//exceptionhandle


return $app;
