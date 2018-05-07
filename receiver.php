<?php
header("Access-Control-Allow-Origin:*");
require 'autoload.php';
require 'Medoo.php';
use Gaoming13\WechatPhpSdk\Wechat;
use Gaoming13\WechatPhpSdk\Api;
/*use Medoo\Medoo;

$db = new Medoo([
'database_type' => 'mysql',
'database_name' => 'echat',
'server' => '127.0.0.1',
'username' => 'root',
'password' => '',
'charset' => 'utf8'
]);
此部分用作数据库的操作，可根据业务逻辑需要自行处理
*/

// AppID(应用ID，在对应的微信公众平台管理页面查看)
$appId = 'wxd928c5eeb23fasfwr';
// AppSecret(应用密钥，同上)
$appSecret = '93143427e98e5080ade4132f454faf01';
// Token(令牌，在对应公众平台设置，详情请查看说明文档)
$token = 'dolashink';
// EncodingAESKey(消息加解密密钥，在对应公众平台管理页面查看)
$encodingAESKey = 'gYel4FJ40AL8srtg123SJLufM7i5wtpLI1adjsli174';
// 注意：以上密钥均为无效密钥，只作提示使用

//实例化一个Wechat对象，并给出关键的三个参数
$wechat = new Wechat(array(
	'appId' 		=>	'wxd928c5eeb0bf7723',
	'token' 		=> 	'dolashink',
	'encodingAESKey' =>	'gYel4h0L0nV3srtgtOP13OufM7i5wtpLI1adhhMt174'
));

//这里的$msg为从微信服务器接收到的消息对象
$msg = $wechat->serve();
//这里的$openid为消息发送者的openid，用户对应此公众号的唯一ID，可用作数据库识别用户的唯一标识
$openid = $msg->FromUserName;

// 建立socket连接到内部推送端口
$client = stream_socket_client('tcp://127.0.0.1:5678', $errno, $errmsg, 1);
// 推送的数据，包含uid字段，表示是给这个uid推送(此uid为客户端进行socket请求时发送的唯一标识，可根据业务逻辑需要自行处理)
$data = array('uid'=>'shink','fromuser'=>"$openid",'msg'=>"$msg->Content");
// 发送数据，注意5678端口是Text协议的端口，Text协议需要在数据末尾加上换行符
fwrite($client, json_encode($data)."\n");