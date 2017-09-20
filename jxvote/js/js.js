function resize()
    {
        var form=document.getElementById('form');
        form.style.height=window.innerHeight*0.8+'px';
    }
    resize();
    function toblue(obj)
    {
        obj.style.border="1.5px solid rgb(52,152,219)";
    }
    function toblack(obj)
    {
        obj.style.border="1.5px solid black";
    }
    function changeheight(obj)
    {
        console.log(obj.value.length);
        if(obj.value.length>39)
        {
            obj.style.height=(parseInt((obj.value.length+1-19)/20)+1)*7+'%';
        }
    }
    function hide()
    {
        document.getElementById('inner1').style.display="none";
    }
    function show()
    {
        document.getElementById('inner1').style.display="block";
    }
    function into()
    {
        var obj=document.getElementsByClassName('self');
        for(i=0;i<obj.length;i++)
        {
            obj[i].style.opacity="0";
        }
        setTimeout(function(){
            document.getElementById('buttonstart').style.display="none";
            show();},400);
    }
    function into2()
    {
        var obj=document.getElementsByClassName('self');
        for(i=0;i<obj.length;i++)
        {
            obj[i].style.opacity="0";
        }
        alert('厉害了我的哥~看来你和我想的一样！\n我们的口号是：搞事！搞事！搞事！\n咩哈哈哈哈哈～你真坏，嘿嘿嘿\n直接用我要报名去给他（她）报名吧～');
        setTimeout(function(){
            document.getElementById('buttonstart').style.display="none";
            show();},400);
    }
    function re()
    {var obj=document.getElementsByClassName('self');
    hide();
    document.getElementById('buttonstart').style.display="block";
    setTimeout(function(){
            for(i=0;i<obj.length;i++)
        {
            obj[i].style.opacity="1";
        }},100);
    }
    hide();