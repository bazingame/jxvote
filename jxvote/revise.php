<?php
include '../class/WeiXin.class.php';
include '../class/User.class.php';
include_once '../class/Sign.class.php';
header("Content-type:text/html;charset=utf-8");
/*获取UA*/
$UA = $_SERVER['HTTP_USER_AGENT'];
if (preg_match('/MicroMessenger/', $UA)) {
    $isWx = 1 ;
    $action = './signData.php';
}
// else{
//     header('Location:index.php');
//     $isWx = 0 ;
//     $action = '';
// }
session_start();
/*获取JDk签名并解析*/
$weixin = new WeiXin();
$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   //获取地址栏完整url（带参数）
$signature = $weixin->getSignature($url);
$signature = json_decode($signature, 1);


$user = new User($_SESSION['openId'], $_SESSION['nickName']);
$user->timePlus();


?>

<!doctype html>
<html>
<head>
    <title>军训时光记 - 三翼工作室</title>
    <meta charset="utf-8"></head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,init-scale=1.0,max-scale=1.0,userscalable=no"/>
<link rel="stylesheet" href="./css/index.css">
<link href="./style.css" type="text/css" rel="stylesheet">
<link href="./css/mui.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="./css/my2.css">
<link rel="stylesheet" href="./Font-Awesome-4.4.0/css/font-awesome.min.css">
<script src="http://cdn.static.runoob.com/libs/jquery/1.10.2/jquery.min.js"></script>
<!-- <script type="text/javascript" src="http://yfree.cc/Lib/Js/jquery-1.4.4.min.js"></script> -->
<script src="./js/mui.min.js"></script>
<script type="text/javascript">
    function check(form){
        if (<?php echo $isWx; ?>) {
            if (form.serverId.value=='') {
                alert("请选择要上传的照片。");
            }
        }
        else{
            alert("微信端才能报名哦，快去关注“湘潭大学三翼校园”吧~");
        }
    }
</script>
</head>
<body>
<div id="back"></div>
<div class="bannerTop">
    <img src="./images/bannerTop2.jpg" alt="">
</div>
<div id="container" class="mui-panel mui--z3">
    <div id="inner1">
        <ul class="mui-tabs__bar mui-tabs__bar--justified">
            <li class="mui--is-active"><a data-mui-toggle="tab" data-mui-controls="pane-default-1">修改报名信息</a></li>
        </ul>
        <div class="mui-tabs__pane mui--is-active" id="pane-default-1">
            <form action="<?php echo $action; ?>" method="POST">
                <div class="mui-textfield mui-textfield--float-label">
                    <input type="text" name="name"  required>
                    <label>姓名</label>
                </div>
                <div class="mui-textfield mui-textfield--float-label">
                    <input type="text" name="sid"  required>
                    <label>学号</label>
                </div>
                <div class="mui-textfield mui-textfield--float-label">
                    <input type="text" name="department"  required>
                    <label>院系</label>
                </div>
                <div class="mui-textfield mui-textfield--float-label">
                    <input type="text" name="QQ"  required>
                    <label>QQ</label>
                </div>
                <div class="mui-textfield mui-textfield--float-label">
                    <input type="text" name="tel"  required>
                    <label>电话</label>
                </div>
                <div class="mui-textfield mui-textfield--float-label">
                    <textarea name="album_subject"  required></textarea>
                    <label>相册主题</label>
                </div>
                <br />
                <button id="confirm" class="mui-btn mui-btn--raised mui-btn--primary" type="submit" style="background-color:rgb(102,153,102);">确认报名</button>
            </form>
        </div>

        <br />
        <div class="mui-divider"></div>
        <br />
        <p style="font-size:18px;margin-top:0%;"><b>奖项设置</b></p></br>
        <div id="zxszContain">
            <div class="awardBox" style="width:100%;">
                <div id="settingPart">
                    <div class="wordAward">奖项设置</div>
                    <div class="awardText">
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
        </div>
    </div>
