<?php 
include '../class/WeiXin.class.php';
include '../class/User.class.php';
include_once '../class/Sign.class.php';
include_once '../class/View.class.php';
header("Content-type:text/html;charset=utf-8");
/*初始化对象并获取用户数据*/
$weixin = new WeiXin();
$userInfo = $weixin->getUserInfo(); 

/*获取UA*/
$UA = $_SERVER['HTTP_USER_AGENT'];
if (preg_match('/MicroMessenger/', $UA)) {
    $isWx = 1 ;
}
else{
    $isWx = 0 ;
}
$isWx = 0 ;
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

$view = new View();
$data = $view->filterVotes();
$times = $view->getTotalVotes();
$num2 = $times[0]['votes'];
$num3 = $times[0]['ipdata'];
$num = count($data);

$user = new User($_SESSION['openId'], $_SESSION['nickName']);
$user->timePlus();

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
    <div class="container mui-panel mui--z3" style="padding: 15px 10px 15px 10px;">
        <div class="welcome">
            十月的湘大有一抹亮丽的<span>迷</span><span>彩</span>色，那是我们的青春。<br />
            这里，是创意军训风采照大赛  ——  一个不低调的舞台。<br />
            只要你有创意、有想法，就可以带走全校瞩目的巨额大奖！
        </div>
        <div class="info">
        <br />
        <div class="vote-tip mui-table">* 投票规则：每个微信号每天能在个人，团队版块各投一票</div>
            <table>
                <tr>
                    <td>报名人数<div class="number"><?php echo $num ?></div></td>
                    <td>累计投票<div class="number"><?php echo $num2 ?></div></td>
                    <td>访问次数<div class="number"><?php echo $num3 ?></div></td>
                </tr>
            </table>
        </div>

        <form class="mui-form--inline" action="./index-search.php" method="GET">
          <div class="form mui-textfield">
            <input type="text" placeholder="请输入选手姓名" name="name">
          </div>
          <button class="butt mui-btn mui-btn--primary" type="submit">搜索</button>
        </form>

        <div class="register clearFix">
            <div class="logo"></div>
            <div class="rank" onclick="javascript:location.href = './index-team.php';">只看团队</div>
            <div class="attention" onclick="javascript:if (!<?php echo $isWx;?>) {alert('微信端才有此功能哦，快去关注“湘潭大学三翼校园”吧~')}else{location.href = './index-fcous.php'}">我的关注</div>
            <div class="new" onclick="javascript:location.href = './index.php';">时间排行</div>
        </div>
        <div class="user clearFix">
            
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
                    <img class="one-img" data-original="../class/recordings/$imgurl" alt="name" width="100%"  onclick="javascript:location.href = './personal.php?id=$id';">
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