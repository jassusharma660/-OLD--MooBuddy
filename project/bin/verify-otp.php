<?php
    error_reporting(0);
    session_start();
    require './DIR.php';
    require './includes/class.helper.php';
    
    if(isset($_POST['otp']) && isset($_POST['email'])){
        
        $usr_dat = new Register;
        $reply = $usr_dat->onlyEmailSyntax($_POST['email']);
        $usr_dat->extra = $_POST['otp'];
        $error = $usr_dat->checkOtpVerification();
        
        if($error == 'true'){
            echo 'true';
        }
        else{
            echo $error;
        }
    }
    else
    {
        echo $error;
    }
?>