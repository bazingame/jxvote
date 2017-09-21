<?php
include_once '../class/DataBase.class.php';
include_once '../class/WeiXin.class.php';
include_once '../class/View.class.php';
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
    }

    /*个人类型报名*/
    function sign($name,$sid,$department, $QQ, $tel, $album_subject){
            $date = date("Y-m-d H:i:s");
            if (!isset($_SESSION['nickName']) || !isset($_SESSION['openId'])) {
                echo "缺失数据！";
                return 0;
            }
            $nickName = $_SESSION['nickName'];
            $openId = $_SESSION['openId'];

            $personal_info = array('sid'=>$sid,'name'=>$name,'college'=>$department,'phone'=>$tel,'qq'=>$QQ);
            $personal_info = json_encode($personal_info,JSON_UNESCAPED_UNICODE);

            $album_info = array('subject'=>$album_subject,'cover'=>'');
            $album_info = json_encode($album_info,JSON_UNESCAPED_UNICODE);

            $view = new View();
            $count = $view->getTotalVotes();
            $sign_num = $count[0]['sign_up_count'];
            $rank = $sign_num+1;

            $vister_count = $vote_count = 0;

            $register_count = '{"count":"0","detail":{"0922":"0","0923":"0","0924":"0","0925":"0","0926":"0","0927":"0","0928":"0","0929":"0","0930":"0","1001":"0","1002":"0","1003":"0","1004":"0"}}';

            //插入数据
            $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
            $userInfo = array('name'=>'dsfdsf', 'openId'=>$openId, 'personal_info'=>$personal_info, 'album_info'=>$album_info, 'rank'=>$rank, 'vister_count'=>$vister_count, 'vote_count'=>$vote_count, 'register_count'=>$register_count, 'update_time'=>$date);
//             print_r($userInfo);

            $DB->insert("candidate", $userInfo);     //插入报名数据
            // print_r($DB->printMessage());

            $DB->update("count",array('sign_up_count'=>$rank) , "Id = 1");   //总人数加一
            return true;
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
