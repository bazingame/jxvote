<?php
include '../class/WeiXin.class.php';
include '../class/User.class.php';
include_once '../class/Sign.class.php';
include_once '../class/View.class.php';

header("Content-type:text/html;charset=utf-8");
/*获取UA*/
$UA = $_SERVER['HTTP_USER_AGENT'];
if (preg_match('/MicroMessenger/', $UA)) {
    $isWx = 1 ;
}
else{
    $isWx = 0 ;
}
$isWx = 0 ;

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
$data = $view->filterTime();
$times = $view->getTotalVotes();
$num2 = $times[0]['votes'];
$num3 = $times[0]['ipdata'];
$num = count($data);
//排名
//$list = $view->getList();

// print_r($data);

// $data = $view->filterTime();

?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>必胜客杯“创意军训”风采照 - 三翼工作室</title>
    <link rel="stylesheet" href="./css/index.css">
    <!-- <link href="./style.css" type="text/css" rel="stylesheet"> -->
    <link href="./css/mui.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="./Font-Awesome-4.4.0/css/font-awesome.min.css">
    <!-- <script type="text/javascript" src="http://yfree.cc/Lib/Js/jquery-1.4.4.min.js"></script> -->
    <script src="./js/mui.min.js"></script>
</head>
<body>
    <div class="background"></div>
    <div class="bannerTop">
        <img src="./images/bannerTop.jpg" alt="">
        <div class="phoneAfrica">
            <img src="./images/phoneAfrica.png" alt="">
        </div>
    </div>
    <div class="container" style="padding: 15px 10px 15px 10px;">
        <div class="opacityPage"></div>
        <div class="info">
            <table id="infoTable">
                <tr>
                    <td>报名人数<div class="number"><?php echo $num ?></div></td>
                    <td>签到总数<div class="number"></div></td>
                    <td>累计投票<div class="number"><?php echo $num2 ?></div></td>
                    <td>访问次数<div class="number"><?php echo $num3 ?></div></td> 
                </tr>
            </table>
        </div>

        <form class="mui-form--inline" action="./index-search.php" method="GET">
          <div class="form mui-textfield">
            <input type="text" placeholder="请输入选手姓名" name="name" style="padding-left:10px;">
          </div>
          <button class="butt mui-btn mui-btn--primary" type="submit" style="background-color:rgb(141,133,242);font-size:1.2em;">搜索</button>
        </form>

        <div class="register clearFix">
            <div class="rank" onclick="javascript:location.href = './index-team.php';" id="New"></div>
            <div class="attention" onclick="javascript:if (!<?php echo $isWx;?>) {alert('微信端才有此功能哦，快去关注“湘潭大学三翼校园”吧~')}else{location.href = './index-fcous.php'}" id="Attention"></div>
            <div class="new" onclick="javascript:location.href = './index-vote.php';" id="Rank"></div>
        </div>
        <div class="user clearFix">
            <div class="list-one">
            <?php
                foreach ($data as $key=>$value) {
                    $row = $value;
                    $imgurl = $row['img'];
                    $imgurl = json_decode($imgurl, 1);
                    $imgurl = $imgurl[0];
                    if ($key%2 == 0) {
                        $id = $row['id'];
                        if ($row['type']=='A') {
                        $html = <<<HTML
                                    <div class="mui-panel mui--z2" style="padding: 0px;width: 95%;" style="padding: 0px;width: 95%;">
                    <div class="userNum"><span>$id</span>号</div>
                    <img class="one-img" data-original="../class/recordings/$imgurl" alt="name" width="100%" onclick="javascript:location.href = './personal.php?id=$id';">
                    <div class="user-bottom">
                        <div class="information">
                            <div class="name">{$row['name']}</div>
                            <div class="vote-count"><span class="voteC" pid="$id" >{$row['votes']}</span>票&nbsp;&nbsp;{$row['introduce']}。</div>
                        </div>
                        <div class="operation">
                            <div class="op-attention" fcous="$id">关注</div>
                            <div class="op-vote" pid="$id">投票</div>
                        </div>
                    </div>
                </div>
HTML;
echo $html;
                        }
                        if ($row['type']=='B') {
                        $html = <<<HTML
                                    <div class="mui-panel mui--z2" style="padding: 0px;width: 95%;" style="padding: 0px;width: 95%;">
                    <div class="userNum"><span>{$row['id']}</span>号</div>
                    <img class="one-img" data-original="../class/recordings/$imgurl" alt="name" width="100%" onclick="javascript:location.href = './personal.php?id=$id';">
                    <div class="user-bottom">
                        <div class="information">
                            <div class="name">{$row['team']}<button class="mui-btn mui-btn--raised mui-btn--primary" style="width: 30px;height: 17px;margin: 0 0 3px 5px;font-size: 1px;padding: 0px;line-height: 1px;">团队</button></div>
                            <div class="vote-count"><span class="voteC" pid="$id" >{$row['votes']}</span>票&nbsp;&nbsp;{$row['introduce']}。</div>
                        </div>
                        <div class="operation">
                            <div class="op-attention" fcous="$id">关注</div>
                            <div class="op-vote" pid="$id">投票</div>
                        </div>
                    </div>
                </div>
HTML;
echo $html;
                        }                        
                       

                    }
                }
            ?>

            </div>
            <div class="list-two">
                        <?php
                foreach ($data as $key=>$value) {
                    $row = $value;
                    $imgurl = $row['img'];
                    $imgurl = json_decode($imgurl, 1);
                    $imgurl = $imgurl[0];
                    if ($key%2 !== 0) {
                        $id = $row['id'];
                        if ($row['type']=='A') {
                        $html = <<<HTML
                                    <div class="mui-panel mui--z2" style="padding: 0px;width: 95%;" style="padding: 0px;width: 95%;">
                    <div class="userNum"><span>$id</span>号</div>
                    <img class="two-img" data-original="../class/recordings/$imgurl" alt="name" width="100%" onclick="javascript:location.href = './personal.php?id=$id';">
                    <div class="user-bottom">
                        <div class="information">
                            <div class="name">{$row['name']}</div>
                            <div class="vote-count"><span class="voteC" pid="$id" >{$row['votes']}</span>票&nbsp;&nbsp;{$row['introduce']}。</div>
                        </div>
                        <div class="operation">
                            <div class="op-attention" fcous="$id">关注</div>
                            <div class="op-vote" pid="$id">投票</div>
                        </div>
                    </div>
                </div>
HTML;
echo $html;
                        }
                        if ($row['type']=='B') {
                        $html = <<<HTML
                                    <div class="mui-panel mui--z2" style="padding: 0px;width: 95%;" style="padding: 0px;width: 95%;">
                    <div class="userNum"><span>{$row['id']}</span>号</div>
                    <img class="two-img" data-original="../class/recordings/$imgurl" alt="name" width="100%"  onclick="javascript:location.href = './personal.php?id=$id';">
                    <div class="user-bottom">
                        <div class="information">
                            <div class="name">{$row['team']}<button class="mui-btn mui-btn--raised mui-btn--primary" style="width: 30px;height: 17px;margin: 0 0 3px 5px;font-size: 1px;padding: 0px;line-height: 1px;">团队</button></div>
                            <div class="vote-count"><span class="voteC" pid="$id" >{$row['votes']}</span>票&nbsp;&nbsp;{$row['introduce']}。</div>
                        </div>
                        <div class="operation">
                            <div class="op-attention" fcous="$id">关注</div>
                            <div class="op-vote" pid="$id">投票</div>
                        </div>
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
            <div class=" bottomNavBtn" style="width:60%;height:60%;" onclick="location.href = './index.php'"> <span>首页</span></div>
        </div>
        <div class="btn-d ">
             <img src="./images/cross.png">
             <div class="bottomSign" style="margin:0px;width: 100%;height: 100%;" onclick="location.href = './sign.php'"> 签到</div>
        </div>
        <div class="btn-d ">
             <div class=" bottomNavBtn2" style="width:60%;height:60%;color:black;" onclick="location.href = './my.php'"> <span>个人</span></div>
        </div>
    </nav>    
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script type="text/javascript" src="http://j.sky31.com/jQuery.LazyLoad/"></script>

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
</body>
</html>