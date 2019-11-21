<?php


class Helper
{
    public static function encrypt($rawdata){
        return openssl_encrypt($rawdata,OPENSSLMETHOD,OPENSSLKEY,OPENSSLOPTION,OPENSSLIV);
    }
    public static function decrypt($encrydata,$iv){
        return openssl_decrypt($encrydata,OPENSSLMETHOD,OPENSSLKEY,OPENSSLOPTION,base64_encode($iv));
    }
    public static function getOriginDest($emergencyLoc){
        $originLoc = $emergencyLoc[0];
        $originLoc = $originLoc['lat'] .",".$originLoc['lng'];

        $destLoc = $emergencyLoc[1];
        $destLoc = $destLoc['lat']. ",".$destLoc['lng'];
        return array($originLoc,$destLoc);
    }
    public static function getDefaultEmergencyURL($type){
        global $db;
        global $firebase;
        $emergency = $db->getOne("select id from emergency where type_id = ? order by date desc ",array($type));
        if($emergency) {
            $emergencyId = $emergency['id'];
            $emergencyLoc = $firebase->get(FIREBASEPATH."/".$emergencyId,array("orderBy" => '"date"',"limitToFirst"=>1));
            $arr = json_decode($emergencyLoc,true);
            if(count($arr) > 0) {
                $emergencyLoc = $arr[key($arr)];
            }else {
                $emergencyLoc = $db->getOne("SELECT lat,lng, date, altitude, speed FROM location where emergency_id = ? order by date", array($emergencyId));
            }
            $originDate = $emergencyLoc['date'];
            $originLoc = $emergencyLoc['lat'] . "," . $emergencyLoc['lng'];
            return "?eid=" . $emergencyId . "&type=" . $type . "&latlng=" . $originLoc . "&date=" . $originDate;
        }
        return null;
    }
    public static function logOut(){
        if(isset($_REQUEST['logout']) ){
            session_destroy();
            session_unset();
            return true;
        }
        if(basename($_SERVER['SCRIPT_NAME']) != 'login.php' && basename($_SERVER['SCRIPT_NAME']) != 'newpwd.php') {
            if (!isset($_SESSION["name"]) || !isset($_SESSION['admin_id']) || !isset($_SESSION['right']) || !isset($_SESSION['email'])) {
                return true;
            }
        }
        return false;
    }
    public static function timeAgo($time_ago)
    {
        $time_ago = strtotime($time_ago);
        $cur_time   = time();
        $time_elapsed   = $cur_time - $time_ago;
        $seconds    = $time_elapsed ;
        $minutes    = round($time_elapsed / 60 );
        $hours      = round($time_elapsed / 3600);
        $days       = round($time_elapsed / 86400 );
        $weeks      = round($time_elapsed / 604800);
        $months     = round($time_elapsed / 2600640 );
        $years      = round($time_elapsed / 31207680 );
        // Seconds
        if($seconds <= 60){
            return "just now";
        }
        //Minutes
        else if($minutes <=60){
            if($minutes==1){
                return "one minute ago";
            }
            else{
                return "$minutes minutes ago";
            }
        }
        //Hours
        else if($hours <=24){
            if($hours==1){
                return "an hour ago";
            }else{
                return "$hours hrs ago";
            }
        }
        //Days
        else if($days <= 7){
            if($days==1){
                return "yesterday";
            }else{
                return "$days days ago";
            }
        }
        //Weeks
        else if($weeks <= 4.3){
            if($weeks==1){
                return "a week ago";
            }else{
                return "$weeks weeks ago";
            }
        }
        //Months
        else if($months <=12){
            if($months==1){
                return "a month ago";
            }else{
                return "$months months ago";
            }
        }
        //Years
        else{
            if($years==1){
                return "one year ago";
            }else{
                return "$years years ago";
            }
        }
    }
}