</div>
<nav class="bottom-nav">
    <div class="btn-d">
        <div class=" bottomNavBtn2" style="width:60%;height:60%;" onclick="location.href = './index.php'"> <span>首页</span></div>
    </div>
    <div class="btn-d ">
        <img src="./images/cross.png">
        <div class="bottomSign" style="margin:0px;width: 100%;height: 100%;" >报名</div>
    </div>
    <div class="btn-d ">
        <div class=" bottomNavBtn2" style="width:60%;height:60%;color:black;" onclick="location.href = './my2.php'"> <span>个人</span></div>
    </div>
</nav>
<div class="giftList">
    <div class="giftWords">
        <div class="wordAward2">投票奖</div>
        <div><span class="wordModeI">冠军：</span><span class="wordModeII">奖金666元 +校级荣誉证书 +价值798元的90户外俱乐部“张家界徒步探险”两日双人游+价值800元的宝岛眼镜店眼镜1副+价值1000元的青鸟琴行吉他1把</span></div>
        <div><span class="wordModeI">亚军：</span><span class="wordModeII">奖金520元+校级荣誉证书+价值598元的90户外俱乐部“武功山穿越”两日双人游+价值500元的宝岛眼镜店眼镜1副+价值208的瑞诗凯瑜伽卡1张</span></div>
        <div><span class="wordModeI">季军：</span><span class="wordModeII">奖金321 元 +校级荣誉证书+价值398元的90户外俱乐部 “浏阳溯溪”两日双人游+价值300元的宝岛眼镜店眼镜1副+价值208的瑞诗凯瑜伽卡1张</span></div>
        <div><span class="wordModeI">第四名：</span><span class="wordModeII">奖金233元+价值673元的康力健身季卡1张+价值278元的厨子当家价值套餐1份+价值208元的瑞诗凯瑜伽卡1张</span></div>
        <div><span class="wordModeI">第五名：</span><span class="wordModeII">奖金123元+价值364元的康力健身月卡1张+价值188元的厨子当家价值套餐1份+价值208元的瑞诗凯瑜伽卡1张</span></div>
        <div><span class="wordModeI">No 6 — No 10 ：</span><span class="wordModeII">价值300元的青鸟琴行月卡1张+价值60元的口琴1把+价值60元的新东方课外书2本+价值20元厨子当家代金劵1张+风信子时尚花艺馆盆栽1盆</span></div>
        <div><span class="wordModeI">No 11— No 20：</span><span class="wordModeII">价值300元的青鸟琴行月卡1张+价值60元的新东方课外书2本+价值20元厨子当家代金劵1张+风信子时尚花艺馆盆栽1盆</span></div>
        <div><span class="wordModeI"> No 21— No 40 ：</span><span class="wordModeII">价值100元的康力健身3次卡1张+价值108元的新东方考研资料2份附带试听课</span></div>
        <div class="wordAward2">特别奖</div>
        <div><span class="wordModeI">最佳创意奖：</span><span class="wordModeII">奖金66.6元+校级荣誉证书+价值200元的启逸理发店烫发名额1个+价值50元的厨子当家代金劵2张+价值60元的口琴1把+风信子时尚花艺馆鲜花1束</span></div>
        <div><span class="wordModeI">最佳搞怪奖：</span><span class="wordModeII">奖金66.6元+校级荣誉证书+价值200元的启逸理发店烫发名额1个+价值50元的厨子当家代金劵2张+价值60元的口琴1把+风信子时尚花艺馆鲜花1束</span></div>
        <div><span class="wordModeI">最具情怀奖：</span><span class="wordModeII">奖金66.6元+校级荣誉证书+价值200元的启逸理发店烫发名额1个+价值50元的厨子当家代金劵2张+价值60元的口琴1把+风信子时尚花艺馆鲜花1束</span></div>
        <div class="wordAward2">特别奖</div>
        <div><span class="wordModeI">打卡时间段： 12:00—24:00 凭兑奖码截图到店登记领取福利</span></div>
        <div><span class="wordModeI">22号 前100名</span><span class="wordModeII">联建虞美人蛋糕店随机礼品1份（招牌原味奶茶/丝袜奶茶/金桔柠檬/早餐包/小清新/杨枝甘露）</span></div>
        <div><span class="wordModeI">23号 前100名</span><span class="wordModeII">联建腾升超市内美妆小屋面膜1张和免费体验券</span></div>
        <div><span class="wordModeI">24号 前100名</span><span class="wordModeII">东门正对面中国电信价值200元的电话卡1张</span></div>
        <div><span class="wordModeI">25号 前150名</span><span class="wordModeII">联建乐茶醇香奶盖茶店奶茶1杯</span></div>
        <div><span class="wordModeI">26号 前150名</span><span class="wordModeII">金瀚林商业街一楼启逸 Show 35元理发代金劵1张</span></div>
        <div><span class="wordModeI">27号 前100名</span><span class="wordModeII">太平市场茶香如语果汁2杯（西瓜/梨子/柠檬/芒果）</span></div>
        <div><span class="wordModeI">28号 前100名</span><span class="wordModeII">太平市场水果Bang霸王水果茶1杯</span></div>
        <div><span class="wordModeI">29号 前60名</span><span class="wordModeII">太平市场茶香如语果汁2杯（西瓜/梨子/柠檬/芒果）</span></div>
        <div><span class="wordModeI">30号 前60名</span><span class="wordModeII">联建麦香园蛋糕店领取抵用券1张（5元/3元/2元）</span></div>
        <div><span class="wordModeI">1 号 前50名</span><span class="wordModeII">联建乐茶醇香奶盖茶店奶茶1杯</span></div>
        <div><span class="wordModeI">2号 前150名</span><span class="wordModeII">金瀚林商业街三楼Beauty上妆园阿道夫小礼包</span></div>
        <div><span class="wordModeI">3号 前170名</span><span class="wordModeII">金瀚林商业街一楼启逸 Show理发体验1次</span></div>
        <div><span class="wordModeI">4号 前N名</span><span class="wordModeII">活动终极神秘大奖（Best wishes：祝你行大运赢大礼，关注8号晚军训之夜，翼宝永远爱你们(๑˃̵ᴗ˂̵)و）</span></div>
        <div class="wordAward2">幸运大奖</div>
        <div><span class="wordModeII">由赞助商随机抽取幸运报名选手赠送价值888元的聚happy轰趴馆体验一次（私人影院+ktv+麻将棋牌+休息室+电玩室+运动区）</span></div>
        <div class="wordAward2">参与奖</div>
        <div><span class="wordModeII">所有参赛选手凭参与活动的截图，享受宝岛眼镜提供的全场眼镜打5折再减50元的优惠。</span></div>
        <div><span class="wordModeII">凡是报名、投票的可以凭截图享受宝岛眼镜提供的三个月内意外事故免费换新；一年之内，免费保修的服务。</span></div>
        <div><span class="wordModeII">凡是报名、投票的可以凭截图到新体育馆康力健身领取单次体验卡一张（或在10月8日军训之夜当晚到三翼摆点处领取）</span></div>
        <div class="wordAward2">关于领奖</div>
        <div><span class="wordModeII">投票奖、特别奖和幸运大奖于10月8日军训之夜当晚三翼摆点处领取，工作人员会提前通知。</span></div>
        <div><span class="wordModeII">打卡奖请凭兑奖码到相应的门店登记领取。（每日前n名获兑奖码，领取时间：9月22日—10月12日）</span></div>
    </div>
    <div id="closeList">关闭</div>
