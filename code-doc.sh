#!/bin/bash 

# 
# Bash script for the code documentation generation.
# 
 
rm -rf doc 

mkdir doc 
mkdir doc/apigen
mkdir doc/jsdoc
mkdir doc/plato

php vendor/bin/apigen generate \
    -s "src/application/controllers,src/application/models,src/application/libraries" \
    -d "doc/apigen" --exclude "*external*" --tree --todo --template-theme "bootstrap"

node node_modules/.bin/jsdoc "src/assets/js" -d "doc/jsdoc"

node node_modules/.bin/plato -r -d "doc/plato" "src/assets/js"