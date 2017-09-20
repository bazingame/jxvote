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
                return $tip;
            }
            else if ($type == 'B' && $data['B']=='0') {
                $tip = "今日的团队类投票机会已用完！";
                return $tip;
            }
            else{
                if ($type == 'A') {
                    $userInfo = array('votes' => 'votes');
                    $DB->updatePlus("candidate", $userInfo, "id = $id");    //增加票数
                    // print_r($DB->printMessage());
                    $data['A'] = '0';
                    $json_data = json_encode($data);
                    $userInfo = array('votes' => $json_data);
                    $DB->update("userinfo", $userInfo, "openid = '$openId'");
                    // print_r($DB->printMessage());
                    $this->votePlus();
                    return true;
                }
                else if ($type == 'B') {
                    $userInfo = array('votes' => 'votes');
                    $DB->updatePlus("candidate", $userInfo, "id = $id");    //增加票数
                    // print_r($DB->printMessage());
                    $data['B'] = '0';
                    $json_data = json_encode($data);
                    $userInfo = array('votes' => $json_data);
                    $DB->update("userinfo", $userInfo, "openid = '$openId'");
                    // print_r($DB->printMessage());
                    $this->votePlus();
                    return true;
                }
            }
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
            $tip = "已关注,请不要重复关注!";
            return $tip;
        }
        else{
            $data[] = $id;     //添加id=$id为关注
            $json_data = json_encode($data);
            $userInfo = array('fcous' => $json_data);
            $DB->update("userinfo", $userInfo, "openid = '$openId'");
            // print_r($DB->printMessage());
            return true;
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
            return true;
        }
        else{
            echo "数据错误！";
        }
    }

    /*计数1*/
    function votePlus(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $userInfo = array('votes' => 'votes');
        $DB->updatePlus("sAv", $userInfo, "id = 1");    //计数加一
    }
}