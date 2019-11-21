<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "vendor/ktamas77/firebase-php/src/firebaseLib.php";
require_once "object/DbHandler.php";
require_once "object/Emergency.php";
require_once "object/Media.php";
require_once "object/Token.php";

/**
 * firebase default settings
 */

$db = new DbHandler();
$firebase = new Firebase\FirebaseLib(FIREBASEURL, FIREBASETOKEN);

if(!isset($_POST["emergencyid"]) || !isset($_POST['key']) ){
    http_response_code(416);
    echo json_encode(array("message" => $_POST, "description" => "Missing required parameter"));
    exit;
}
$key = $_POST['key'];
$token = new Token();
if(!$token->is_valid($key)){
    http_response_code(403);
    echo json_encode(array("message" => 0, "description" => "Invalid API key"));
    exit;
}
$emergencyId = $_REQUEST['emergencyid'];
$locationData = $firebase->get(FIREBASEPATH."/".$emergencyId);
$locationData = json_decode($locationData,true);
$locationData = array_values($locationData);
foreach ($locationData as $data){
    $date = $data['date'];
    $altitude = $data['altitude'];
    $lat = $data['lat'];
    $lng = $data['lng'];
    $speed = $data['speed'];
    $db->execute("insert into location (emergency_id, lat, lng, date, altitude, speed) VALUES (?,?,?,?,?,?)",
        array($emergencyId,$lat,$lng,$date,$altitude,$speed));
}
$firebase->delete(FIREBASEPATH."/".$emergencyId);
http_response_code(200);
echo json_encode(array("message" => 1,"emergencyId" => $emergencyId));
exit;