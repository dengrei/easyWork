<?php
namespace Illuminate\Payments\Alipay;

class PaymentAlipay
{
	private $config = array();
	
	/**
	 *
	|+----------------------------------------
	| 初始化数据
	|+----------------------------------------
	 */
	public function __construct()
	{
		/*
		 * alipay.wap.create.direct.pay.by.user 手机网址支付
		 */
		$this->config = array(
				'service'			=> 'alipay.wap.create.direct.pay.by.user',
				'partner'    		=> '',
				'_input_charset'	=> '',
				'sign_type'			=> '',
				'sign'				=> '',
				'notify_url'		=> '',
		);
	}
	/**
	 *
	|+----------------------------------------
	| 添加配置信息
	| @param string $name
	| @param string $value
	|+----------------------------------------
	 */
	public function __set($name,$value)
	{
		$this->config[$name] = $value;
	}
	/**
	 * 获取支付数据
	|+----------------------------------------
	| @see \Illuminate\Payments\PaymentInterface::getPaymentData()
	|+----------------------------------------
	 */
	public function getPaymentData($out_trade_no,$subject,$total_fee,$show_url,$body)
	{
		//文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.2Z6TSk&treeId=60&articleId=103693&docType=1
		$data = array(
				"service"       => $this->config['service'],
				"partner"       => $this->config['partner'],
				"seller_id"     => $this->config['seller_id'],
				"payment_type"	=> $this->config['payment_type'],
				"notify_url"	=> $this->config['notify_url'],
				"return_url"	=> $this->config['return_url'],
				"_input_charset"=> trim(strtolower($this->config['input_charset'])),
				"out_trade_no"	=> $out_trade_no,
				"subject"	    => $subject,
				"total_fee"	    => $total_fee,
				"show_url"	    => $show_url,
				"body"	        => $body,
		);
		return $data;
	}
	/**
	 *
	|+----------------------------------------
	| 提交支付请求
	| @param array $parameter     支付参数
	| @param string $requestType  请求类型，post get
	| @param string $button       提交表单按钮文字
	|+----------------------------------------
	 */
	public function bliudRequsetSubmit($parameter,$requestType,$button='')
	{
		$button = $button == ''?'确认':$button;
	}
	/**
	 * 支付回调处理
	|+----------------------------------------
	| @see \Illuminate\Payments\PaymentInterface::notifyHandle()
	|+----------------------------------------
	 */
	public function notifyHandle(){}
}