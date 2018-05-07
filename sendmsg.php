<?php
header("Access-Control-Allow-Origin:*");
require 'autoload.php';
require 'Medoo.php';
use Gaoming13\WechatPhpSdk\Api;
use Medoo\Medoo;

$db = new Medoo([
'database_type' => 'mysql',
'database_name' => 'echat',
'server' => '127.0.0.1',
'username' => 'root',
'password' => '',
'charset' => 'utf8'
]);


// AppID(应用ID)
$appId = 'wxd928c5eeb23fasfwr';
// AppSecret(应用密钥)
$appSecret = '93143427e98e5080ade4132f454faf01';

// 实例化一个Api对象，给出关键参数并需要用户自己实现access_token的返回和保存
$api = new Api(
	array(
		'appId' => $appId,
		'appSecret'	=> $appSecret,
		'get_access_token' => function() use($db){
			/* 用户需要自己实现access_token的返回，本例为从自己数据库取出token
			return $db->get("access_token","value",[
				"id" => 1
			]);
			*/
		},
		'save_access_token' => function($token) use($db){
			/*用户需要自己实现access_token的保存，当get_access_token方法执行完后会自动验证access_token的有效性，如果无效会重新向微信服务器发起请求并更新
			$db->update("access_token",[
				"value" => $token
			],[
				"id" => 1
			]);
			*/
		}
	)
);

// 将消息发送给对应的用户，userid为数据库中自定义的用户id
$userid = $_POST['userid'];
// 给用户发送的消息内容
$msg = $_POST['msg'];


// 用userid查询并获取用户的openid，发送消息并返回发送结果
if($api->send($db->get("wechatuser","openid",[
	"id" => $userid
]),$msg)){
	$result=['errno'=>0,'msg'=>'Message send success'];
}else{
	$result=['errno'=>1,'msg'=>'Message send failed'];
}

echo json_encode($result);