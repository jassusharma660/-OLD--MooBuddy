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
            if($email_err!='status'){
                 header('location:./register.php');
            }
            $usr_dat->email = $_SESSION['email'];
            
            $exceptional_error = $usr_dat->password($_POST['pass'],$_POST['rpass']);
            if($exceptional_error){
                if($usr_dat->addUserDataIntoDb()){
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
        
        <div class="content">
            <div id="steps">
                <a href="./register.php">
                    <span class="step">1</span>
                </a>
                <span class="box"></span>

                <span class="step  active-step">2</span>

                <span class="box"></span>

                <span class="step">3</span>
            </div>
            <?php

                if ($status==false) {

            ?>
            <div class="register-box">
                 <span class="form_error">
                    <?php if(isset($exceptional_error)) echo $exceptional_error;?>
                </span>
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 

                    <label for="password">Password</label>
                    <input type="password" class="text text-field" name="pass" placeholder="Your password" minlength="6" maxlength="20" onchange="form.rpass.pattern=this.value;" required/>

                    <label for="rpassword">Repeat Password</label>
                    <input type="password" class="text text-field" name="rpass" placeholder="Repeat password" minlength="6" maxlength="20" title="Please enter same password as entered above!" onchange="this.setCustomValidty(this.validity.patternMismatch?this.title:'');" required/>
                    <?php
                        if(isset($_GET['rd'])){
                    ?>
                        <input type="hidden" value="<?php echo htmlspecialchars($_GET['rd']);?>" name="rd"/>
                    <?php
                        }
                    ?>
                    <button class="text-button">
                        Next
                    </button>
                </form>
            </div>
            <?php
                }
                else
                {
                    //check if email is send
            ?>
            <div class="register-sent">
                <img src="<?php echo "$icons"?>check.svg" height="40px" alt="done" style="position:relative;top:10px;margin:3px;"/>
                <span style="font-size:20px;">All set</span>
                <br/>
                <p style="margin:20px;font-size:13px;">
                    You have come a long way. Now let's get ahead to set up your profile.
               
                </p>
                <?php
                    if(isset($_POST['rd']))
                        $link = './set-profile.php?rd='.htmlspecialchars($_POST['rd']);
                    else
                        $link = './set-profile.php';    
                ?>
                
                <a href="<?php echo $link;?>">
                    <button class="text-button2">
                        Go on
                    </button>
                </a>
                
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