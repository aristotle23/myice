<?php
date_default_timezone_set("Africa/Lagos");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "object/config.php";
require_once "object/Emergency.php";
require_once "object/Media.php";
require_once "object/Token.php";


if((!isset($_POST["latlng"]) && $_POST["type"] != 5) || !isset($_POST["type"]) || !isset($_POST["userid"]) || !isset($_POST['key']) ){
    http_response_code(416);
    echo json_encode(array("message" => 0, "description" => "Missing required parameter"));
    exit;
}
$key = $_POST['key'];
$token = new Token();
if(!$token->is_valid($key)){
    http_response_code(403);
    echo json_encode(array("message" => 0, "description" => "Invalid API key"));
    exit;
}

$coordinate = (!isset($_REQUEST["latlng"]))? array(null,null) : explode(",",$_REQUEST["latlng"]);
$lat = $coordinate[0];
$lng = $coordinate[1];
$type = $_POST['type'];
$userId = $_POST['userid'];
$emergency = new Emergency($type);
if(!$emergency->is_valid()){
    http_response_code(404);
    echo json_encode(array("message" => 0, "description" => "Invalid MyICE emergency type"));
    exit;
}
if(!isset($_REQUEST['emergencyid'])){
    $emergencyId = $emergency->setEmergency($userId);
}else{
    $emergencyId = $_REQUEST['emergencyid'];
}
if($lat != null && $lng != null){
    if(!isset($_POST['altspd'])){
        $emergency->removeEmergency($emergencyId);
        http_response_code(416);
        echo json_encode(array("message" => 0, "description" => "Missing required parameter"));
        exit;
    }
    $altspd = explode(",",$_REQUEST["altspd"]);
    $alt = $altspd[0];
    $spd = $altspd[1];
    $emergency->setLocation($emergencyId,$lat,$lng,$alt,$spd);
}

if(isset($_REQUEST['narration'])){
    $emergency->setOptionalField($emergencyId,"narration",$_REQUEST['narration']);
}
if (isset($_FILES['media'])){
    $media = new Media($_FILES['media'],$userId);
    $mediaTarget = $media->upload();
    if(!$mediaTarget){
        $emergency->removeEmergency($emergencyId);
        http_response_code(500);
        echo json_encode(array("message" => 0, "description" => $media->err_message));
        exit;
    }
    $emergency->setOptionalField($emergencyId,"media",$mediaTarget);
}
if (isset($_REQUEST['location'])){
    $emergency->setOptionalField($emergencyId,"location",$_REQUEST['location']);
}
if(isset($_REQUEST['incident'])){
    $chck = $emergency->valid_incident($_REQUEST['incident']);
    if(!$chck){
        $emergency->removeEmergency($emergencyId);
        http_response_code(400);
        echo json_encode(array("message" => 0, "description" => "Unknown Incident"));
        exit;
    }
    $emergency->setOptionalField($emergencyId,"incident_type_id",$_REQUEST['incident']);
}
http_response_code(200);
echo json_encode(array("message" => 1,"emergencyId" => $emergencyId));
exit;