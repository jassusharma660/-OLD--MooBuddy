<?php
    error_reporting(0);
    session_start();
    require './DIR.php';
    require '../config/config.php';
    require './includes/class.helper.php';
    require './includes/country.php';
    require './includes/PHPMailerAutoload.php';

    $status = false;
     
    if($_SERVER['REQUEST_METHOD']=="POST"){
            if($_POST['check'] == 'check'){
                try{
                    $args = array("beatsniff"=>$beatsniff);
                    $usr_dat = new Register($args);
                    $usr_dat->email = $_SESSION['email'];
                    
                    if(isset($_POST['country']))
                        $country = htmlspecialchars($_POST['country']);
                    else
                        $country = '';

                    if(isset($_POST['mobile-code']) && isset($_POST['mobile-no']))
                        $mobile = htmlspecialchars($_POST['mobile-code'].$_POST['mobile-no']);
                     else
                        $mobile = '';

                    if(isset($_POST['gender']))
                        $gender = htmlspecialchars($_POST['gender']);
                     else
                        $gender = '';

                    if(isset($_POST['textarea']))
                        $textarea = htmlspecialchars($_POST['textarea']);
                     else
                        $textarea = '';

                    if(isset($_POST['quotes']))
                        $quotes = htmlspecialchars($_POST['quotes']);
                     else
                        $quotes = '';
                    
                    $status = $usr_dat->addProfileDataIntoDb($country, $mobile, $gender, $textarea, $quotes);
                    
                    if($status == true){
                        if(isset($_POST['rd']))
                            $usr_dat->redir = "./login.php?rd=".htmlspecialchars($_POST['rd']);
                        else
                            $usr_dat->redir = "./login.php";
                    
                        $usr_dat->redirect($usr_dat->redir);
                    }
                }
                catch(Exception $e){
                    $exceptional_error = "An internal error occured. Please Try again or contact administrator.";
                }
            }
            else
            {
                $exceptional_error = "Please accept Terms and Conditions.";   
            }
        
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Create Moobuddy Account</title>
        <script src="<?php echo $script;?>jquery-2.2.3.min.js"></script>
        <script src="<?php echo $script;?>jquery.easing.1.3.min.js"></script>
        <script src="<?php echo $script;?>country.js"></script>
        
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

                <span class="step">2</span>

                <span class="box"></span>

                <span class="step  active-step">3</span>
            </div>
            <?php

                if ($status == false) {
            ?>

            <div class="register-box">
                
                <span class="profile-title">Set up your profile</span>

                <span class="form_error">
                    <?php if(isset($exceptional_error)) echo $exceptional_error;?>
                </span>
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 

                    <label for=country"">Country</label>
                    <select type="text" class="text text-field" name="country" id="country" onchange='change_country(this.value);'>
                        <option value="select" selected>Select Country</option>
                        <?php
                        foreach ($countries as $code => $name){
                        echo "<option value=\"$name\">$code</option>\n";
                        }
                        ?>
                    </select>

                    <label for="mobile">Mobile</label>
                    <input type="text" class="text text-field-xsml" name="mobile-code" id="mobile-code" placeholder="+00" readonly/>
                    <input type="text" class="text text-field-ml" name="mobile-no" id="mobile-no" placeholder="XXXXXXXXXX" maxlength="12"  onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')"/>

                    <label for="Gender">Gender</label>
                    <select type="text" class="text text-field" name="gender" id="gender">
                        <option value="select" selected>
                            Select Gender
                        </option>
                        <option value="male">
                            male
                        </option>
                        <option value="female">
                            female
                        </option>
                        <option value="other">
                            other
                        </option>
                    </select>

                    <label for="about">About</label>
                    <textarea type="text" class="text text-field-textarea" name="textarea" id="about"></textarea>

                    <label for="about">Quotes</label>
                    <textarea type="text" class="text text-field-textarea" name="quotes" id="quotes"></textarea>

                    <div class="checkbox">
                         <input id="check" type="checkbox" name="check" value="check" required>
                        <label for="check">I agree to the <a href="#" style="color:#465C66;">Terms and Condition</a>.</label>
                    </div>
                     <?php
                        if(isset($_GET['rd'])){
                            $redir = "./login.php?rd=".htmlspecialchars($_GET['rd']);
                    ?>
                        <input type="hidden" value="<?php echo htmlspecialchars($_GET['rd']);?>" name="rd"/>
                    <?php
                        }
                    ?>
                    <div class="reg-about-bottom" style="position:relative;top:-20px;">
                        <a class="text-resend text-skip"  style="position:relative;left:100px;background:#ECEFF1;" href="<?php echo $redir;?>">
                            Skip
                        </a>
                        <button class="text-button" style="position:relative;left:130px;">
                            Next
                        </button>
                    </div>
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
                <a href="./set-profile.php">
                    <button class="text-button ">
                        Go on
                    </button>
                </a>
            </div>
        </div>
        <?php
            }
         include $includes.'footer.php';
        ?>
    </body>
</html>