:: Easy!Appointments
::
:: This script generates the code documentation of the system
:: using the jsdoc and apigen tools. In order to run properly 
:: the system should have Java and ApiGen installed already.
:: 
:: THIS SCRIPT RUNS ONLY ON WINDOWS. YOU MIGHT NEED TO CHANGE 
:: THE APIGEN SCRIPT PATH TO WHERE YOUR INSTALLATION EXISTS.
::
:: You might need to change the mermory_limit setting on your 
:: php.ini file to successfully generate the php documentation.
::@echo off
TITLE Easy!Appointments
DEL "js\*.*" /Q
DEL "php\*.*" /Q
CALL ..\..\data\scripts\jsdoc\jsdoc ..\..\src\assets\js\frontend ..\..\src\assets\js\backend ..\..\src\assets\js -d js
CALL C:\wamp\bin\php\php5.4.3\apigen --source ..\..\src\application\controllers --source ..\..\src\application\models --destination php