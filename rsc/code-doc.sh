#!bin/bash

# Generate PHP Documentation.
rm -rf "../doc/code/php/*"
php apigen.phar generate -s "../src/application/controllers,../src/application/models,../src/application/libraries" -d "../doc/code/php" --exclude "*external*" --tree --todo --template-theme "bootstrap"

# Generate JS Documentation (requires jsdoc in nodejs environment). 
rm -rf "../doc/code/js/*"
jsdoc "../src/assets/js" -d "../doc/code/js"