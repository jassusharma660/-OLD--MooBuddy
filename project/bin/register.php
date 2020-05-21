<?php
    error_reporting(0);
    session_start();
    require './DIR.php';
    require '../config/config.php';
    require './includes/class.helper.php';
    require './includes/PHPMailerAutoload.php';

    $status = false;
    
    if($_SERVER['REQUEST_METHOD']=="POST"){
    
        if(isset($_POST['misc'])){
            if($_POST['misc']=='resume')
                $status = true;
        }
        else{
            try{
                $usr_dat = new Register;
                $fname_error = $usr_dat->fname($_POST['fname']);
                $lname_error = $usr_dat->lname($_POST['lname']);
                $email_error = $usr_dat->email($_POST['email']);
                $date_error = $usr_dat->day($_POST['date'],$_POST['month'],$_POST['year']);
                
                if(isset($_POST['rd'])){
                    $usr_dat->redir = htmlspecialchars($_POST['rd']);
                }
                
                if($email_error=='status'){
                    $status = true;
                    $exceptional_error = "This email was already used!<br/> If you are the one who requested this last time then enter the otp sent to your email. ";
                }
                else{
                    if($usr_dat->sendEmailVerification()){
                        $status = true;
                    }
                    else{
                        $status = false;
                    }
                }
            }
            catch(Exception $e){
                $exceptional_error = "An internal error occured. Please Try again or contact administrator.";
            }
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
            Create an account
        </div>
        
        <div class="content">
            <div id="steps">
                <a href="./register.php">
                    <span class="step active-step">1</span>
                </a>
                <span class="box"></span>

                <span class="step">2</span>

                <span class="box"></span>

                <span class="step">3</span>
            </div>
            <?php

                if ($status == false) {

            ?>
            
            <div class="register-box">
                
                <span class="form_error">
                    <?php if(isset($exceptional_error)) echo $exceptional_error;?>
                </span>
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
                    <label for="fname">Name</label>
                    <input type="text" class="text text-field-lrg" name="fname" placeholder="First" value="<?php if(isset($_POST['fname']))echo $_POST['fname'];?>" minlength="1" maxlength="50" required/>
                    
                    <input type="text" class="text text-field-lrg" name="lname" placeholder="Last" value="<?php if(isset($_POST['lname']))echo $_POST['lname'];?>" minlength="1" maxlength="50"/>
                    
                    <span class="form_error"><?php if(isset($fname_error)) echo $fname_error;?></span>
                    <span class="form_error"><?php if(isset($lname_error)) echo $lname_error;?></span>
                    
                    <label for="email">Email</label>
                    <input type="email" class="text text-field" name="email" placeholder="yourname@website" value="<?php if(isset($_POST['fname'])) echo $_POST['email'];?>"  minlength="5" maxlength="100" required/>
                    
                    <span class="form_error"><?php if(isset($email_error)) echo $email_error;?></span>
                    
                    <label for="dob">Birthday</label>

                    <select type="text" class="text text-field-sml" name="date" required>
                        <option value="">Day</option>
                        <?php
                            for($i=01;$i<=31;$i++) 
                                if($i==$_POST['date'])
                                    echo "<option value='$i' selected>$i</option>";
                                else
                                    echo "<option value='$i'>$i</option>";
                        ?>
                    </select>

                    <select type="text" class="text text-field-sml" name="month" required>
                        <option value="">Month</option> 
                        <?php
                            $j=1;
                            for($i=-5;$i<=6;$i++)
                            {
                                $month = date("F",strtotime($i>0?"+$i months":"$i months"));
                                if($j==$_POST['month'])
                                    echo "<option value='$j' selected>$month</option>";
                                else
                                    echo "<option value='$j'>$month</option>";
                                $j++;
                            }
                        ?>
                    </select>
                    <select type="text" class="text text-field-sml" name="year" required>
                        <option value="">Year</option>
                        <?php
                            for($i=1950;$i<=date("Y");$i++)
                                if($i==$_POST['year'])
                                    echo "<option value='$i' selected>$i</option>";
                                else
                                    echo "<option value='$i'>$i</option>";
                        ?>
                    </select>
                                        
                    <span class="form_error"><?php if(isset($date_error))echo $date_error;?></span>
                    
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
                 
                <span class="form_error">
                    <?php if(isset($exceptional_error)) echo $exceptional_error;?>
                </span>
                <span class="resent_confirm" style="display:<?php if(isset($exceptional_error)) echo "none";else echo "inherit";?>;">
                    <img src="<?php echo "$icons"?>check.svg" height="20px" alt="done" style="position:relative;top:5px;margin:3px;"/>An email with OTP was sent to you.
                </span>
                <form method="post">
                    <label for="pin">OTP</label>
                    <input type="password" class="pin1 pin" name="pin1" placeholder="*" maxlength="1" autofocus="true" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" required/>
                    <input type="password" class="pin2 pin" name="pin2" placeholder="*" maxlength="1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" required/>
                    <input type="password" class="pin3 pin" name="pin3" placeholder="*" maxlength="1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" required/>
                    <input type="password" class="pin4 pin" name="pin4" placeholder="*"  maxlength="1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" required/>
                    <br/>
                    <?php
                        if(isset($_POST['rd'])){
                    ?>
                        <input type="hidden" value="<?php echo htmlspecialchars($_POST['rd']);?>" name="rd" id="rd"/>
                    <?php
                        }
                    ?>
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
                    var dataString = 'resend=yes&email='+email;                    
                    
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
                var rd = $('#rd').val();
                if(rd)
                    var link = './set-password.php?rd='+rd;
                else
                    var link = './set-password.php';
                    
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
                               window.location.href = link;
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