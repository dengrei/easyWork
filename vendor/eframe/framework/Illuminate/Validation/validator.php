<?php
/**
 *
|+----------------------------------------
|表单验证库

使用案例:
$rules = array(
		'name' => array(
				'rules'   => 'require|uri',
				'message' => array(
						'require' => '名称必填',
						'min'     => '最小{min}',
						'max'     => '用户名称最大为{max}位',
						'uri'     => '请填写正确的网址'
				)
		),
		'pwd'  => array(
				'rules'   => 'require|min:6|max:12|trim',
				'message' => array(
						'require' => '密码必填',
						'min'     => '最小{min}',
						'max'     => '最大{max}'
				)
		),
);
$data = array(
		'name' => 'http://www.baidu.com',
		'pwd'  => ' asdasd ',
		'email'=> 'z@163.com'
);
$obj   = new Illuminate\Validation\Validator($data,$rules);
$data  = $obj->validate();
$error = $obj->getError();
|+----------------------------------------
 */
namespace Illuminate\Validation;

use Illuminate\Support\Str;
class Validator
{
	/**
	 *
	|+----------------------------------------
	| 禁止接收的参数或允许接收的参数，根据初始化的type而定
	| @var array
	|+----------------------------------------
	 */
	private $getForms  = array();
	/**
	 *
	|+----------------------------------------
	| 接收的表单数据
	| @var array
	|+----------------------------------------
	 */
	private $data  = array();
	/**
	 *
	|+----------------------------------------
	| 解析后的验证规则
	| @var array
	|+----------------------------------------
	 */
	private $rules = array();
	/**
	 *
	|+----------------------------------------
	| 验证提示信息模板
	| @var array
	|+----------------------------------------
	 */
	private $message = array();
	/**
	 *
	|+----------------------------------------
	| 验证错误信息
	| @var array
	|+----------------------------------------
	 */
	private $error = array();
	/**
	 *
	|+----------------------------------------
	| 接收类型，deny禁止，allow允许，与$getForms配合使用
	| @var string
	|+----------------------------------------
	 */
	private $type  = 'deny';
	/**
	 *
	|+----------------------------------------
	| 过滤库，可添加自定义过滤函数
	| @var array
	|+----------------------------------------
	 */
	private $filters = array('trim','intval','floatval','htmlspecialchars','strval','ltrim','rtrim','htmlentities');
	/**
	 *
	|+----------------------------------------
	| 初始化
	| @param array $data  验证规则
		$data = array(
			'name' => array(
				'rules'   => 'require|min:1|max:2',
				'message' => array(
						'require' => '名称必填',
						'min'     => '最小{min}',
						'max'     => '最大{max}'
				)
			),
		)
	| @param string $type 验证类型,deny禁止接收参数,allow允许接收的参数,配置$getForms
	|+----------------------------------------
	 */
	public function __construct($data,$rules,$type='deny',$forms=array())
	{
		$this->getForms = $forms;
		$this->data     = $data;
		$this->type     = $type;
		$this->getFormsRules($rules);
	}
	/**
	 *
	|+----------------------------------------
	| 执行验证
	| @return array[]
	|+----------------------------------------
	 */
	public function validate()
	{
		foreach($this->data as $name=>$val){
			if($this->type == 'deny' && !in_array($name,$this->getForms)){
				$this->vali($name);
			}elseif($this->type == 'allow' && in_array($name,$this->getForms)){
				$this->vali($name);
			}
		}
		return $this->data;
	}
	/**
	 *
	|+----------------------------------------
	| 获取需要的数据规则
	| @param array $data
	|+----------------------------------------
	 */
	protected function getFormsRules($data)
	{
		foreach ($data as $name => $item){
			$this->getValidateForms($name,$item);
		}
	}
	/**
	 *
	|+----------------------------------------
	| 初始完成验证字段，规则，提示信息
	| @param unknown $name
	| @param unknown $item
	| @return NULL[]
	|+----------------------------------------
	 */
	protected function getValidateForms($name,$item)
	{
		$itemRule            = empty($item['rules'])?array():explode('|', $item['rules']);
		$this->message[$name]= $item['message'];
		$this->rules[$name]  = $this->explodeRules($itemRule);
	}
	/**
	 *
	|+----------------------------------------
	| 解析验证规则
	| @param array $rules
	| @return array
	|+----------------------------------------
	 */
	protected function explodeRules(array $rules)
	{
		foreach ($rules as $key => $rule) {
			if (Str::contains($rule, '*')) {
				unset($rules[$key]);
			} else {
				$rules[$key] = $rule;
			}
		}
	
		return $rules;
	}
	/**
	 *
	|+----------------------------------------
	| 验证数据
	| @param string $name
	|+----------------------------------------
	 */
	protected function vali($name)
	{
		$value = $this->data[$name];
		$rules = $this->rules[$name];
		
		if($rules !== NULL){
			$status = true;
			$filters= array();
			foreach($rules as $rule){
				$ruleArr  = strpos($rule,':')!==false?explode(':',$rule):array($rule);
				$ruleName = $ruleArr[0];

				if(in_array($ruleName,$this->filters)){
					//过滤数据
					$filters[] = $ruleName;
				}else{
					//验证数据
					$method   = 'vali'.ucwords($ruleName);
					$bool     = $this->$method($value,$ruleArr);
				}

				if($bool === false){
					$this->error[$name] = $this->setError($name,$ruleArr);
					$status = false;
					break;
				}
			}

			if($status===false){
				$this->data = array();
			}elseif($filters){
				$filtersStr        = implode(',',$filters);
				$this->data[$name] = $this->filterParam($value,$filtersStr);
			}
		}
	}
	/**
	 *
	|+----------------------------------------
	| 存储错误信息
	| @param string $name
	| @param array $ruleArr
	| @return mixed
	|+----------------------------------------
	 */
	protected function setError($name,$ruleArr)
	{
		$errorMsg = $this->message[$name][$ruleArr[0]];
		if(preg_match('/\{'.$ruleArr[0].'\}/',$errorMsg)){
			$errorMsg = str_replace('{'.$ruleArr[0].'}', $ruleArr[1], $errorMsg);
		}
		return $errorMsg;
	}
	/**
	 *
	|+----------------------------------------
	| 获取错误信息
	| @return array
	|+----------------------------------------
	 */
	public function getError()
	{
		return $this->error;
	}
	/**
	 *
	|+----------------------------------------
	| 过滤数据
	| @param mixed $value
	| @param string $filters,如：trim,intval,但必须存在于库$filters中
	| @return mixed
	|+----------------------------------------
	 */
	public function filterParam($value,$filters='')
	{
		if($filters != ''){
			$filtersArr = explode(',', $filters);
			foreach($filtersArr as $filter){
				$value = $this->array_map_recursive($filter, $value);
			}
		}
		return $value;
	}
	/**
	 * 回调自定义函数
	 * @param 函数名称 $func
	 * @param 参数           $data
	 */
	protected function array_map_recursive($func,$data)
	{
		if(!empty($func)){
			if(is_array($data)){
				$data = array_map($func, $data);
			}else{
				$data = $func($data);
			}
		}
		return $data;
	}
	
