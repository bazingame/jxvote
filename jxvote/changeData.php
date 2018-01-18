<?php
include_once "../class/View.class.php";
include_once '../class/DataBase.class.php';

$view = new View();
$name = $_POST['name'];
$tel  = $_POST['tel'];
$introduce = $_POST['introduce'];
$type = $_POST['type'];
$change = ($_POST['change']) ? $_POST['change'] : '0' ;
$cnm = $_POST['serverId'];
$view->changeData($name, $tel, $introduce, $type, $change, $cnm);


// session_start();

// $openid = $_SESSION['openId'];
// $result = $DB->query("SELECT * FROM `candidate` WHERE openid = '$openid' AND type = '$type'");
// $id = $result[0]['id'];


header("Location:./index.php");
