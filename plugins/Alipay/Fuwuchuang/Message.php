<?php
/**
 * 在调用此类前，必须在控制器中先初始化Lotus
 * $this->initLotus();
 * Lotus是一个第三方框架，里面包含很多自定义方法
 */
namespace Alipay\Lib\Fuwuchuang;

use Alipay\Lib\Fuwuchuang\AbstractLib;
use Alipay\Lib\Fuwuchuang\PushMsg;

class Message extends AbstractLib{

	public function Message($biz_content) {
		if($biz_content){
			header ( "Content-Type: text/xml;charset=GBK" );
			//writeLog ( $biz_content );
			$UserInfo = $this->getNode ( $biz_content, "UserInfo" );
			$FromUserId = $this->getNode ( $biz_content, "FromUserId" );
			$AppId = $this->getNode ( $biz_content, "AppId" );
			$CreateTime = $this->getNode ( $biz_content, "CreateTime" );
			$MsgType = $this->getNode ( $biz_content, "MsgType" );
			$EventType = $this->getNode ( $biz_content, "EventType" );
			$AgreementId = $this->getNode ( $biz_content, "AgreementId" );
			$ActionParam = $this->getNode ( $biz_content, "ActionParam" );
			$AccountNo = $this->getNode ( $biz_content, "AccountNo" );
			$push = new PushMsg();
			// 收到用户发送的对话消息
			if ($MsgType == "text") {

				$text = $this->getNode ( $biz_content, "Text" );
				//writeLog ( "收到的文本：" . $text );

				$text_msg = $push->mkTextMsg ( "你好，这是对话消息" );

				// 发给这个关注的用户
				$biz_content = $push->mkTextBizContent ( $FromUserId, $text_msg );
				$biz_content = iconv ( "UTF-8", "GBK//IGNORE", $biz_content );
				//writeLog ( iconv ( "UTF-8", "GBK", "\r\n发送的biz_content：" . $biz_content ) );
				// $return_msg = $push->sendMsgRequest ( $biz_content );
				$return_msg = $push->sendRequest ( $biz_content );
				// 日志记录
				//writeLog ( "发送对话消息返回：" . $return_msg );
			}

			// 接收用户发送的 图片消息
			if ($MsgType == "image") {

				$mediaId = $this->getNode ( $biz_content, "MediaId" );
				$format = $this->getNode ( $biz_content, "Format" );

				$biz_content = "{\"mediaId\":\"" . $mediaId . "\"}";

				$fileName = realpath ( "img" ) . "/$mediaId.$format";
				// 下载保存图片
				$push->downMediaRequest ( $biz_content, $fileName );

				//writeLog ( "收到的图片路径：" . $fileName );

				$text_msg = $push->mkTextMsg ( "你好，图片已接收。" );

				// 发给这个关注的用户
				$biz_content = $push->mkTextBizContent ( $FromUserId, $text_msg );
				$biz_content = iconv ( "UTF-8", "GBK//IGNORE", $biz_content );
				//writeLog ( iconv ( "UTF-8", "GBK", "\r\n发送的biz_content：" . $biz_content ) );
				// $return_msg = $push->sendMsgRequest ( $biz_content );
				$return_msg = $push->sendRequest ( $biz_content );
				// 日志记录
				//writeLog ( "发送对话消息返回：" . $return_msg );
			}

			// 收到用户发送的关注消息
			if ($EventType == "follow") {
				// 处理关注消息
				// 一般情况下，可推送一条欢迎消息或使用指导的消息。
				// 如：
				$image_text_msg1 = $push->mkImageTextMsg ( "标题，感谢关注", "描述", "http://wap.taobao.com", "https://i.alipayobjects.com/e/201310/1H9ctsy9oN_src.jpg", "loginAuth" );
				$image_text_msg2 = $push->mkImageTextMsg ( "标题", "描述", "http://wap.taobao.com", "https://i.alipayobjects.com/e/201310/1H9ctsy9oN_src.jpg", "loginAuth" );
				// 组装多条图文信息
				$image_text_msg = array (
					$image_text_msg1,
					$image_text_msg2
				);
				// 发给这个关注的用户
				$biz_content = $push->mkImageTextBizContent ( $FromUserId, $image_text_msg );

				$return_msg = $push->sendRequest ( $biz_content );
				// 日志记录
				file_put_contents ( "log.txt", $return_msg . "\r\n", FILE_APPEND );
			} elseif ($EventType == "unfollow") {
				// 处理取消关注消息
			} elseif ($EventType == "enter") {

				// 处理进入消息，扫描二维码进入,获取二维码扫描传过来的参数

				$arr = json_decode ( $ActionParam );
				if ($arr != null) {
					//writeLog ( "二维码传来的参数：" . var_export ( $arr, true ) );

					$sceneId = $arr->scene->sceneId;
					//writeLog ( "二维码传来的参数,场景ID：" . $sceneId );
					// 这里可以根据定义场景ID时指定的规则，来处理对应事件。
					// 如：跳转到某个页面，或记录从什么来源(哪种宣传方式)来关注的本服务窗
				}
				// 处理关注消息
				// 一般情况下，可推送一条欢迎消息或使用指导的消息。
				// 如：
				$image_text_msg1 = $push->mkImageTextMsg ( "标题，进入服务窗", "描述：进入服务窗", "http://wap.taobao.com", "", "loginAuth" );
				// $image_text_msg2 = $push->mkImageTextMsg ( "标题", "描述", "http://wap.taobao.com", "https://i.alipayobjects.com/e/201310/1H9ctsy9oN_src.jpg", "loginAuth" );
				// 组装多条图文信息
				$image_text_msg = array (
					$image_text_msg1
					// $image_text_msg2
				);

				// 发给这个关注的用户
				$biz_content = $push->mkImageTextBizContent ( $FromUserId, $image_text_msg );

				$return_msg = $push->sendRequest ( $biz_content );
				// 日志记录
				//writeLog ( "发送消息返回：" . var_export ( $return_msg, true ) );
			} elseif ($EventType == "click") {
				// 处理菜单点击的消息

				// 在服务窗后台配置一个菜单，菜单类型为调用服务，菜单参数为sendmsg，用户点击次菜单后，就会调用到这里
				if ($ActionParam == "sendmsg") {
					$image_text_msg1 = $push->mkImageTextMsg ( "标题，发送消息测试", "描述：发送消息测试", "http://wap.taobao.com", "", "loginAuth" );
					// 组装多条图文信息
					$image_text_msg = array (
						$image_text_msg1
					);

					// 发给这个关注的用户
					$biz_content = $push->mkImageTextBizContent ( $FromUserId, $image_text_msg );

					$return_msg = $push->sendRequest ( $biz_content );
					// 日志记录
					//writeLog ( "发送消息返回：" . var_export ( $return_msg, true ) );
				}
			}

			// 给支付宝返回ACK回应消息，不然支付宝会再次重试发送消息,再调用此方法之前，不要打印输出任何内容
			echo self::mkAckMsg ( $FromUserId );
			exit ();
		}
	}
	public function mkAckMsg($toUserId) {
		$as = new AlipaySign();
		$config = $this->getConfig();
		$response_xml = "<XML><ToUserId><![CDATA[" . $toUserId . "]]></ToUserId><AppId><![CDATA[" . $config['app_id'] . "]]></AppId><CreateTime>" . time () . "</CreateTime><MsgType><![CDATA[ack]]></MsgType></XML>";
		
		$return_xml = $as->sign_response ( $response_xml, $config ['charset'], $config ['merchant_private_key_file'] );
		//writeLog ( "response_xml: " . $return_xml );
		return $return_xml;
	}

	/**
	 * 直接获取xml中某个结点的内容
	 * @param unknown $xml
	 * @param unknown $node
	 * @return string
	 */
	public function getNode($xml, $node) {
		$xml = "<?xml version=\"1.0\" encoding=\"GBK\"?>" . $xml;
		$dom = new \DOMDocument("1.0","GBK");
		$dom->loadXML ( $xml );
		$event_type = $dom->getElementsByTagName ( $node );
		return $event_type->item ( 0 )->nodeValue;
	}

}