# WechatMessageReceive-Push
PHP微信公众平台消息接收和推送。可以接收用户发来的消息并通过socket推送到指定客户端，也可通过客户端推送平台消息到指定用户。
## Introduction
本项目实现了公众号平台消息的即时接收和推送，因项目只提供一个简单的Demo，目前只支持文本消息的接收和推送，如果有需要请参考后另行开发。
项目主要使用到以下PHP库/框架
1. Medoo - 国人开发的轻量级数据库框架，详情请参考：https://medoo.lvtao.net/
2. Workman - 一个高性能的PHP Socket 服务器框架，详情请参考：http://www.workerman.net/
3. Wechat-php-sdk - 微信PHP-SDK，详情请参考： https://github.com/gaoming13/wechat-php-sdk
## Requirement
1. PHP >= 5.6
2. socket 拓展
## Usage
#### 微信公众平台配置
1. 首先确认公众平台的开发->基本设置->服务器配置项处于开启状态，并将服务器地址(URL)配置到项目的receiver.php文件
2. $AppID等参数请到公众平台自行查看更改
3. 以下Demo为项目内 `receiver.php`文件
```php
// AppID(应用ID)
$appId = 'wxd928c5eeb23fasfwr';
// AppSecret(应用密钥)
$appSecret = '93143427e98e5080ade4132f454faf01';
// Token(令牌)
$token = 'dolashink';
// EncodingAESKey(消息加解密密钥)
$encodingAESKey = 'gYel4FJ40AL8srtg123SJLufM7i5wtpLI1adjsli174';

$wechat = new Wechat(array(
	'appId' 		=>	$appID,
	'token' 		=> 	$token,
	'encodingAESKey' =>	$encodingAESKey
));

$msg = $wechat->serve();
$openid = $msg->FromUserName;

// 建立socket连接到内部推送端口
$client = stream_socket_client('tcp://127.0.0.1:5678', $errno, $errmsg, 1);
// 推送的数据，包含uid字段，表示是给这个uid推送(此uid为客户端进行socket请求时发送的唯一标识，可根据业务逻辑需要自行处理)
$data = array('uid'=>'shink','fromuser'=>"$openid",'msg'=>"$msg->Content");
// 发送数据，注意5678端口是Text协议的端口，Text协议需要在数据末尾加上换行符
fwrite($client, json_encode($data)."\n");
```
#### 服务端开启socket服务
开启命令行，进入项目根目录下，启动`start.php`  
命令：`php start.php start`  
注：因我们需要将微信服务器推送到自己服务器的消息通过本服务器的socket服务推送出去，所以这里建立了一个文本协议把接收到的消息转发送到了socket服务中，
然后再由socket进行即时推送至客户端，实际这个服务中还包含了一个文本协议。详情查看项目中`start.php`文件的注释。<br>
Tips：记得给公众平台添加ip白名单，否则获取不到access_token<br>
默认socket端口：2347
## Summary
如果想即时回复公众号用户的消息，需自行开发客户端，用socket连接服务器2347端口即可。客户端不受任何限制，可以是Web应用可以是移动应用只要支持socket即可。最后，本项目仅提供一个简单的思路，可以方便公众平台更高效快捷的回复客户消息，有兴趣的同学可以进行二次开发，
使其可以支持图片，语音，视频消息等。      
任何问题可以提交到Issues区块，第一时间回复。       
不足之处欢迎指出，期待交流共同提高。


