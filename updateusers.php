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

$api = new Api(
	array(
		'appId' => $appId,
		'appSecret'	=> $appSecret,
		'get_access_token' => function() use($db){
			/* 用户需要自己实现access_token的返回
			return $db->get("access_token","value",[
				"id" => 1
			]);
			*/
		},
		'save_access_token' => function($token) use($db){
			/* 用户需要自己实现access_token的保存
			$db->update("access_token",[
				"value" => $token
			],[
				"id" => 1
			]);
			*/
		}
	)
);

// 拉取用户时需要客户端提供的标识和key，用作用户权限验证，根据业务自行处理
$uid = $_POST['uid'];
$key = $_POST['key'];

// 拉取公众号关注者的用户信息，根据业务自行处理
$userlist = $api->get_user_list()[1]->data->openid;
$userinfo;

// 进行数据库存储工作，如果用户存在则不做处理，否则储存用户信息并返回结果
if($uid!='' && $key!=''){
	if(is_array($userlist)){
		forEach($userlist as $value){
			$userinfo = $api->get_user_info($value);
			if(!$db->has("wechatuser",[
				"openid" => $userinfo[1]->openid
			])){
				$db->insert("wechatuser",[
					"openid" => $userinfo[1]->openid,
					"nickname" => $userinfo[1]->nickname,
					"sex" => $userinfo[1]->sex,
					"language" => $userinfo[1]->language,
					"city" => $userinfo[1]->city,
					"province" => $userinfo[1]->province,
					"country" => $userinfo[1]->country,
					"headimgurl" => $userinfo[1]->headimgurl,
					"subscribe_time" => $userinfo[1]->subscribe_time,
					"remark" => $userinfo[1]->remark
				]);
			}
		}
	}
	$result=['errno'=>0,'msg'=>'update success!'];	
}else{
	$result=['errno'=>1,'msg'=>'update failed!']
}
echo json_encode($result);
