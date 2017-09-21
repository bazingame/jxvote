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

if($isWx) {
    /*初始化对象并获取用户数据*/
    $weixin = new WeiXin();
    $userInfo = $weixin->getUserInfo();

    /*解析用户数据*/
    $userInfo = json_decode($userInfo, 1);
    session_start();
    $openId = $userInfo['openid'];
    $nickName = $userInfo['nickname'];       //用户昵称
    $headImgurl = substr($userInfo['headimgurl'], 0, -2) . "/132"; //用户头像

    /*数据存入session*/
    if (!isset($_SESSION['openId']) || !isset($_SESSION['nickName']) || !isset($_SESSION['headImgurl'])) {
        $_SESSION['openId'] = $openId;
        $_SESSION['nickName'] = $nickName;
        $_SESSION['headImgurl'] = $headImgurl;
    }

    $user = new User($_SESSION['openId'], $_SESSION['nickName']);
    $user->timePlus();
}

$id = $_GET['id'];

$view = new View();
$data = $view->getPersonalAlbum($id)[0];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <meta name="viewport"content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
      <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/my2.css">
    <link rel="stylesheet" href="css/allPhotoPage.css">
</head>
<body>
    <div class="background"></div>
     <div class="bannerTop">
        <img src="./images/bannerTop2.jpg" alt="">
        <div class="phoneAfrica">
            <img src="./images/phoneAfrica.png" alt="">
        </div>
     </div>
      <div class="container"  style="padding: 15px 10px 15px 10px;">

            <div class="opacityPage">
             <div class="userNamePart">
                 <img src="./images/heart.png" alt="">
                 <?php
//                    $subject = json_decode($data['name'],true);
                 echo $data['name']; ?>
                 <img src="./images/littlePerson.png" alt="" style="width:auto; height:50%;">
             </div>
             <ul class="photoPageNav">
                 <?php
                 $rank = $data['rank'];
                 $visiter_count = $data['vister_count'];
                 $vote_count = $data['vote_count'];
                 $html = <<<HTML
                 <li>排名:{$rank}</li>
                 <li>访问:{$visiter_count}</li>
                 <li>票数:{$vote_count}</li>
HTML;
                echo $html;

                 ?>
             </ul>
             <div id="voteBtn" pid = "<?php echo $id; ?>">投一票</div>
         </div>
          <?php
            $album_info = json_decode($data['photo_list'],true);
            foreach (array_reverse($album_info) as $key => $value){
                $month = substr($key,0,2);
                $day = substr($key,2,2);
                $sign_time = $month.'.'.$day;
                $img = '';
                foreach ($value['pic'] as $val){
                    $img .= '<img src="../class/recordings/'.$val.'" alt="">';
                }
                $html = <<<HTML
                    <div class="onePhotoPartBox">
                        <div class="onePhotoPart">
                           <div class="divideLine"></div>
                            {$img}
                            <span class="itdPhotoWord">{$value['words']}</span></br>
                            <span style="margin-left:66%;">--</span>
                            <span class="dateWord">{$sign_time}</span>
                            <img src="./images/giftBox.png" alt="" class="giftBox" style="width:10%;">
                             <div class="giftWord"><img src="./images/yellowCircle.png" alt=""></div>
                        </div>
                        <!--<ul class="Labels">-->
                           <!--<li>自拍</li>-->
                           <!--<li>自拍</li>-->
                    <!--</ul>-->
                   </div>
HTML;
                echo $html;
            }
          ?>


          <div class="awardBox">
              <div id="settingPart">
                  <div class="wordAward">奖项设置</div>
                  <div class="awardText" style="margin-top:40px;">
                      <div><span class="wordModeI">投票奖</span><span class="wordModeII">(No.1-20,壕礼等你)</span></div>
                      <div><span class="wordModeI">特别奖</span><span class="wordModeII">(最创意，最搞怪，最情怀)</span></div>
                      <div><span class="wordModeI">投票奖</span><span class="wordModeII">(每日不同，超乎想象)</span></div>
                      <div><span class="wordModeI">投票奖</span><span class="wordModeII">(来试试运气？)</span></div>
                      <div><span class="wordModeI">投票奖</span><span class="wordModeII">(点击底部签到进行报名，即可得奖)</span></div>
                      <div id="showList"><span class="wordModeI">【点击查看礼品清单】</span></div>
                  </div>
              </div>
              <div id="receiveWayPart">
                  <div class="wordAward">领奖方式</div>
                  <div class="awardText">
                      <div><span class="wordModeI">投票奖</span><span class="wordModeII">、</span><span class="wordModeI">特别奖</span><span class="wordModeII">和</span><span class="wordModeI">幸运大奖</span><span class="wordModeII">于10月8日军训之夜当晚三翼摆点处领取，工作人员会提前通知</span></div>
                      <div><span class="wordModeI">特别奖</span><span class="wordModeII">请凭兑奖码到相应的门店登记领取</span></div>
                      <div><span class="wordModeII">(每日前n名获兑奖码，领取时间:9月22日--10月12日)</span></div>
                  </div>
              </div>
              <div id="chooseWayPart">
                  <div class="wordAward">评选方式</div>
                  <div class="awardText">
                      <div><span class="wordModeI">投票奖:</span><span class="wordModeII">关注三翼校园官方微信平台，输入我要投票，进入投票系统，选择自己最喜欢的三个参赛相册进行投票。</span></div>
                      <div><span class="wordModeI">特别奖:</span><span class="wordModeII">由三翼评审人员综合贴吧评论，官q评论，以及大众评审评选出"最佳创意奖" "最佳搞怪奖" 以及"最具情怀奖"</span></div>
                      <div><span class="wordModeI">打卡奖:</span><span class="wordModeII">每日打卡前n名即可获得</span></div>
                      <div><span class="wordModeI">幸运大奖:</span><span class="wordModeII">由赞助商随机抽取幸运报名选手赠送</span></div>
                      <div><span class="wordModeI">参与奖:</span><span class="wordModeII">参赛即可领取</span></div>
                  </div>
              </div>
          </div>

            <div class="theSideLine">
                <div class="circleGrayTop"></div>
                <div class="circleGrayBottom"></div>
            </div>
    </div>
    <nav class="bottom-nav"> 
        <div class="btn-d">
            <div class=" bottomNavBtn2" style="width:60%;height:60%;" onclick="location.href = './index.php'"> <span>首页</span></div>
        </div>
        <div class="btn-d ">
            <img src="./images/cross.png">
            <div class="bottomSign" style="margin:0px;width: 100%;height: 100%;" onclick="javascript:if (!<?php echo $isWx;?>) {alert('请进入三翼校园公众号，点击下方菜单或回复军训时光记使用该功能')}else{location.href = './register.php'}"> 签到</div>
        </div>
        <div class="btn-d ">
            <div class=" bottomNavBtn2" style="width:60%;height:60%;color:black;" onclick="javascript:if (!<?php echo $isWx;?>) {alert('请进入三翼校园公众号，点击下方菜单或回复军训时光记使用该功能')}else{location.href = './my.php'}"> <span>个人</span></div>
        </div>
    </nav>
    <script src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>
        var voting=false;
        $('#voteBtn').on("click",function(){
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

    </script>
</body>

</html>