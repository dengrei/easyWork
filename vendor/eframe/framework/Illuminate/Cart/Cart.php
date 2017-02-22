<?php
namespace Illuminate\Cart;

use Illuminate\Foundation\Exceptions\HandleException;
class Cart
{
	use \Illuminate\Cart\CartTrait;
	
	public function add()
	{
		if($this->cartobj!=NULL){
			$this->cartobj->add();
		}else{
			try{
				throw new HandleException('cart is no-object');
			}catch (HandleException $e){
				echo $e->showError();
			}
		}
	}
}