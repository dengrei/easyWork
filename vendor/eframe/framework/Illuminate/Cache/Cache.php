<?php
namespace Illuminate\Cache;

class Cache
{
	protected $_service;
	
	
	public function registerCacheHandle($name,$definition)
	{
		$this->_service[$name] = $definition;
	}
	public function get($name)
	{
		if($this->_service[$name]){
			$definition = $this->_service[$name];
		}
		if(is_object($definition)){
			$definition = call_user_func($definition);
		}
		
		return $definition;
	}
	
}