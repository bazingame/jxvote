<?php
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

include "../class/WeiXin.class.php";

$serverID = $_GET['id'];

$wx = new WeiXin();

$wx->getPic($serverID);

echo $serverID;

