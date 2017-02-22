<?php
namespace Illuminate\Cart;

class CartException extends \Exception
{
	public function showError()
	{
		return '<div style="color:red;">Error：'.$this->getMessage().'<br> &nbsp;&nbsp;&nbsp;in '.$this->getFile().' '.$this->getLine().'</div>';
	}
}