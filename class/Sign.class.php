<?php
include_once '../class/DataBase.class.php';
include_once '../class/WeiXin.class.php';
date_default_timezone_set('PRC');

/**
* 报名的相关操作
*/
class Sign
{
    function __construct()
    {
        session_start();
        $data = $this->checkChance();
        $this->chanceA = $data['chanceA'];
        $this->chanceB = $data['chanceB'];
        if (!$data['chanceA'] && !$data['chanceB']) {
            echo "报名机会已用完。";
        }
    }

    /*个人类型报名*/
    function signA($name, $QQ, $tel, $introduce, $serverID){
        if ($this->chanceA > 0) {
            $weixin = new WeiXin();
            $imgData_json = $weixin->downloadfile($serverID);
            $date = date("Y-m-d H:i:s");
            $type = "A";
            if (!isset($_SESSION['nickName']) || !isset($_SESSION['openId'])) {
                echo "缺失数据！";
                return 0;
            }
            $nickName = $_SESSION['nickName'];
            $openId = $_SESSION['openId'];
            $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
            $userInfo = array('name'=>$name, 'type'=>$type, 'QQ'=>$QQ, 'tel'=>$tel, 'introduce'=>$introduce, 'openid'=>$openId, 'nickname'=>$nickName, 'signdate'=>$date, 'team'=>time(), 'img'=>$imgData_json);
            // print_r($userInfo);
            $DB->insert("candidate", $userInfo);     //插入报名数据
            // print_r($DB->printMessage());
            preg_match("/id为：(.*?)$/", $DB->printMessage(), $idData);
            $id = $idData[1];

            $DB->select("userinfo", "sign", "openid = '$openId'");     //数据查询
            $result = $DB->fetchArray(MYSQL_ASSOC);
            $data = $result[0]['sign'];
            $data = json_decode($data, 1);
            $data['chanceA'] = $data['chanceA']-1;
            $data['sign'][] = $id;
            $json_data = json_encode($data);
            $userInfo = array('sign' => $json_data);
            $DB->update("userinfo", $userInfo, "openid = '$openId'");    //报名机会减一
            $this->plusChance($DB);    //抽奖机会+1
            // print_r($DB->printMessage());
            return true;
        }
        else{
            echo "个人报名机会已用完。";
        }
    }

    /*团队类型*/
    function signB($name, $QQ, $tel, $introduce, $team, $serverID){
        if ($this->chanceB > 0) {
            $weixin = new WeiXin();
            $imgData_json = $weixin->downloadfile($serverID);
            $date = date("Y-m-d H:i:s");
            $nickName = $_SESSION['nickName'];
            $openId = $_SESSION['openId'];
            $type = "B";
//            $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
            $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
            $userInfo = array('name'=>$name, 'type'=>$type, 'tel'=>$tel, 'QQ'=>$QQ, 'introduce'=>$introduce, 'team'=>$team, 'openid'=>$openId, 'nickname'=>$nickName, 'signdate'=>$date, 'img'=>$imgData_json);
            print_r($userInfo);
            $DB->insert("candidate", $userInfo);     //插入报名数据
            preg_match("/id为：(.*?)$/", $DB->printMessage(), $idData);
            $id = $idData[1];
            preg_match("/错误编号：(.*?)错/", $DB->printMessage(), $data);
            if ($data[1] == '1062') {
                $tip = "该团队已报名！";
                return $tip;
            }
            else{
                $DB->select("userinfo", "sign", "openid = '$openId'");      //数据查询
                $result = $DB->fetchArray(MYSQL_ASSOC);
                $data = $result[0]['sign'];
                $data = json_decode($data, 1);
                $data['chanceB'] = $data['chanceB']-1;
                $data['sign'][] = $id;
                $json_data = json_encode($data);
                $userInfo = array('sign' => $json_data);
                $DB->update("userinfo", $userInfo, "openid = '$openId'");    //报名机会减一
                $this->plusChance($DB);    //抽奖机会+1
                // print_r($DB->printMessage());
                return true;
            }
        }
        else{
            echo "团体报名机会已用完。";
        }
    }

    /*检查报名机会*/
    function checkChance(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $openId = $_SESSION['openId'];
        $DB->select("userinfo", "*", "openid = '$openId'");      //数据查询
        $data = $DB->fetchArray(MYSQL_ASSOC);
        $data = $data[0]['sign'];
        $data = json_decode($data, 1);
        return $data;
    }

    /*抽奖机会+1*/
    private function plusChance($DB){
        $openid   = $_SESSION['openId'];
        $DB->select("userinfo", "prize", "openid = '$openid'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = $result[0]['prize'];
        $data = json_decode($data, 1);
        $chance = $data['chance'];
        $chance += 1;
        $data['chance'] = $chance;
        $json_data = json_encode($data);
        $userInfo = array('prize' => $json_data);
        $DB->update("userinfo", $userInfo, "openid = '$openid'");
        // print_r($DB->printMessage());
    }

    /*抽奖机会+1  帮别人加*/
    function plusChanceOther($openid){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select("userinfo", "prize", "openid = '$openid'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = $result[0]['prize'];
        $data = json_decode($data, 1);
        $chance = $data['chance'];
        $chance += 1;
        $data['chance'] = $chance;
        $json_data = json_encode($data);
        $userInfo = array('prize' => $json_data);
        $DB->update("userinfo", $userInfo, "openid = '$openid'");
        // print_r($DB->printMessage());
    }

    /*抽奖机会-1*/
    function subChance(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $openid   = $_SESSION['openId'];
        $DB->select("userinfo", "prize", "openid = '$openid'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = $result[0]['prize'];
        $data = json_decode($data, 1);
        $chance = $data['chance'];
        $chance -= 1;
        $data['chance'] = $chance;
        $json_data = json_encode($data);
        $userInfo = array('prize' => $json_data);
        $DB->update("userinfo", $userInfo, "openid = '$openid'");
        // print_r($DB->printMessage());
        return true;
    }


    /*获取报名机会数据*/
    public function getChanceData(){
        $data = $this->checkChance();
        $data = json_encode($data);
        return $data;
    }

    /*获取抽奖机会*/
    function getPrizeChance(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $openid   = $_SESSION['openId'];
        $DB->select("userinfo", "prize", "openid = '$openid'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = $result[0]['prize'];
        $data = json_decode($data, 1);
        $times = $data['chance'];
        return $times;
    }
}
