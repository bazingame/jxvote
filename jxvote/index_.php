<?php 
include '../class/WeiXin.class.php';
include '../class/User.class.php';
include_once '../class/Sign.class.php';
include_once '../class/View.class.php';
header("Content-type:text/html;charset=utf-8");
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
    <div class="container mui-panel mui--z3" style="padding: 15px 10px 15px 10px;">
        <div class="welcome">
            十月的湘大有一抹亮丽的<span>迷</span><span>彩</span>色，那是我们的青春。<br />
            这里，是创意军训风采照大赛  ——  一个不低调的舞台。<br />
            只要你有创意、有想法，就可以带走全校瞩目和巨额大奖！
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

        <form class="mui-form--inline">
          <div class="form mui-textfield">
            <input type="text" placeholder="请输入选手姓名">
          </div>
          <button class="butt mui-btn mui-btn--primary">搜索</button>
        </form>

        <div class="register clearFix">
            <div class="logo"></div>
            <div class="new">最新参赛</div>
            <div class="rank">排行榜</div>
            <div class="attention">我的关注</div>
        </div>
        <div class="user clearFix">
            <div class="list-one">
            <?php
                foreach ($data as $value) {
                    $row = $value;
                    $imgurl = $row['img'];
                    $imgurl = json_decode($imgurl, 1);
                    $imgurl = $imgurl[0];
                    if ($row['id']%2 == 0) {
                        $id = $row['id'];
                        if ($row['type']=='A') {
                        $html = <<<HTML
                                    <div class="mui-panel mui--z2" style="padding: 0px;width: 95%;" style="padding: 0px;width: 95%;">
                    <div class="userNum"><span>$id</span>号</div>
                    <img src="../class/recordings/$imgurl" alt="name" width="100%" onclick="javascript:location.href = './personal.php?id=$id';">
                    <div class="user-bottom">
                        <div class="information">
                            <div class="name">{$row['name']}</div>
                            <div class="vote-count"><span>{$row['votes']}</span>票</div>
                        </div>
                        <div class="operation">
                            <div class="op-attention">关注</div>
                            <div class="op-vote">投票</div>
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
                    <img src="../class/recordings/$imgurl" alt="name" width="100%" onclick="javascript:location.href = './personal.php?id=$id';">
                    <div class="user-bottom">
                        <div class="information">
                            <div class="name">{$row['team']}</div>
                            <div class="vote-count"><span>{$row['votes']}</span>票</div>
                        </div>
                        <div class="operation">
                            <div class="op-attention">关注</div>
                            <div class="op-vote">投票</div>
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
                foreach ($data as $value) {
                    $row = $value;
                    $imgurl = $row['img'];
                    $imgurl = json_decode($imgurl, 1);
                    $imgurl = $imgurl[0];
                    if ($row['id']%2 !== 0) {
                        $id = $row['id'];
                        if ($row['type']=='A') {
                        $html = <<<HTML
                                    <div class="mui-panel mui--z2" style="padding: 0px;width: 95%;" style="padding: 0px;width: 95%;">
                    <div class="userNum"><span>$id</span>号</div>
                    <img src="../class/recordings/$imgurl" alt="name" width="100%" onclick="javascript:location.href = './personal.php?id=$id';">
                    <div class="user-bottom">
                        <div class="information">
                            <div class="name">{$row['name']}</div>
                            <div class="vote-count"><span>{$row['votes']}</span>票</div>
                        </div>
                        <div class="operation">
                            <div class="op-attention">关注</div>
                            <div class="op-vote">投票</div>
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
                    <img src="../class/recordings/$imgurl" alt="name" width="100%"  onclick="javascript:location.href = './personal.php?id=$id';">
                    <div class="user-bottom">
                        <div class="information">
                            <div class="name">{$row['team']}</div>
                            <div class="vote-count"><span>{$row['votes']}</span>票</div>
                        </div>
                        <div class="operation">
                            <div class="op-attention">关注</div>
                            <div class="op-vote">投票</div>
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
            <button class="mui-btn" style="margin:0px;width: 100%;height: 100%;"><i class="fa fa-home"> 首页</i></button>
        </div>
        <div class="btn-d mui--divider-left">
             <button class="mui-btn" style="margin:0px;width: 100%;height: 100%;"><i class="fa fa-heartbeat" onclick="javascript:alert('马上就会开放啦~');"> 心跳</i></button>
        </div>
        <div class="btn-d mui--divider-left">
             <button class="mui-btn" style="margin:0px;width: 100%;height: 100%;" onclick="location.href = './sign.php'"><i class="fa fa-pencil-square-o"> 报名</i></button>
        </div>
        <div class="btn-d mui--divider-left">
             <button class="mui-btn" style="margin:0px;width: 100%;height: 100%;"><i class="fa fa fa-user"> 个人</i></button>
        </div>
    </nav>    
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script type="text/javascript">
    // $('#id233').html();
        $('.op-vote').click(function() {
                $.ajax({
                    url: './vote.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {id: 259},
                    success: function(data){
                        alert('投票成功！');
                    },
                    error: function(error) {
                        console.dir('error');
                    },
                })
            });        
    </script>
</body>
</html>