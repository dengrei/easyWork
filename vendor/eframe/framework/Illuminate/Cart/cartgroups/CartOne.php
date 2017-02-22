<?php
/**
 *
|+----------------------------------------
|购物车类库1
|+----------------------------------------
 */
namespace Illuminate\Cart\cartgroups;

use Illuminate\Cart\Cartinterface;
class CartOne implements Cartinterface
{
	public function add()
	{
		echo 2;
	}
}