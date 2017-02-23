<?php
/**
 *
|+----------------------------------------
|请求处理
|@author Administrator
|
|tags
|+----------------------------------------
 */
namespace Illuminate\Route\Request;

class Request
{
	public function matchRoutes()
	{
		$url = $this->getRequestUri();
		
		preg_match('/\/User\/(.*)/', $url,$match);

		$obj = new \App\Modules\Front\Controllers\IndexController();
		
		$method  = 'action'.ucwords($match[1]);
		
		$confobj = app('config');

		$val     = $confobj->load('app')->get('db');
		
		if(method_exists($obj, $method)){
			call_user_func_array(array($obj,$method),array('a'=>$method));
		}else{
			header('HTTP/1.0 404 NOT FOUND');
			exit('404 NOT FOUND');
		}
	}
	protected function getParamaters($type='all')
	{
		$data = array();
		if($type == 'GET'){
			$data = $_GET;
		}
		elseif($type == 'POST')
		{
			$data = $_POST;
		}else{
			$data = empty($_GET)?$_POST:$_GET;
		}
		return $data;
	}
	protected function getRequestUri()
	{
		return isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:$_SERVER['QUERY_STRING'];
	}
}