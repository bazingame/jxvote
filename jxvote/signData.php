<?php
/*报名*/
include_once '../class/Sign.class.php';
header("Content-type:text/html;charset=utf-8");
$sign = new Sign();

session_start();
$openid = $_SESSION['openId'];

/*邀请码增加抽奖机会*/
if ($_POST['code'] !== '' && $openid !== $_POST['code']) {
    $code = $_POST['code'];
    $DB = new DataBase('localhost', 'root', '***REMOVED***', "junxun");
    $DB->select("userinfo", "*", "openid = '$code'");
    $result = $DB->fetchArray(MYSQL_ASSOC);
    $prizeData = json_decode($result[0]['prize'], 1);
    $prizeData = $prizeData['prize'];
    $num = count($prizeData);
    unset($DB);
    if ($result && $num < 6) {     //限制最多抽五个奖
        $sign->plusChanceOther($code);
    }
    else{
        ;
    }
}


if ($_POST['type'] == 'A') {
    $status = $sign->signA($_POST['name'], $_POST['QQ'], $_POST['tel'], $_POST['introduce'], $_POST['serverId']);
    if ($status) {
        // print_r($_POST);
        header("Location:./lottery.php");
        // echo "$status";
    }
}
if ($_POST['type'] == 'B') {
    $status = $sign->signB($_POST['name'], $_POST['QQ'], $_POST['tel'], $_POST['introduce'], $_POST['team'], $_POST['serverId']);
    if ($status) {
        header("Location:./index.php");
        // echo "$status";
    }
}


