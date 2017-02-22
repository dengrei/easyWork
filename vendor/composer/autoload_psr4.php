<?php
$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
		'Illuminate\\' => array($vendorDir . '/eframe/framework/Illuminate'),
		'App\\' => array($baseDir . '/app'),
		
);