<?php
/*报名*/
include_once '../class/Sign.class.php';
header("Content-type:text/html;charset=utf-8");
$sign = new Sign();

session_start();
$openid = $_SESSION['openId'];

    $status = $sign->sign($_POST['name'], $_POST['sid'],$_POST['department'],$_POST['QQ'], $_POST['tel'], $_POST['album_subject']);
    if ($status) {
        echo "<script>alert('报名成功！请开始上传照片吧！');location.href='register.php'</script>";
        // print_r($_POST);
//        header("Location:./lottery.php");
        // echo "$status";
    }



