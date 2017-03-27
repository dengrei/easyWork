<?php


$start = microtime(true);
$app = new Illuminate\Foundation\Application(
			realpath(__DIR__.'/../')
	);


// $kernel = $app->make(Illuminate\Foundation\Http\Kernel::class,[$app]);

// $kernel->handle('1');


// $end  = microtime(true);
//echo '<div style="color:red;">耗时:'.($end - $start).'</div>';
$obj = $app->make(Illuminate\Payment\Payment::class);
$instance = $obj::getInstance();

$instance->bootstrap('AlipayJs',array('type'=>'Md5'));
$instance->setConfig(array(
    'out_trade_no' => 'GN'.mt_rand(00000000,99999999),
    'subject'   => '测试商品',
    'total_fee' => '0.01',
));

$instance->doPay();


return $app;
