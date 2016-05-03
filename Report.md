# Drving Behavior Analysis Final Report

## Contributors

* [@Cheng Zhang](https://github.com/zhangchengx)
* [@Zhaoqian Lu](https://github.com/zhaoqianlu)
* [@Yufeng Yuan](https://github.com/FrankeyYuan)

## Adviser
* [@Guanling Chen](https://github.com/gchenhub)

## Project Goal

The goal of the project is creating an Android Application to help drivers understand their driving behaviors.All data will be collected from vehicle and sensors by using OBD2 device and an Android smartphone. After that the system will send the data to our web server for analyzing the data, in order to determin if the driver drives the auto appropriately. The system could detest these driving behaviors,  full stop, hard break, exhibition speed, etc..
After analyzing the data, the system will give a report about their driving behavior based on the data we collected.

## Project Features

	* Collecting vehicle engine data(speed, RPM, engine working duration)
	* Collection position data(Latitude, Longtitud)
	* Upload data(upload data files to the server)
	* Driving behaviors report.

## Data Collection

* OBD2
	* Speed
	* RPM
	* Drving Duration
	* Engine Data
* Phone
	* GPS Location
	* Time
	* Google map Information
	* Accelerometer

## Project Design
	
The system structure of the project is show as Figure 1. The OBD2 device will connect with the user’s car and collect data all information about the car, such as speed and engine information. The smartphone will connect the OBD2 by bluetooth, and the application will keep receiving the data about the car. Meanwhile, the application will also collect the data from the smartphone, such as position and direction, etc. After
collecting all data both from OBD2 and smartphone, the app will send the data to our web server. The web server will receive the data and start analyzing. Finally, the web server will give a report to user for helping drivers understand their driving behaviors.

<img style="text-align:center" src="/image/Screenshot 2016-05-02 19.23.22.png">

Figure1: System strcture

## File Structure

* __Android App__: 

_main source code is stored in src/main/java/edu/uml/cs/obd/driving/_

| Filename| Function| Author|
|---------|---------|-------|
|activity/MainActivity.java|holding main UI, app is started from this activity|Cheng Zhang<br>Zhaoqian Lu<br>Yufeng Yuan|
|activity/ConfigActivity.java|holding config UI, the app can be set up from this activity|Yufeng Yuan|
|io/BluetoothManager.java|bluetooth detecting, connecting, enable and disable|Zhaoqian Lu|
|io/FileUploadUtil.java|upload the latest log file to server|Cheng Zhang|
|io/LogCSVWriter.java|real-time record data into log file from OBD reader|Cheng Zhang|
|io/ObdCommandJob.java<br>io/ObdGatewayService.java<br>net/ObdReading.java<br>net/ObdService.java|OBD reader API|open source code|

* __Server__:

| Filename| Function| Author|
|---------|---------|-------|
|analyse.php|read the data file(.csv)<br>finde the driving behaviors(full stop, hard break, exhibition speed)|Zhaoqian Lu<br>Cheng Zhang<br>Yufeng Yuan|
|upload.php|receive the log file that the app uploaded|Zhaoqian Lu|

## Project Evaluation

Here is a paper that has similar goals with our app: 
[Driving Behavior Analysis Based on Vehicle OBD Information and AdaBoost Algorithms](http://www.iaeng.org/publication/IMECS2015/IMECS2015_pp102-106.pdf)

Summarization of the paper basic idea as follows,

> 1. Collect good driving data and bad driving data as training set.
> 2. Using AdaBoost algorithms to create a driving behavior classification model.
> 3. Import driving data which is collected from OBD reader.
> 4. Determine whether the current driving behavior belongs to safe driving or not.

As you can see, it is a general classification algorithm based on mechine learning method. 
Obviously, it has several points are not perfect.

> 1. It needs a lot of data set for training.
> 2. No 100% accurateness.
> 3. Need a lot computing cost.

Anyway, the good news is, all of the issues listed above have been solved by our app. 
The app can reach 100% accurateness, no training data needed, and less computing cost, 
even moving computing part into smartphone inside, the mobile CPU is able to 100% handle it.

Although the app's algorithm is simple, but simple is powerful, simple is forcible :)


## Reference
> [1] http://blog.lemberg.co.uk/how-guide-obdii-reader-app-development

> [2] https://code.google.com/archive/p/android-obd-reader/downloads

> [3] OBD-II Adapter: http://freematics.com/pages/products/arduino-obd-adapter/

> [4] OBD-II Telematics DIY Kit: http://freematics.com/pages/products/arduino-telematics-kit-3/



