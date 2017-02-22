<?php
namespace Illuminate\Foundation;

class EventServiceProvider
{
	protected $app;
	
	public function __construct($app)
	{
		$this->app = $app;
	}
	
	public function register()
	{
		$this->app->singleton('events', function ($app) {
			return  (new Dispatcher($app))->setQueueResolver(function () use ($app) {
				return $app->make('Illuminate\Contracts\Queue\Factory');
			});
		});
	}
}