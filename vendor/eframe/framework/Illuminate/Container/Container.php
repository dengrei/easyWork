<?php
namespace Illuminate\Container;

use Closure;
use ArrayAccess;
use Illuminate\Container\Contract as ContainerContract;
use ReflectionClass;

class Container implements ArrayAccess,ContainerContract
{
	/**
	 *
	|+----------------------------------------
	| 单例对象
	| @var object
	|+----------------------------------------
	 */
	protected static $instance = null;
	/**
	 *
	|+----------------------------------------
	| 注入对象集合
	| @var array
	|+----------------------------------------
	 */
	protected $instances;
	/**
	 *
	|+----------------------------------------
	| 绑定闭包集合
	| @var array
	|+----------------------------------------
	 */
	protected $bindings;
	/**
	 *
	|+----------------------------------------
	| 映射集合
	| @var array
	|+----------------------------------------
	 */
	protected $aliases;
	
	/**
	 * 绑定数据
	|+----------------------------------------
	| @see ArrayAccess::offsetSet()
	|+----------------------------------------
	 */
	public function offsetSet($offset, $value) {
		$this->bindings[$offset] = $value;
	}
	/**
	 * 检查数据是否存在
	|+----------------------------------------
	| @see ArrayAccess::offsetExists()
	|+----------------------------------------
	 */
	public function offsetExists($offset) {
		return isset($this->bindings[$offset]);
	}
	/**
	 * 解除数据绑定
	|+----------------------------------------
	| @see ArrayAccess::offsetUnset()
	|+----------------------------------------
	 */
	public function offsetUnset($offset) {
		unset($this->bindings[$offset]);
	}
	/**
	 * 获取数据
	|+----------------------------------------
	| @see ArrayAccess::offsetGet()
	|+----------------------------------------
	 */
	public function offsetGet($offset) {
		return isset($this->bindings[$offset]) ? $this->bindings[$offset] : null;
	}
	
	/**
	 *
	|+----------------------------------------
	| 创建单例对象
	| @return string
	|+----------------------------------------
	 */
	public static function setInstance(ContainerContract $container)
	{
		static::$instance = $container;
	}
	/**
	 *
	|+----------------------------------------
	| 获取单例对象
	| @return string
	|+----------------------------------------
	 */
	public static function getInstance()
	{
		return self::$instance;
	}
	/**
	 *
	|+----------------------------------------
	| 存储单例对象
	| @param string $abstract
	| @param object $instance
	|+----------------------------------------
	 */
	public function instance($abstract,$instance)
	{
		$bound = $this->bound($abstract);

		$this->instances[$abstract] = $instance;
	}
	/**
	 *
	|+----------------------------------------
	| 注入对象
	| @param string $abstract
	| @param \Closure|string|null $concrete
	|+----------------------------------------
	 */
	public function singleton($abstract, $concrete = null)
	{
		$this->bind($abstract, $concrete, true);
	}
	/**
	 *
	|+----------------------------------------
	| 绑定对象
	| @param string $abstract
	| @param \Closure|string|null $concrete
	| @param string $shared     是否共享
	| @return boolean
	|+----------------------------------------
	 */
	public function bind($abstract, $concrete = null, $shared = false)
	{
		if (is_null($concrete)) {
			$concrete = $abstract;
		}
		if (! $concrete instanceof Closure) {
			return false;
		}
	
		$this->bindings[$abstract] = compact('concrete', 'shared');

	}
	/**
	 *
	|+----------------------------------------
	| 调用已绑定的对象
	| @param string $abstract
	| @param mixed $parameter 参数
	|+----------------------------------------
	 */
	public function make($abstract,$parameters = [])
	{
		$abstract = $this->getAlias($this->normalize($abstract));
		
		if(isset($this->instances[$abstract])){
			return $this->instances[$abstract];
		}

		$concrete = $this->getConcrete($abstract);
		
		//创建新的实例
		if(is_null($concrete)){
			$reflection = new ReflectionClass($abstract);
			$object     = $reflection->newInstance($parameters);
		}else{
			$object     = $concrete($this, $parameters);
		}
		//共享对象
		if($this->isShared($abstract)){
			$this->instances[$abstract] = $object;
		}
		
		return $object;
	}
	/**
	 *
	|+----------------------------------------
	| 对象是否共享
	| @param string $abstract
	| @return boolean
	|+----------------------------------------
	 */
	public function isShared($abstract)
	{
		$abstract = $this->normalize($abstract);
	
		if (isset($this->instances[$abstract])) {
			return true;
		}
	
		if (! isset($this->bindings[$abstract]['shared'])) {
			return false;
		}
	
		return $this->bindings[$abstract]['shared'] === true;
	}
	/**
	 *
	|+----------------------------------------
	| 添加映射
	| @param string $abstract
	| @param string $alias
	|+----------------------------------------
	 */
	protected function alias($abstract, $alias)
	{
		$this->aliases[$alias] = $this->normalize($abstract);
	}
	/**
	 *
	|+----------------------------------------
	| 获取映射
	| @param string $abstract
	| @return string
	|+----------------------------------------
	 */
	protected function getAlias($abstract)
	{
		return isset($this->aliases[$abstract]) ? $this->aliases[$abstract] : $abstract;
	}
	protected function extractAlias(array $definition)
	{
		return [key($definition), current($definition)];
	}
	/**
	 *
	|+----------------------------------------
	| 检查对象是否存在
	| @param unknown $abstract
	| @return NULL
	|+----------------------------------------
	 */
	protected function bound($abstract)
	{
		return isset($this->instances[$abstract])?$this->instances[$abstract]:null;
	}
	/**
	 *如果是命名空间，则移除最左侧的"\\"
	|+----------------------------------------
	| Enter description here ...
	| @param mixed $service
	| @return string|unknown
	|+----------------------------------------
	 */
	protected function normalize($service)
	{
		return is_string($service) ? ltrim($service, '\\') : $service;
	}
	/**
	 *
	|+----------------------------------------
	| 获取闭包
	| @param string $abstract
	| @return \Closure
	|+----------------------------------------
	 */
	protected function getConcrete($abstract)
	{
		return isset($this->bindings[$abstract])?$this->bindings[$abstract]['concrete']:null;
	}
}