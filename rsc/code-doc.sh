#!bin/bash
cd ${0%/*}

# Generate PHP Documentation (requires phpdoc in /usr/bin directory).
rm -rf "../doc/code/php/*"
phpdoc -d "../src/application/controllers,../src/application/models,../src/application/libraries" -t "../doc/code/php" -i "*libraries/external/*"
find "../doc/code/php" -name "phpdoc-cache*" -delete

# Generate JS Documentation (requires jsdoc in nodejs environment). 
rm -rf "../doc/code/js/*"
jsdoc "../src/assets/js" -d "../doc/code/js"