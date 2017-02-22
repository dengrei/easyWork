<?php
namespace Illuminate\Foundation;

abstract class ServiceProvider
{
	protected $app;
	
	/**
	 *
	|+----------------------------------------
	| 初始化服务
	| @param object $app 应用实例
	|+----------------------------------------
	 */
	public function __construct($app)
	{
		$this->app = $app;
	}
	/**
	 *
	|+----------------------------------------
	| 注册服务
	|+----------------------------------------
	 */
	abstract function register();
	
	/**
	 *
	|+----------------------------------------
	| 禁止访问 提示
	| @param string $method
	| @param mixed $params
	| @throws \BadMethodCallException
	|+----------------------------------------
	 */
	public function __call($method,$params)
	{
		throw new \BadMethodCallException('call to undefined method ['.$method.']');
	}
}