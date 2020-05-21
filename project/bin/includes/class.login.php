<?php
    
      error_reporting(1);

    /*////////////////////////////////////////////////////////
    
    REMEMBER TO INCLUDE:
    
    class.helper.php 
    PHPMailerAutoload.php -> for email
    Browser.php -> for browser detection
    config.php -> for config vars
    
    ///////////////////////////////////////////////////////*/
        
    class Login extends Register{
        
        /*////////////////////////////////////////
        Check password from userrootdata for login
        ////////////////////////////////////////*/
        
        function check_pass($allow_cookie, $expire=86400, $user='mb'){
            $password = $this->getDataFromUserroot('password',$this->email);
            
            if(isset($this->redir)){
                if($this->redir=="beatsniff_home"){
                    $user = 'bs';
                }
            }
            
            if($this->destroyPassword($password)){
                $this->startLoginSession($allow_cookie,$expire,$user);
                return true;
            }
            else{
                return false;
            }
        }
        
        /*/////////////////
        Start login Session
        /////////////////*/
    
        function startLoginSession($set_cookie, $expire, $user){
            if($this->addSessionIntoDb($expire,$user)){

                $cookie = $this->makeHashedStr($this->cookie);
                $_SESSION['for'] = $this->email;
                $_SESSION['what'] = $cookie;

                if($set_cookie == 'check'){
                    setcookie('for',$this->email,time()+$expire);
                    setcookie('what',$cookie,time()+$expire);
                }
                if(empty($this->redir))
                    $this->redirect('./dashboard.php');
                else{
                    $this->redirect($this->redir);
                }
            }
            else
                return false;
        }
         
        /*////////////////////
        Add session vars in db
        ////////////////////*/
       
        protected function addSessionIntoDb($expire,$user){
            $ip = $this->getIp();
            $this->cookie = $this->random(100);
         
            $time_now = date('Y/m/d h:i:s a');
            $time_now = strtotime($time_now);
            $this->cookie_expire = $time_now+$expire;
            $this->cookie_expire = date('Y/m/d h:i:s a',$this->cookie_expire);
                        
            $user_agent = $this->getUserAgent();
       
            $data = $this->getDataFromUserroot('all', $this->email);
            
            $conn = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER,
            $this->DB_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $conn->prepare("SELECT * FROM usersessiondata WHERE email = :email"); 
            
            $rm_stmt = $conn->prepare("DELETE FROM usersessiondata WHERE email = :email"); 
            
            $select_stmt = $conn->prepare("SELECT * FROM userrootdata WHERE email = :email"); 

            $set_stmt = $conn->prepare("UPDATE usersessiondata SET  expire=:expire, cookie=:cookie WHERE email=:email");
    
            $new_stmt = $conn->prepare("INSERT INTO usersessiondata VALUES (:user_id,:email,:expire, :ip, :useragent, :cookie, :user)");
    
            if($this->checkUserExist($this->email, 'usersessiondata')){
                
                $stmt->bindParam(':email', $this->email);
                $stmt->execute();
                $data = $stmt->fetch();

                $db_timestamp = strtotime($data['expire']);

                if($db_timestamp>$time_now){
                    //extend session 
                    $set_stmt->bindParam(':email', $this->email);
                    $set_stmt->bindParam(':expire', $this->cookie_expire);
                    $set_stmt->bindParam(':cookie', $this->cookie);
                    $set_stmt->execute();
                    return true;
                }
                else{
                    $rm_stmt->bindParam(':email', $this->email);
                    $rm_stmt->execute();
                    
                    $select_stmt->bindParam(':email', $this->email);
                    $select_stmt->execute();
                    $result = $select_stmt->fetch();
                
                    $new_stmt->bindParam(':user_id', $result['user_id']);
                    $new_stmt->bindParam(':email', $this->email);
                    $new_stmt->bindParam(':expire', $this->cookie_expire);
                    $new_stmt->bindParam(':ip', $this->ip);
                    $new_stmt->bindParam(':useragent', $user_agent);
                    $new_stmt->bindParam(':cookie', $this->cookie);
                    $new_stmt->bindParam(':user', $user);

                    if($new_stmt->execute())
                        return true;
                    else
                        return false;
                }
            }
            else{
                    $select_stmt->bindParam(':email', $this->email);
                    $select_stmt->execute();
                    $result = $select_stmt->fetch();
                
                    $new_stmt->bindParam(':user_id', $result['user_id']);
                    $new_stmt->bindParam(':email', $this->email);
                    $new_stmt->bindParam(':expire', $this->cookie_expire);
                    $new_stmt->bindParam(':ip', $this->ip);
                    $new_stmt->bindParam(':useragent', $user_agent);
                    $new_stmt->bindParam(':cookie', $this->cookie);
                    $new_stmt->bindParam(':user', $user);

                    if($new_stmt->execute())
                        return true;
                    else
                        return false;
            }
        }
        
        public function checkCookies(){
            $cookie = $_COOKIE['what'];
            $email = $_COOKIE['for'];
            $data = $this->getDataFromUserSessionData('all',$email);
             
            if($data){
                if($this->destroyHashedStr($data['cookie'],$cookie)){
                    if(!empty($this->redir))
                        $this->redirect($this->redir);
                    
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
    }
?>