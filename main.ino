#include <Wire.h>
#include <HDC100X.h>
#include <BMP280.h>
#include <Makerblog_TSL45315.h>
#include <VEML6070.h>
#include <SPI.h>
#include <Ethernet2.h>
#include <ArduinoJson.h>


byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED }; //MAC Addres ()
char server[] = "gymebi-wetter.bplaced.net"; //Server URL (wetterstation url)

EthernetClient client;

//#define I2C_ADDR 0x38
//#define IT_1   0x1 //1T

HDC100X HDC(0x43);
BMP280 BMP;
VEML6070 VEML;
Makerblog_TSL45315 TSL = Makerblog_TSL45315(TSL45315_TIME_M4);

void setup() {
  Serial.begin(9600);
  Serial.println("Starting program...");
  Serial.println("Setting up Sensors...");

  HDC.begin(HDC100X_TEMP_HUMI, HDC100X_14BIT, HDC100X_14BIT, DISABLE);
  VEML.begin();
  TSL.begin();
  BMP.begin();
  BMP.setOversampling(4);

  delay(500);

  Serial.println("Sensors setted up");
  Serial.println("Setting up Network...");

  if (Ethernet.begin(mac) == 0) {
    Serial.println("Failed to configure Ethernet using DHCP");
    //Ethernet.begin(mac, ip);
  }

  delay(500);

  Serial.println("Setted up Network");
  Serial.println("System ready for use!");
}

void loop() {
  //Get data
    int humindity = HDC.getHumi(); //Humindity
    
    int temperature = HDC.getTemp(); //Temperature

    char result = BMP.startMeasurment();
    delay(result);
    double T=0, P=0;
    BMP.getTemperatureAndPressure(T, P);
    int air_pressure = P; //Air pressure

    uint32_t illumiance = TSL.readLux(); //Illumiance

    uint16_t uvindex = VEML.getUV(); //UV Index

    Serial.println(temperature);

   String d = "temperature=";
   d += String(temperature);
   d += "&humidity=";
   d += String(humindity);
   d += "&uvindex=";
   d += String(uvindex);
   d += "&air_pressure=";
   d += String(air_pressure);
   d += "&illumiance=";
   d += String(illumiance);
   int packet_length = d.length();
   Serial.println(d);
   Serial.println(packet_length);

  //Send data
    if (client.connect(server, 80)) {
    Serial.println("verbunden!");
    // Make a HTTP request:
    client.print("POST /api.php?key=<key>");
    client.println(" HTTP/1.1");
    client.print("Host: ");
    client.println(server);
    client.print("Content-Length: ");
    client.println(packet_length);
    client.println("Content-Type: application/x-www-form-urlencoded\r\n");
    client.print(d);
  }else {
    Serial.println("Connecting failed!");
  }
  
  String responce = "";
  
  while(client.connected()){
    if (client.available()) {
      char c = client.read();
      responce += c;
      //Serial.print(c);
      if(c == '\n'){
        responce = "";
      }else if(c == '\r'){
        
      }else if(c == '}'){
        if(responce.indexOf("{") != -1){
          break;
        }
      }
    }
  }
  client.stop();
  Serial.println(responce);

  char charBuf[responce.length() + 1];
  responce.toCharArray(charBuf, responce.length() + 1);
  
  StaticJsonBuffer<15> jsonBuffer2;

  JsonObject& root2 = jsonBuffer2.parseObject(charBuf);
  if (root2.success()) {

  if(root2["error"]){
    Serial.println("Error while connecting...");
    Serial.print("Error message: ");
    root2["message"].printTo(Serial);
    Serial.print("\n");

  }else{
    Serial.println("Request succed");
  }
  }else{
     Serial.println("Error while parsing Json");
  }
  delay(600000);
}
