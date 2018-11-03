/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

const exec = require('child_process').execSync;
const fs = require('fs-extra');
const path = require('path');

/**
 * Generate code documentation.
 */
module.exports = (gulp, plugins) => {
    return (done) => {
        fs.removeSync('docs/apigen/html');
        fs.mkdirSync('docs/apigen/html');
        fs.removeSync('docs/jsdoc/html');
        fs.mkdirSync('docs/jsdoc/html');
        fs.removeSync('docs/plato/html');
        fs.mkdirSync('docs/plato/html');

        const commands = [
            'php docs/apigen/apigen.phar generate ' +
            '-s "src/application/controllers,src/application/models,src/application/libraries" ' +
            '-d "docs/apigen/html" --exclude "*external*" --tree --todo --template-theme "bootstrap"',

            path.join('.', 'node_modules', '.bin', 'jsdoc') + ' "src/assets/js" -d "docs/jsdoc/html"',

            path.join('.', 'node_modules', '.bin', 'plato') + ' -r -d "docs/plato/html" "src/assets/js"'
        ];

        commands.forEach(function (command) {
            exec(command, function (err, stdout, stderr) {
                console.log(stdout);
                console.log(stderr);
            });
        });

        done();
    };
};
