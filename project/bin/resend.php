  <?php
    error_reporting(0);
    require './DIR.php';
    require '../config/config.php';
    require './includes/class.helper.php';
    require './includes/PHPMailerAutoload.php';
    
    if(isset($_POST['resend']) && isset($_POST['email']) && $_POST['resend']=='yes'){
        
        $usr_dat = new Register;
        $reply = $usr_dat->onlyEmailSyntax($_POST['email']);
        $usr_dat->email = $_POST['email'];
        $return = $usr_dat->resendEmailVerification('recover');
        
        if($return == 'true'){
            echo "true";
        }
        else{
            echo "false";
        }
    }
    else
    {
        echo "false";
    }
?>