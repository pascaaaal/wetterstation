<?php
set_time_limit(1000);
if($_SERVER['REQUEST_METHOD'] == "GET"){
    sendError(0, "Invalid Request");
}elseif ($_SERVER['REQUEST_METHOD'] == "PUT"){
    $base = file_get_contents("php://input");
    $data = json_decode($base);
    
    $device = $data->device;
    $type = $data->type;
    $message = $data->message;
    
    if($device == "mobile"){
        $mmessage = "Bugreport from Mobile App:\r\n" . $message;
        if(mail("pascal.faude@gmail.com", "Weatherstation Bugreport!", $mmessage)){
            sendMessage(array("error" => false));
        }else{
            sendError(1, "Error while sending");
        }
    }else{
    
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
