<?php
namespace Illuminate\Cart;

use Illuminate\Foundation\Exceptions\HandleException;
trait CartTrait
{
	protected $cartobj = NULL;
	
	/**
	 *
	|+----------------------------------------
	| 初始化购物车
	| @param number $type    需要调用的购物车类库，可配置多种
	| @throws CartException
	|+----------------------------------------
	 */
	public function __construct($type)
	{
		$typearr = array(
				'1' => 'Illuminate\Cart\cartgroups\CartOne',
		);
		
		if(!empty($typearr[$type])){
			$this->cartobj = new $typearr[$type];
		}else{
			try{
				throw new HandleException('cart class not find');
			}catch (HandleException $e){
				echo $e->showError();
			}
		}
	}
}