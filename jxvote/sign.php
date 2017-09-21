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
else{
    header('Location:index.php');
    $isWx = 0 ;
    $action = '';
}
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
        <link rel="stylesheet" href="./Font-Awesome-4.4.0/css/font-awesome.min.css">
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
    <div id="container" class="mui-panel mui--z3">
        <div id="inner1">
        <ul class="mui-tabs__bar mui-tabs__bar--justified">
          <li class="mui--is-active"><a data-mui-toggle="tab" data-mui-controls="pane-default-1">填写报名信息</a></li>
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
        <div id="zxszContain"></div>
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
</body>

<script type="text/javascript" src="//res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    
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
