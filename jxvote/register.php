<?php
include '../class/WeiXin.class.php';
include '../class/User.class.php';
include_once '../class/Sign.class.php';
session_start();


$UA = $_SERVER['HTTP_USER_AGENT'];
if (preg_match('/MicroMessenger/', $UA)) {
    $isWx = 1 ;
    $action = './signData.php';
}
else{
    $isWx = 0 ;
    $action = '';
}

/*获取JDk签名并解析*/
$weixin = new WeiXin();
$url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   //获取地址栏完整url（带参数）
$signature = $weixin->getSignature($url);
$signature = json_decode($signature, 1);
//print_r($signature);


$user = new User($_SESSION['openId'], $_SESSION['nickName']);
$user->timePlus();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title>军训时光记 - 三翼工作室</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/upLoad.css">
    <link href="./css/mui.min.css" rel="stylesheet" type="text/css" />
    <script src="./js/mui.min.js"></script>
    <!--    <script src="//cdn.static.runoob.com/libs/jquery/1.10.2/jquery.min.js"></script>-->
    <script src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
<div id="coverPage"></div>

     <div id="topSignBanner">
         <div id="cancelSend" onclick="location.href='./index.php'">取消</div>
         <div id="signWord">
             签到
         </div>
         <div id="userName"><?php echo $_SESSION['nickName'];?></div>
         <div class="send" id = "confirm">发送</div>
     </div>
     <img src="./images/myWord2.png" alt="" id="imgWord">
     <div id="upLoadContainer">
        <div id="writeAndUpLoad">
             <input type="text" class="inputFeel" placeholder="分享你今天拍下的变化和军训心情.." id="words">

<!--            <div class="upBox" id="showImg">-->
<!--            </div>-->


            <div class="upBox" id="add-photo2">
                <img src="./images/plus.png" alt="" class="plusImg">
            </div>
            <br><br><br><span style="font-size: small;font-family: 'Microsoft Yahei'; display: inline-block;
    height:20px;
    text-align: center;
    line-height: 20px;
    margin: 0;
    padding-left:10px;
    padding-right:10px;
    background-color: #3dd3b2;
    color: white;
     margin-top:10px;">第一次打卡的第一张会作为封面哦</span>
<!--            <div id="add-photo2"><i id="text-photo2" class="fa fa-plus"></i></div>-->
            <div id="add-photo"><i id="text-photo2" class="fa fa-plus"></i></div>

<!--            <div id="confirm"><i id="text-photo2" class="fa fa-plus"></i></div>-->
            <form action="signData.php?type=pic" method="post">
            <input id="ttt" name="serverId" type="hidden" value="" required>
<!--            <div  class="addLabelBtn" id="confirm">确认上传</div>-->
            </form>

<!--            <div class="addLabelBtn">添加标签</div>-->
<!--            <div id="addRemind">已添加X个，还可添加3-X个</div>-->
<!--            <div id="labelPart">-->
<!--                <div class="lbPart1">-->
<!--                    G里G气-->
<!--                    <div class="lbPart2"></div>-->
<!--                </div>-->
<!--            </div>-->
        </div>
     </div>
         <nav class="bottom-nav"> 
        <div class="btn-d">
            <div class=" bottomNavBtn2" style="width:60%;height:60%;" onclick="location.href = './index.php'"> <span>首页</span></div>
        </div>
        <div class="btn-d ">
             <img src="./images/signIn.png" alt="" id="signInImg">
        </div>
        <div class="btn-d ">
             <div class=" bottomNavBtn2" style="width:60%;height:60%;color:black;" onclick="location.href = './my.php'"> <span>个人</span></div>
        </div>
    </nav>
     <div id="redeemCode" class="mui-panel mui--z2">
<!--         <div class="mui-card">-->
<!--             <div class="mui-card-content">-->
<!--                 <div class="mui-card-content-inner">123</div>-->
<!--             </div>-->
<!--         </div>-->
          <div id="redeemWords"></div>
         <div id="redeemCodes"><img id = "redeemPic" src="./images/richman/0924.jpg"></div>
         <ul>
<!--             <li id="copy">复制</li>-->
             <li id="close"><button id="close1" class="mui-btn mui-btn-block" style="width: 200px">确定</button></li>
         </ul>
     </div>


<!-- script    -------------------->

     <script type="text/javascript" src="//res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
     <script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
     <script>
       var windowHeight=document.body.clientHeight;
       function lockHeight(){
           $("body").css("height",windowHeight+"px");
       }
       window.onload=lockHeight();
     </script>
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
     <script type="text/javascript">
         wx.ready(function () {
         });

         wx.error(function (res) {
//              alert(res.errMsg);
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
//                          alert('已选择 ' + res.localIds.length + ' 张图片');
//                         showPicChoosed();
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


         //预览已选图片
         function showPicChoosed() {

         }

         // 上传图片
         function uploadImage(){
             if (images.localId.length == 0) {
                 // alert('请先使用 chooseImage 接口选择图片');
                 return;
             }
             var i = 0, length = images.localId.length, ttt = new Array();
             images.serverId = [];
             function upload() {
                 wx.uploadImage({
                     localId: images.localId[i],
                     success: function (res) {
                         //预览图片
                         var img = '<div class="upBox" style="background-image:url('+images.localId[i]+');background-size: cover "></div>';
                         $("#add-photo2").before(img);
                         i++;
                         // alert('已上传：' + i + '/' + length);
                         var serverId = res.serverId;
                         // alert(serverId);
                         ttt[i-1] = serverId;

                         images.serverId.push(res.serverId);
                         if (i < length) {
                             upload();
                         }
                         else{
                             var ttte = document.getElementById('ttt');
                             ttte.value = JSON.stringify(ttt);
//                             alert('上传完毕。')
//                             alert(ttte.value)
                         }},
                     fail: function (res) {
                         alert(JSON.stringify(res));
                     }
                 });
             }
             upload();
         };

         $("#close").on('click',function () {
             location.href="./index.php";
         });

         // 下载图片
         document.querySelector('#confirm').onclick = function () {
             if (images.serverId.length === 0) {
                  alert('请先选择上传图片');
                 return;
             }
             if(confirm("确定签到?")){
                 $.ajax({
                     url:'signData.php?type=pic',
                     type:'POST',
                     data:{
                         serverId:$("#ttt").val(),
                         words:$("#words").val()
                     },
                     success:function (res) {
                         var jsonres = JSON.parse(res);
                         if(jsonres.code == 1){
                         $("#redeemWords").html(jsonres.res);
                         $("#redeemPic").attr('src',jsonres.pic);
                         $("#coverPage").css('display','block');
                         $("#redeemCode").css('display','block');
//                            alert('1');
                         }else if(jsonres.code==0){
                             alert(jsonres.res);
                         }
//
//                        location.href="./index.php";
//                        alert('上传成功!');
                     }
                 });
             }else {

             }
         };




         var images2 = {
             localId: [],
             serverId: []
         };


     </script>
<!-- script    -------------------->

    <script>
      $(".lbPart1").click(function(){
         if($(this).css("padding-top")=="0.5px"){
              $(this).css("background-color","#d2d2d2");
              $(this).children(".lbPart2").css("background-color","#d2d2d2");
              $(this).css("padding-top","0.4px");
         }
         else  if($(this).css("padding-top")=="0.4px"){
              $(this).css("background-color","#ff6633");
              $(this).children(".lbPart2").css("background-color","#ff6633");
              $(this).css("padding-top","0.5px");
         }
      })
    </script>
</body>
</html>

