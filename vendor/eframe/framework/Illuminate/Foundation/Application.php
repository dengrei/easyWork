<?php
namespace Illuminate\Foundation;

use Illuminate\Container\Container;
use Illuminate\Route\RoutingServiceProvider;

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
		//$this->register(new EventServiceProvider($this));
	
		$this->register(new RoutingServiceProvider($this));
		
		$this->registerAlias();
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
		
		$this->bindPathsInContainer();
		
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
	/**
	 *
	|+----------------------------------------
	| 绑定应用结构目录
	|+----------------------------------------
	 */
	protected function bindPathsInContainer()
	{
		$this->instance('path', $this->path());
	
		foreach (['base', 'config', 'database', 'lang', 'public', 'storage'] as $path) {
			$this->instance('path.'.$path, $this->{$path.'Path'}());
		}
	}
	/**
	 *
	|+----------------------------------------
	| 应用目录
	| @return string
	|+----------------------------------------
	 */
	public function path()
	{
		return $this->basePath.DIRECTORY_SEPARATOR.'/app';
	}
	/**
	 *
	|+----------------------------------------
	| 应用根目录
	| @return string
	|+----------------------------------------
	 */
	public function basePath()
	{
		return $this->basePath;
	}
	/**
	 *
	 |+----------------------------------------
	 | 配置目录
	 | @return string
	 |+----------------------------------------
	 */
	public function configPath()
	{
		return $this->basePath.DIRECTORY_SEPARATOR.'/config';
	}
	/**
	 *
	 |+----------------------------------------
	 | 数据库目录
	 | @return string
	 |+----------------------------------------
	 */
	public function databasePath()
	{
		return $this->basePath.DIRECTORY_SEPARATOR.'/database';
	}
	/**
	 *
	 |+----------------------------------------
	 | 语言目录
	 | @return string
	 |+----------------------------------------
	 */
	public function langPath()
	{
		return $this->basePath.DIRECTORY_SEPARATOR.'/lang';
	}
	/**
	 *
	 |+----------------------------------------
	 | 公共资源目录
	 | @return string
	 |+----------------------------------------
	 */
	public function publicPath()
	{
		return $this->basePath.DIRECTORY_SEPARATOR.'/public';
	}
	/**
	 *
	 |+----------------------------------------
	 | 数据存储目录
	 | @return string
	 |+----------------------------------------
	 */
	public function storagePath()
	{
		return $this->basePath.DIRECTORY_SEPARATOR.'/storage';
	}
	/**
	 *
	|+----------------------------------------
	| 创建对象并自动执行 bootstrap
	| @param array $bootstrappers
	|+----------------------------------------
	 */
	public function bootstrapWith(array $bootstrappers)
	{
		
		foreach ($bootstrappers as $bootstrapper) {
			$this->make($bootstrapper)->bootstrap($this);
		}
	}
	/**
	 *
	|+----------------------------------------
	|
	|+----------------------------------------
	 */
	public function handle()
	{
		echo 'handle';
	}
	/**
	 *
	|+----------------------------------------
	| 注册核心类库
	|+----------------------------------------
	 */
	protected function registerAlias()
	{
		$alias = [
				'config' =>'Illuminate\Foundation\Config\Config'
		];
		
		foreach($alias as $key=>$alia){
			$this->alias($key, $alia);
		}
	}
}