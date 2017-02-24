<?php
namespace Illuminate\Container;

use Closure;
use ArrayAccess;
use Illuminate\Container\Contract as ContainerContract;
use ReflectionClass;
use ReflectionParameter;
use Illuminate\Exception\BindingResolutionException;

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
	
	protected $buildStack = [];
	
	protected $extenders  = [];
	
	protected $resolved   = [];
	
	protected $reboundCallbacks = [];
	
	protected $globalResolvingCallbacks = [];
	
	protected $globalAfterResolvingCallbacks = [];
	
	protected $resolvingCallbacks = [];
	
	protected $afterResolvingCallbacks = [];
	
	public $contextual = [];
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
		//如果是命名空间，则去掉最左侧的双斜杠 "\\"
		$abstract = $this->normalize($abstract);
		
		if (is_array($abstract)) {
			list($abstract, $alias) = $this->extractAlias($abstract);
		
			$this->alias($abstract, $alias);
		}
		//如果映射池中存在则删除
		unset($this->aliases[$abstract]);
		
		//检查绑定池，对象池，映射池中是否存在
		$bound = $this->bound($abstract);

		//存储到对象池
		$this->instances[$abstract] = $instance;
		
		if($bound){
			$this->rebound($abstract);
		}
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
	public function make($abstract,array $parameters = [])
	{
		$abstract = $this->getAlias($this->normalize($abstract));
		
		if(isset($this->instances[$abstract])){
			return $this->instances[$abstract];
		}
		
		//获取闭包，如果是命名空间则直接返回
		$concrete = $this->getConcrete($abstract);
		
		if($this->isBuildable($concrete, $abstract)){
			$object = $this->build($concrete,$parameters);
		}else{
			$object = $this->make($concrete,$parameters);
		}
		
		foreach ($this->getExtenders($abstract) as $extender) {
			$object = $extender($object, $this);
		}
		
		//共享对象
		if($this->isShared($abstract)){
			$this->instances[$abstract] = $object;
		}
		
		$this->fireResolvingCallbacks($abstract, $object);
		
		$this->resolved[$abstract] = true;
		
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
		$this->aliases[$abstract] = $this->normalize($alias);
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
	public function bound($abstract)
	{
		$abstract = $this->normalize($abstract);
		
		return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]) || $this->isAlias($abstract);
	}
	/**
	 *
	|+----------------------------------------
	| 是否存在映射
	| @param string $name
	| @return mixed
	|+----------------------------------------
	 */
	public function isAlias($name)
	{
		return isset($this->aliases[$this->normalize($name)]);
	}
	/**
	 *
	|+----------------------------------------
	|
	| @param \Closure $concrete
	| @param string $parameters
	|+----------------------------------------
	 */
	public function build($concrete,$parameters)
	{
		//如果$concrete是闭包，则直接返回
		if($concrete instanceof Closure){
			return $concrete;
		}
		
		//创建一个$concrete对象
		$reflector = new ReflectionClass($concrete);
		
		if (! $reflector->isInstantiable()) {
			if (! empty($this->buildStack)) {
				$previous = implode(', ', $this->buildStack);
		
				$message = "Target [$concrete] is not instantiable while building [$previous].";
			} else {
				$message = "Target [$concrete] is not instantiable.";
			}
		
			throw new BindingResolutionException($message);
		}
		
		$this->buildStack[] = $concrete;
		
		$constructor = $reflector->getConstructor();
		
		//如果ReflectionClass创建对象失败，则从绑定中移除，并使用new创建对象
		if(is_null($constructor)){
			array_pop($this->buildStack);
			
			return new $concrete;
		}
		
		$dependencies = $constructor->getParameters();
		
		//获取参数
		$parameters   = $this->keyParametersByArgument($dependencies, $parameters);
		
		$instances    = $this->getDependencies($dependencies, $parameters);
		//释放资源
		array_pop($this->buildStack);
		//创建一个带参数的对象，参数将传递到构建函数
		return $reflector->newInstanceArgs($instances);
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
		if (! is_null($concrete = $this->getContextualConcrete($abstract))) {
			return $concrete;
		}
		
		//如果绑定闭包不存在，则直接返回名称
		if (! isset($this->bindings[$abstract])) {
			return $abstract;
		}
		
		return $this->bindings[$abstract]['concrete'];
	}
	/**
	 *
	|+----------------------------------------
	| 获取具体绑定类库
	| @param unknown $abstract
	| @return unknown
	|+----------------------------------------
	 */
	protected function getContextualConcrete($abstract)
	{
		if (isset($this->contextual[end($this->buildStack)][$abstract])) {
			return $this->contextual[end($this->buildStack)][$abstract];
		}
	}
	/**
	 *
	|+----------------------------------------
	| 反射回调
	| @param string $abstract
	|+----------------------------------------
	 */
	protected function rebound($abstract)
	{
		//创建对象
		$instance = $this->make($abstract);
	
		/*
		 * 将对象回调给闭包
		 *$this->getReboundCallbacks($abstract)  闭包数组
		 */
		foreach ($this->getReboundCallbacks($abstract) as $callback) {
			//将应用以及自身对象回调给闭包处理
			call_user_func($callback, $this, $instance);
		}
	}
	/**
	 *
	|+----------------------------------------
	| 从闭包池中获取需要的闭包
	| @param string $abstract
	| @return array
	|+----------------------------------------
	 */
	protected function getReboundCallbacks($abstract)
	{
		if (isset($this->reboundCallbacks[$abstract])) {
			return $this->reboundCallbacks[$abstract];
		}
	
		return [];
	}
	/**
	 *
	|+----------------------------------------
	| 检查是否是闭包
	| @param string|\Closure $concrete
	| @param string $abstract
	| @return boolean
	|+----------------------------------------
	 */
	protected function isBuildable($concrete, $abstract)
	{
		return $concrete === $abstract || $concrete instanceof Closure;
	}
	/**
	 *
	|+----------------------------------------
	| 通过对象构建函数的参数获取传入的参数
	| @param array $dependencies
	| @param array $parameters
	| @return array
	|+----------------------------------------
	 */
	protected function keyParametersByArgument(array $dependencies, array $parameters)
	{
		foreach ($parameters as $key=>$value){
			if(is_numeric($key)){
				unset($parameters[$key]);
				
				$parameters[$dependencies[$key]->name] = $value;
			}
		}
		
		return $parameters;
	}
	/**
	 *
	|+----------------------------------------
	| 注入所有依赖
	| @param array $dependencies
	| @param array $parameters
	|+----------------------------------------
	 */
	protected function getDependencies(array $parameters, array $primitives)
	{
		$dependencies = [];
		
		foreach ($parameters as $parameter) {
			$dependency = $parameter->getClass();
			
			if (array_key_exists($parameter->name, $primitives)) {
				$dependencies[] = $primitives[$parameter->name];
			} elseif (is_null($dependency)) {
				$dependencies[] = $this->resolveNonClass($parameter);
			} else {
				$dependencies[] = $this->resolveClass($parameter);
			}
		}
		return $dependencies;
	}
	/**
	 *
	|+----------------------------------------
	| 解析类实例
	| @param ReflectionParameter $parameter
	| @throws BindingResolutionException
	| @return mixed|\Closure
	|+----------------------------------------
	 */
	protected function resolveClass(ReflectionParameter $parameter)
	{
		try {
			//创建对象
			return $this->make($parameter->getClass()->name);
		}
	
		// 如果不能解析类实例，将检查值是否可选
		// 可选则返回可选参数的值
		catch (BindingResolutionException $e) {
			if ($parameter->isOptional()) {
				return $parameter->getDefaultValue();
			}
	
			throw $e;
		}
	}
	/**
	 *
	|+----------------------------------------
	|
	| @param ReflectionParameter $parameter
	| @throws BindingResolutionException
	| @return mixed|\Closure|unknown
	|+----------------------------------------
	 */
	protected function resolveNonClass(ReflectionParameter $parameter)
	{
		//如果存在闭包，则返回
		if (! is_null($concrete = $this->getContextualConcrete('$'.$parameter->name))) {
			if ($concrete instanceof Closure) {
				return call_user_func($concrete, $this);
			} else {
				return $concrete;
			}
		}
	
		//如果类属性有默认值，则返回
		if ($parameter->isDefaultValueAvailable()) {
			return $parameter->getDefaultValue();
		}
	
		$message = "Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}";
	
		throw new BindingResolutionException($message);
	}
	protected function getExtenders($abstract)
	{
		if (isset($this->extenders[$abstract])) {
			return $this->extenders[$abstract];
		}
	
		return [];
	}
	/**
	 *
	|+----------------------------------------
	| 自动调用，分前后
	| @param string $abstract
	| @param object $object
	|+----------------------------------------
	 */
	protected function fireResolvingCallbacks($abstract, $object)
	{
		$this->fireCallbackArray($object, $this->globalResolvingCallbacks);
	
		$this->fireCallbackArray(
				$object, $this->getCallbacksForType(
						$abstract, $object, $this->resolvingCallbacks
						)
				);
	
		$this->fireCallbackArray($object, $this->globalAfterResolvingCallbacks);
	
		$this->fireCallbackArray(
				$object, $this->getCallbacksForType(
						$abstract, $object, $this->afterResolvingCallbacks
						)
				);
	}
	/**
	 *
	|+----------------------------------------
	| 自动调用类
	| @param unknown $object
	| @param array $callbacks
	|+----------------------------------------
	 */
	protected function fireCallbackArray($object, array $callbacks)
	{
		foreach ($callbacks as $callback) {
			$callback($object, $this);
		}
	}
	/**
	 *
	|+----------------------------------------
	| 获取回调类型
	| @param unknown $abstract
	| @param unknown $object
	| @param array $callbacksPerType
	|+----------------------------------------
	 */
	protected function getCallbacksForType($abstract, $object, array $callbacksPerType)
	{
		$results = [];
	
		foreach ($callbacksPerType as $type => $callbacks) {
			if ($type === $abstract || $object instanceof $type) {
				$results = array_merge($results, $callbacks);
			}
		}
	
		return $results;
	}
}