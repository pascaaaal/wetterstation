<?php

if ($_SERVER['REQUEST_METHOD'] == "GET"){
    sendMessage(array("error" => false, "temp" => 1, "humidity" => 1, "uvindex" => 1, "air_pressure" => 1, "illumiance" => 1));
}elseif ($_SERVER['REQUEST_METHOD'] == "PUT"){
    $key = file_get_contents("keystore.txt");
    if(hash('sha512', $_GET["key"]) == $key){
        //Data
        $base = file_get_contents("php://input");
        $wdata = json_decode($base);
        $temp = $wdata["temperature"];
        $huminity = $wdata["humidity"];
        $uvindex = $wdata["uvindex"];

        //Response
        $newkey = generateRandomString(200);
        file_put_contents("keystore.txt", hash('sha512', $newkey));
        sendMessage(array("error" => false, "key" => $newkey));
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
