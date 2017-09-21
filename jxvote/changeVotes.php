<?php
include_once '../class/DataBase.class.php';

$DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);

$votes = array('A'=>'1', 'B'=>'1');
$json_votes = json_encode($votes);

$userInfo = array('votes' => $json_votes);
$DB->update("userinfo", $userInfo);
print_r($DB->printMessage());
