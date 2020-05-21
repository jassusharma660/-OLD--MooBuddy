<?php

    error_reporting(0);

    /*////////////////////////////////////////////////////////

    REMEMBER TO INCLUDE:

    PHPMailerAutoload.php
    Browser.php
    config.php

    ///////////////////////////////////////////////////////*/

    class Register{

        var $DB_HOST = 'localhost';
        var $DB_NAME =	'UserWebData';
        var $DB_USER =	'__ENTER_YOUR_DB_USERNAME__';
        var $DB_PASS = 	'__ENTER_YOUR_DB_PASSWORD__';

        var $ip = '';
        var $fname = '';
        var $lname = '';
        var $email = '';
        var $day = '';
        var $month = '';
        var $year = '';
        var $pass = '';
        var $err_count = 0;
        var $extra = '';
        var $timestamp = '';
        var $email_exist = false;
        var $redir = '';


        var $cookie = '';
        var $cookie_expire = '';

        /*///////////////////
            set timezone
        //////////////////*/

        function __construct(){
            date_default_timezone_set('Asia/Kolkata');
                $this->removeOldconfirmationdata();
        }

        /*////////////////
        Redirect to a page
        ////////////////*/

        function redirect($link){
            $link = filter_var($link,FILTER_SANITIZE_STRING);
            if($link == "beatsniff_home")
                $link = "http://beatsniff.in/redirect/".$_SESSION['for']."/".$_SESSION['what'];
            header('location:'.$link.'');
        }

        /*//////////////////////////////////////////////////////
        Remove old confirmation requests if exist in db by email
        //////////////////////////////////////////////////////*/

        protected function removeOldconfirmationdata(){
            $time_now = date('Y/m/d h:i:s a');
            $time_now = strtotime($time_now);

            $conn = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT * FROM confirmationdata");
            $stmt->execute();
            $result = $stmt->fetchAll();

            $stmt2 = $conn->prepare("DELETE FROM confirmationdata WHERE email = :email");

            foreach($result as $data) {
                $timestamp = $time_now-strtotime($data['reg_date']);
                if($timestamp>3600){
                    $stmt2->bindParam(':email', $data['email']);
                    $stmt2->execute();
                }
            }
        }

        /*///////////////////////////////////////////////////////////
        Remove confirmation requests date after registration by email
        ///////////////////////////////////////////////////////////*/

        protected function removeRowByEmail($email,$table){

            $conn = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("DELETE FROM $table WHERE email = :email");
            $stmt->bindParam(':email', $email);

            if($stmt->execute()){
                return true;
            }
            else
                return false;
        }

        /*/////////////////////////////////
        check if user exist in db by email
        /////////////////////////////////*/

        protected function checkUserExist($id,$table){
            $conn = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("SELECT email FROM $table WHERE email=:email");
            $stmt->bindParam(':email', $id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount()==0){
                return false;
            }
            else{
                return true;
            }
        }

        /*///////////////////////
        Add reg step1 vars in db
        ///////////////////////*/

        protected function addVerificationIntoDb($arg = 'default'){
            GLOBAL $bin_url;

            $ip = $this->getIp();
            $user_id = $this->fname.'_'.$this->lname.$this->random(5);
            $string = $this->random();

            $otp = $this->random(4,"123456789");

            $time_now = date('Y/m/d h:i:s a');

            $link = $bin_url.'confirm-email.php?email='.$this->email.'&auth='.$string;

            if(isset($this->redir))
                $link .='&rd='.$this->redir;

             switch($arg){
                case 'recover':

                                $data = $this->getDataFromUserroot('all',$this->email);

                                $user_id = $data['user_id'];
                                $this->fname = $data['fname'];
                                $this->lname = $data['lname'];
                                $this->day = $data['day'];
                                $this->month = $data['month'];
                                $this->year = $data['year'];

                                $link = $bin_url.'confirm-email.php?email='.$this->email.'&auth='.$string.'&action=recover';
                                break;
            }

        try{
              if($this->sendEmail($this->getName(),$this->email,$otp,$link)){

                  $conn = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);
                  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                  $stmt = $conn->prepare("INSERT INTO confirmationdata VALUES (:user_id,:fname,:lname, :email, :day, :month, :year, :now, :ip, :string, :otp,0)");
                  $stmt->bindParam(':user_id', $user_id);
                  $stmt->bindParam(':fname', $this->fname);
                  $stmt->bindParam(':lname', $this->lname);
                  $stmt->bindParam(':email', $this->email);
                  $stmt->bindParam(':day', $this->day);
                  $stmt->bindParam(':month', $this->month);
                  $stmt->bindParam(':year', $this->year);
                  $stmt->bindParam(':now', $time_now);
                  $stmt->bindParam(':ip', $this->ip);
                  $stmt->bindParam(':string', $string);
                  $stmt->bindParam(':otp', $otp);

                  if($stmt->execute()){
                      return true;
                  }
                  else{
                      return false;
                  }
              }
              else{
                  return false;
              }
          }
          catch(PDOException $e)
          {
              return false;
          }

        }
        /*///////////////////////
        Modify reg step1 vars in db
        ///////////////////////*/

        protected function modifyVerificationInDb($arg = 'default'){
            GLOBAL $bin_url;

            $ip = $this->getIp();
            $string = $this->random();

            $otp = $this->random(4,"123456789");

            $time_now = date('Y/m/d h:i:s a');
            $link = $bin_url.'confirm-email.php?email='.$this->email.'&auth='.$string.'&rd='.$this->redir;

            switch($arg){
                case 'default':
                                $data = $this->getDataFromConfirmationdata('all',$this->email);

                                $this->fname = $data['fname'];
                                $this->lname = $data['lname'];

                                break;
                case 'recover':

                                $data = $this->getDataFromUserroot('all',$this->email);

                                $user_id = $data['user_id'];
                                $this->fname = $data['fname'];
                                $this->lname = $data['lname'];

                                $link = $bin_url.'confirm-email.php?email='.$this->email.'&auth='.$string.'&action=recover';
                                break;
            }

          try{
              if($this->sendEmail($this->getName(),$this->email,$otp,$link)){

                  $conn = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);
                  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                  $stmt = $conn->prepare("UPDATE confirmationdata SET reg_date=:now,ip=:ip,string=:string,otp=:otp,attempts=0 WHERE email=:email");
                  $stmt->bindParam(':email', $this->email);
                  $stmt->bindParam(':now', $time_now);
                  $stmt->bindParam(':ip', $this->ip);
                  $stmt->bindParam(':string', $string);
                  $stmt->bindParam(':otp', $otp);

                  if($stmt->execute()){
                      return true;
                  }
                  else{
                      return false;
                  }
              }
              else{
                  return false;
              }
          }
          catch(PDOException $e)
          {
              return false;
          }
        }


        /*/////////////////////////////
        Get data from confirmationdata
        /////////////////////////////*/
        protected function getDataFromConfirmationdata($arg,$email){

            try{
                $conn = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);

                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $conn->prepare("SELECT * FROM confirmationdata WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $result = $stmt->fetch();

                switch($arg)
                {
                    case 'user_id' : return $result['user_id'];break;
                    case 'fname' : return $result['fname'];break;
                    case 'lname' : return $result['lname'];break;
                    case 'email' : return $result['email'];break;
                    case 'day' : return $result['day'];break;
                    case 'month' : return $result['month'];break;
                    case 'year' : return $result['year'];break;
                    case 'reg_date' : return $result['reg_date'];break;
                    case 'ip' : return $result['ip'];break;
                    case 'string' : return $result['string'];break;
                    case 'otp' : return $result['otp'];break;
                    case 'attempts' : return $result['attempts'];break;
                    case 'all' : return $result;break;
                    default: return false;
                }
            }
            catch(PDOException $e)
            {
                return false;
            }
        }
        /*/////////////////////////////
        Get data from userrootdata
        /////////////////////////////*/
        protected function getDataFromUserroot($arg,$email){

            try{
                $conn = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);

                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $conn->prepare("SELECT * FROM userrootdata WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $result = $stmt->fetch();

                switch($arg)
                {
                    case 'user_id' : return $result['user_id'];break;
                    case 'fname' : return $result['fname'];break;
                    case 'lname' : return $result['lname'];break;
                    case 'email' : return $result['email'];break;
                    case 'day' : return $result['day'];break;
                    case 'month' : return $result['month'];break;
                    case 'year' : return $result['year'];break;
                    case 'reg_date' : return $result['reg_date'];break;
                    case 'ip' : return $result['ip'];break;
                    case 'string' : return $result['string'];break;
                    case 'otp' : return $result['otp'];break;
                    case 'blockpriority' : return $result['blockpriority'];break;
                    case 'useragent' : return $result['useragent'];break;
                    case 'password' : return $result['password'];break;
                    case 'all' : return $result;break;
                    default: return false;
                }
            }
            catch(PDOException $e)
            {
                return false;
            }
        }
        /*/////////////////////////////
        Get data from usersessiondata
        /////////////////////////////*/
        protected function getDataFromUserSessionData($arg,$email){

            try{
                $conn = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);

                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $conn->prepare("SELECT * FROM usersessiondata WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $result = $stmt->fetch();

                switch($arg)
                {
                    case 'user_id' : return $result['user_id'];break;
                    case 'email' : return $result['email'];break;
                    case 'expire' : return $result['expire'];break;
                    case 'ip' : return $result['ip'];break;
                    case 'useragent' : return $result['useragent'];break;
                    case 'cookie' : return $result['cookie'];break;
                    case 'user' : return $result['user'];break;
                    case 'all' : return $result;break;
                    default: return false;
                }
            }
            catch(PDOException $e)
            {
                return false;
            }
        }

        /*/////////////////
        Validate first name
        /////////////////*/

        function fname($arg){
            if($arg!=''){
                $arg = filter_var($arg,FILTER_SANITIZE_STRING);
                if($this->checkLength($arg,2,50)==true){
                    if(preg_match("/^[a-zA-Z]+$/",$arg)===1){
                        $this->fname = $arg;
                    }
                    else{
                        $this->err_count++;
                        return "Fist name is not valid. Use only a-z/A-Z.";
                    }
                }
                else{
                    $this->err_count++;
                    return "First name should be between 2-50 characters.";
                }
            }
            else{
                $this->err_count++;
                return "Please enter first name.";
            }
        }

        /*/////////////////
        Validate last name
        /////////////////*/

        function lname($arg){
            if(!$arg==0){
                $arg = filter_var($arg,FILTER_SANITIZE_STRING);

                if($this->checkLength($arg,2,50)==true){
                    if(preg_match("/^[a-zA-Z]+$/",$arg)===1)
                        $this->lname = $arg;
                    else{
                        $this->err_count++;
                        return "Last name is not valid. Use only a-z/A-Z.";
                    }
                }
                else{
                    $this->err_count++;
                    return "Last name should be between 2-50 characters.";
                }
            }
        }

        /*////////////
        Validate date
        ////////////*/

        function day($arg_date,$arg_month,$arg_year){

            if($arg_date!='' && $arg_month!='' && $arg_year!=''){
                if(checkdate($arg_month,$arg_date,$arg_year)){
                    $this->day = $arg_date;
                    $this->month = $arg_month;
                    $this->year = $arg_year;
                }
                else{
                    $this->err_count++;
                    return "Date is invalid.";
                }
            }
            else{
                $this->err_count++;
                return "Please select date, month, year.";
            }
        }

        /*///////////////////////////////////////////
        Get full name - fname/lname should be pre-set
        ///////////////////////////////////////////*/

        function getName(){
            return $this->fname.' '.$this->lname;
        }

        /*////////////////////
        Validate email address
        ////////////////////*/

        function email($arg){
            if($arg!=''){
                $arg = filter_var($arg,FILTER_SANITIZE_EMAIL);

                if($this->checkLength($arg,5,100)==true){
                    if(filter_var($arg,FILTER_VALIDATE_EMAIL)){

                        if($this->checkUserExist($arg,"userrootdata")){
                            $this->err_count++;
                            $this->email_exist = true;
                            return "Email already in use.";
                        }
                        else
                        {
                             if($this->checkUserExist($arg,"confirmationdata")){
                                 return 'status';
                             }
                            else{
                                $this->email = $arg;
                            }
                        }
                    }
                    else{
                        $this->err_count++;
                        return "Please check the email again.";
                    }
                }
                else{
                    $this->err_count++;
                    return "Email should be between 5-100 characters.";
                }
            }
            else{
                $this->err_count++;
                return "Please enter email.";
            }
        }
        /*////////////////////////////////
        Validate only email address syntax
        ////////////////////////////////*/

        function onlyEmailSyntax($arg){
            if($arg!=''){
                $arg = filter_var($arg,FILTER_SANITIZE_EMAIL);

                if($this->checkLength($arg,5,100)==true){
                    if(filter_var($arg,FILTER_VALIDATE_EMAIL)){
                        $this->email = $arg;
                    }
                    else{
                        $this->err_count++;
                        return "Please check the email again.";
                    }
                }
                else{
                    $this->err_count++;
                    return "Email should be between 5-100 characters.";
                }
            }
            else{
                $this->err_count++;
                return "Please enter email.";
            }
        }

        /*///////////////////////////////////
        Validate password and repeat password
        ///////////////////////////////////*/

        function password($pass,$rpass){
            if($pass!='' && $rpass !=''){

                if($this->checkLength($pass,6,20)==true && $this->checkLength($rpass,6,20)==true){
                        if($pass==$rpass){
                            $this->pass = $pass;
                            return true;
                        }
                        else{
                                $this->err_count++;
                                return "Password and Repeat password are not same.";
                        }
                }
                else{
                    $this->err_count++;
                    return "Password should be between 6-20 characters.";
                }
            }
            else{
                $this->err_count++;
                return "Please enter a password.";
            }
        }

        /*////////////////////
        Validate password only
        ////////////////////*/

        function onlyPasswordSyntax($pass){
            if($pass!=''){
                if($this->checkLength($pass,6,20)==true){
                    $this->pass = $pass;
                }
                else{
                    $this->err_count++;
                    return "Password should be between 6-20 characters.";
                }
            }
            else{
                $this->err_count++;
                return "Please enter a password.";
            }
        }

        /*///////////////////
        Encode password to DB
        ///////////////////*/

        protected function makePassword(){
            $hash = password_hash($this->pass, PASSWORD_DEFAULT);
            return $hash;
        }

        /*///////////////////////
        Decode Password from DB
        ///////////////////////*/

        protected function destroyPassword($hash){

            if(password_verify($this->pass, $hash)) {
                return true;
            } else {
                return false;
            }

        }
        /*/////////////////////
        Encode string and return
        /////////////////////*/

        protected function makeHashedStr($str){
            $hash = password_hash($str, PASSWORD_DEFAULT);
            return $hash;
        }

        /*///////////////////////
        Decode string and return
        ///////////////////////*/

        protected function destroyHashedStr($str,$hashed){
            if(password_verify($str, $hashed)) {
                return true;
            } else {
                return false;
            }

        }

        /*////////////////////
        Validate email address
        ////////////////////*/

        function validateEmailForRecovery($arg){
            if($arg!=''){
                $arg = filter_var($arg,FILTER_SANITIZE_EMAIL);

                if($this->checkLength($arg,5,100)==true){
                    if(filter_var($arg,FILTER_VALIDATE_EMAIL)){

                        if($this->checkUserExist($arg,"userrootdata")){
                            $this->email = $arg;

                            if($this->checkUserExist($arg,"confirmationdata")){
                                $this->removeRowByEmail($this->email,'confirmationdata');
                            }

                            return 'exist';
                        }
                        else
                        {
                             if($this->checkUserExist($arg,"confirmationdata")){
                                 return 'status';
                             }
                            else{
                                return "Email does not exist.";
                            }
                        }
                    }
                    else{
                        $this->err_count++;
                        return "Please check the email again.";
                    }
                }
                else{
                    $this->err_count++;
                    return "Email should be between 5-100 characters.";
                }
            }
            else{
                $this->err_count++;
                return "Please enter email.";
            }
        }
        /*///////////////////////
        Add user data vars in db
        ///////////////////////*/

        function addUserDataIntoDb(){

            $data = $this->getDataFromConfirmationdata('all',$this->email);
            $password = $this->makePassword();
            if(isset($password)){

                try{
                      $conn = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);
                      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                      $stmt = $conn->prepare("INSERT INTO userrootdata(user_id, fname, lname, email, day, month, year, reg_date, ip, useragent, password) VALUES (:user_id,:fname,:lname, :email, :day, :month, :year, :reg_date, :ip, :useragent, :password)");
                      $stmt->bindParam(':user_id', $data['user_id']);
                      $stmt->bindParam(':fname', $data['fname']);
                      $stmt->bindParam(':lname', $data['lname']);
                      $stmt->bindParam(':email', $data['email']);
                      $stmt->bindParam(':day', $data['day']);
                      $stmt->bindParam(':month', $data['month']);
                      $stmt->bindParam(':year', $data['year']);
                      $stmt->bindParam(':reg_date', $data['reg_date']);
                      $stmt->bindParam(':ip', $data['ip']);
                      $stmt->bindParam(':useragent', $this->getUserAgent());
                      $stmt->bindParam(':password', $password);

                      if($stmt->execute()){
                          $this->removeRowByEmail($this->email,'confirmationdata');
                          return true;
                      }
                      else{
                          return false;
                      }
                }
                catch(PDOException $e)
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }

        /*///////////////////////
        Modify user data vars in db
        ///////////////////////*/

        function modifyPasswordInDb($pass, $user_id){

            try{
                  $conn = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);
                  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                  $stmt = $conn->prepare("UPDATE userrootdata SET password = :pass WHERE user_id = :user_id");
                  $stmt->bindParam(':user_id', $user_id);
                  $stmt->bindParam(':pass', $pass);

                  if($stmt->execute()){
                      $this->removeRowByEmail($this->email,'confirmationdata');

                      return true;
                  }
                  else{
                      return false;
                  }
            }
            catch(PDOException $e)
            {
                return false;
            }
        }

        /*/////////////////////////
        Add profile data vars in db
        /////////////////////////*/

        function addProfileDataIntoDb($country, $mobile, $gender, $textarea, $quotes){
                $data = $this->getDataFromUserroot('all',$this->email);

                if($this->checkUserExist($data['email'],"userrootdata")){
                    try{
                        $conn = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $stmt = $conn->prepare("INSERT INTO userprofiledata(user_id, email, country, mobile, gender, about, quotes, profile_pic, wall_pic) VALUES (:user_id,:email,:country, :mobile, :gender, :textarea, :quotes, :profile_pic, :wall_pic)");

                        $stmt->bindParam(':user_id', $data['user_id']);
                        $stmt->bindParam(':email', $data['email']);
                        $stmt->bindParam(':country', $country);
                        $stmt->bindParam(':mobile', $mobile);
                        $stmt->bindParam(':gender', $gender);
                        $stmt->bindParam(':textarea', $textarea);
                        $stmt->bindParam(':quotes', $quotes);
                        $stmt->bindParam(':profile_pic', $profile_pic);
                        $stmt->bindParam(':wall_pic', $wall_pic);

                        if($stmt->execute()){
                            return true;
                        }
                        else{
                            return false;
                        }
                    }
                    catch(PDOException $e)
                    {
                        return false;
                    }
                }
            else{
                return false;
            }
        }

        /*/////////////////
        Get user ip address
        /////////////////*/

        function getIp() {
            $ipaddress = '';
            if (isset($_SERVER['HTTP_CLIENT_IP']))
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            else if(isset($_SERVER['HTTP_X_FORWARDED']))
                $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
            else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
                $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
            else if(isset($_SERVER['HTTP_FORWARDED']))
                $ipaddress = $_SERVER['HTTP_FORWARDED'];
            else if(isset($_SERVER['REMOTE_ADDR']))
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            else{
                $this->err_count++;
                return false;
            }

            if(filter_var($ipaddress,FILTER_VALIDATE_IP))
                $this->ip = $ipaddress;
            else{
                $this->err_count++;
                return false;
            }
        }

        /*//////////////////////////////////////////////////
        Validate length of input data with min/max arguments
        /////////////////////////////////////////////////*/

        function checkLength($arg,$min,$max){
            if(strlen($arg)>$min && strlen($arg)<$max)
                return true;
            else
                return false;
        }
        protected function random($characters=25,$letters = '012345689AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz'){
            $str='';
            for ($i=0; $i<$characters; $i++) {
                $str .= substr($letters, mt_rand(0, strlen($letters)-1), 1);
            }
            if(strlen($str)==$characters)
                return $str;
            else
                $this->random($characters,$letters);
        }

        /*//////////////////////////////////////////////
        Send verification email by confirming validation
        //////////////////////////////////////////////*/

        function sendEmailVerification($arg = 'default'){
            if($this->checkError()){

                switch($arg){
                    case 'default':
                                if($this->addVerificationIntoDb()){
                                    return true;
                                }
                                else{
                                    return false;
                                }
                                break;

                    case 'recover':

                        if($this->addVerificationIntoDb('recover')){
                                    return true;
                                }
                                else{
                                    return false;
                                }
                                break;

                }
            }
            else{
                return false;
            }
        }
        /*//////////////////////////////////////////////
        ReSend verification email by confirming validation
        //////////////////////////////////////////////*/

        function resendEmailVerification($arg = 'default'){

            if($this->checkError()){

                switch($arg){
                    case 'default':
                                if($this->modifyVerificationInDb()){
                                    return true;
                                }
                                else{
                                    return false;
                                }
                                break;

                    case 'recover':

                                if($this->modifyVerificationInDb('recover')){
                                    return true;
                                }
                                else{
                                    return false;
                                }
                                break;

                }

            }
            else{
                return false;
            }
        }

        /*//////////////////////////////////////
        Send php email using phpmailer framework
        //////////////////////////////////////*/
        /*
        *********************************************
        *  THIS FUNCTION IS SET TO DEVELOPER MODE   *
        *  PLEASE RETURN FALSE TO CHANGE THE MODE   *
        *    AND REMOVE MULTI LINE COMMENT SIGNS    *
        *********************************************

        */
        protected function sendEmail($name,$email,$code,$link){
            /*
            GLOBAL $images_url;
            $mail = new PHPMailer;

            $mail->From = "no-reply@moobuddy.com";
            $mail->FromName = "MooBuddy Accounts";

            $mail->addAddress($email, $name);
            $mail->isHTML(true);

            $mail->Subject = "MooBuddy Account Registration";
            $mail->Body = "
            <style>body{font-family: Roboto, 'Helvetica Neue', Helvetica,  sans-serif;}</style>
            <div style=\"margin-left:auto;margin-right:auto;width:600px;\">
            <img  src=\"".$images_url."/logos/logo.png\" height=\"80px\" style=\"padding-bottom:20px;word-wrap: break-word;\"/>
            <div style=\"text-align:center;\">
            <div style=\"color:#fff;background:#2196F3;width:100%;padding:50px 0;position:relative;top:-20px;margin-right:auto;margin-left:auto;font-size:1.5em;box-shadow: 1px 1px 10px #9e9e9e;word-wrap: break-word;\"><span style=\"font-weight:bold;font-size:1.6em;\">".$name."</span>,<br/>You are on the right track.</div>

            <div style=\"color:#444;background:#263238;padding:50px 0;position:relative;top:-30px;font-size:15px;box-shadow: 1px 1px 10px #9e9e9e;\">
               <a href=\"".$link."\" style=\"text-decoration:none;color:#fff;\">
               <div style=\"padding:10px 10px;font-size:30px;border:0;color:#fff;background:#2196F3;cursor:pointer;width:200px;margin-right:auto;margin-left:auto;box-shadow: 1px 1px 10px #222;\">
               ".$code."
               </div>
               </a>
               <div style=\"font-weight:bold;padding-top:20px;color:#fff;font-size:0.8em;\">
                Here is your OTP. Use this or Click the OTP.
               </div>
            </div>
            </div><br/><br/>
            <div style=\"color:#263238;font-size:12px;\">If the above link is not working copy and paste this link in url bar <span style=\"color:#2196F3;\">".$link."</span></div>
            <div style=\"text-align:center;margin:20px;font-size:14px;font-weight:bold;color:#2196F3;\">Powered by<br/><img src=\"".$images_url."/logos/beatsniff.png\" height=\"35px\" style=\"margin:10px;\"/><img src=\"".$images_url."/logos/developers.png\" height=\"35px\" style=\"margin:10px;\"/></div>
            </div>
            ";
            $mail->AltBody = "Here is your OTP.";
            */
            if(1/*!$mail->send()*/)
            {
                return true;//return false;<-THIS SHOULD BE false
            }
            else
            {
                return true;
            }
        }

        /*/////////////////////////////
        Check for all validation errors
        /////////////////////////////*/

        function checkError(){
            if($this->err_count==0)
                return true;
            else
                return false;
        }
         /*///////////////
        Get user's agent
        ////////////////*/

        function getUserAgent(){

            $useragent = new Browser();
            $browser = $useragent->getBrowser()."/".$useragent->getVersion()."/".$useragent->getPlatform();

            return $browser;
        }

        /*/////////////////
        Verify user by otp
        /////////////////*/

        function checkOtpVerification(){
            $flag = 0;
            $attempts = $this->getDataFromConfirmationdata('attempts',$this->email);

            if($attempts > 2){
                return "OTP expired! Request a new otp.";
            }
            else{

                if($this->checkUserExist($this->email,"confirmationdata")){
                    $conn = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $stmt = $conn->prepare("SELECT * FROM confirmationdata");
                    $stmt->execute();
                    $result = $stmt->fetchAll();

                    foreach($result as $data) {
                        if($data['email']==$this->email){
                            if($data['otp']==$this->extra){
                                $this->startPasswordSetSession($this->email);
                                $flag = 0;
                                return true;
                            }
                            else{
                                $this->addFailedAttemptsInDb("confirmationdata");
                                return "Entered OTP is not correct.";
                            }
                        }
                        else{
                            $flag++;
                        }
                    }
                    if($flag>0)
                        return "Have you registered! <br/>Please start over or contact administrator.";
                }
                else{
                    return "Have you registered! <br/>Please start over or contact administrator.";
                }
            }
        }

        /*/////////////////////////
        Verify user by emailed link
        /////////////////////////*/

        function checkEmailVerification($email,$auth){
            $flag = 0;

            if($this->checkUserExist($this->email,"confirmationdata")){
                $conn = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $conn->prepare("SELECT * FROM confirmationdata");
                $stmt->execute();
                $result = $stmt->fetchAll();

                foreach($result as $data) {
                    if($data['email']==$this->email){
                        if($data['string']==$this->extra){
                            $this->startPasswordSetSession($this->email);
                            $flag=0;
                            return true;
                        }
                        else{
                            return false;
                        }
                    }
                    else{
                       $flag++;
                    }
                }
                if($flag>0)
                    return false;
            }
            else{
                return false;
            }
        }
        /*/////////////////////////
        Start Password Set Session
        /////////////////////////*/

        function startPasswordSetSession($email){
            $_SESSION['email'] = $email;
        }

        /*//////////////////////////
        Add failed attempt var in db
        //////////////////////////*/

        protected function addFailedAttemptsInDb($table){

          try{
                  $conn = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);
                  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                  $stmt = $conn->prepare("UPDATE ".$table." SET attempts=attempts+1 WHERE email=:email");
                  $stmt->bindParam(':email',$this->email);

                  if($stmt->execute()){
                      return true;
                  }
                  else{
                      return false;
                  }
          }
          catch(PDOException $e)
          {
              return false;
          }

        }

        /*//////////////////////////////////
        Sets class variable to init recovery
        //////////////////////////////////*/

        function setDataForRecovery(){
            getDataFromUserroot('all',$this->email);
        }

        /*//////////////////////////////
        Reset password by modifing in db
        //////////////////////////////*/

        function resetPassword(){
            $password = $this->makePassword();
            $user_id = $this->getDataFromUserroot('user_id',$this->email);

            if($this->modifyPasswordInDb($password, $user_id)){
                return true;
            }
            else{
                return false;
            }
        }

    }
?>
