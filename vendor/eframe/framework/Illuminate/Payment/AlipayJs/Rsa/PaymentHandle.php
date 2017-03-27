<?php
namespace Illuminate\Payment\AlipayJs\Rsa;

class PaymentHandle
{
    protected $instance = null;
    protected $option   = array();
    
    public function bootstrap($option = array())
    {
        if($this->instance === null){
            $this->instance = new PaymentHandle();
        }
        
        $this->option = $option;
        
        return $this->instance;
    }
    
    public function setConfig()
    {
        echo 11;
    }
}