</div>
<div id="coverPage"></div>
</body>

<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>

<script type="text/javascript">
    wx.config({
        debug: false, // 开启调试模式
        appId: '<?php echo $signature['appid']; ?>', // 必填，公众号的唯一标识
        timestamp: '<?php echo $signature['timestamp']; ?>', // 必填，生成签名的时间戳
        nonceStr: '<?php echo $signature['noncestr']; ?>', // 必填，生成签名的随机串
        signature: '<?php echo $signature['signature']; ?>',// 必填，签名，见附录1
        jsApiList: [
            'checkJsApi',
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'onMenuShareQZone',
            'hideMenuItems',
            'showMenuItems',
            'hideAllNonBaseMenuItem',
            'showAllNonBaseMenuItem',
            'translateVoice',
            'startRecord',
            'stopRecord',
            'onVoiceRecordEnd',
            'playVoice',
            'onVoicePlayEnd',
            'pauseVoice',
            'stopVoice',
            'uploadVoice',
            'downloadVoice',
            'chooseImage',
            'previewImage',
            'uploadImage',
            'downloadImage',
            'getNetworkType',
            'openLocation',
            'getLocation',
            'hideOptionMenu',
            'showOptionMenu',
            'closeWindow',
            'scanQRCode',
            'chooseWXPay',
            'openProductSpecificView',
            'addCard',
            'chooseCard',
            'openCard'
        ]
    });
