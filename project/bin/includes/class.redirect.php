<?php 
    
    error_reporting(0);

    /*////////////////////////////////////////////////////////
    
    REMEMBER TO INCLUDE:
    
    class.helper.php
    Browser.php -> for browser detection
    config.php -> for config vars
    
    ///////////////////////////////////////////////////////*/
        
    class Redirect extends Register{
        
        function __construct($sucessRedirTo, $failRedirTo, $sessionVars, $checkSessionFirst){
            if($checkSessionFirst){
                if($this->isSessionExist($what,$for)){
                    //header('location:'.$sucessRedirTo);
                }
                else{
                    //session_destroy();
                    //header('location:'.$mb.'bin/login.php?rd=beatsniff_home');
                }
            }
        }
        
        function redirectWithSession($redirTo,$sessionVars,$checkSessionFirst){
               
        }
        
        function isSessionExist($cookie,$email){
            $data = $this->getDataFromUserSessionData('all',$email);
             
            if($data){
                if($this->destroyHashedStr($data['cookie'],$cookie)){
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