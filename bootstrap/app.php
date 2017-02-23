<?php

// namespace App;

// use ReflectionClass;

// class a {
// 	public function __construct() {
// 		echo 2;
// 	}
// }

// //创建一个新的实例
// $r = new ReflectionClass("App\a");
// $r->newInstance();


// exit;

$app = new Illuminate\Foundation\Application(
			realpath(__DIR__.'/../')
	);

// $app->singleton('config',function(){
// 	$obj = new Illuminate\Foundation\Config\Config();
// 	$obj->setConfigPath(ROOT_PATH.'/config');
	
// 	return $obj;
// });

// $app->singleton('request',function(){
// 	$request = new Illuminate\Route\Request\Request();
// 	return $request;
// });

// $app->singleton('route',function(){
// 	$route = new Illuminate\Route\Route();
// 	$route->setModel(2);
	
// 	return $route;
// });


//$obj = app('route');
//$obj->handle();

//var_dump(app('config'));
$obj = app('config');
$obj->setConfigPath(ROOT_PATH.'/config');
$val = $obj->load('app')->get('db');

var_dump($val);
//var_dump($obj->handle());
return $app;
