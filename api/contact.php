<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "object/DbHandler.php";
require_once "object/User.php";

if(!isset($_POST["userid"]) || !isset($_POST['key']) || !isset($_POST["action"]) ){
    http_response_code(416);
    echo json_encode(array("description" => "Missing required parameter","message" => 0));
    exit;
}
$userId = $_POST['userid'];
$user = new User($userId);
if(!$user->is_valid($userId)){
    http_response_code(406);
    echo json_encode(array("description" => "Invalid user ID","message" => 0));
    exit;
}
if($_POST['action'] == "set"){
    if(!isset($_POST['phone']) || !isset($_POST['name'])){
        http_response_code(416);
        echo json_encode(array("description" => "Missing required parameter","message" => 0));
        exit;
    }
    $address = (!isset($_POST['address'])) ? null : $_POST['address'];

    $contact = $user->setContact($_REQUEST['name'],$_POST['phone'],$address);
    if(!$contact){
        http_response_code(400);
        echo json_encode(array("description" => "Unable to add private emergency number","message" => 0));
        exit;
    }
    http_response_code(206);
    echo json_encode(array("message" => 1,"description" => "success"));
    exit;
}elseif ($_POST['action'] == "get"){
    $contact = $user->getContact();
    if(!$contact){
        http_response_code(400);
        echo json_encode(array("description" => "No private emergency number for this user","message" => 0));
        exit;
    }
    http_response_code(206);
    echo json_encode(array("message" => 1,"body" => $contact));
    exit;
}else{
    http_response_code(406);
    echo json_encode(array("description" => "Invalid request","message" => 0));
    exit;
}