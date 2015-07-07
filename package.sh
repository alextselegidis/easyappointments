#!/bin/bash

# 
# Bash script for the creation of the easyappointments.zip
#

clear

rm -f easyappointments.zip
rm -rf .tmp-package/**

mkdir .tmp-package
cp -rf src/** .tmp-package
rm -f .tmp-package/config.php
mv -f .tmp-package/config-sample.php .tmp-package/config.php
cp CHANGELOG.md .tmp-package/CHANGELOG
cp LICENSE .tmp-package/LICENSE

cd .tmp-package
zip -r ../easyappointments.zip ./*
cd ..
rm -rf .tmp-package