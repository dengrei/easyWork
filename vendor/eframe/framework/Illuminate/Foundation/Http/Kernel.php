<?php

namespace Illuminate\Foundation\Http;


use Illuminate\Contracts\Http\Kernel as KernelContract;
use Illuminate\Foundation\Application;

class Kernel implements KernelContract
{
    /**
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;
    /**
     * 自动加载的类库列表
     * @var array
     */
    protected $bootstrappers = [
        'Illuminate\Foundation\Config\Config',
    ];

    /**
     *
    |+----------------------------------------
    | 初始化
    | @param Application $app
    |+----------------------------------------
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * 自动创建核心功能
     *
     * @return void
     */
    public function bootstrap()
    {
    	$this->app->bootstrapWith($this->bootstrappers);
    }
    /**
     * 开启应用
    |+----------------------------------------
    | @see \Illuminate\Contracts\Http\Kernel::handle()
    |+----------------------------------------
     */
    public function handle($request)
    {
    	$this->bootstrap();
    	
    	$this->app->handle($request);
    }
    public function terminate($request, $response)
    {}
    public function getApplication()
    {}
   
}
