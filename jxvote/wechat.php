<?php
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

include_once './lib/WeiXin.class.php';
// $wx = new WeiXin("wxa1d044ab6bda090a", "e1a2015b7d768801d8e1257fb3a01cfd", "osvcaw-JiPeaENHEVTMtShJzocGg" , true);
$wx = new WeiXin("wxc5d217408956f8ea", "143ac50a4abb8a47c9ac8f330fc1972a", "oYeDBjpSeFbpwbZiKuJKZXqSNo60" , "print");
echo $wx->getUserInfoByAT("oYeDBjpSeFbpwbZiKuJKZXqSNo60");
// echo $wx->makeUrl("http://yfree.cc/wechat/wechat.php", "info");
// $dir = dirname(__FILE__)."/1.jpg";
// echo $wx->foreUpload("image", $dir);
// $wx->foreDownload("KBM6QP0yPSnUiaOvT-Qf1jKv3eOuYWlXZujTYUgZMeI");

// phpinfo();
?>
