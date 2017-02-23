<?php
/**
 *
|+----------------------------------------
|路由处理
|@author Administrator
|
|tags
|+----------------------------------------
 */
namespace Illuminate\Route;

class Route implements RouteConstract
{
	protected $urlModel = 1;
	
	public function setModel($model = 1)
	{
		$this->urlModel = $model;
	}
	public function getModel()
	{
		return $this->urlModel;
	}
	public function handle()
	{
		$this->getUrlParamaters();
	}
	protected function getUrlParamaters()
	{
		$request =  app('request');
		
		$request->matchRoutes();
	}
}