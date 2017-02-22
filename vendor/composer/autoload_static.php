<?php
/**
 *
|+----------------------------------------
|5.6版本以上处理
|
|tags
|+----------------------------------------
 */

namespace Composer\Autoload;

class ComposerStaticInit
{
    public static function getprefixLengthsPsr4()
    {
    	return array(
    			'I' =>
    			array (
    					'Illuminate\\' => 11,
    			),
    			'A' =>
    			array (
    					'App\\' => 4,
    			),
    	);
    }
    public static function getprefixDirsPsr4()
    {
    	$vendorDir = dirname(__DIR__);
    	$baseDir   = dirname($vendorDir);
    	return array(
    			'Illuminate\\' => array (
    					0 => $vendorDir . '/eframe/framework/Illuminate',
    			),
    			'App\\' => array (
    					0 => $baseDir . '/app',
    			),
    	);
    }
    public static function getprefixesPsr0()
    {
    	
    }
    public static function getclassMap()
    {
    	
    }

    public static function getFiles()
    {
    	
    }
    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit::getprefixLengthsPsr4();
            $loader->prefixDirsPsr4 = ComposerStaticInit::getprefixDirsPsr4();
            $loader->prefixesPsr0 = ComposerStaticInit::getprefixesPsr0();
            $loader->classMap = ComposerStaticInit::getclassMap();

        }, null, ClassLoader::class);
    }
}
