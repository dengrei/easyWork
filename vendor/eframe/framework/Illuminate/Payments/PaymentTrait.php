<?php
/**
 *
|+----------------------------------------
|支付中间件
|+----------------------------------------
 */
namespace Illuminate\Payments;

trait PaymentTrait
{
	public $payment     = '';
	public $paymentData = array();
	public $payobj      = NULL;
	/**
	 *
	|+----------------------------------------
	| 获取支付类型
	| @param string $payment 支付方式
	| @param array $options  支付数据
	|+----------------------------------------
	 */
	public function __construct($payment,$options)
	{
		$this->payment = $payment;

		//支付方式配置，可放到配置文件
		$payments      = array(
				'Alipay'	=> 'Illuminate\Payments\Alipay\PaymentAlipay',
				'Wechat'	=> 'Illuminate\Payments\Wechat\PaymentWechat'
		);
		
		if(!empty($payments[$payment])){
			$this->payobj  = new $payments[$payment];
			
			//调用不同的数据处理
			$method = 'set'.$payment.'Data';
			$this->$method($options);
		}
	}
	
	/**
	 *
	|+----------------------------------------
	| 支付宝支付数据处理
	| @param array $options
	|+----------------------------------------
	 */
	private function setAlipayData($options)
	{
		$out_trade_no = trim($options['out_trade_no']);
		$subject      = trim($options['subject']);
		$total_fee    = floatval($options['total_fee']);
		$show_url     = trim($options['shop_url']);
		$body         = trim($options['body']);
		
		$this->paymentData = $this->payobj->getPaymentData($out_trade_no,$subject,$total_fee,$show_url,$body);
	}
	/**
	 *
	|+----------------------------------------
	| 微信支付数据处理
	| @param array $options
	|+----------------------------------------
	 */
	private function setWechatData($options)
	{
		$out_trade_no = trim($options['out_trade_no']);
		$subject      = trim($options['subject']);
		$total_fee    = floatval($options['total_fee']);
		$show_url     = trim($options['shop_url']);
		$body         = trim($options['body']);
		
		$this->paymentData = $this->payobj->getPaymentData($out_trade_no,$subject,$total_fee,$show_url,$body);
	}
}