<?php
/**
 *
|+----------------------------------------
|
|+----------------------------------------
 */
namespace Illuminate\Cache\Memcache;

use Memcache;
use RuntimeException;
class MemcacheConnect
{
	/**
	 *
	|+----------------------------------------
	| 获取memcache实例
	| @return \Memcache
	|+----------------------------------------
	 */
	protected function getMemcached()
	{
		return new Memcache;
	}
	/**
	 *
	|+----------------------------------------
	| 连接memcache服务器
	| @throws \RuntimeException
	| @throws RuntimeException
	| @return \Memcache
	|+----------------------------------------
	 */
	public function connect($servers)
	{
		$memcache = $this->getMemcached();
		
		if($servers){
			foreach($servers as $server){
				$memcache->addserver($server['host'],$server['port'],$server['persistent'],$server['weight']);
			}
		}
		$version = $memcache->getversion();
		if($version === false){
			throw new RuntimeException('No Memcached servers added');
		}
// 		if (in_array('255.255.255', $version) && count(array_unique($version)) === 1) {
// 			throw new RuntimeException('Could not establish Memcached connection.');
// 		}
		
		return $memcache;
	}
}