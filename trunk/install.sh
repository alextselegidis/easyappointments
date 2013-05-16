#! /bin/bash

clear

echo "========================================="
echo "                                         "
echo "Easy!Appointments Installation Script    "
echo "                                         "
echo "========================================="

printf "Before you continue ensure that your MAMP 
\nserver is running and you already know the 
\nfolder path in which the application is 
\ngoing to be installed.\n\n"

read -p "Press Enter to Continue" -n 1
if [[ ! $REPLY =~ ^[Yy]$ ]]
then
	# Copy Application Files
	printf "\n\nEnter destination directory:"	
	read DEST 
	cp -r "easy_appointments" $DEST

	# Install MySQL Database	
	printf "\n>>>Installing database ..."
	/Applications/MAMP/Library/bin/mysql -uroot -p
	/Applications/MAMP/Library/bin/mysql "CREATE DATABASE IF NOT EXISTS easy_appointments"
	/Applications/MAMP/Library/bin/mysql "easy_appointments" < "easy_appointments.sql"
	
	printf "\n\n>>>Review your configuration.php file before trying to use the application."

	# End of install operation
	printf "\n\n======================================"
 	printf "\nInstallation completed successfully!"
	read
fi
