var voting=false;
$("#Vvote").on("click",function(){
    if (isWx) {
        if(voting)return false;
        $("#Vvote").html("ing...");
        voting=true;
        $.ajax({
            url:"./vote.php?id="+pid,
            type:"get",
            success:function(data){
                try{
                    var jsonD=JSON.parse(data);
                    if(jsonD.code==0){
                        alert(jsonD.msg);
                        voteC++;
                        $("#voteCounter").html(voteC);
                    }
                    else{
                        alert(jsonD.msg);
                    }
                }
                catch(e){
                    alert("解析错误");
                }
                voting=false;
                $("#Vvote").html("投票");
            }
        });        
    }
    else{
        alert('微信端才能投票哦，快去关注“湘潭大学三翼校园”吧~');
    }

});
