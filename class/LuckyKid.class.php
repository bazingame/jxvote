<?php
include_once '../class/DataBase.class.php';

/**
* 幸运抽奖
* 参数(中奖概率[0.8]，奖品数组)
*/
class LuckyKid
{
    function __construct($prob, $prize_array_json)
    {
        session_start();
        $this->prob =  $prob;
        if (!$this->prizeCheck()) {
            $this->init($prize_array_json);
        }
    }

    /*初始化*/
    function init($prize_array_json){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $num = $this->getTotal($prize_array_json);
        $num = ceil($num/$this->prob);
        $prize_data = array('prize'=>$prize_array_json, 'num'=>$num);
        $DB->insert("prizedata", $prize_data);
        // print_r($DB->printMessage());
        return true;
    }

    /*获取抽奖结果*/
    function getResult(){
        if ($this->checkChance()) {
            $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
            $DB->select("prizedata", "*", "id = '0'");
            $result = $DB->fetchArray(MYSQL_ASSOC);
            $total = $result[0]['num'];
            $rand = rand(1, $total);
            $prize_data = $result[0]['prize'];
            $prize_data = json_decode($prize_data, 1);
            // print_r($prize_data);
            $i = 0;
            while ($rand > 0) {
                $rand -= $prize_data[$i]['num'];
                $i++;
            }
            $i--;
            $prize = $prize_data[$i]['name'];  //取得奖品结果
            $id = $prize_data[$i]['tikect'];

            /*更新数据*/
            $prize_data[$i]['num'] -= 1;
            $result[0]['num'] -= 1; 
            $json = $this->json_encode_ex($prize_data);
            $new_data = array('prize' => $json, 'num' => $result[0]['num']);
            $DB->update("prizedata", $new_data, "id = 0");
            // print_r($DB->printMessage());
            // echo $prize;
            $prize = array('prize' => $prize, 'id' => $id);
            $this->addLottery($id);
            return $prize;  //数组
        }
        else{
            echo "没有抽奖机会！";
        }
        
    }

    /*获取奖品总数*/
    function getTotal($prize_array_json){
        $data = json_decode($prize_array_json, 1);
        $prize_num = 0;
        foreach ($data as $key => $value) {
            $prize_num += $value['num'];
        }
        return $prize_num;
    }

    /*抽奖：添加奖品*/
    function addLottery($id){
        $openid   = $_SESSION['openId'];
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select("userinfo", "prize", "openid = '$openid'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = $result[0]['prize'];
        $data = json_decode($data, 1);
        $prize_num = $data['prize'];
        $prize_num[] = $id;     //添加id=$id为奖品
        $data['prize'] = $prize_num;
        $json_data = json_encode($data);
        $userInfo = array('prize' => $json_data);
        $DB->update("userinfo", $userInfo, "openid = '$openid'");
        $this->subChance();           //抽奖机会-1
        // print_r($DB->printMessage());
        return true;
    } 

    /*领取奖品*/
    function getLottery($openid){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select("userinfo", "prize", "openid = '$openid'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = $result[0]['prize'];
        $data = json_decode($data, 1);
        $data['prize'] = array('NaN');     //清空奖品
        $json_data = json_encode($data);
        $userInfo = array('prize' => $json_data);
        $DB->update("userinfo", $userInfo, "openid = '$openid'");
        return 1;
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

    /*检查奖品数据是否已注册*/
    function prizeCheck(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select("prizedata", "*", "id = '0'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        if ($result) {
            return 1;
        }
        else{
            return 0;
        }
    }

    /*汉字json编码转换*/
    function json_encode_ex($value)
    {
        if ( version_compare(PHP_VERSION,'5.4.0','<'))
        {
            $str = json_encode($value);
            $str =  preg_replace_callback(
                "#\\\u([0-9a-f]{4})#i",
                function($matchs)
                {
                    return  iconv('UCS-2BE', 'UTF-8',  pack('H4',  $matchs[1]));
                },
                $str
            );
            return  $str;
        }
        else
        {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
    }

    /*查询抽奖机会-直接返回抽奖次数*/
    function checkChance(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $openid   = $_SESSION['openId'];
        $DB->select("userinfo", "prize", "openid = '$openid'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = $result[0]['prize'];
        $data = json_decode($data, 1);
        return $data['chance'];
    }
}

