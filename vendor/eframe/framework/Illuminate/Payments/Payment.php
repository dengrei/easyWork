<?php
/**
 *
|+----------------------------------------
|支付类库
$payobj = new Illuminate\Payments\Payment('Wechat',array(
	'out_trade_no' => 'as645as4d6a'
));

$payobj->doPay();
|+----------------------------------------
 */
namespace Illuminate\Payments;


class Payment
{
	use \Illuminate\Payments\PaymentTrait;
	
	/**
	 *
	|+----------------------------------------
	| 支付请求
	|+----------------------------------------
	 */
	public function doPay()
	{
		if($this->payobj !== NULL){
			$this->payobj->bliudRequsetSubmit($this->paymentData,'post');
		}else{
			echo 'error';
		}
	}
}