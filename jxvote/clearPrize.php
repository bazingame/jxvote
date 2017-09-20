<?php

ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

include_once "../class/LuckyKid.class.php";

$prize_array = '';

$openid = $_GET['openid'];

$prize = new LuckyKid(1, $prize_array);

$prize->getLottery($openid);

header('Location:my.php');