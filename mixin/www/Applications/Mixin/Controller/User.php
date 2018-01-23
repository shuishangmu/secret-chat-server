<?php
/**
 * Created by PhpStorm.
 * User: luckr
 * Date: 2017/2/23
 * Time: 16:12
 */

namespace Controller;
use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Db;

class User
{
    public static function login($data=array(), $client_id="")
    {
        /*$userRow = Db::instance('db')
            ->select('account_id,frist_pwd,email')
            ->from('mx_account')
            ->where("username='".$data['username']."'")
            ->row();
        if ($userRow){
            if ($userRow['frist_pwd'] == $data['pwd']){
                $userProfile = Db::instance('db')
                    ->select('P.role_id,R.manage_range,P.nickname,P.depart_code')
                    ->from('mx_account_profiles P')
                    ->leftJoin("mx_account_roles R", "R.id = P.role_id")
                    ->where("P.account_id=".$userRow['account_id']."")
                    ->row();
                //保存session
                $_SESSION['uid'] = $userRow['account_id'];
                $_SESSION['username'] = $data['username'];
                $_SESSION['email'] = $userRow['email'];
                $_SESSION['role_id'] = $userProfile['role_id'];
                $_SESSION['manage_range'] = $userProfile['manage_range'];
                $_SESSION['nickname'] = $userProfile['nickname'];
                $_SESSION['depart_code'] = $userProfile['depart_code'];
                \Workerman\Worker::log(json_encode($_SESSION));
                Gateway::bindUid($client_id, $userRow['account_id']);
                $response['errCode'] = "0";
                $response['errMessage'] = "登录成功";
                $response['data'] = array();
            }else {
                $response['errCode'] = "1000";
                $response['errMessage'] = "密码错误";
                $response['data'] = array();
            }
        }else {
            $response['errCode'] = "1000";
            $response['errMessage'] = "用户不存在";
            $response['data'] = array();
        }
        return Gateway::sendToClient($client_id, json_encode($response, JSON_UNESCAPED_UNICODE));*/
        return "OK";
    }
}