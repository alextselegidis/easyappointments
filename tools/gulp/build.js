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

const del = require('del');
const fs = require('fs-extra');
const zip = require('zip-dir');

module.exports = (gulp, plugins) => {
    return (done) => {
        del.sync([
            'build',
            'easyappointments-0.0.0.zip'
        ]);

        fs.copySync('src', 'build');
        fs.removeSync('build/config.php');
        fs.copySync('CHANGELOG.md', 'build/CHANGELOG.md');
        fs.copySync('README.md', 'build/README.md');
        fs.copySync('LICENSE', 'build/LICENSE');

        del.sync([
            'build/storage/uploads/*',
            '!build/storage/uploads/index.html'
        ]);

        del.sync([
            'build/storage/logs/*',
            '!build/storage/logs/index.html'
        ]);

        del.sync([
            'build/storage/sessions/*',
            '!build/storage/sessions/.htaccess',
            '!build/storage/sessions/index.html'
        ]);

        del.sync([
            'build/storage/cache/*',
            '!build/storage/cache/.htaccess',
            '!build/storage/cache/index.html'
        ]);

        zip('build', {saveTo: 'easyappointments-0.0.0.zip'}, function (err, buffer) {
            if (err)
                console.log('Zip Error', err);

            done();
        });
    };
};
