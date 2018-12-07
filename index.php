<?php
set_time_limit(100);
if($_SERVER['REQUEST_METHOD'] == "GET"){
    sendMessage(json_decode(file_get_contents("data.txt")));
}elseif ($_SERVER['REQUEST_METHOD'] == "PUT"){
    if(hash('sha512', $_GET["key"]) == "a420f1fe3884e5ce3e133b8f3e0a748e9ec7f0b5f14b4aca0d75989610909ecb9b28e997a7ec6b851bdbc1bad46da1464a0203eb8d039af74c6cada5b67d78ce"){
        $base = file_get_contents("php://input");
        $wdata = json_decode($base);
        if($wdata != null){
            if(isset($wdata->temperature) && isset($wdata->humidity) && isset($wdata->uvindex) && isset($wdata->air_pressure) && isset($wdata->illumiance)){
                $temp = $wdata->temperature;
                $huminity = $wdata->humidity;
                $uvindex = $wdata->uvindex;
                $air_pressure = $wdata->air_pressure;
                $illumiance = $wdata->illumiance;
                
                if(file_put_contents("data.txt", json_encode(array("error" => false, "temp" => $temp, "humidity" => $huminity, "uvindex" => $uvindex, "air_pressure" => $air_pressure, "illumiance" => $illumiance)))){
                    sendMessage(array("error" => false));
                }else{
                    sendError(4, "Erorr while saving data");
                }
            }else{
                sendError(3, "Incomplete json");
            }
        }else{
            sendError(2, "Bad json " . $base);
        }
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
    header("Content-Length: " . strval(strlen(json_encode($data))));
    echo json_encode($data);
}
function generateRandomString($length){
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle($chars), 0, $length);
}
