#include <SoftwareSerial.h>

// Configurations
const short port_RX = 11; // Receiver port
const short port_TX = 10; // Transmitter port
const long baudrate = 115200; // Device communication rate
const int dev_pin[] = { A0, A1, A2, A3, A4, A5, A6, A7 }; // Used pins

// Initial definitions
float time_delay = 1000;
float time_transfer = 1.0;
String data, data_header, data_separator, data_footer, respond;
SoftwareSerial telemetry(port_RX, port_TX);

void setup() {
  // Declare the input pins
  for (int i = 0; i < 8; i++) {
    pinMode(dev_pin[i], INPUT);
  }

  // Initialize serial communications
  Serial.begin(baudrate);
  telemetry.begin(baudrate);

  // Data formats
  data = String();
  data_header = String("[");
  data_separator = String("|");
  data_footer = String("]");
}

void loop() {
  // Writing a line of data collections
  String data = "";
  data += data_header;
  for (int i = 0; i < 8; i++) {
    data += String(analogRead(dev_pin[i]), DEC); // Reading values from pin
    if (i < 7) {
      data += data_separator; // Add separator between each other data
    }
    delayMicroseconds(100);
  }
  data += data_footer;
  telemetry.println(data);

  // Reading device respond
  respond = telemetry.readString();
  if (respond != "") { // Avoiding blank data
    Serial.println(respond);
  }

  // Changing delay time if there's a data with "TIME" on the beginning of line
  if (respond.substring(0, 4) == "TIME") {
    time_transfer = respond.substring(5).toFloat();
    if (time_transfer <= 0.0) {
      time_transfer = 1.0;
    }
    time_delay = (time_transfer * 1000) - 1000;
    if (time_delay <= 0.0) {
      time_delay = 1000;
    }
  }

  delay(time_delay);
}
