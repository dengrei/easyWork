<?php
/**
 *字符串处理
 */
namespace Illuminate\Support;

class Str
{
	/**
	 *
	|+----------------------------------------
	| 检查字符串中是否存在某个字符
	| @param unknown $haystack
	| @param string $neddle
	| @return boolean
	|+----------------------------------------
	 */
	public static function contains($haystack,$needles)
	{
		foreach ((array) $needles as $needle) {
			if ($needle != '' && strpos($haystack, $needle) !== false) {
				return true;
			}
		}
		
		return false;
	}
}