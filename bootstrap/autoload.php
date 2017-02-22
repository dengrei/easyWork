<?php

define('APP_START', microtime(true));
define('ROOT_PATH', dirname(__DIR__));
define('LOG_PATH', ROOT_PATH.'/storage/logs');

//error_reporting(0);

require_once __DIR__.'/../vendor/autoload.php';
/**
 *加载缓存
 */
$compiledPath = __DIR__.'/../storage/cache/compiled.php';

if (file_exists($compiledPath)) {
	require $compiledPath;
}