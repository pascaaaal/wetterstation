<?php
set_time_limit(100);
if ($_SERVER['REQUEST_METHOD'] == "GET"){
    sendMessage(array("error" => false, "temp" => 1, "humidity" => 1, "uvindex" => 1, "air_pressure" => 1, "illumiance" => 1));
}elseif ($_SERVER['REQUEST_METHOD'] == "PUT"){
    $key = file_get_contents("keystore.txt");
    if(hash('sha512', $_GET["key"]) == str_replace("\r\n", "", $key)){
        $base = file_get_contents("php://input");
        $wdata = json_decode($base);
        if($wdata != null){
            //if(isset($wdata["temperature"]) && isset($wdata["humidity"]) && isset($wdata["uvindex"]) && isset($wdata["air_pressure"]) && isset($wdata["illumiance"])){
                $temp = $wdata["temperature"];
                $huminity = $wdata["humidity"];
                $uvindex = $wdata["uvindex"];
                $air_pressure = $wdata["air_pressure"];
                $illumiance = $wdata["illumiance"];

                $newkey = generateRandomString(200);
                if(file_put_contents("keystore.txt", hash('sha512', $newkey))){
                    sendMessage(array("error" => false, "key" => $newkey));
                }else{
                    sendError(4, "Erorr while saving key");
                }
            //}else{
            //    sendError(3, "Incomplete json");
            //}
        }else{
            sendError(2, "Bad json");
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
    echo json_encode($data);
}
function generateRandomString($length){
    $str = "";
	$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
	$max = count($characters) - 1;
	for ($i = 0; $i < $length; $i++) {
		$rand = mt_rand(0, $max);
		$str .= $characters[$rand];
	}
	sendMessage(array("d" => $str);
	return $str;
}
