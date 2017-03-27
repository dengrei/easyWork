<?php
/**
 *
|+----------------------------------------
|支付宝 手机网站支付，MD5签名
|@author Administrator
|
|tags
|+----------------------------------------
 */
namespace Illuminate\Payment\AlipayJs\Md5;

class PaymentHandle
{
    protected $instance = null;
    protected $option   = array();
    protected $config   = array();
    
    /**
     *
    |+----------------------------------------
    | 初始化
    | @param array $option
    | @return \Illuminate\Payment\AlipayJs\Md5\PaymentHandle
    |+----------------------------------------
     */
    public function bootstrap($option = array())
    {
        if($this->instance === null){
            $this->instance = new PaymentHandle();
        }
        
        $this->option = $option;
        
        return $this->instance;
    }
    /**
     *
    |+----------------------------------------
    | 支付配置
    | @param array $data 参数
    |+----------------------------------------
     */
    public function setConfig($data = array())
    {
        $pay_config   = array(
            'partner'        => '2088021083728654', //合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
            'seller_id'      => 'scshux@qq.com',
            'key'            => '9t1yvksltjph47slu6hbsfha7h8d4pa9', //// MD5密钥，安全检验码，由数字和字母组成的32位字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
            'notify_url'     => '',
            'return_url'     => '',
            'sign_type'      => strtoupper('MD5'),
            'input_charset'  => strtolower('utf-8'),
            'cacert'         => getcwd().'\\cacert.pem', //ca证书路径地址，用于curl中ssl校验 请保证cacert.pem文件在当前文件夹目录中
            'transport'      => 'http', //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
            'payment_type'   => '1',
            'service'        => 'alipay.wap.create.direct.pay.by.user',
            
            "out_trade_no"	=> '',
            "subject"	    => '',
            "total_fee"	    => '0.00',
            "show_url"	    => '',
            "body"	        => '',
        );
        $this->config = array_merge($pay_config,$data);
    }
    /**
     *
    |+----------------------------------------
    | 去支付
    |+----------------------------------------
     */
    public function doPay()
    {
        $parameter = array(
            "service"       => $this->config['service'],
            "partner"       => $this->config['partner'],
            "seller_id"     => $this->config['seller_id'],
            "payment_type"	=> $this->config['payment_type'],
            "notify_url"	=> $this->config['notify_url'],
            "return_url"	=> $this->config['return_url'],
            "_input_charset"=> $this->config['input_charset'],
            "out_trade_no"	=> $this->config['out_trade_no'],
            "subject"	    => $this->config['subject'],
            "total_fee"	    => $this->config['total_fee'],
            "show_url"	    => $this->config['show_url'],
            "body"	        => $this->config['body'],
            //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.2Z6TSk&treeId=60&articleId=103693&docType=1
            //如"参数名"	=> "参数值"   注：上一个参数末尾需要“,”逗号。
        );
        
       // require_once("lib/alipay_submit.class.php");
        
        $alipaySubmit = new \Illuminate\Payment\AlipayJs\Md5\lib\AlipaySubmit($this->config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        
        $html = <<<str
            <!DOCTYPE html>
            <html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <title>支付宝手机网站支付接口接口</title>
            </head>
        
            <body>
                $html_text
            </body>
            </html>
str;
        echo $html;
    }
    /**
     *
    |+----------------------------------------
    | 异步回调
    |+----------------------------------------
     */
    public function AlipayJsNotify()
    {
        //计算得出通知验证结果
        $alipayNotify  = new \Illuminate\Payment\AlipayJs\Md5\lib\AlipayNotify($this->config);
        $verify_result = $alipayNotify->verifyNotify();
        
        if($verify_result) {
            //验证成功
            
            //商户订单号
            $out_trade_no = htmlspecialchars($_POST['out_trade_no']);
            //支付宝交易号
            $trade_no     = htmlspecialchars($_POST['trade_no']);
            //交易状态
            $trade_status = htmlspecialchars($_POST['trade_status']);
        
            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序
        
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
        
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序
        
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
        
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
        
            echo "success";
        }
        else {
            //验证失败
            echo "fail";
        
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }
    /**
     *
    |+----------------------------------------
    | 同步回调
    |+----------------------------------------
     */
    public function AlipayJsCallback()
    {
        //计算得出通知验证结果
        $alipayNotify  = new \Illuminate\Payment\AlipayJs\Md5\lib\AlipayNotify($this->config);
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result) {
            //验证成功
            
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
        
            //商户订单号
        
            $out_trade_no = $_GET['out_trade_no'];
        
            //支付宝交易号
        
            $trade_no = $_GET['trade_no'];
        
            //交易状态
            $trade_status = $_GET['trade_status'];
        
        
            if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
            }
            else {
                echo "trade_status=".$_GET['trade_status'];
            }
        
            echo "验证成功<br />";
        }
        else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数
            echo "验证失败";
        }
    }
}