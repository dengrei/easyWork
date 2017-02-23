<?php
/**
 *
|+----------------------------------------
|配置文件处理
|@author Administrator
|
|tags
|+----------------------------------------
 */
namespace Illuminate\Foundation\Config;

use ArrayAccess;
use Exception;

class Config implements ArrayAccess
{
	/**
	 *
	|+----------------------------------------
	| 配置文件名称
	| @var string
	|+----------------------------------------
	 */
	protected $name;
	/**
	 *
	|+----------------------------------------
	| 配置文件后缀
	| @var string
	|+----------------------------------------
	 */
	protected $fix  = '.php';
	/**
	 *
	|+----------------------------------------
	| 配置文件目录
	| @var string
	|+----------------------------------------
	 */
	protected $configPath;
	/**
	 *
	|+----------------------------------------
	| 配置文件数据
	| @var array
	|+----------------------------------------
	 */
	protected $data = [];
	/**
	 *
	|+----------------------------------------
	| 允许使用的配置文件后缀
	| @var array
	|+----------------------------------------
	 */
	protected $accessFix = ['.php','.ini'];
	
	public function __construct($options = [])
	{
		
	}
	/**
	 *
	|+----------------------------------------
	| 设置配置文件后缀
	| @param string $fix
	|+----------------------------------------
	 */
	public function setConfigFix($fix)
	{
		$this->fix  = $fix;
	}
	/**
	 *
	|+----------------------------------------
	| 设置配置文件目录
	| @param string $path
	|+----------------------------------------
	 */
	public function setConfigPath($path)
	{
		$this->configPath = $path;
	}
	/**
	 * @param $offset
	 */
	 public function offsetExists ($key)
	 {
	 	if(isset($this->data[$key])){
	 		return $this->data[$key];
	 	}else{
	 		return null;
	 	}
	 }
	
	/**
	  *
	 |+----------------------------------------
	 | 获取配置文件值，KEY支持获取多维数组的值 db.dervice.mysql
	 | @param string $key
	 | @return NULL|mixed
	 |+----------------------------------------
	  */
	 public function offsetGet ($key)
	 {
	 	$value = null;
	 	 
	 	if(strpos($key, '.') === false){
	 		$value = $this->data[$key];
	 	}else{
	 		$keyArr = explode('.', $key);
	 		foreach($keyArr as $k){
	 			if(is_null($value)){
	 				$value = $this->offsetExists($k);
	 			}else{
	 				$value = $value[$k];
	 			}
	 		}
	 	}
	 	return $value;
	 }
	
	/**
	 * @param $offset
	 * @param $value
	 */
	 public function offsetSet ($offset, $value) {}
	
	/**
	 * @param $offset
	 */
	 public function offsetUnset ($offset) {}
	 /**
	  *
	 |+----------------------------------------
	 | 加载的配置文件名
	 | @param string $name
	 | @return \Illuminate\Foundation\Config\Config
	 |+----------------------------------------
	  */
	 public function load($name)
	 {
	 	$this->name = $name;
	 	
	 	return $this;
	 }
	 /**
	  *
	 |+----------------------------------------
	 | 获取配置缓存文件 KEY支持获取多维数组的值 db.dervice.mysql
	 | @param string $key
	 | @return NULL|mixed
	 |+----------------------------------------
	  */
	 public function get($key)
	 {
	 	$filename= $this->getFile();
	 	$file    = $this->configPath.'/'.$filename;
	 	
	 	if(file_exists($file)){
	 		if(empty($data)){
	 			$this->data = require $file;
	 		}
	 		
	 		$value = $this->offsetGet($key);
	 		return $value;
	 	}
	 }
	 /**
	  *
	 |+----------------------------------------
	 | 获取配置文件名称
	 | @return string
	 |+----------------------------------------
	  */
	 protected function getFile()
	 {
	 	if(!in_array($this->fix, $this->accessFix)){
	 		try{
	 			throw new Exception('不支持的配置文件后缀');
	 		}catch (Exception $e)
	 		{
	 			exit($e->getMessage());
	 		}
	 	}
	 	
	 	return $this->name.$this->fix;
	 }
}