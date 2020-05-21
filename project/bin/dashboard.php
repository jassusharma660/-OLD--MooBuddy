<?php
    session_start();
    require './DIR.php';
    echo $_SESSION['for']." ".$_SESSION['what'];
    echo $_COOKIE['for']." ".$_COOKIE['what'];
?>
<!DOCTYPE html>
<html>
    <head>
        <title>MooBuddy - Dashboard</title>
        <script src="<?php echo $script;?>jquery-2.2.3.min.js"></script>
        <script src="<?php echo $script;?>jquery.easing.1.3.min.js"></script>
        <script src="<?php echo $script;?>side-panel-and-more.js"></script> 
        <script src="<?php echo $script;?>jquery.slimscroll.min.js"></script>
       
        <link href="<?php echo $css;?>style.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $css;?>search-style.css" rel="stylesheet" type="text/css">
            
        <link rel="icon" type="image/ico" href="<?php echo $favicon;?>home.ico"/>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="side-panel">
            <div id="side-panel-header">
                <img src="<?php echo $logos;?>logo3.svg" width="50%" class="side-panel-logo"/>
                <div id='hide-panel'><img src="<?php echo $icons;?>back.svg" width="13px"/></div>
            </div>
        </div>
        <div id="header">
            <img src="<?php echo $icons;?>menu.svg" width="20px" id='show-panel'/>
            <img src="<?php echo $icons;?>icon3.svg" width="40px" class="header-logo"/>
            
            <form class="search-box">   
                    <img src="<?php echo $icons;?>search.svg" height="28px" class="search-svg"/>
                <input type="text" class="search-text"/>
            </form>
            <span class="user-profile-pic">
                <img src="<?php echo $profile_pics_small;?>jassu50x50.jpg"/>
                <span class="user-profile-fname"><?php $fname="Jassu";echo $fname;?></span>
                <span class="popup_show user-profile-setting">
                    <img src="<?php echo $icons;?>settings.svg"/>
                </span>
            </span>
        </div>
        <div class="content">
            <div id="sub-header">
                <span class="sub-header-count sub-header-alert-count">1</span>
                <img src="<?php echo $icons;?>alert.svg" class="sub-header-icon sub-header-alert"/>
                
                
                <span class="sub-header-count sub-header-msg-count">999</span>
                <img src="<?php echo $icons;?>msg.svg" class="sub-header-icon sub-header-msg"/>
                
                
                <span class="sub-header-count sub-header-request-count">1 M</span>
                <img src="<?php echo $icons;?>request.svg" class="sub-header-icon sub-header-request"/>
            </div>
            
            <?php include './includes/show-tiles.php';?>
            
        </div>
        <?php include './includes/footer.php';?>
        
        <div id="popup">
            <span id="popup-icon">
                <img src="<?php echo $icons;?>settings.svg"/>
                <span>Settings</span>
            </span>
            <span id="popup-text">Video provides a powerful way to help you prove your point. When you click Online Video, you can paste in the embed code for the video you want to add. You can also type a keyword to search online for the video that best fits your document.
To make your document look professionally produced, Word provides header, footer, cover page, and text box designs that complement each other. For example, you can add a matching cover page, header, and sidebar. Click Insert and then choose the elements you want from the different galleries.
Themes and styles also help keep your document coordinated. When you click Design and choose a new Theme, the pictures, charts, and SmartArt graphics change to match your new theme. When you apply styles, your headings change to match the new theme.
Save time in Word with new buttons that show up where you need them. To change the way a picture fits in your document, click it and a button for layout options appears next to it. When you work on a table, click where you want to add a row or a column, and then click the plus sign.
Reading is easier, too, in the new Reading view. You can collapse parts of the document and focus on the text you want. If you need to stop reading before you reach the end, Word remembers where you left off - even on another device.</span>
            <span id="popup-action">
            </span>
        </div>
    </body>
</html>