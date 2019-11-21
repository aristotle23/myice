<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "object/DbHandler.php";
require_once "object/Token.php";

$token = new Token();
$apiKey = $token->generateApiKey();
if(!$apiKey){
    http_response_code(400);
    echo json_encode(array("message" => "fail"));
}

http_response_code(200);
echo json_encode(array("key" => $apiKey));