<?php
include_once '../class/DataBase.class.php';
date_default_timezone_set('PRC');

/**
* 添加一个用户
*/
class User
{
    public $openid;
    public $nickname;
    
    function __construct($openid, $nickname)
    {
        $this->openid   = $openid;
        $this->nickname = $nickname;
        if (!$this->checkUser()) {
            $this->addUser();
        }
        else{
            ;  //什么都不做
        }
    }


    //添加总访问量
    function addVister(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select('count','*',"Id = 1");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $old_num = $result[0]['vister_count'];
        $old_num++;
//        return $old_num;
        $DB->update('count',array('vister_count'=>$old_num),"Id = 1");
    }

    //添加个人访问量
    function addVisterPersonal($id){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select('candidate','*',"Id = '$id'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $old_num = $result[0]['vister_count'];
        $old_num++;
//        return $old_num;
        $DB->update('candidate',array('vister_count'=>$old_num),"Id = '$id'");

    }


    /*添加新用户*/
    function addUser(){
        $openid   = $this->openid;
        $nickname = $this->nickname;
        $date = date("Y-m-d H:i:s");

        /*奖池数据*/
        $prize = array('chance'=>'0', 'prize'=>array('NaN'));
        $json_prize = json_encode($prize);

        /*报名数据*/
        $sign = array('chanceA'=>'1', 'chanceB'=>'1', 'sign'=>array('NaN'));
        $json_sign = json_encode($sign);

        /*关注数据*/
        $fcous = array('NaN');
        $json_fcous = json_encode($fcous);

        /*每日票数*/
        $votes = array('A'=>'1', 'B'=>'1');
        $json_votes = json_encode($votes);

        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $userInfo = array('openid'=>$openid, 'nickname'=>$nickname, 'prize'=>$json_prize, 'votes'=>$json_vote, 'fcous'=>$json_fcous, 'votes'=>$json_votes, 'sign'=>$json_sign, 'adddate'=>$date);
        $DB->insert("userinfo", $userInfo);
        // print_r($DB->printMessage());
    }

    /*检查用户是否存在*/
    function checkUser(){
        $openid = $this->openid;
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select("userinfo", "*", "openid = '$openid'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        if ($result) {
            return 1;
        }
        else{
            return 0;
        }
    }

    /*计数2*/
    function timePlus(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $userInfo = array('ipdata' => 'ipdata');
        $DB->updatePlus("sAv", $userInfo, "id = 1");    //计数加一
    }

    /*检查心跳*/
    function checkHeart(){
        $openid = $this->openid;
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select("userinfo", "*", "openid = '$openid'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        if (!$result[0]['isheart']) {
            $DB->query("UPDATE userinfo SET isheart = '1' WHERE openid = '$openid'");
            return 1;
        }
        else{
            return 0;
        }
    }
}