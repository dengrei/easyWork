<?php
namespace Illuminate\Route;

class RoutingServiceProvider
{
	protected $app;
	
	public function __construct($app)
	{
		$this->app = $app;
	}
	
	public function register()
	{
		echo 'test';
	}
}