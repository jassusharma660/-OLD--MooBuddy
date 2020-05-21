<?php
    error_reporting(0);
    session_start();
    require './DIR.php';
    require '../config/config.php';
    require './includes/class.helper.php';
    require './includes/PHPMailerAutoload.php';
    require './includes/Browser.php';
        
    $status = false;
    
    if(!isset($_SESSION['email'])){
        session_destroy();
        header('location:./register.php');
    }
    
    if($_SERVER['REQUEST_METHOD']=="POST"){
    
        try{
            $usr_dat = new Register;
            $email_err = $usr_dat->email($_SESSION['email']);
            if($email_err!='status' && $usr_dat->email_exist != true){
                 header('location:./register.php');
            }
            $usr_dat->email = $_SESSION['email'];
            
            $exceptional_error = $usr_dat->password($_POST['pass'],$_POST['rpass']);
            if($exceptional_error){
                if($usr_dat->resetPassword()){
                    $exceptional_error = '';
                    $status = true;
                }
            }
        }
        catch(Exception $e){
            $exceptional_error = "An internal error occured. Please Try again or contact administrator.";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Create Moobuddy Account</title>
        <script src="<?php echo $script;?>jquery-2.2.3.min.js"></script>
        <script src="<?php echo $script;?>jquery.easing.1.3.min.js"></script>
        
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
        
        <div id="heading">
            Recover your account.
        </div>
        
        <div class="content">
          
            <?php

                if ($status==false) {

            ?>
            <div class="register-box">
                 <span class="form_error">
                    <?php if(isset($exceptional_error)) echo $exceptional_error;?>
                </span>
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 

                    <label for="password">New Password</label>
                    <input type="password" class="text text-field" name="pass" placeholder="Your password" minlength="6" maxlength="20" onchange="form.rpass.pattern=this.value;" required/>

                    <label for="rpassword">Repeat Password</label>
                    <input type="password" class="text text-field" name="rpass" placeholder="Repeat password" minlength="6" maxlength="20" title="Please enter same password as entered above!" onchange="this.setCustomValidty(this.validity.patternMismatch?this.title:'');" required/>

                    <button class="text-button">
                        Next
                    </button>
                </form>
            </div>
            <?php
                }
                else
                {
            ?>
            <div class="register-sent">
                <img src="<?php echo "$icons"?>check.svg" height="40px" alt="done" style="position:relative;top:10px;margin:3px;"/>
                <span style="font-size:20px;">All set</span>
                <br/>
                <p style="margin:20px;font-size:13px;">
                    You have come a long way. Now let's get ahead to set up your profile.
                </p>
                    <button class="text-button2" onclick="window.location.href='./login.php'">
                        Go on
                    </button>
            </div>
        </div>
        <?php
            }
        
        include $includes.'footer.php';
        ?>
        <script>
            $(document).ready(function(){
                
            });               
        </script>
    </body>
</html>