</script>
<script>
    $("#showList").click(function(){
        $(".giftList").css("display","block");
        $("#coverPage").css("display","block");
    })
    $("#closeList").click(function(){
        $(".giftList").css("display","none");
        $("#coverPage").css("display","none");
    })

</script>
<script type="text/javascript">
    wx.ready(function () {

    });

    wx.error(function (res) {
        // alert(res.errMsg);
    });

    var images = {
        localId: [],
        serverId: []
    };
    document.querySelector('#add-photo').onclick = function () {
        if (<?php echo $isWx; ?>) {
            wx.chooseImage({
                success: function (res) {
                    images.localId = res.localIds;
                    // alert('已选择 ' + res.localIds.length + ' 张图片');
                    uploadImage();
                }
            });
        }
        else{
            alert("微信端才能报名哦，快去关注“湘潭大学三翼校园”吧~");
        }
    };

    document.querySelector('#add-photo2').onclick = function () {
        if (<?php echo $isWx; ?>) {
            wx.chooseImage({
                success: function (res) {
                    images.localId = res.localIds;
                    // alert('已选择 ' + res.localIds.length + ' 张图片');
                    uploadImage();
                }
            });
        }
        else{
            alert("微信端才能报名哦，快去关注“湘潭大学三翼校园”吧~");
        }
    };

    // 上传图片
    function uploadImage(){
        if (images.localId.length == 0) {
            // alert('请先使用 chooseImage 接口选择图片');
            return;
        }
        var i = 0, length = images.localId.length, ttt = new Array(), ddd = new Array();
        images.serverId = [];
        function upload() {
            wx.uploadImage({
                localId: images.localId[i],
                success: function (res) {
                    i++;
                    // alert('已上传：' + i + '/' + length);
                    var serverId = res.serverId;
                    // alert(serverId);
                    ttt[i-1] = serverId;
                    ddd[i-1] = serverId;

                    images.serverId.push(res.serverId);
                    if (i < length) {
                        upload();
                    }
                    else{
                        var ttte = document.getElementById('ttt');
                        var ddde = document.getElementById('ggg');
                        ttte.value = JSON.stringify(ttt);
                        ddde.value = JSON.stringify(ttt);
                        alert('上传完毕。')
                    }},
                fail: function (res) {
                    alert(JSON.stringify(res));
                }
            });
        }
        upload();
    };

    // 下载图片
    document.querySelector('#confirm').onclick = function () {
        if (images.serverId.length === 0) {
            // alert('请先使用 uploadImage 上传图片');
            return;
        }
        var i = 0, length = images.serverId.length;
        images.localId = [];
        function download() {
            wx.downloadImage({
                serverId: images.serverId[i],
                success: function (res) {
                    i++;
                    // alert('已下载：' + i + '/' + length);
                    images.localId.push(res.localId);
                    if (i < length) {
                        download();
                    }
                }
            });
        }
        download();
    };

    var images2 = {
        localId: [],
        serverId: []
    };


</script>
</html>