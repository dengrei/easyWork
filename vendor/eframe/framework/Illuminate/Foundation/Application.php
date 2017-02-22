<?php
namespace Illuminate\Foundation;

use Closure;
use Illuminate\Container\Container;
class Application extends Container
{
	protected  $basePath;
	protected  $loadedProviders = [];
	protected  $serviceProviders= [];
	
	public function __construct($basePath)
	{
		$this->registerBaseBindings();
		
		$this->registerBaseServiceProviders();
		
		if ($basePath) {
			$this->setBasePath($basePath);
		}
	}
	protected function registerBaseBindings()
	{
		static::setInstance($this);
	
		$this->instance('app', $this);
	
		$this->instance('Illuminate\Container\Container', $this);
	}
	protected function registerBaseServiceProviders()
	{
		$this->register(new EventServiceProvider($this));
	
		//$this->register(new RoutingServiceProvider($this));
	}
	/**
	 *
	|+----------------------------------------
	| 设置根路径
	| @param string $basePath
	| @return \Illuminate\Foundation\Application
	|+----------------------------------------
	 */
	public function setBasePath($basePath)
	{
		$this->basePath = rtrim($basePath, '\/');
		
		return $this;
	}
	public function register($provider, $options = [], $force = false)
	{
		$provider->register();

		$this->markAsRegistered($provider);

		return $provider;
	}
	
	
	protected function markAsRegistered($provider)
	{
		$class = get_class($provider);
		//$this['events']->fire($class = get_class($provider), [$provider]);
	
		$this->serviceProviders[] = $provider;
	
		$this->loadedProviders[$class] = true;
	}
}