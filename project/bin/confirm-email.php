<?php
    error_reporting(0);
    session_start();
    require './DIR.php';
    require './includes/class.helper.php';

    $status = false;
    
    if(isset($_GET['email']) && $_GET['auth']){
        $usr_dat = new Register; 
        $email_error = $usr_dat->onlyEmailSyntax($_GET['email']);
        $usr_dat->extra = $_GET['auth'];
        
        if($usr_dat->checkEmailVerification($_GET['email'],$_GET['auth']))
        {
            $status = true;
        }
        else
        {
            $status = false;
        }
    }
    else
        $status = false;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Create Moobuddy Account</title>
       
        <link href="<?php echo $css;?>style.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $css;?>search-style.css" rel="stylesheet" type="text/css">
            
        <link rel="icon" type="image/ico" href="<?php echo $favicon;?>home.ico"/>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
<body>
        <div id="header">
            <img src="<?php echo $logos;?>logo3.svg" height="40px" class="header-logo" alt="MooBuddy"/>
            <a href="./login.php">
                <img src="<?php echo $icons;?>user.svg" height="40px" class="header-login" alt="user"/>
            </a>
        </div>
        
        
      
            <?php

                if ($status == true) {   
            ?>  
        <div id="heading">
            Create an account
        </div>
        <div class="content">
            <?php if(!isset($_GET['action'])){?>
            <div id="steps">
                <a href="./register.php">
                    <span class="step active-step">1</span>
                </a>
                <span class="box"></span>

                <span class="step">2</span>

                <span class="box"></span>

                <span class="step">3</span>
            </div>
            <?php }?>
            <div class="register-sent">
                <img src="<?php echo "$icons"?>check.svg" height="40px" alt="done" style="position:relative;top:10px;margin:3px;"/>
                <span style="font-size:20px;">Verified!</span>
                <br/>
                <p style="margin:20px;font-size:12px;">
                    (<?php echo $_GET['email'];?>)
                </p>
                <p style="margin:20px;font-size:13px;">
                    Good! Let's get started by setting up a password.
                </p>
                <?php 
                    if(isset($_GET['action']) && $_GET['action']=='recover')
                        $link = './reset-password.php';
                    else{
                        if(isset($_GET['rd']))
                            $link = './set-password.php?rd='.htmlspecialchars($_GET['rd']);
                        else
                            $link = './set-password.php';
                    }
                ?>
                <a href="<?php echo $link;?>">
                    <button class="text-button">
                        Go on
                    </button>
                </a>
            </div>
            <?php
                }
                else
                {
                ?>
            
                <div class="register-sent">
                    <p style="margin-top:40px;font-size:17px;">
                        Please register first to perform this action!
                    </p>
                    <a href="./register.php">
                    <button class="text-button ">
                        Register
                    </button>
                    </a>
                </div>
                
                <?php
                } 
            include $includes.'footer.php';
            ?>