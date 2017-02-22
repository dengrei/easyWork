<?php
namespace Illuminate\Cache;

class redisDB
{
	protected $_di;
	
	protected $_options;
	
	public function __construct($options = null)
	{
		$this->_options = $options;
	}
	
	public function setDI($di)
	{
		$this->_di = $di;
	}
	
	public function find($key, $lifetime)
	{
		return 22;
	}
	
	public function save($key, $value, $lifetime)
	{
		// code
	}
	
	public function delete($key)
	{
		// code
	}
}