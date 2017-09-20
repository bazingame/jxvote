<?php
include_once '../class/DataBase.class.php';

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
        $DB->selectMore("candidate", "*", $data);
        $result = $DB->fetchArray(MYSQL_ASSOC);
        // print_r($result);
        return $result;
    }

    /*按时间排序返回-可选类型*/
    function filterTime($type = ''){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        if ($type != '') {
            $DB->selectOrder("candidate", "signDate", "DESC", "*", "type = '$type'");
        }
        else{
            $DB->selectOrder("candidate", "signDate", "DESC", "*");
        }
        $result = $DB->fetchArray(MYSQL_ASSOC);
        // print_r($result);
        return $result;
    }

    /*按票数排序返回-可选类型*/
    function filterVotes($type = ''){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        if ($type != '') {
            $DB->selectOrder("candidate", "votes", "DESC", "*", "type = '$type'");
        }
        else{
            $DB->selectOrder("candidate", "votes", "DESC", "*");
        }
        $result = $DB->fetchArray(MYSQL_ASSOC);
        print_r($result);
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
        $DB->select("sAv", "*", "id = '1'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        // print_r($result);
        return $result;
    }
}