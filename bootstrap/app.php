<?php



$start = microtime(true);
$app = new Illuminate\Foundation\Application(
			realpath(__DIR__.'/../')
	);


$kernel = $app->make(Illuminate\Foundation\Http\Kernel::class,[$app]);

$kernel->handle('1');


$end  = microtime(true);
echo '<div style="color:red;">耗时:'.($end - $start).'</div>';

return $app;
