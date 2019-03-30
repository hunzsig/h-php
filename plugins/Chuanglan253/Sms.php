<?php
namespace plugins\Chuanglan253;

class Sms{

	//创蓝发送短信接口URL, 如无必要，该参数可不用修改
	const API_SEND_URL='http://sms.253.com/msg/send';

	//创蓝短信余额查询接口URL, 如无必要，该参数可不用修改
	const API_BALANCE_QUERY_URL='http://sms.253.com/msg/balance';

	private $API_ACCOUNT='';//创蓝账号 替换成你自己的账号

	private $API_PASSWORD='';//创蓝密码 替换成你自己的密码

	private $API_SIGN = '';


	public $errorCode = array(
		"101"=>"无此用户",
		"102"=>"密码错",
		"103"=>"提交过快（提交速度超过流速限制）",
		"104"=>"系统忙（因平台侧原因，暂时无法处理提交的短信）",
		"105"=>"敏感短信（短信内容包含敏感词）",
		"106"=>"消息长度错（>536或<=0）",
		"107"=>"包含错误的手机号码",
		"108"=>"手机号码个数错（群发>50000或<=0）",
		"109"=>"无发送额度（该用户可用短信数已使用完）",
		"110"=>"不在发送时间内",
		"113"=>"extno格式错（非数字或者长度不对）",
		"116"=>"签名不合法或未带签名（用户必须带签名的前提下）",
		"117"=>"IP地址认证错,请求调用的IP地址不是系统登记的IP地址",
		"118"=>"用户没有相应的发送权限（账号被禁止发送）",
		"119"=>"用户已过期",
		"120"=>"违反放盗用策略(日发限制)",
		"121"=>"必填参数。是否需要状态报告，取值true或false",
		"122"=>"5分钟内相同账号提交相同消息内容过多",
		"123"=>"发送类型错误",
	);

	public function __construct($account,$password,$sign){
		$this->API_ACCOUNT = $account;
		$this->API_PASSWORD = $password;
		$this->API_SIGN = $sign;
	}

    /**
     * 发送短信
     *
     * @param string $mobile 手机号码
     * @param string $msg 短信内容
     * @param int $needstatus 是否需要状态报告
     * @return mixed
     */
	public function sendSMS( $mobile, $msg, $needstatus = 1) {

		//创蓝接口参数
		$postArr = array (
			'un' => $this->API_ACCOUNT,
			'pw' => $this->API_PASSWORD,
			'msg' => $this->API_SIGN.$msg,
			'phone' => $mobile,
			'rd' => $needstatus
		);

		$result = $this->curlPost( self::API_SEND_URL , $postArr);
		return $result;
	}

	/**
	 * 查询额度
	 *
	 *  查询地址
	 */
	public function queryBalance() {

		//查询参数
		$postArr = array (
			'un' => $this->API_ACCOUNT,
			'pw' => $this->API_PASSWORD,
		);
		$result = $this->curlPost(self::API_BALANCE_QUERY_URL, $postArr);
		return $result;
	}

	/**
	 * 处理返回值
	 *
	 */
	public function execResult($result){
		$result=preg_split("/[,\r\n]/",$result);
		return $result;
	}

	/**
	 * 通过CURL发送HTTP请求
	 * @param string $url  //请求URL
	 * @param array $postFields //请求参数
	 * @return mixed
	 */
	private function curlPost($url,$postFields){
		$postFields = http_build_query($postFields);
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
		$result = curl_exec ( $ch );
		curl_close ( $ch );
		return $result;
	}

	//魔术获取
	public function __get($name){
		return $this->$name;
	}

	//魔术设置
	public function __set($name,$value){
		$this->$name=$value;
	}

}