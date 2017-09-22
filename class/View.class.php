<?php
include_once '../class/DataBase.class.php';
include_once '../class/WeiXin.class.php';
date_default_timezone_set('PRC');

/**
* 按条件过滤
*/
class View
{
    
    function __construct()
    {
        session_start();
    }

    /*按类型过滤-直接返回信息数组-不输入参数则为查看所有-不推荐使用*/
    function filterByType($type = ''){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        if ($type != '') {
            $DB->select("candidate", "*", "type = '$type'");
        }
        else{
            $DB->select("candidate", "*");
        }
        $result = $DB->fetchArray(MYSQL_ASSOC);
        // print_r($result);
        // print_r($DB->printMessage());
        return $result;
    }

    /*返回自己的关注*/
    function filterFcous(){
        $openId = $_SESSION['openId'];
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select("userinfo", "fcous", "openid = '$openId'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = $result[0]['fcous'];
        $data = json_decode($data, 1);
        $num = count($data);
        // echo "$num";
        if ($num > 1 ) {
            $DB->selectMore("candidate", "*", $data);
            $result = $DB->fetchArray(MYSQL_ASSOC);
            // print_r($result);
            shuffle($result);
            return $result;
        }
    }

    /*按时间排序返回-可选类型*/
    function filterTime(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->selectOrder("candidate", "update_time", "DESC", "*",'has_upload = 1');
        $result = $DB->fetchArray(MYSQL_ASSOC);
        // print_r($result);
        return $result;
    }


    /*返回个人相册*/
    function getPersonalAlbum($id){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select("candidate", "*","id = $id");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        return $result;
    }

    /*随机返回-可选类型*/
    function filterHeart($type = ''){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        if ($type != '') {
            $DB->selectOrder("candidate", "signDate", "DESC", "*", "type = '$type'");
        }
        else{
            $DB->selectOrder("candidate", "signDate", "DESC", "*");
        }
        $result = $DB->fetchArray(MYSQL_ASSOC);
        shuffle($result);
        // print_r($result);
        return $result;
    }

    /*按票数排序返回-可选类型*/
    function filterHeartA($type = ''){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        if ($type != '') {
            $DB->selectOrder("candidate", "heart", "DESC", "*", "type = '$type'");
        }
        else{
            $DB->selectOrder("candidate", "heart", "DESC", "*");
        }
        $result = $DB->fetchArray(MYSQL_ASSOC);
        // print_r($result);
        return $result;
    }

    /*按票数排序返回-可选类型*/
    function filterVotes(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->selectOrder("candidate", "vote_count", "DESC", "*");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        // print_r($result);
        return $result;
    }

    /*搜索返回*/
    function filterSearch($str){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->selectSearch("candidate", $str, "*", "name");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        // print_r($result);
        return $result;
    }

    /*返回vote和ipdata总票数-数组*/
    function getTotalVotes(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select("count", "*", "id = '1'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        // print_r($result);
        return $result;
    }
    /*返回总票数排名-数组*/
    function getList(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->query("SELECT * FROM `candidate` ORDER BY `votes` DESC,`id` ASC");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        return $result;
    }

    /*根据openid查询报名的内容*/
    function searchById(){
        $openid = $_SESSION['openId'];
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->query("SELECT * FROM `candidate` WHERE openid = '$openid' ");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        return $result;
    }

    /*修改报名数据*/
    function changeData($name, $tel, $introduce, $type, $change, $serverID){
        $openid = $_SESSION['openId'];
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->query("SELECT * FROM `candidate` WHERE openid = '$openid' AND type = '$type'");
        $result = $DB->fetchArray(MYSQL_ASSOC);

        if ($serverID && $serverID !== '233') {
            $weixin = new WeiXin();
            $imgData_json = $weixin->downloadfile($serverID);
            $imgData_array_new = json_decode($imgData_json, 1);
            $imgData_array_old = json_decode($result[0]['img'], 1);
            if (!$change) {
                $imgData = array_merge($imgData_array_old, $imgData_array_new);
                $imgData_json = json_encode($imgData);
            }
            else{
                $imgData = array_merge($imgData_array_new, $imgData_array_old);
                $imgData_json = json_encode($imgData);
            }
        }
        else{
            $imgData_json = $result[0]['img'];
        }
        if ($name) {
            $name = $name;
        }
        else{
            $name = $result[0]['name'];
        }
        if ($tel) {
            $tel = $tel;
        }
        else{
            $tel = $result[0]['tel'];
        }
        if ($introduce) {
            $introduce = $introduce;
        }
        else{
            $introduce = $result[0]['introduce'];
        }
        $date = date("Y-m-d H:i:s");
        $userInfo = array('name' => $name, 'tel'=>$tel, 'introduce'=>$introduce, 'updatetime'=>$date, 'img'=>$imgData_json);
        $DB->update("candidate", $userInfo, "openid = '$openid' AND type = '$type'");
        preg_match("/id为：(.*?)$/", $DB->printMessage(), $idData);
        $id = $idData[1];
        preg_match("/错误编号：(.*?)错/", $DB->printMessage(), $data);
        if ($data[1] == '1062') {
            $tip = "你修改的团队名已经存在！";
            return $tip;
        }
    }
}