<?php

use Illuminate\Container\Container;

if(! function_exists('app')){
	function app($make = null , $parameter = [])
	{
		$instance = Container::getInstance();
		if(is_null($make)){
			return $instance;
		}
		
		return $instance->make($make,$parameter);
	}
}
if(! function_exists('config')){
	function config($key = null)
	{
		$instance = app('config');
		if(is_null($key)){
			return $instance;
		}

		return $instance->get($key);
	}
}