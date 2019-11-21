<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once "object/DbHandler.php";
require_once "object/User.php";
require_once "object/Token.php";

if(!isset($_POST['key']) || !isset($_POST['appid']) || !isset($_POST['email']) || !isset($_POST['password']) ){
    http_response_code(416);
    echo json_encode(array("description" => "Missing required parameter","message" => 0));
    exit;
}

$key = $_POST['key'];
$token = new Token();
if(!$token->is_valid($key)){
    http_response_code(403);
    echo json_encode(array("description" => "Invalid API key","message" => 0));
    exit;
}
$email = $_POST['email'];
$password = $_POST['password'];
$appid = $_POST['appid'];
$user = new User();
$userId = $user->login($email,$password,$appid);
if(!$userId){
    http_response_code(400);
    echo  json_encode(array("description" => $user->err_message, "message" => 0));
    exit;
}
http_response_code(200);
echo json_encode(array("message" => 1,"userId" => $userId));
exit;