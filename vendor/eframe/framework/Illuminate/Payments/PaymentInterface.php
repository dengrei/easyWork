<?php
/**
 *
|+----------------------------------------
|支付接口
|+----------------------------------------
 */
namespace Illuminate\Payments;

interface PaymentInterface
{
	
	public function getPaymentData(){}
	public function notifyHandle(){}
}