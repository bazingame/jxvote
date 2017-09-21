<?php 
include '../class/WeiXin.class.php';
include '../class/User.class.php';
include_once '../class/Sign.class.php';
include_once '../class/View.class.php';
include_once '../class/VF.class.php';
header("Content-type:text/html;charset=utf-8");
/*获取UA*/
$UA = $_SERVER['HTTP_USER_AGENT'];
if (preg_match('/MicroMessenger/', $UA)) {
    $isWx = 1 ;
}
else{
    $isWx = 0 ;
}
 $isWx = 0;
/*初始化对象并获取用户数据*/
$weixin = new WeiXin();
$userInfo = $weixin->getUserInfo(); 

/*解析用户数据*/
$userInfo = json_decode($userInfo, 1);
session_start();
$openId     = $userInfo['openid'];
$nickName   = $userInfo['nickname'];       //用户昵称
$headImgurl = substr($userInfo['headimgurl'], 0,-2)."/132" ; //用户头像

/*数据存入session*/
if (!isset($_SESSION['openId'])||!isset($_SESSION['nickName'])||!isset($_SESSION['headImgurl'])) {
  $_SESSION['openId']     = $openId;
  $_SESSION['nickName']   = $nickName;
  $_SESSION['headImgurl'] = $headImgurl;
}

/*获取JDk签名并解析*/
// $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   //获取地址栏完整url（带参数）
// $signature = $weixin->getSignature($url);
// $signature = json_decode($signature, 1);


$user = new User($_SESSION['openId'], $_SESSION['nickName']);
$user->timePlus();

$view = new View();
$data = $view->filterHeartA('A');
$num = count($data);



?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>心跳 - 三翼工作室</title>
    <link rel="stylesheet" href="./css/index.css">
    <!-- <link href="./style.css" type="text/css" rel="stylesheet"> -->
    <link href="./css/mui.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="./Font-Awesome-4.4.0/css/font-awesome.min.css">
    <!-- <script type="text/javascript" src="http://yfree.cc/Lib/Js/jquery-1.4.4.min.js"></script> -->
    <link href="css/shake.css" rel="stylesheet" media="all">
    <script src="./js/mui.min.js"></script>
