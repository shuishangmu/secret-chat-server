<?php
/**
 * Created by PhpStorm.
 * User: luckr
 * Date: 2017/2/23
 * Time: 13:53
 */

namespace Controller;
use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Db;
use \Util\MyRedis;


class Message
{
    static public function send($data=array(), $client_id="")
    {
        //验证用户是否存在
        /*$toUserRow = Db::instance('db')
            ->select('account_id')
            ->from('mx_account')
            ->where("account_id=".$data['toID']." AND delete_flag=0")
            ->row();
        if (!$toUserRow){
            $response['errCode'] = "1104";
            $response['errMessage'] = "用户不存在";
            $response['data'] = array();
            return Gateway::sendToClient($client_id, json_encode($response, JSON_UNESCAPED_UNICODE)."\n");
        }*/
        //验证消息是否为重复的
        /*$msgRow = Db::instance('db')
            ->select('id')
            ->from('mx_messages')
            ->where("message_id='".$data['msgID']."'")
            ->row();
        if ($msgRow){
            $response['errCode'] = "0";
            $response['errMessage'] = "OK";
            $response['data'] = array();
            return Gateway::sendToClient($client_id, json_encode($response, JSON_UNESCAPED_UNICODE)."\n");
        }*/
        /*$isSend = false;
        if (!$isSend && isset($data['fromGroupID']) && $data['fromGroupID']){
            //判断消息接收人是否也在同一群组中
            $memRow = Db::instance('db')
                ->select('id')
                ->from('mx_friend_group_members')
                ->where("group_id=".$data['fromGroupID']." AND account_id=".$data['toID']." AND delete_flag=0")
                ->row();
            if ($memRow) $isSend = true;
        }
        if (!$isSend){
            //验证两人是否已经解除好友关系
            $contactList = Db::instance('db')
                ->select('id')
                ->from('mx_friend_contacts')
                ->where("(account_id=".$_SESSION['uid']." AND friend_id=".$data['toID']." AND status=1) OR (account_id=".$data['toID']." AND friend_id=".$_SESSION['uid']." AND status=1)")
                ->query();
            if (count($contactList) == 2) $isSend = true;
        }
        if (!$isSend){
            //判断两人是否为临时会话
            $temporaryRow = Db::instance('db')
                ->select('id')
                ->from('mx_temporary_friends')
                ->where("(account_id=".$_SESSION['uid']." AND friend_id=".$data['toID']." AND status=1) OR (account_id=".$data['toID']." AND friend_id=".$_SESSION['uid']." AND status=1)")
                ->row();
            if ($temporaryRow) $isSend = true;
        }
        if (!$isSend) {
            $toUserRoleRow = Db::instance('db')
                ->select('P.role_id,R.manage_range,P.nickname,P.depart_code')
                ->from('mx_account_profiles P')
                ->leftJoin("mx_account_roles R", "R.id = P.role_id")
                ->where("P.account_id=".$data['toID']."")
                ->row();
            if ($_SESSION['manage_range'] && $_SESSION['manage_range'] != "all") {
                $fromManageRange = explode(",", $_SESSION['manage_range']);
                $isSend = in_array($toUserRoleRow['role_id'], $fromManageRange);
            }
            if ($isSend){
                //建立临时会话
                if ($toUserRoleRow['manage_range'] && $toUserRoleRow['manage_range'] != "all") {
                    $toManageRange = explode(",", $toUserRoleRow['manage_range']);
                    if (!in_array($_SESSION['role_id'], $toManageRange)){
                        $insertData['account_id'] = $_SESSION['uid'];
                        $insertData['friend_id'] = $data['toID'];
                        $insertData['status'] = 1;
                        $insertData['created_at'] = date("Y-m-d H:i:s");
                        Db::instance('db')->insert('mx_temporary_friends')->cols($insertData)->query();
                    }
                }
            }
        }
        if (!$isSend){
            $response['errCode'] = "1205";
            $response['errMessage'] = "已经解除密友关系，请重新验证好友";
            $response['data'] = array();
            return Gateway::sendToClient($client_id, json_encode($response, JSON_UNESCAPED_UNICODE)."\n");
        }*/

        $attach['time_length'] = isset($data['timeLength'])?$data['timeLength']:0;
        $attach['image_width'] = isset($data['imageWidth'])?$data["imageWidth"]:0;
        $attach['image_height'] = isset($data['imageHeight'])?$data['imageHeight']:0;
        $attach['file_size'] = isset($data['fileSize'])?$data['fileSize']:0;
        $attach['file_type'] = isset($data['fileType'])?$data['fileType']:0;
        $attach['file_name'] = isset($data['fileName'])?$data['fileName']:"";
        $attach['from_group_id'] = isset($data['fromGroupID'])?$data['fromGroupID']:0;
        $attach['isMobiRead'] = isset($data['isMobiRead'])?$data['isMobiRead']:1;

        $param['message_id'] = $data['msgID'];
        $param['from_id'] = $_SESSION['uid'];
        $param['to_id'] = $data['toID'];
        $param['message_type'] = $data['msgType'];
        $param['content'] = $data['content'];
        $param['live_time'] = $data['liveTime'];
        $param['send_time'] = time();
        $param['milli_send_time'] = self::millitotime();
        $param['key'] = $data['key'];
        $param['status'] = 1;
        $param['attach'] = json_encode($attach, JSON_UNESCAPED_UNICODE);
        //将消息转发给接收人toID
        $clientList = Gateway::getClientIdByUid($data['toID']);
        \Workerman\Worker::log(json_encode($clientList));
        if ($clientList){
            foreach ($clientList as $clientID){
                $sendMsg['type'] = "person_msg";
                $sendMsg['data'] = $param;
                Gateway::sendToClient($clientID, json_encode($sendMsg, JSON_UNESCAPED_UNICODE));
            }
        }else {
            Db::instance('db')->insert("mx_messages")->cols($param)->query();
        }

        self::staticMsgLog($_SESSION['uid'], $data['msgType'], $data['liveTime']);
        $response['errCode'] = "0";
        $response['errMessage'] = "OK";
        $response['data'] = array("msgID"=>$data['msgID']);
        return Gateway::sendToClient($client_id, json_encode($response, JSON_UNESCAPED_UNICODE));
    }
    static public function millitotime()
    {
        $time = microtime(true);
        $time = $time * 1000;
        $time2 = explode(".", $time);
        $time = $time2[0];
        return (float)$time;
    }
    static public function staticMsgLog($userID=0, $msgType=0, $liveTime=0)
    {
        //记录用户活跃
        $nowDay = date("Y-m-d");
        MyRedis::getInstance("local")->sAdd("active_".$nowDay,$userID);
        MyRedis::getInstance("local")->expire("active_".$nowDay, 30*24*60*60);
        //记录每种生存周期的消息发送数
        MyRedis::getInstance("local")->hIncrBy("live_time", $liveTime, 1);

        $typeList[0] = "words";
        $typeList[1] = "sound";
        $typeList[2] = "image";
        $typeList[3] = "video";
        $typeList[4] = "link";
        $typeList[5] = "local_file";
        $typeList[6] = "cloud_file";
        if (isset($typeList[$msgType])) {
            //记录每小时消息发送数
            $staticTime = date("YmdH");//date("Y-m-d H:00:00");
            MyRedis::getInstance("local")->hIncrBy("statics_".$staticTime,$typeList[$msgType],1);
            MyRedis::getInstance("local")->hIncrBy("account_".$userID,$typeList[$msgType],1);
            return true;
        } else {
            return false;
        }
    }

}