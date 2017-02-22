<?php
/**
 *
|+----------------------------------------
|缓存接口
|+----------------------------------------
 */
namespace Illuminate\Cache;

interface Cacheinterface
{
	/**
	 *
	|+----------------------------------------
	| 获取缓存
	|+----------------------------------------
	 */
	public function get($key);
	public function set($key,$value,$expire=0);
	public function delete($key);
	public function flush();
	
}