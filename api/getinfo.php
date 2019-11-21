<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "object/DbHandler.php";
require_once "object/Emergency.php";
require_once "object/Token.php";
require_once "object/User.php";

if(!isset($_POST['key']) || !isset($_POST['source']) ){
    http_response_code(416);
    echo json_encode(array("message" => 0, "description" => "Missing required parameter"));
    exit;
}
$emergency = new Emergency();


$key = $_POST['key'];
$token = new Token();
if(!$token->is_valid($key)){
    http_response_code(403);
    echo json_encode(array("message" => 0, "description" => "Invalid API key"));
    exit;
}

if($_POST['source'] == "etype"){
    $types = $emergency->getAllType();
    http_response_code(200);
    echo json_encode(array("message" => 1, "types" => $types));
    exit;
}
if($_POST['source'] == "inctype"){
    $types = $emergency->getAllIncType();
    http_response_code(200);
    echo json_encode(array("message" => 1, "incidences" => $types));
    exit;
}
if($_POST['source'] == "user"){
    if(!isset($_POST['userid'])){
        http_response_code(416);
        echo json_encode(array("message" => 0,"description" => "Missing required parameter"));
        exit;
    }
    $user = new User($_POST['userid']);
    $userInfo = $user->getInfo();
    http_response_code(200);
    echo json_encode(array("message" => 1, "user" => $userInfo));
    exit;
}
if($_POST['source'] == "enumbers"){
    $numbers = $emergency->getPublicEmergencyNumbers();
    http_response_code(200);
    echo json_encode(array("message" => 1, "numbers" => $numbers));
    exit;
}
