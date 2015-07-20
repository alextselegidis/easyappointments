#!/bin/bash 

# 
# Bash script for the code documentation generation.
# 
 
rm -rf doc 

mkdir doc 
mkdir doc/apigen
mkdir doc/jsdoc

php rsc/apigen.phar generate \
    -s "src/application/controllers,src/application/models,src/application/libraries" \
    -d "doc/apigen" --exclude "*external*" --tree --todo --template-theme "bootstrap"


node node_modules/.bin/jsdoc "src/assets/js" -d "doc/jsdoc"