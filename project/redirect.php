<?php
    error_reporting(0);
    session_start();
    if(!isset($_SESSION['for']) && !isset($_SESSION['what'])){
        session_destroy();
        header('location:./bin/login.php?rd=beatsniff_home');
    }
    require './DIR.php';
    require './config/config.php';
    require $includes.'class.helper.php';
    require $includes.'class.redirect.php';

    $rdobj = new Redirect;
    if(!$rdobj->isSessionExist($_SESSION['what'],$_SESSION['for'])){
        session_destroy();
        header('location:./bin/login.php?rd=beatsniff_home');   
    }
    else{
?>
<!DOCTYPE html>
<html>
    <head>
        <title>MooBuddy</title>
        <link rel="icon" type="image/ico" href="<?php echo $favicon;?>home.ico"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
            /* reset */
html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,dl,dt,dd,ol,nav ul,nav li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,embed,figure,figcaption,footer,header,hgroup,menu,nav,output,ruby,section,summary,time,mark,audio,video
{margin:0;padding:0;border:0;font-size:100%;font:inherit;vertical-align:baseline;}
article, aside, details, figcaption, figure,footer, header, hgroup, menu, nav, section {display: block;}
ol,ul{list-style:none;margin:0;padding:0;}
blockquote,q{quotes:none;}
blockquote:before,blockquote:after,q:before,q:after{content:'';content:none;}
table{border-collapse:collapse;border-spacing:0;}
*{margin: 0;}
a{text-decoration: none;}
/*font includes */

@font-face {
    font-family: 'Nexa';
    src: url('<?php echo $fonts."Nexa Light.otf";?>');
    font-weight: normal;
    font-style: normal;
}
html,body{
    font-family: 'Nexa';
    margin: 0;
    color: #fff;
    height: 100%;
    min-height: 100%;
    font-size: 1vw;
}
button{
    text-decoration: none;
    color: #fff;
    background: none;
    outline: none;
}
#background{
    background: #263238;
    height: 100%;
    width: 110%;
    position: fixed;
    top:0;
    left: 0;
    z-index: -100;
    overflow: hidden;
}
#background1{
    background: rgba(38,50,56,0.8);
    height: 100%;
    width: 100%;
    position: fixed;
    top:0;
    left: 0;
    z-index: -88;
}
#background #pic{
    height: 150px;
    width: calc(100%/10);
    height: calc(100%/3);
    display: inline-block;
    margin: -2px;
}
#background #pic img{
    height: 100%;
}
#logo{
    width: 20%;
    display: block;
    margin: 0 auto;
    position: relative;
    top: 30%;
    transform: translateY(-50%);
}
#logo img{
    width: 100%;
}
#moto{
    color: #fff;
    display: block;
    text-align: center;
    font-size: 30px;
    letter-spacing: 2px;
    position: relative;
    top: 20%;
}
#moto .button{
    border: 2px solid #fff;
    font-size: 15px;
    padding: 10px;
    margin:0 auto;
    width: 20%;
    margin-top: 20px;
    border-radius: 50px;
    cursor: pointer;
    display: block;
}
#moto .button:hover{
    color: #444;
    background: #fff;
}
#footer{
    height: 50px;
    position: absolute;
    bottom: 10px;
    right: 10px;
} 
#footer img{
    height: 100%;
}
#year{
    position: absolute;
    bottom: 10px;
    left: 10px;
    color: #fff;
    font-size: 15px;
}
</style>
    </head>
    <body>
        <div id="background1"></div>
        <div style="text-align:center;position:relative;top:50%;transform:translateY(-50%);font-size:15px;margin-left:-100px;">
            <img src="<?php echo $icons."loading.gif";?>" style="height:100px;display:inline-block;"/>
            <span style="height:100px;isplay:inline-block;position:absolute;">
                <span style="position:relative;top:40%;transform:translateY(-50%);text-align:center;left:-50px;">
                    Setting up few things !
                    <iframe src="<?php echo $beatsniff."redirect.php?email=".$_SESSION['email']."&cookie=".$_SESSION['cookie'];?>" style="visibility:hidden;width:0px;height:0px;"></iframe>
                </span>
            </span>
        </div>
        <div id="footer">
            <img src="<?php echo $logos;?>mb.svg" alt="beatsniff"/>
        </div>
        <div id="year"><?php echo date('Y');?></div>
        
    </body>
</html>
<?php
}
?>