</head>
<body>
    <div class="background" style="background-image: url(./images/heart.jpg);width: 100%;height: 100%;"></div>
    <div class="container mui-panel mui--z3" style="padding: 15px 10px 15px 10px;width: 100%;margin-bottom: 50px;">

        <div style="width: 100%;height: 30px;">
            <div style="width: 80%;height: 100%;margin:auto;">
                <div style="width:27%;margin-left: 5%; float: left;height: 100%;">
                    <div style="width: 100%;margin: auto;background-color: #39a1f4;font-size: 14px;height: 100%;color: white;padding: 5px 9px;border-radius: 4px;" onclick="location.href = './heart.php'">随机排序</div>
                </div>
                <div style="width: 27%;margin-left: 5%;float: left;height: 100%;">
                    <div style="width: 100%;margin: auto;background-color: #39a1f4;font-size: 14px;height: 100%;color: white;padding: 5px 9px;border-radius: 4px;">————</div>
                </div>
                <div style="width: 27%;margin-left: 5%;float: left;height: 100%;">
                    <div style="width: 100%;margin: auto;background-color: #39a1f4;font-size: 14px;height: 100%;color: white;padding: 5px 9px;border-radius: 4px;" onclick="location.href = './heart-vote.php'">心跳排行</div>
                </div>
            </div>
        </div>
        <div class="user clearFix" style="margin-bottom: 30px;">
            <div class="list-one" style="width: 100%;">
            <?php
                foreach ($data as $key=>$value) {
                    $row = $value;
                    $imgurl = $row['img'];
                    $imgurl = json_decode($imgurl, 1);
                    shuffle($imgurl);
                    $imgurl = $imgurl[0];
                    if (1) {
                        $id = $row['id'];
                        if ($row['type']=='A') {
                            $vf = new VF();
                            $num = $vf->getHeart($id);
                            $html = <<<HTML
                            <div class="mui-panel mui--z2" style="padding:0;width: 23%;float: left;margin:0px 1% 30px 1%;height: 130px;">
                                <img class="one-img" data-original="../class/recordings/$imgurl" alt="name" height="100%" width="100%" align="middle" onclick="javascript:location.href = './personal.php?id=$id';">
                                <div class="user-bottom" style="height: 25px;padding: 2px 0">
                                    <div class="d" style="color: #E91E63;font-size: 16px;margin-left: 5%;display: inline-block;" num="$id"><i class="fa fa-heartbeat">&nbsp;&nbsp;&nbsp;</i></div><span class="get" id="$id">{$num}</span>
                                </div>
                            </div>
HTML;
echo $html;
                        }                     
                       
                    }
                }
            ?>

            </div>
        </div>
        <div class="footer">Copyright &copy; 2004-2016湘潭大学三翼工作室</div>
    </div>
    <nav class="bottom-nav"> 
        <div class="btn-d">
            <button class="mui-btn" style="margin:0px;width: 100%;height: 100%;" onclick="location.href = './index.php'"><i class="fa fa-home"> 首页</i></button>
        </div>
        <div class="btn-d mui--divider-left">
             <button class="mui-btn" style="margin:0px;width: 100%;height: 100%;" onclick="location.href = './heart.php'"><i class="fa fa-heartbeat"> 心跳</i></button>
        </div>
        <div class="btn-d mui--divider-left">
             <button class="mui-btn" style="margin:0px;width: 100%;height: 100%;" onclick="location.href = './sign.php'"><i class="fa fa-pencil-square-o"> 报名</i></button>
        </div>
        <div class="btn-d mui--divider-left">
             <button class="mui-btn" style="margin:0px;width: 100%;height: 100%;" onclick="location.href = './my.php'"><i class="fa fa fa-user"> 个人</i></button>
        </div>
    </nav>    
    <script src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script type="text/javascript" src="//j.sky31.com/jQuery.LazyLoad/"></script>

    <script>
        $(function() {
            $("img.one-img").lazyload({
                effect : "fadeIn",
                failurelimit : 10
            });
            $("img.two-img").lazyload({
                effect : "fadeIn",
                failurelimit : 10
            });
        });
        var voting=false; 
        $('.op-vote').on("click",function(){
            if (<?php echo $isWx; ?>) {
                if(voting)return false;
                var cur=$(this);
                var pid=$(this).attr("pid");
                var counter=$(".voteC[pid="+pid+"]");
                var counterNum=parseInt(counter.html());
                voting=true;
                $(this).html("ing...");
                $.ajax({
                    url:"./vote.php?id="+pid,
                    type:"get",
                    success:function(data){
                        try{
                            var jsonD=JSON.parse(data);
                            if(jsonD.code==0){
                                alert(jsonD.msg);
                                counterNum++;
                                counter.html(counterNum);
                            }
                            else{
                                alert(jsonD.msg);
                            }
                        }
                        catch(e){
                            alert("解析错误");
                        }
                        voting=false;
                        cur.html("投票");
                    }
                });                
            }
            else{
                alert("投票已经截止咯。");
            }                
        }); 
        $('.op-attention').on("click",function(){
        if (<?php echo $isWx; ?>) {
            var fcousStr=$(this);
            var fcous=$(this).attr("fcous");
            $(this).html("ing...");
            $.ajax({
                url:"./fcous.php?id="+fcous,
                type:"get",
                success:function(data){
                    try{
                        var jsonD=JSON.parse(data);
                        if(jsonD.code==0){
                            alert(jsonD.msg);
                            fcousStr.html("已关注");
                        }
                        else{
                            alert(jsonD.msg);
                            fcousStr.html("已关注");
                        }
                    }
                    catch(e){
                        alert("解析错误");
                        fcousStr.html("关注");
                    }
                }
            });                
        }
        else{
            alert("投票已经截止咯。");
        }
        });           
    </script>
    <script type="text/javascript">
        function sleep(numberMillis) { 
           var now = new Date();
           var exitTime = now.getTime() + numberMillis;  
           while (true) { 
               now = new Date(); 
               if (now.getTime() > exitTime)    return;
            }
        }
        function removeC(){
            $('.d').removeClass('shake');
        }    
        $('.d').click(function() {
            $(this).addClass('shake');
            var id=$(this).attr("num");
            var geter = $(".get[id="+id+"]");
            var heartnum = parseInt(geter.html());
            $.ajax({
                url:"./heart-add.php?id="+id,
                type:"get",
                success:function(data){
                    heartnum += 10;
                    geter.html(heartnum);
                }                
            });
            setTimeout("removeC()",150);
        });
    </script>
</body>
</html>