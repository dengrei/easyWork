<?php
namespace Illuminate\Foundation\Exceptions;

class HandleException extends \Exception
{
	public function showError()
	{
		return '<div style="color:red;">Error：'.$this->getMessage().'<br> &nbsp;&nbsp;&nbsp;in '.$this->getFile().' '.$this->getLine().'</div>';
	}
}