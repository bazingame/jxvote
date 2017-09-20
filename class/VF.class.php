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
        $data  = $this->votes($DB);
        if ($data['A'] || $data['B']) {
            $DB->select("candidate", "*", "id = $id");    //查询
            $result = $DB->fetchArray(MYSQL_ASSOC);
            $type = $result[0]['type'];
            if ($type == 'A' && $data['A']=='0') {
                $tip = "今日的个人类投票机会已用完！";
                $jsonA= array(
                    'code'=>-1,
                    'msg'=>$tip
                );
                die(json_encode($jsonA));
                //return $tip;
            }
            else if ($type == 'B' && $data['B']=='0') {
                $tip = "今日的团队类投票机会已用完！";
                $jsonA= array(
                    'code'=>-1,
                    'msg'=>$tip
                );
                die(json_encode($jsonA));
                //return $tip;
            }
            else{
                if ($type == 'A') {
                    $userInfo = array('votes' => 'votes');
                    $DB->updatePlus("candidate", $userInfo, "id = $id");    //增加票数
                    // print_r($DB->printMessage());
                    $votesA = $data['A'] - 1;
                    $votesAll = array('A' => $votesA, 'B' => $data['B']);
                    $votesAll_json = json_encode($votesAll);
                    $userInfo = array('votes' => $votesAll_json);
                    $DB->update("userinfo", $userInfo, "openid = '$openId'");
                    // print_r($DB->printMessage());
                    $this->votePlus();
                    //return true;
                }
                else if ($type == 'B') {
                    $userInfo = array('votes' => 'votes');
                    $DB->updatePlus("candidate", $userInfo, "id = $id");    //增加票数
                    // print_r($DB->printMessage());
                    $votesB = $data['B'] - 1;
                    $votesAll = array('A' => $data['A'], 'B' => $votesB);
                    $votesAll_json = json_encode($votesAll);
                    $userInfo = array('votes' => $votesAll_json);
                    $DB->update("userinfo", $userInfo, "openid = '$openId'");
                    // print_r($DB->printMessage());
                    $this->votePlus();
                }
            }
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
        $openId = $_SESSION['openId'];
        $DB->select("userinfo", "votes", "openid = '$openId'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = json_decode($result[0]['votes'], 1);
        // print_r($DB->printMessage());
        return $data;
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

    /*计数1*/
    function votePlus(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $userInfo = array('votes' => 'votes');
        $DB->updatePlus("sAv", $userInfo, "id = 1");    //计数加一
        $jsonA= array(
            'code'=>0,
            'msg'=>"投票成功"
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