<?php
namespace Illuminate\Payment;

class Payment extends PaymentBase
{
    /**
     *
    |+----------------------------------------
    | 添加数据
    | @param array $config
    |+----------------------------------------
     */
    public function setConfig($config)
    {
        $this->pay_instance->setConfig($config);
    }
    /**
     *
    |+----------------------------------------
    | 去支付
    |+----------------------------------------
     */
    public function doPay()
    {
        $this->pay_instance->doPay();
    }
}