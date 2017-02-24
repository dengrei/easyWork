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
use Illuminate\Foundation\Application;

class Config implements ArrayAccess
{
	protected $app;
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

	/**
	 *
	|+----------------------------------------
	| 初始化
	| @param Application $app
	|+----------------------------------------
	 */
	public function bootstrap(Application $app)
	{
		$this->app = $app;
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
	 public function offsetGet ($keyArr)
	 {
	 	$value = null;
	 	
	 	foreach($keyArr as $k){
	 		if(is_null($value)){
	 			$value = $this->offsetExists($k);
	 		}else{
	 			$value = $value[$k];
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
	 | 获取配置缓存文件 KEY支持获取多维数组的值 app.db.dervice.mysql,第一位作为文件名使用
	 | @param string $key
	 | @return NULL|mixed
	 |+----------------------------------------
	  */
	 public function get($key)
	 {
	 	$keyArr  = $this->getFileName($key);
	 	
	 	$filename= $this->getFile();
	 	$file    = app()->configPath().'/'.$filename;
	 	
	 	if(file_exists($file)){
	 		if(empty($this->data)){
	 			$this->data = require $file;
	 		}
	 		
	 		$value = $this->offsetGet($keyArr);
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
	 /**
	  *
	 |+----------------------------------------
	 | 获取文件名，如：app.db,则是获取app配置文件中db的值
	 | @param string $key
	 |+----------------------------------------
	  */
	 protected function getFileName($key)
	 {
	 	$keyArr = explode('.', $key);
	 	if(!$this->name){
	 		$this->name = $keyArr[0];
	 		array_shift($keyArr);
	 	}
	 	return $keyArr;
	 }
}