	/**
	 *
	|+----------------------------------------
	| 非空验证
	| @param string $value
	| @param array $ruleArr
	| @return boolean
	|+----------------------------------------
	 */
	protected function valiRequire($value,$ruleArr=array())
	{
		return empty($value)?false:true;
	}
	/**
	 *
	|+----------------------------------------
	| 最小长度验证
	| @param string $value
	| @param array $ruleArr
	| @return boolean
	|+----------------------------------------
	 */
	protected function valiMin($value,$ruleArr=array())
	{
		return mb_strlen($value,'UTF-8') < $ruleArr[1]?false:true;
	}
	/**
	 *
	 |+----------------------------------------
	 | 最大长度验证
	 | @param string $value
	 | @param array $ruleArr
	 | @return boolean
	 |+----------------------------------------
	 */
	protected function valiMax($value,$ruleArr=array())
	{
		return mb_strlen($value,'UTF-8') > $ruleArr[1]?false:true;
	}
	/**
	 *
	 |+----------------------------------------
	 | 邮箱验证
	 | @param string $value
	 | @param array $ruleArr
	 | @return boolean
	 |+----------------------------------------
	 */
	protected function valiEmail($value,$ruleArr=array())
	{
		return !preg_match('/^\w+@\w+(\.[a-zA-Z]+)+$/',$value)?false:true;
	}
	/**
	 *
	 |+----------------------------------------
	 | 手机验证
	 | @param string $value
	 | @param array $ruleArr
	 | @return boolean
	 |+----------------------------------------
	 */
	protected function valiMobile($value,$ruleArr=array())
	{
		return !preg_match('/^1[0-9]{10}$/',$value)?false:true;
	}
	/**
	 *
	 |+----------------------------------------
	 | 联系电话验证，包含手机和座机
	 | @param string $value
	 | @param array $ruleArr
	 | @return boolean
	 |+----------------------------------------
	 */
	protected function valiContactPhone($value,$ruleArr=array())
	{
		return !preg_match('/^(1|[0-9]{3,5})(\-)*[0-9]{7,10}$/',$value)?false:true;
	}
	/**
	 *
	 |+----------------------------------------
	 | 网址验证
	 | @param string $value
	 | @param array $ruleArr
	 | @return boolean
	 |+----------------------------------------
	 */
	protected function valiUri($value,$ruleArr=array())
	{
		return !preg_match('/^http(s)?:\/\/?(www\.)\w+(\.[a-zA-Z]+)+/',$value)?false:true;
	}
	/**
	 *
	 |+----------------------------------------
	 | 确认密码验证
	 | @param string $value
	 | @param array $ruleArr
	 | @return boolean
	 |+----------------------------------------
	 */
	protected function valiConfirm($value,$ruleArr=array())
	{
		return empty($this->data[$ruleArr[1]]) || $value != $this->data[$ruleArr[1]]?false:true;
	}
}

