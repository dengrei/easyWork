<?php
namespace Illuminate\Cache\Memcache;

use Illuminate\Cache\Cacheinterface;
use Illuminate\Cache\CacheTrait;
class MemcacheStore implements Cacheinterface
{
	use CacheTrait;
	
	protected $contnect;
	
	public function setConnectId(\Memcache $connect)
	{
		$this->contnect = $connect;
	}
	/**
	 *
	|+----------------------------------------
	| 开启大值自动压缩
	| @param int $threshold 控制多大值进行自动压缩的阈值
	| @param real $min_saving 指定经过压缩实际存储的值的压缩率，支持的值必须在0和1之间。默认值是0.2表示20%压缩率
	| @return bool
	|+----------------------------------------
	 */
	public function setCompressThreshold($threshold,$min_saving=0.2)
	{
		return $this->contnect->setcompressthreshold($threshold,$min_saving);
	}
	public function get($key)
	{
		return $this->contnect->get($key);
	}
	/**
	 * 设置缓存
	|+----------------------------------------
	| @see \Illuminate\Cache\Cacheinterface::set()
	|+----------------------------------------
	 */
	public function set($key,$value,$expire=0,$zlib=false)
	{
		$zlib = $zlib?MEMCACHE_COMPRESSED:$zlib;
		return $this->contnect->set($key,$value,$zlib,$expire);
	}
	public function delete($key)
	{
		return $this->contnect->delete($key);
	}
	public function flush()
	{
		return $this->contnect->flush();
	}
	public function getVersion()
	{
		return $this->contnect->getversion();
	}
	/**
	 *
	|+----------------------------------------
	| 增加元素的值,value必须为数值类型或可被转换成数值的值
	| @param string $key
	| @param number $value
	| @return int
	|+----------------------------------------
	 */
	public function increment($key,$value=1)
	{
		return $this->contnect->increment($key,$value);
	}
	/**
	 *
	|+----------------------------------------
	| 减少元素的值,value必须为数值类型或可被转换成数值的值
	| @param string $key
	| @param number $value
	| @return int
	|+----------------------------------------
	 */
	public function decrement($key,$value=1)
	{
		return $this->contnect->decrement($key,$value);
	}
	/**
	 *
	|+----------------------------------------
	| 获取所有缓存服务器的状态信息
	| @return array
	|+----------------------------------------
	 */
	public function getExtendedStats()
	{
		return $this->contnect->getextendedstats();
	}
	/**
	 *
	|+----------------------------------------
	| 获取一个服务器的在线/离线状态
	| @param string $host
	| @param number $port
	| @return int 0离线，非0在线
	|+----------------------------------------
	 */
	public function getServerStatus($host,$port=11211)
	{
		return $this->contnect->getServerStatus($host,$port);
	}
	/**
	 *
	|+----------------------------------------
	| 运行时修改服务器参数和状态
	| @param string $host
	| @param number $port
	| @param number $timeout
	| @param string $retry_interval 服务器连接失败时重试的间隔时间，默认值15秒。如果此参数设置为-1表示不重试
	| @param number $status
	|控制此服务器是否可以被标记为在线状态。
	|设置此参数值为FALSE并且retry_interval参数设置为-1时允许将失败的服务器保留在一个池中以免影响key的分配算法。
	|对于这个服务器的请求会进行故障转移或者立即失败， 这受限于memcache.allow_failover参数的设置。
	|该参数默认TRUE，表明允许进行故障转移
	| @param string $failure_callback
	| @return bool
	|+----------------------------------------
	 */
	public function setServerParams($host,$port=11211,$timeout=10,$retry_interval=15,$status=false,$failure_callback=false)
	{
		return $this->contnect->setserverparams($host,$port,$timeout,$retry_interval,$status,$failure_callback);
	}
}