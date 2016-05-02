# Final Report
Drving Behavior Analysis
Cheng Zhang, Zhaoqian Lu, Yufeng Yuan

# Project Goal

The goal of the project is creating an Android Application to help drivers understand their driving behaviors.All data will be collected from vehicle and sensors by using OBD2 device and an Android smartphone. After that the system will send the data to our web server for analyzing the data, in order to determin if the driver drives the auto appropriately. The system could detest these driving behaviors,  full stop, hard break, exhibition speed, etc..
After analyzing the data, the system will give a report about their driving behavior based on the data we collected.

#Project Features

	* Collecting vehicle engine data(speed, RPM, engine working duration)
	* Collection position data(Latitude, Longtitud)
	* Upload data(upload data files to the server)
	* Driving behaviors report.

# Project Design
	
The system structure of the project is show as Figure 1.  The OBD2 device will connect with the user’s car and collect data all information about the car, such as speed and engine information. The smartphone will connect the OBD2 by bluetooth, and the application will keep receiving the data about the car. Meanwhile, the application will also collect the data from the smartphone, such as position and direction, etc. After
collecting all data both from OBD2 and smartphone, the app will send the data to our web server. The web server will receive the data and start analyzing. Finally, the web server will give a report to user for helping drivers understand their driving behaviors.
<!--<div style="text-align:center" markdown="1">-->
<img style="text-align:center" src="Screenshot 2016-05-02 19.23.22.png">
<!--![a](Screenshot 2016-05-02 19.23.22.png)-->
Figure1: System strcture
<!--Figure1: system structure: - center-->
<!--</div>-->
File Structure

* Android App: 

main source code is stored in src/main/java/edu/uml/cs/obd/driving/

| Filename| Function| Author|
|---------|---------|-------|
|activity/MainActivity.java|holding main UI, app is started from this activity|Cheng Zhang<br>Zhaoqian<br>Lu Yufeng Yuan|
|activity/ConfigActivity.java|holding config UI, the app can be set up from this activity|Yufeng Yuan|
|io/BluetoothManager.java|bluetooth detecting, connecting, enable and disable|Zhaoqian Lu|
|io/FileUploadUtil.java|upload the latest log file to server|Cheng Zhang|
|io/LogCSVWriter.java|real-time record data into log file from OBD reader|Cheng Zhang|
|io/ObdCommandJob.java<br>io/ObdGatewayService.java<br>net/ObdReading.java<br>net/ObdService.java|OBD reader API|open source code|

* Server:

| Filename| Function| Author|
|---------|---------|-------|
|analyse.php|read the data file(.csv)<br>finde the driving behaviors(full stop, hard break, exhibition speed)|Zhaoqian Lu<br>Cheng Zhang<br>Yufeng Yuan|
|upload.php|receive the log file that the app uploaded|Zhaoqian Lu|


Project Evaluation



