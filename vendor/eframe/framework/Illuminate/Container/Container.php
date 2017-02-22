<?php
namespace Illuminate\Container;

use Closure;
use ArrayAccess;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use InvalidArgumentException;

class Container implements ArrayAccess
{
	protected $instance;
	protected $instances;
	protected $bindings;
	protected $aliases;
	
	public function offsetSet($offset, $value) {
		$this->bindings[$offset] = $value;
	}
	public function offsetExists($offset) {
		return isset($this->bindings[$offset]);
	}
	public function offsetUnset($offset) {
		unset($this->bindings[$offset]);
	}
	public function offsetGet($offset) {
		return isset($this->bindings[$offset]) ? $this->bindings[$offset] : null;
	}
	
	
	public function setInstance(){}
	public function getInstance(){}
	public function instance($abstract,$instance)
	{
		$this->instance[$abstract] = $instance;
	}
	public function singleton($abstract, $concrete = null)
	{
		$this->bind($abstract, $concrete, true);
	}
	public function bind($abstract, $concrete = null, $shared = false)
	{
		if (is_null($concrete)) {
			$concrete = $abstract;
		}
		if (! $concrete instanceof Closure) {
			$concrete = $this->getClosure($abstract, $concrete);
		}
	
		$this->bindings[$abstract] = compact('concrete', 'shared');

	}
}