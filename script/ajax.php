<?php
date_default_timezone_set("Africa/Lagos");
require_once "../vendor/ktamas77/firebase-php/src/firebaseLib.php";
require_once "config.php";
require_once "DbHandler.php";
require_once "Emergency.php";
require_once "Helper.php";
require_once "User.php";

$db = new DbHandler();
$firebase = new Firebase\FirebaseLib(FIREBASEURL, FIREBASETOKEN);
if(!isset($_REQUEST['state'])){
    http_response_code(416);
    echo json_encode(array("message" => "Missing required parameter"));
    exit;
}
if($_REQUEST['state'] == "indexcountdown"){
    $countdown = array();
    $date = date("Y-m-d");
    $date .= "%";
    $types = $db->getAll("select id,arg from type");
    foreach ($types as $type){

        $emergency = $db->getOne("select count(id) as count from emergency where type_id = ? and date like ?",array($type['id'],$date));
        array_push($countdown,array($type['arg'] => $emergency['count']));
    }
    echo json_encode($countdown);
}elseif ($_REQUEST['state'] == "screen"){

    $etype = $_REQUEST['etype'];
    $emergency = new Emergency($etype);
    $eid = $_REQUEST['eid'];
    $date = $_REQUEST['date'];
    if($_REQUEST['task'] == "info"){
        $data = array();

        $emergencyArr = $emergency->getEmergency($eid);
        $user = new User($emergencyArr['user_id']);
        $userArr = $user->getInfo();

        array_push($data, $emergencyArr['date'],$userArr['name'],$userArr['email'],$userArr['address'],$userArr['phone'],$userArr['sex']);

        array_push($data, $emergencyArr['narration'], $emergencyArr['location'],$emergencyArr['media'], $emergencyArr['incidence']);

        echo json_encode($data);
        exit;
    }elseif ($_REQUEST['task'] == "destLoc"){

        $emergencyLoc = $emergency->getLocation($eid,true,$date);

        /*$originLoc = $emergencyLoc[0];
        $originLoc = $originLoc['lat'] .",".$originLoc['lng'];*/

        $destLoc = $emergencyLoc[1];
        //$destLoc = $destLoc['lat']. ",".$destLoc['lng'];

        echo json_encode($destLoc);
        exit;

    }

}elseif ($_REQUEST['state'] == "notification"){
    $emergencyObj = new Emergency();
    $idx = (!isset($_REQUEST['notifyIdx']) || count($_REQUEST['notifyIdx']) < 1 ) ? array() : array_values($_REQUEST['notifyIdx']);
    $lastId = 0;
    $limit = $_REQUEST["notificationLimit"];
    $data = array();
    $recentData = array();
    $pastData = array();
    $existingData = array();
    $existingEId = array();
    $recentEmergencies = array();
    $pastEmergencies = array();
    $sql = "select id from emergency where notify = 0 ";
    $choiceSql = "select e.id, t.arg, e.user_id,e.date,e.type_id from emergency e inner join type t on e.type_id = t.id where notify = 0 ";
    /***
     * if $idx is greater than 0 it means there is notification in the notification drop down
     */
    if(count($idx) > 0) {
        foreach ($idx as $key => $id) {
            $lastId = ($lastId < $id) ? $id : $lastId;
            if ($key == 0) {
                $sql .= "and id in ( ?";
                $choiceSql .= "and e.id not in ( ?";
                continue;
            }
            $sql .= ",?";
            $choiceSql .= ",?";
        }
        $sql .= " ) order by date desc";
        $choiceSql .= " ) ";

        $existingEId = $db->getAll($sql, $idx);
        foreach ($existingEId as $eid) {
            array_push($existingData, (int)$eid['id']);
        }
    }
    /**
     * if existing notification data is less than limit (the total number of notification to be displayed to user)
     * get the recent notification, sum the recent notification with the existing notification. If the the summation
     * of both notification is less than the the limit get the past notification
     */
    array_push($idx,$lastId);
    $recentEmergencies = $db->getAll($choiceSql . " and e.id > ? order by date desc limit ".$limit,$idx);
    $recentEmergencies = array_reverse($recentEmergencies);
    $ttlRecentEmergencies = count($recentEmergencies);
    $ttlEmergencies = $ttlRecentEmergencies + count($existingData);
    if($ttlEmergencies > $limit){
        $slice = $ttlEmergencies - $limit;
        $slice = count($existingData) - $slice;
        $existingData = array_slice($existingData,0,$slice);
    }elseif($ttlEmergencies < $limit){
        $newLimit = $limit - $ttlEmergencies;
        $limit = ($newLimit < 1) ? $limit :  $newLimit;
        $pastEmergencies = $db->getAll($choiceSql ." and e.id < ? order by date desc limit ".$limit,$idx);
    }

    /**
     * the two foreach arrange the notification data of both recent and past to fit the arrangement needed for display
     */
    foreach($recentEmergencies as $emergency){
        $user = new User($emergency['user_id']);
        $fname = explode(" ",$user->getInfo("name"));
        $fname = ucfirst("".$fname[0]);
        $h4 = $emergency["arg"] ." - ". $fname;
        $timeAgo = Helper::timeAgo($emergency["date"]);
        $emergencyLoc = $emergencyObj->getLocation($emergency["id"]);
        $origin = $emergencyLoc['lat'] .",".$emergencyLoc['lng'];
        $link = "screen.php?eid=".$emergency['id']."&type=".$emergency['type_id']."&latlng=".$origin."&date=".$emergency["date"];
        array_push($recentData, array("h4"=>$h4,"time"=>$timeAgo,"eid"=>$emergency["id"],"href"=>$link));

    }
    foreach($pastEmergencies as $emergency){
        $user = new User($emergency['user_id']);
        $fname = explode(" ",$user->getInfo("name"));
        $fname = ucfirst("".$fname[0]);
        $h4 = $emergency["arg"] ." - ". $fname;
        $timeAgo = Helper::timeAgo($emergency["date"]);
        $emergencyLoc = $emergencyObj->getLocation($emergency["id"]);
        $origin = $emergencyLoc['lat'] .",".$emergencyLoc['lng'];
        $link = "screen.php?eid=".$emergency['id']."&type=".$emergency['type_id']."&latlng=".$origin."&date=".$emergency["date"];
        array_push($pastData, array("h4"=>$h4,"time"=>$timeAgo,"eid"=>$emergency["id"],"href"=>$link));

    }
    echo json_encode(array("existing" => $existingData,"recent"=> $recentData,"past" => $pastData));
    //echo json_encode(array($sql,$idx));
}elseif($_REQUEST['state'] == 'notifycountdown'){
    $countdown = 0;
    if(isset($_REQUEST['etype'])){
        $data = $db->getOne("select count(id) as count from emergency where notify = 0 and  type_id = ?",array($_REQUEST['etype']));
        $countdown = $data['count'];
    }else{
        $data = $db->getOne("select count(id) as count from emergency where  notify = 0");
        $countdown = $data['count'];
    }
    echo json_encode($countdown);
}elseif($_REQUEST['state'] == 'notifyclicked'){
    $eid = $_REQUEST['eid'];
    $db->execute("update emergency set notify = 1 where id = ?",array($eid));
}elseif ($_REQUEST['state'] == 'popupNotify'){
    $hist  = array();
    $sql = "select user_id,e.id,type_id,t.arg from emergency e inner join type t on e.type_id = t.id where notify = 0 ";
    if(isset($_REQUEST['hist'])) {
        $hist = $_REQUEST['hist'];
        foreach ($hist as $key => $eId) {
            if ($key == 0) {
                $sql .= "and e.id not in ( ?";
                continue;
            }
            $sql .= ",?";
        }
        $sql .= " )";
    }
    $sql .= " and notify_elapse >= ?  order by date desc";
    array_push($hist,date("Y-m-d H:i:s"));
    $res = $db->getAll($sql,$hist);

    echo json_encode($res);

}