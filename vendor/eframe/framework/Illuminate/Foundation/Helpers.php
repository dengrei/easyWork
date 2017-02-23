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