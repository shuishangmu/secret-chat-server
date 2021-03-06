<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;
use \Workerman\Lib\Timer;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id) {
        /*$_SESSION['auth_timer_id'] = Timer::add(30, function($client_id){
            Gateway::closeClient($client_id);
        }, array($client_id), false);*/
        // 向当前client_id发送数据 
        //Gateway::sendToClient($client_id, "Hello $client_id\n");
        // 向所有人发送
        //Gateway::sendToAll("$client_id login\n");
        $_SESSION['id'] = session_id();
        $return = array('type'=>'welcome','sessionID'=>time());
        Gateway::sendToCurrentClient($return);
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $data 具体消息
    */
   public static function onMessage($client_id, $data) {
       \Workerman\Worker::log("$client_id said ".json_encode($data));
       // 判断数据是否正确
       /*if(empty($data['class']) || empty($data['method']) || !isset($data['param_array']))
       {
           // 发送数据给客户端，请求包错误
           Gateway::sendToClient($client_id, array('code'=>400, 'msg'=>'bad request', 'data'=>null));
           //return $connection->send(array('code'=>400, 'msg'=>'bad request', 'data'=>null));
       }
       // 获得要调用的类、方法、及参数
       $class = $data['class'];
       $method = $data['method'];
       $param_array = $data['param_array'];
       if(!class_exists($class))
       {
           Gateway::sendToClient($client_id, array('code'=>404, 'msg'=>"class $class not found", 'data'=>null));
       }else {
           Gateway::sendToClient($client_id, array('code'=>0, 'msg'=>'success', 'data'=>null));
       }*/
       /*$message = json_decode($data, true);
       switch($message['action'])
       {
           case 'login':
               \Controller\User::login($message['data'], $client_id);
               // 认证成功，删除 30关闭连接定 的时器
               //Timer::del($_SESSION['auth_timer_id']);
               break;
           case 'message':
               \Controller\Message::send($message['data'], $client_id);
               break;
           default:
               $response['errCode'] = "0";
               $response['errMessage'] = "OK";
               $response['data'] = array();
               Gateway::sendToClient($client_id, json_encode($response, JSON_UNESCAPED_UNICODE)."\n");
       }*/
       // 向所有人发送
       //Gateway::sendToAll(array('errorCode'=>'0','errorMsg'=>'OK','data'=>'1001'));
       $message = "0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789
       0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789";
       //$data = mb_convert_encoding($data,'UTF-8','GBK');
       $return = array('errorCode'=>'0','errorMsg'=>'OK','data'=>$data,'msg'=>$message);
       Gateway::sendToCurrentClient($return);
   }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id) {
       // 向所有人发送 
       //GateWay::sendToAll("$client_id logout");
   }
}
