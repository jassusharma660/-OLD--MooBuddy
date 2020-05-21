<?php
    error_reporting(0);
    session_start();
    require './DIR.php';
    require '../config/config.php';
    require './includes/class.helper.php';
    require './includes/PHPMailerAutoload.php';

    $status = false;
    if($_SERVER['REQUEST_METHOD']=="POST"){
    
        try{
            $usr_dat = new Register;
            $email_error = $usr_dat->validateEmailForRecovery($_POST['email']);
            
            if($email_error == 'exist'){
                $email_error = ''; 
                $usr_dat->err_count = 0;
                
                if($usr_dat->sendEmailVerification('recover')){
                    $status = true;
                }
                else{
                    $status = false;
                }
            }else
            if($email_error=='status'){
                $email_error = '';
                $exceptional_error = "<form method='post' action='./register.php'><input name='email' value='$email' hidden/><span style='font-size:12px;'>You were about to confirm this email during registration.</span><input name='misc' value='resume' hidden><input type='submit' value='Resume Now' style='font-size:10px;cursor:pointer;background:#eee;border:none;display:inline;margin-top:10px;padding:5px;color:#aaa;width:40%;'></form>";
            }
            else
            if($usr_data->email_exist == false){
                $email_error = '';
                $exceptional_error = 'User does not exist!';
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
        <title>Moobuddy Account Recovery</title>
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

                if ($status == false) {

            ?>
            
            <div class="register-box">
                
                <span class="form_error">
                    <?php if(isset($exceptional_error)) echo $exceptional_error;?>
                </span>
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
                   
                    <label for="email">Email</label>
                    <input type="email" class="text text-field" name="email" placeholder="yourname@website" value="<?php if(isset($_POST['fname'])) echo $_POST['email'];?>"  minlength="5" maxlength="100" required/>
                    
                    <span class="form_error"><?php if(isset($email_error)) echo $email_error;?></span>
                    
                   
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
                 
                <span class="form_error">
                    <?php if(isset($exceptional_error)) echo $exceptional_error;?>
                </span>
                <span class="resent_confirm" style="display:<?php if( isset($exceptional_error)) echo "none";else echo "inherit";?>;">
                    <img src="<?php echo "$icons"?>check.svg" height="20px" alt="done" style="position:relative;top:5px;margin:3px;"/>An email with OTP was sent to you.
                </span>
                <form method="post"> 
                    <label for="pin">OTP</label>
                    <input type="password" class="pin1 pin" name="pin1" placeholder="*" maxlength="1" autofocus="true" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" required/>
                    <input type="password" class="pin2 pin" name="pin2" placeholder="*" maxlength="1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" required/>
                    <input type="password" class="pin3 pin" name="pin3" placeholder="*" maxlength="1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" required/>
                    <input type="password" class="pin4 pin" name="pin4" placeholder="*"  maxlength="1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" required/>
                    <br/> 
                    <input type="password" class="email_string" style="display:none;" value="<?php echo $_POST['email'];?>"/>
                    <button class="verify-pin text-button2">
                        Next
                    </button>
                    <a class="text-resend text-skip">
                        Resend
                    </a><br/>
                </form>
            </div>
            <?php
                }
             include $includes.'footer.php';
            ?>
        </div>
        <script>
            $(document).ready(function(){
                $('.verify-pin').click(function(event){
                    var pin1 = $('.pin1').val();
                    var pin2 = $('.pin2').val();
                    var pin3 = $('.pin3').val();
                    var pin4 = $('.pin4').val();
                    
                    var otp = pin1+pin2+pin3+pin4; event.preventDefault();
                       
                    if(otp==''){
                         $('.form_error').html('OTP should not be empty!.');
                    }
                    else{
                        var email = $('.email_string').val();
                        verifyOtp(otp,email);
                    }
                });
                $('.text-resend').click(function(){
                    
                    var email = $('.email_string').val();
                    var dataString = 'resend=yes&email='+email+'&action=recover';                    
                    
                    $.ajax({
                        type: "POST",
                        url: "./resend.php",
                        data: dataString,
                        cache: false,
                        success: function(html)
                        {
                            html = html.replace(/\s/g,'');
                            $('.resent_confirm').hide();
                            
                                if(html=="true")
                                {
                                    $('.form_error').html('We have sent you an OTP on your email.<br/>Please go and check it.');
                                }
                                else{
                                    $('.form_error').html("There is some problem in resending OTP.<br/>Please try again later");
                                }
                        }
                    });
                });
                
                $('.pin').keyup(function(){
                    var length1 = $('.pin1').val().length;
                    var length2 = $('.pin2').val().length;
                    var length3 = $('.pin3').val().length;
                    
                    if(length1==1){
                        $('.pin2').focus();
                        
                        if(length2==1){
                            $('.pin3').focus();
                            
                            if(length3==1)
                                $('.pin4').focus();
                        }
                    }
                    else
                        $('.pin1').focus();
                });
                
                $('.pin4').keydown(function(event){
                    
                    var key = event.keyCode || event.charCode; 
                    
                    if($('.pin4').val().length==0)
                        if(key==46 || event.which==8 || key==229){
                            $('.pin4').val('');
                            $('.pin3').focus();
                        }
                });
                $('.pin3').keydown(function(event){
                   
                    var key = event.keyCode || event.charCode;
                    
                    if($('.pin3').val().length==0)
                        if(key==46 || event.which==8 || key==229){
                            $('.pin3').val('');
                            $('.pin2').focus();
                        }
                });
                $('.pin2').keydown(function(event){
                   
                    var key = event.keyCode || event.charCode;
                    
                    if($('.pin2').val().length==0)
                        if(key==46 || event.which==8 || key==229){
                            $('.pin2').val('');
                            $('.pin1').focus();
                        }
                });
            });  
            function verifyOtp(otp,email){
                var dataString = 'otp='+otp+'&email='+email;                    
                    $.ajax({
                        type: "POST",
                        url: "./verify-otp.php",
                        data: dataString,
                        cache: false,
                        success: function(html)
                        {
                                if(html=="true")
                                {
                                   window.location.href = './reset-password.php';
                                }
                                else{
                                    $('.form_error').html(html);
                                }
                        }
                    });
            }
        </script> 
    </body>
</html>