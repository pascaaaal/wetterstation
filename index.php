<?php
set_time_limit(100);
if($_SERVER['REQUEST_METHOD'] == "GET"){
    sendMessage(json_decode(file_get_contents("data.txt")));
}elseif ($_SERVER['REQUEST_METHOD'] == "POST"){
    if(hash('sha512', $_GET["key"]) == "<key>"){
            if(isset($_POST["temperature"]) && isset($_POST["humidity"]) && isset($_POST["uvindex"]) && isset($_POST["air_pressure"]) && isset($_POST["illumiance"])){
                $temp = intval($_POST["temperature"]);
                $huminity = intval($_POST["humidity"]);
                $uvindex = intval($_POST["uvindex"]);
                $air_pressure = intval($_POST["air_pressure"]);
                $illumiance = intval($_POST["illumiance"]);

                if(file_put_contents("data.txt", json_encode(array("error" => false, "timestamp" => date("d-m-Y H:i"), "temp" => $temp, "humidity" => $huminity, "uvindex" => $uvindex, "air_pressure" => $air_pressure, "illumiance" => $illumiance)))) {
                    if (file_exists(date("d-m-Y") . ".json")) {
                        $old = file_get_contents(getcwd() . "/" . date("d-m-Y") . ".json");
                        $old_json = json_decode($old, true);
                        }else{
                            $dayfile = fopen(date("d-m-Y") . ".json", "w+");
                            $old_json = array("values" => []);
                        }
                    array_push($old_json["values"], array("time" => date("H:i:s"), "temp" => $temp, "humidity" => $huminity, "uvindex" => $uvindex, "air_pressure" => $air_pressure, "illumiance" => $illumiance));
                if(file_put_contents(date("d-m-Y") . ".json", json_encode($old_json))){
                    sendMessage(array("error" => false));
                }
                fclose($dayfile);
                }else{
                    sendError(4, "Erorr while saving data");
                }
            }else{
                sendError(3, "Incomplete json");
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
