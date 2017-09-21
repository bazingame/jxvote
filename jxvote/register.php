<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title>Document</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/upLoad.css">
    <script src="http://cdn.static.runoob.com/libs/jquery/1.10.2/jquery.min.js"></script>
</head>
<body>
     <div id="topSignBanner">
         <div id="cancelSend">取消</div>
         <div id="signWord">
             签到
         </div>
         <div id="userName">台湾小帅哥第一次来到湘大</div>
         <div id="send">发送</div>
     </div>
     <img src="./images/myWord2.png" alt="" id="imgWord">
     <div id="upLoadContainer">
        <div id="writeAndUpLoad">
             <input type="text" class="inputFeel" placeholder="分享你今天拍下的变化和军训心情..">
            <div class="upBox">
                <img src="./images/closeImg.png" alt="" class="deleteImg">
                <img src="./images/blackImg.jpg" alt="" class="upImg">
            </div>
            <div class="upBox">
                <img src="./images/plus.png" alt="" class="plusImg">
            </div>
            <div class="addLabelBtn">添加标签</div>
            <div id="addRemind">已添加X个，还可添加3-X个</div>
            <div id="labelPart">
                <div class="lbPart1">
                    G里G气
                    <div class="lbPart2"></div>
                </div>
            </div>
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