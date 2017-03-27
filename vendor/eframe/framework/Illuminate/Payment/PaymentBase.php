<?php
/**
 *
|+----------------------------------------
|支付基类
|@author Administrator
|
|tags
|+----------------------------------------
 */
namespace  Illuminate\Payment;

class PaymentBase
{
	protected static $instance = null;
	/**
	 *
	|+----------------------------------------
	| 支付对象
	|+----------------------------------------
	 */
	protected $pay_instance    = null;
	/**
	 *
	|+----------------------------------------
	| 当前使用的支付方式
	| @var string
	|+----------------------------------------
	 */
	protected $payment         = '';
	/**
	 *
	|+----------------------------------------
	| 允许使用的支付方式,使用别名
	| @var array
	|+----------------------------------------
	 */
	protected $access_payments = array(
			'AlipayJs'     => 'AlipayJs',
	);
	/**
	 *
	|+----------------------------------------
	| 错误信息
	| @var array
	|+----------------------------------------
	 */
	protected $error_msg = array();
	/**
	 *
	|+----------------------------------------
	| 单例
	| @return object
	|+----------------------------------------
	 */
	public static function getInstance()
	{
		if(self::$instance === null){
			self::$instance = new static;
		}
		
		return self::$instance;
	}
	/**
	 *
	|+----------------------------------------
	| @param string $payment 支付方式
	| @param array $options  可选参数,不为空时格式：$options = array('type'=>'',data=>array()),
	|                        type为支付方式下签名分类，data为额外数据
	|+----------------------------------------
	 */
	public function bootstrap($payment,$options = array())
	{
	    $this->payment = $payment;
	    
	    $paymentlist = array_values($this->access_payments);
	    if(in_array($payment, $paymentlist)){
	        $this->payment = $payment;
	        	
	        //加载支付类库
	        $this->loadClass($options);
	    }else{
	        $this->setErrorMsg('支付方式错误');
	    }
	}
	/**
	 *
	|+----------------------------------------
	| 添加错误信息
	| @param string $msg
	| @param string $code
	|+----------------------------------------
	 */
	public function setErrorMsg($msg,$code = null)
	{
		$this->error_msg = array(
				'msg' => $msg,
				'code'=> $code
		);
	}
	/**
	 *
	|+----------------------------------------
	| 获取错误信息
	| @return array
	|+----------------------------------------
	 */
	public function getErrorMsg()
	{
		return $this->error_msg;
	}
	/**
	 *
	|+----------------------------------------
	| 加载相应的支付类库
	| @param array $options
	|+----------------------------------------
	 */
	protected function loadClass($options)
	{
		if($this->error_msg){
			return false;
		}
		
		$pay_classes = array(
				'AlipayJs' => array(
						'Md5' => array(
						    'Illuminate\Payment\AlipayJs\Md5\PaymentHandle'
						),
						'Rsa' => array(
						    'Illuminate\Payment\AlipayJs\Rsa\PaymentHandle'
						),
				)
		);
		
		$classes = $pay_classes[$this->payment];
		if($options){
			$classes = $pay_classes[$this->payment][$options['type']];
		}
		
		foreach($classes as $class){
			$class_file = CORE_PATH.(str_replace('\\', '/', $class)).'.php';
			if(file_exists($class_file)){
				require_once $class_file;
			}
		}
		
		$param_arr = isset($options['data'])?$options['data']:array();
		//自动执行支付类中bootstrap方法处理并返回支付方式单例对象
		$class_name= end($classes);
		$obj = call_user_func_array(array(new $class_name,'bootstrap'), $param_arr);
		
		$this->pay_instance = $obj;
		
		return $obj;
	}
}