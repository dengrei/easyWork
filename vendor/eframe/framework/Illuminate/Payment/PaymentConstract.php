<?php
namespace Illuminate\Payment;

interface PaymentConstract
{
	public function setConfig();
	public function getConfig($key = null);
	public function setErrorMsg($msg,$code = null);
	public function getErrorMsg();
}