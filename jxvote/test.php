<?php
//set_time_limit(300);
//ini_set('max_execution_time', '0');
include_once '../class/DataBase.class.php';
include_once '../class/WeiXin.class.php';
include_once '../class/VF.class.php';
header('Content-type:text/html;charset=utf-8');
//include_once '../class/View.class.php';
//echo 123;



//生成随机数-------------------------------------------------
//function NoRand($begin=10000,$end=99999,$limit=100){
//    $rand_array=range($begin,$end);
//    shuffle($rand_array);//调用现成的数组随机排列函数
//    return array_slice($rand_array,0,$limit);//截取前$limit个
//}
//$arr = NoRand();
//
//$DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
//foreach ($arr as $key => $value){
//    $val = array('date'=>'0924','code_order'=>($key+1),'code'=>$value);
//    print_r($val);
//    $DB->insert("prize_code",$val);
//    print_r($key.$value.'</br>');
//}
//-----------------------------=-------------------------------

//$wx = new WeiXin();
//$access_token = $wx->access_token;
//$access_token = $wx->getAccessToken2('wxc5d217408956f8ea','143ac50a4abb8a47c9ac8f330fc1972a');
//print_r($access_token);


//$vf = new VF();
//$DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
//$res = $vf->addRecord('10');
//$res = $vf->getArea('202.197.225.16');
//print_r($res);


$time = date("m").date("d");
//$a = array("code"=>'1',"res"=>$time);
//print_r(json_encode($a,JSON_UNESCAPED_UNICODE));
$word = '恭喜您获得！您的兑奖码是:请凭兑奖码在有效时间内到门店登记领取';
?>