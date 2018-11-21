<?php

if ($_SERVER["HTTP_REFERER"] == "GET"){
    sendMessage(array(["error" => false, "temp" => 1]));
}elseif ($_SERVER["HTTP_REFERER"] == "PUT"){
    $key = file_get_contents("keystore.txt");
    if($_GET["key"] == $key){
        //Data
        $base = file_get_contents("php://input");
        $wdata = json_decode($base);
        $temp = $wdata["temperature"];
        $huminity = $wdata["huminity"];
        $uvindex = $wdata["uvindex"];

        //Response
        $newkey = generateRandomString(200);
        file_put_contents("keystore.txt", $newkey);
        sendMessage(array(["error" => false, "key" => $newkey]));
    }else{
        sendError(1, "Invalid Key");
    }
}else{
    sendError(0, "Invalid Request");
}

function sendError($type, $message){
    sendMessage(array("error" => true, "type" => $type, "message" => $message));
}

function sendMessage($data){
    header("Content-Type: application/json");
    echo json_encode($data);
}

function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
