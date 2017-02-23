<?php
namespace Illuminate\Container;

interface Contract
{
	public function instance($abstract, $instance);
}