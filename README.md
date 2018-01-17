# webdevstuff
List of Stuff Written in PHP w/ CodeIgniter Framework
Since the CodeIgniter Framework is quite large, I'll be uploading just the models, views, controllers, and some other important stuff.

Some of the filenames have been renamed to their functionality.

PHP Files:
Used framework is CodeIgniter 2.0 (MVC framework)

1) dashboard_controller.php -
  (Controller) is the center of the dashboard in which the controller is the linking the views (interface) and the model (database).
  multiple functions created to draw and manipulate Highcharts (https://www.highcharts.com/) graphs.
  partnered with javascript to automate the update of the graphs using setInterval function.

2) access_to_db.php -
  (Model) handles the querying to the MySQL database set in the web application settings, called by functions from the controller to get data from the database.
  
3) project_monitoring.php -
  (Views) shows the user the graphs created using Highcharts library, multiple javascript files are also included to automate manipulation of the graphs giving a 'real time' feel to it.

4) urlaccess_to_csv.php -
  (Controller) set of functions used to received data from the devices at remote location through access of URL to pass data. This is used as alternative mode to pass data between server and remote locationin case firewall does not allow FTP and MySQL connections to our database server.
  
5) urlupdate.php -
  (Controller) set of functions used to update c application of the device on site through the use of downloads.
  Usage:
    a script at site sends the current version of the c app to check with the server, if an updated was created on the server, download the app and update its version file.
  
Javascript Files:
1) graph_1.js -
  Using Highcharts library to create graphs used to display in project_monitoring.php
2) data_refresh.js -
  Script used to get information from the dashboard_controller to update the Views.
 
  
