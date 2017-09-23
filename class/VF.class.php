<?php
include_once '../class/DataBase.class.php';

/**
* 投票和关注
*/
class VF
{
    
    function __construct()
    {
        session_start();
    }

    /*投上一票*/
    function vote($id){
        $openId = $_SESSION['openId'];
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $remainVote  = $this->votes($DB);//获得今日可用投票数
//        $data = 1;
        if ($remainVote) {
            $DB->select("candidate", "*", "Id = $id");    //查询
            $result = $DB->fetchArray(MYSQL_ASSOC);
            $userInfo = array('vote_count' => 'vote_count');
            $DB->updatePlus("candidate", $userInfo, "Id = $id");    //增加票数
            //添加用户信息---
            $this->addRecord($id);
            //增加总计数
//            更新排名
            $this->updateRank($id);
            $remainVote--;
            $this->votePlus($remainVote);
        }
        else{
            $jsonA= array(
                'code'=>-1,
                'msg'=>"今日可投票数已投完"
            );
            die(json_encode($jsonA));
        }
    }

    /*返回剩余可投票数*/
    function votes($DB){
        $canVote = 3;
        $date = date("m").date("d");
//        $openId = 'ewgfug';
        $openId = $_SESSION['openId'];
        //用户名相同且投票时间戳同时相同则不可再投
        $DB->select("record", "*", "user_key = '$openId' AND time_no = '$date'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $hasVote = count($result);
        $voteRemain = $canVote-$hasVote;
        return $voteRemain;
    }

    /*返回剩余可投票数*/
    function getVotes(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $openId = $_SESSION['openId'];
        $DB->select("userinfo", "votes", "openid = '$openId'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = json_decode($result[0]['votes'], 1);
        $json = json_encode($data);
        // print_r($DB->printMessage());
        return $json;
    }

    //更新排名信息
    function updateRank($id){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select('candidate','vote_count',"Id = $id");
        $res = $DB->fetchArray(MYSQL_ASSOC);
        $voteCountOld = $res[0]['vote_count'];
        $DB->select('candidate','vote_count',"vote_count > $voteCountOld");
        $res = $DB->fetchArray(MYSQL_ASSOC);
        $voteCount = count($res);
        $voteCount++;
        $DB->update('candidate',array('rank'=>$voteCount),"Id = $id");
        return $voteCount;
    }

    //添加投票记录
    function addRecord($id){
        $date = date("m").date("d");
        $time = date("Y-m-d H:i:s");
        $ip = $this->getip();
        $vote_area = $this->getArea($ip);
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->insert('record',array('user_key'=>$_SESSION['openId'],'vote_username'=>$_SESSION['nickName'],'towho'=>$id,'vote_area'=>$vote_area,'vote_ip'=>$ip,'time_no'=>$date,'votetime'=>$time));
    }


    public function getip() {
        //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $res =  preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
        return $res;
        //dump(phpinfo());//所有PHP配置信息
    }

    function getArea($ip){
        $ip = @file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=".$ip);
        $ip = json_decode($ip,true);
        $area = $ip['data']['region'].$ip['data']['city'];
        return $area;
    }

    /*添加关注*/
    function addFcous($id){
        $openId = $_SESSION['openId'];
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select("userinfo", "fcous", "openid = '$openId'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = $result[0]['fcous'];
        $data = json_decode($data, 1);
        if (in_array($id, $data)){
            //$tip = "已关注,请不要重复关注!";
            //return $tip;
            $jsonA= array(
                'code'=>1,
                'msg'=>"已关注,请不要重复关注!"
            );
            die(json_encode($jsonA));
        }
        else{
            $data[] = $id;     //添加id=$id为关注
            $json_data = json_encode($data);
            $userInfo = array('fcous' => $json_data);
            $DB->update("userinfo", $userInfo, "openid = '$openId'");
            // print_r($DB->printMessage());
            //return true;
            $jsonA= array(
                'code'=>0,
                'msg'=>"关注成功"
            );
            die(json_encode($jsonA));
        }
    }

    /*取消关注*/
    function cancelFcous($id){
        $openId = $_SESSION['openId'];
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select("userinfo", "fcous", "openid = '$openId'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = $result[0]['fcous'];
        $data = json_decode($data, 1);
        $key = array_search($id, $data);
        if ($key !== false){
            array_splice($data, $key, 1);
            $json_data = json_encode($data);
            $userInfo = array('fcous' => $json_data);
            $DB->update("userinfo", $userInfo, "openid = '$openId'");
            // print_r($DB->printMessage());
            $jsonA= array(
                'code'=>0,
                'msg'=>"已取消关注"
            );
            die(json_encode($jsonA));
            //return true;
        }
        else{
            $jsonA= array(
                'code'=>-1,
                'msg'=>"取消关注失败"
            );
            die(json_encode($jsonA));
            //echo "数据错误！";
        }
    }

    /*总计数1*/
    function votePlus($remainVote){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $userInfo = array('vote_count' => 'vote_count');
        $DB->updatePlus("count", $userInfo, "Id = 1");    //计数加一
        $msg = '投票成功,今日可再投'.$remainVote.'票';
        $jsonA= array(
            'code'=>0,
            'msg'=>$msg
        );
        die(json_encode($jsonA));
    }

    /*增加心跳指数，和判断性增加心动人数，判断性添加心动人ID*/
    function addHeart($id){
        $openId = $_SESSION['openId'];
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->query("SELECT * FROM userinfo WHERE openid = '$openId'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $userId = $result[0]['id'];
        $DB->query("SELECT * FROM candidate WHERE id = '$id'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $heartData_json = $result[0]['heartdata'];
        $heartData_array = json_decode($heartData_json, 1);
        $personId = $heartData_array['personid'];
        if (!in_array($userId, $personId)) {
            $heartData_array['num'] += 10;
            $personId[] = $userId;
            $heartData_array['personid'] = $personId;
            $heartData_array['numperson'] += 1;
            $heartData_json = json_encode($heartData_array);
        }
        else{
            $heartData_array['num'] += 10;
            $heartData_array['personid'] = $personId;
            $heartData_json = json_encode($heartData_array);
        }
        $userInfo = array('heartdata' => $heartData_json, 'heart'=>$heartData_array['num']);
        $DB->update("candidate", $userInfo, "id = '$id'");
        // print_r($DB->printMessage());
        return 1;
    }

    /*返回心跳指数*/
    function getHeart($id){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->query("SELECT * FROM candidate WHERE id = ".$id);
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $heartData_json = $result[0]['heartdata'];
        $heartData_array = json_decode($heartData_json, 1);
        print_r($heartData_array);
        $num = $heartData_array['num'];
        return $num;
    }

    /*开放QQ号*/
    function openShare($id){
        $openId = $_SESSION['openId'];
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->query("SELECT * FROM 'userinfo' WHERE openid = '$openId'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = $result[0]['isshare'];
        return $data;
    }    
}