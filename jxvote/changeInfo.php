<?php 
include_once '../class/WeiXin.class.php';
include_once '../class/User.class.php';
include_once '../class/Sign.class.php';
include_once '../class/View.class.php';
header("Content-type:text/html;charset=utf-8");
$user = new User($_SESSION['openId'], $_SESSION['nickName']);
$user->timePlus();

 
/*获取UA*/
$UA = $_SERVER['HTTP_USER_AGENT'];
if (preg_match('/MicroMessenger/', $UA)) {
    $isWx = 1 ;
    $action = './changeData.php';
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

$view = new View();
$signData = $view->searchById();

foreach ($signData as $key => $value) {
  if ($value['type'] == 'A') {
    $dataA = $value;
  }
  else{
    $dataB = $value;
  }
}
if (!$dataB) {
  $dataB['team'] = "无团队数据";
}
if (!$dataA) {
  $dataA['name'] = "无个人数据";
}

?>

<!doctype html>
<html>
    <head>
        <title>修改信息 · 创意军训风采照</title>
        <meta charset="utf-8"></head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width,init-scale=1.0,max-scale=1.0,userscalable=no"/>
        <link href="./style.css" type="text/css" rel="stylesheet">
        <link href="./css/mui.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="./Font-Awesome-4.4.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
        <script src="./js/mui.min.js"></script>
        <script type="text/javascript">
          function check(form){
            if (<?php echo $isWx; ?>) {
              
            }
            else{
              alert("微信端才能报名哦，快去关注“湘潭大学三翼校园”吧~");
            }
          }
        </script>
        <style type="text/css">
          
        </style>  
    </head>
<body>
    <div id="back" style="background-image: url(./images/change.jpg);"></div>
    <div id="container" class="mui-panel mui--z3">
        <div id="inner1">
        <ul class="mui-tabs__bar mui-tabs__bar--justified">
          <li class="mui--is-active"><a data-mui-toggle="tab" data-mui-controls="pane-default-1"><?php echo $dataA['name']; ?></a></li>
          <li><a data-mui-toggle="tab" data-mui-controls="pane-default-2"><?php echo $dataB['team']; ?></a></li>
        </ul>
        <div class="mui-tabs__pane mui--is-active" id="pane-default-1">
            <form action="<?php echo $action; ?>" method="POST">
              <div class="mui-textfield mui-textfield--float-label">
                <input type="text" name="name">
                <label>修改姓名</label>
              </div>
              <div class="mui-textfield mui-textfield--float-label">
                <input type="text" name="tel">
                <label>修改联系方式</label>
              </div>
              <div class="mui-textfield mui-textfield--float-label">
                <textarea name="introduce"></textarea>
                <label>修改参赛宣言</label>
              </div>
              <input name="type" type="hidden" value="A">
              <input id="ttt" name="serverId" type="hidden" value="233">
              <p style="font-size: 15px;color:  rgba(0,0,0,.26);">添加更多照片</p>
              <h7 style="font-size: 15px;color:  rgba(0,0,0,.26);">更改此次的首张照片作为封面</h7>
              <div style="float: right">
                <input class='tgl tgl-ios' id='cb2' type='checkbox' name="change" value="1">
                <label class='tgl-btn' for='cb2'></label>
              </div>
              <br /><br />
              <div id="add-photo"><i id="text-photo" class="fa fa-plus"></i></div>
              <br />
              <button id="confirm" class="mui-btn mui-btn--raised mui-btn--primary" type="submit" onclick="check(this.form)">确认修改</button>
              <h7 style="font-size: 15px;color:  rgba(0,0,0,.26);float: right;line-height: 45px;margin-right: 10%;">不添加的项代表不修改</h7>
            </form>
        </div>
        <div class="mui-tabs__pane" id="pane-default-2">
            <form action="<?php echo $action; ?>" method="POST">
              <div class="mui-textfield mui-textfield--float-label">
                <input type="text" name="team">
                <label>修改团队名称</label>
              </div>
              <div class="mui-textfield mui-textfield--float-label">
                <input type="text" name="tel">
                <label>修改联系方式</label>
              </div>
              <div class="mui-textfield mui-textfield--float-label">
                <textarea name="introduce"></textarea>
                <label>修改参赛宣言</label>
              </div>
              <input name="type" type="hidden" value="B">
              <input id="ggg" name="serverId" type="hidden" value="233">
              <p style="font-size: 15px;color:  rgba(0,0,0,.26);">添加更多照片</p>
              <h7 style="font-size: 15px;color:  rgba(0,0,0,.26);">更改此次的首张照片作为封面</h7>
              <div style="float: right">
                <input class='tgl tgl-ios' id='cb3' type='checkbox' name="change" value="1">
                <label class='tgl-btn' for='cb3'></label>
              </div>
              <br /><br />
              <div id="add-photo2"><i id="text-photo2" class="fa fa-plus"></i></div>
              <br />
              <button id="confirm2" class="mui-btn mui-btn--raised mui-btn--primary" type="submit" onclick="check(this.form)">确认修改</button>
              <h7 style="font-size: 15px;color:  rgba(0,0,0,.26);float: right;line-height: 45px;margin-right: 10%;">不添加的项代表不修改</h7>
            </form>
        </div>
        <br />
        <div class="mui-divider"></div>
        <br />
        <p style="font-size:18px;margin-top:0%;"><b>奖项设置</b></p></br>
        <p>票数No.1：<span class="red">1000</span>元奖金+校级荣誉证书+由康力健身提供价值498元的学期卡一张</p>
        <p>票数No.2：<span class="red">500</span>元奖金+校级荣誉证书+由康力健身提供的价值248元的月卡一张</p>
        <p>票数No.3：<span class="red">100</span>元奖金+校级荣誉证书+由康力健身提供的价值128元的15天体验卡一张</p>
        <p>综合评选：“最佳创意搞怪奖”“最佳视觉享受奖”“最佳红色文化奖”赠送三翼精美明信片一套以及校级荣誉证书</p>
        </br>
        <p>*最佳创意搞怪奖将拥有卫星马场永久免费名额</p>
        <p>*最佳视觉享受奖将拥有万事屋提供的龙猫玩具</p>
        <p>*最佳红色文化奖将拥有万事屋提供的汽车模型</p>
        </br>
        <p>*凡报名者均可享康力健身<span class="red">6.8</span>折优惠</p></br>
        <p onclick="hide()">独家赞助：必胜客</p>
        <div id="zzpic"></div>
        <p style="font-size:13px; margin-top:5%;">*点餐前出示本人有效学生证，或在支付宝上“校园生活”中认证即可享必胜客欢乐餐厅堂食8折美食优惠（部分特别说明产品除外）</p></br></br>
        <p style="font-size:13px; margin-bottom:10%;">*本活动最终解释权归三翼工作室所有</p>
        </div>
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
<script src="js/index.js"></script>
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
