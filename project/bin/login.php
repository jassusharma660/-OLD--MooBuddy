<?php
    session_start();
    error_reporting(0);
    require './DIR.php'; 
    require '../config/config.php';
    require './includes/class.helper.php';
    require './includes/class.login.php';
    require './includes/Browser.php';
    
    $usr_dat = new Login;
    /*
    if(isset($_GET['rd'])){
        $usr_dat->redir = htmlspecialchars($_GET['rd']);
        $usr_dat->checkCookies();
    }
    */      
    if($_SERVER['REQUEST_METHOD']=="POST"){

        try{
            $email = htmlspecialchars($_POST['email']);
            $email_error = $usr_dat->email($email);
            $pass_error = $usr_dat->onlyPasswordSyntax($_POST['pass']);
            
            if(isset($_POST['rd'])){
                $usr_dat->redir = htmlspecialchars($_POST['rd']);
            }
            if($email_error == 'status'){
                $email_error = '';
                $exceptional_error = "<form method='post' action='./register.php'><input name='email' value='$email' hidden/><span style='font-size:12px;'>You were about to confirm this email during registration.</span><input name='misc' value='resume' hidden><input type='submit' value='Resume Now' style='font-size:10px;cursor:pointer;background:#eee;border:none;display:inline;margin-top:10px;padding:5px;color:#aaa;width:40%;'></form>";
            }
            else
            if(!$usr_dat->email_exist){
                $email_error = '';
                $exceptional_error = 'Email or password is wrong.';
            }
            else
            if($email_error == true){
                if($usr_dat->email_exist == true){
                    $email_error = '';
                    $usr_dat->email = $email;
                    if(isset($_POST['check']))
                        $check = htmlspecialchars($_POST['check']);
                    else
                        $check = '';
                    
                    if(!$usr_dat->check_pass($check)){
                        $email_error = '';
                        $pass_error = '';
                        $exceptional_error = 'Email or password is wrong.';
                    }
                }
            }
        }
        catch(Exception $e){
            echo $e;
             $exceptional_error = "An internal error occured. Please Try again or contact administrator.";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Moobuddy - SignIn</title>
        <script src="<?php echo $script;?>jquery-2.2.3.min.js"></script>
        <script src="<?php echo $script;?>jquery.easing.1.3.min.js"></script>
        <script src="<?php echo $script;?>side-panel-and-more.js"></script> 
        
        <link href="<?php echo $css;?>style.css" rel="stylesheet" type="text/css">
        
        <link href="<?php echo $css;?>search-style.css" rel="stylesheet" type="text/css">
        
        <link rel="icon" type="image/ico" href="<?php echo $favicon;?>home.ico"/>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div id="header">
            <img src="<?php echo $logos;?>logo3.svg" height="40px" class="header-logo"/>
        </div>
        <div class="content">
            <img src="<?php echo $banners;?>space.svg" class="login-title-image"/>
                
            <div id="login-title-image-head">
                
                <span class="line1">
                    Good to see you here!
                </span><br/>
                <span class="line2">
                    Have you created your MooBuddy account?<br/>
                </span><br/>
                <span class="line3">
                    Not yet ! <a class="register-now" href="./register.php">Register Now</a>
                </span>
                
            </div>
            <div class="login-box">

                 <div id="head-text">
                    Login to your account
                </div>
                <span class="form_error" style="margin:15px 0;">
                    <?php if(isset($exceptional_error)) echo $exceptional_error;?>
                </span>
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 

                    <label for="login-email">Username</label>
                    <input type="email" class="text login-text-field" name="email" placeholder="Username / email" required/>
                    <span class="form_error" style="margin: 10px 0;"><?php if(isset($email_error)) echo $email_error;?></span>
                    
                    <label for="login-password">Password</label>
                    <input type="password" class="text login-text-field" name="pass" placeholder="Password" minlength="6" maxlength="20" required/>
                    <span class="form_error"><?php if(isset($pass_error)) echo $pass_error;?></span>
                    <?php
                        if(isset($_GET['rd'])){
                    ?>
                        <input type="hidden" value="<?php echo $_GET['rd'];?>" name="rd"/>
                    <?php
                        }
                    ?>
                    <div class="checkbox">
                         <input id="check" type="checkbox" name="check" value="check">
                        <label for="check">Remember me</label>
                    </div>     
                    
                    <button type="submit">
                        Login
                    </button>
                </form><br/>
                <a href="./recover.php" style="font-size:13px;color:#444;">Need a help!</a>
            </div>
        </div>
        <?php
         include $includes.'footer.php';
        ?>
        <script>
            $(document).ready(function(){
                 
            });               
        </script>
    </body>
</html>