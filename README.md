# Room Occupancy Detector Based on Light Detection Telemetry

![Maintenance](https://img.shields.io/maintenance/no/2018?style=for-the-badge)

Author: Yoga Perdana Putra<br>
Last Update: February 2018<br>

>  **Author's Note:**<br>
>  This project was created when the author was still an electronics student a few years ago, therefore the source code is no longer updated. Also for the first time learning to use the Python programming language on this project.

The interface views (application and web page) are presented in **Bahasa Indonesia**, except for system-generated error messages. This documentation is intentionally written in English and doesn't provide a complete detailed explanation of entire project. Please kindly contact the author if you have any questions.

## Introduction

This is an IoT (Internet of Things) project using multiple light sensors connected by Arduino Nano (compatible) board and send detected values wirelessly to PC via radio telemetry device. PC will collect the data stream, store it into database and presenting to a simple web-based analytical interface that can be made accessible through the network.

![System Block Diagram](/docs/images/system_block_diagram.png)<br>
_Picture above: Entire system block diagram_

The concept is that each light sensor will be placed in a separate room. Any light detected by the sensor in the room will be considered the room active or "ON". Otherwise the room will be considered inactive or "OFF". All sensor nodes will be connected to each other and placed for example on the ceiling of the building. Data is transmitted wirelessly so that the devices that record the data (in this case it's a PC) can be placed more flexible.

## Requirements

### Hardware

*  [Arduino Nano](https://arduino.cc/en/Main/ArduinoBoardNano)
*  433MHz 3DR-based USB Radio Telemetry Kit ([example product](http://jogjarobotika.com/wireless-gps-xbee-module/1222-3dr-radio-telemetry-kit-433mhz-100mw.html))
*  Plain PCB (printed circuit board)
*  LDR (light-dependent resistor) with 10k-ohm resistor as voltage divider
*  Additional housing pins, cables, read more on another parts of this documentation.

### Software

Current working environments:

**Curcuit & Board Development**
*  [EAGLE](https://autodesk.com/products/eagle/overview) 6.3
*  [Arduino IDE](https://arduino.cc/en/software) 1.8.5

**Middleware, Database & Web Interface**
*  Microsoft Windows 10<br>
   _It's possible to run within Linux kernel or MacOS. Not fully tested yet._
*  [Python](https://python.org/) 3.6.4 with installed packages:
   *  PySerial 3.4
   *  PyMySQL 0.8.0
   *  PyInstaller 3.3.1 for executable application builder (optional, Windows only)
*  [Windows SDK](https://developer.microsoft.com/en-us/windows/downloads/windows-sdk/) (optional, Windows only)
*  [XAMPP](https://apachefriends.org/) 7.1.1, bundled with:
   *  Apache 2.4.25
   *  PHP 7.1.1
   *  MariaDB 10.1.21 (MySQL database compatible)

## Devices

### Physical Units

This project consist of 3 elements:

1. Main microcontroller board<br>
   Arduino Nano controls the entire physical devices group based on given command program.
2. Telemetry device<br>
   Send data from board to PC and vice versa.
3. Sensor circuit<br>
   Small circuit unit, each one contains a light sensor.

Two types of circuit boards are made. Arduino and telemetry device can be assembled into one package. Sensor board made separately as many according on demand, up to 8 pcs based on the availability of Arduino Nano input pins.

![Controller board](/docs/images/device_asm_control.png)<br>
_Picture above: The controller board's appearance (final assembly)_

![Sensor unit boards](/docs/images/device_asm_sensor.png)
_Picture above: The sensor unit boards' appearance (final assembly)_

The telemetry device consist of two separate units: (1) Pin-based unit, and (2) USB-port unit. Each can act as transmitter or receiver based on the process cycle. The first unit is paired with Arduino and the USB unit is plugged into the PC.

USB radio telemetry hardware currently tested running smoothly on 115200 baudrate (can be different if using another compatible clone device)

### Circuit Design

![Circuit Schematic Diagram](/docs/images/device_circuit_schematic.png)<br>
_Picture above: Circuit schematic diagram_

Each Arduino analog pin is connected to an LDR component. A voltage divider circuit is formed by adding a 10k-ohm resistor in parallel. Different types of LDR may require different voltage divider resistance.

All components connected to Arduino are given a voltage of 5V. Arduino Nano (rev 3.0) has 8 analog pins (A0-A7) that can be used all or partially according on demand.

Sensors and Arduino will be farly separated and connected by a long cables.

Telemetry device connected to Arduino's receiver-transmitter pins which are allocated on pin D0 for RX and D1 for TX.

### Board Design

![EAGLE 6.3](https://img.shields.io/badge/EAGLE-6.3.4-red.svg?style=flat-square)

![Board Design](/docs/images/device_board_schematic.png)<br>
_Picture above: Board design for the physical unit (top: for controller, bottom: for sensor)_

I've provided source files that can be accessed using [EAGLE software](https://autodesk.com/products/eagle/overview). Files have been created using EAGLE version 6.3. Print-ready document is also provided.

Navigate to [`device/board/`](device/board/) directory to download.

### Arduino Program

![Arduino IDE 1.8.5](https://img.shields.io/badge/Arduino_IDE-1.8.5-teal.svg?style=flat-square)

Please refer to [The Official Documentation of Arduino Nano](https://www.arduino.cc/en/Guide/ArduinoNano) for complete installation instructions.

Make sure the Arduino device is successfully connected into PC via USB port. Check the port address from device manager (e.g. COM3 on Windows).

Load the sketch file [`device/arduino/nano.ino`](device/arduino/nano.ino) into Arduino IDE. Select the correct board type and port. Then, upload and run the sketch file.

Adjust the configuration variables before uploading to microcontroller if you have assembled with a different setup.

### Data Stream

Data from all sensors will be collected at the same time as a row stream with following format:

```
[<s0_data>|<s1_data>|...|<s7_data>]
```

For example:

```
[1023|1023|1023|1023|1023|1023|1023|1023]
```

Unused or disconnected pin will return a value of zero. Brighter light detection will return a higher integer value up to 1023.

>  Reading value refers to 10-bit analog to digital converter. This means that it will map input voltages between 0 and the operating voltage (5V) into integer values between 0 and 1023.

## Middleware Application

![Python 3.6.4](https://img.shields.io/badge/Python-3.6.4-blue.svg?style=flat-square)
![PySerial 3.4](https://img.shields.io/badge/PySerial-3.4-steelblue.svg?style=flat-square)
![PyMySQL 0.8.0](https://img.shields.io/badge/PyMySQL-0.8.0-steelblue.svg?style=flat-square)
![PyInstaller 3.3.1](https://img.shields.io/badge/PyInstaller-3.3.1-steelblue.svg?style=flat-square)

### Usage

1. **Install Python**<br>
   Download from [Python's official download page](https://www.python.org/downloads/) and follow their documentation for installation instructions.<br>
   If you want to keep up with the original working environment of this project, you can choose [Python version 3.6.4](https://www.python.org/downloads/release/python-364/).

2. **Navigate to the middleware directory**<br>
   Run the following command:
   ```sh
   cd middleware
   ```

3. **Install the `virtualenv` package**<br>
   The `virtualenv` package is required to create virtual environments. You can install it with pip:<br>
   ```sh
   pip install virtualenv
   ```

4. **Create the virtual environment**<br>
   Specify a local directory name for the environtment, e.g. `room-app-env`, type the following:<br>
   ```sh
   virtualenv room-app-env
   ```

5. **Activate the virtual environment**<br>
   Run the following command:<br>
   - Linux/MacOS:
     ```sh
     source room-app-env/bin/activate
     ```
   - Windows:
     ```sh
     room-app-env\Scripts\activate
     ```

6. **Install the requirement tools**<br>
   Run the following command:
   ```sh
   pip install -r requirements.txt
   ```

7. **Run the application script**<br>
   Run the following command:
   ```sh
   python app.py
   ```

### Windows Executable File

If you want to run applications directly through Windows OS without installing Python, you can directly execute the file: [`middleware/app.exe`](/middleware/app.exe). Tested on Windows 10.

Rest assured, the provided executable file is completely free of viruses or malicious injection because it is directly compiled from the Python scripts. But it can cause some bugs such as the python.exe process that won't exit when the application is closed.

### Code Compile

The script can be compiled into an executable file for portable use on Windows environment. You must successfully installed these packages first before performing compile:
* PyInstaller
* [Windows SDK (Software Development Kit)](https://developer.microsoft.com/en-us/windows/downloads/windows-sdk/)

Windows 10 SDK contains UCRT DLL files like `api-ms-win-*.dll` that required to build Python's Tkinter window interfaces.

Run Command Prompt or PowerShell as Administrator. Use command below to start compiling:

```sh
pyinstaller app.py -p '<path-to-sdk-directory>' -w -F -y --clean
```

For example:

```sh
pyinstaller app.py -p 'C:\Program Files (x86)\Windows Kits\10\Redist\ucrt\DLLs\x86' -w -F -y --clean
```

For debugging, remove -w paramater to show terminal window when program is running.

This will create two new directories: `build` and `dist`. The `app.exe` file is in the `dist` directory.

### How To Use

![Middleware Application](/docs/images/app_middleware.png)<br>
_Picture above: Screenshot of middleware application_

Select active port of radio telemetry on list and press Select button. The app will receiving data from radio telemetry continously unless you press Stop button. Received data will be displayed on data history box.

On the first run, maybe you will get incorrect interval data transmissions. Change the transmission interval to any seconds. I recommend you to give value about 5 seconds or greater.

You must specify the cutoff sensor value with your own calibration by looking on the detection area. Set how much light amount is detected to set as ON or OFF state.

To store the data into database, fill MySQL hostname, username and password that assigned to database. Then press Connect button. If anything goes wrong, error messages will be displayed on the database monitor box. You can change database interval anytime.

Make sure you have successfuly created the correct database and specified tables before connecting middleware app to MySQL server. Read next documentation for more instructions.

## Analytical Data Interface

![PHP 7.1.1](https://img.shields.io/badge/PHP-7.1.1-blue.svg?style=flat-square)
![MariaDB 10.1.21](https://img.shields.io/badge/MariaDB-10.1.21-brightgreen.svg?style=flat-square)

### Web and Database Server

If you want to keep up with the original working environment of this project, I use [XAMPP](https://apachefriends.org/) which contains packages such as Apache web server to serve PHP web language and MariaDB (MySQL database compatible) to storing data.

Install XAMPP first (see their documentation for more instructions) and make sure the Apache and MySQL services are successfuly started. Don't forget to configure MySQL's authentication.

Alternatively if you wish to serve the web and database server separately from the middleware, I recommend using Linux server distro and installing Apache-PHP & MySQL server separately. Please adjust the configuration by youself.

### Database Preparation

In order to store received data from sensors, a database and tables must be created first. Run the batch SQL script with following command:

```sh
mysql -h <host> -u <user> -p < database/initial.sql
```

The command will ask you for the SQL server password. Please adjust the hostname and username with your SQL server configuration. For example with default authentication:

```sh
mysql -h localhost -u root -p < database/initial.sql
```

If you're using XAMPP, script can be executed via phpMyAdmin. Login first to phpMyAdmin page. You can copy the contents of the script and paste it on SQL tab or import the SQL file through import tab.

The script will create a new database and two new table: `sensor_config` and `sensor_data` with the specified structure.

Make sure the database and tables is successfully created before continue to next step.

For some reason, if you want to clear the sensor data from database, you can truncate with following SQL command:

```sql
TRUNCATE TABLE sensor_data
```

### Web Page

Copy our [`webroot`](/webroot/) directory to `htdocs` folder inside XAMPP's installation directory. Navigate to http://localhost/webroot/ from your browser to access the webpage. Please adjust if you've changed the directory name.

**Library dependencies:**
-  UI Framework: [Bootstrap](https://getbootstrap.com/) 3.3.6<br>
-  JavaScript Library: [JQuery](https://jquery.com/) 2.2.4<br>
-  Graph Library: [D3.js](https://d3js.org/) 4.5.0

![Web Page](/docs/images/web_fullpage.png)
_Picture above: Screenshot of analytical data page (sample data)_

## Future Developments

This project might still contain several bugs and code might be deprecated. The project is working with basic requirements mentioned above.

Some ideas that can be done next:

- Upgrade telemetry device with better specifications. Several tests have been carried out at different distances but found unsatisfactory results.
- Replace with multiple telemetry nodes to represent each sensor and flexible location. This project need a very long cables that causes some little voltage loss and affected to detection data.
- Do detection with another type of sensors. For example: phototransistor types, photodiode types, FIR (far infrared) thermal sensor, etc.
- Serve web-based application with Python web framework. Could be packaged together with the middleware application or separated microservices with integration.

## Copyrights

Anyone can make a replication and/or modifications of all contents of this project with any purpose. Please do a cite the author and this repository if any project is made and published based on source code or included asset from this project.
