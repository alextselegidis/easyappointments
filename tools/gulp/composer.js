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

/**
 * Install and copy the required files from the "vendor" directory.
 *
 * Composer needs to be installed and configured in order for this command to
 * work properly.
 */
module.exports = (gulp, plugins) => {
    return () => {
        del.sync([
            './src/vendor/**/*',
            '!./src/vendor/index.html'
        ]);

        return gulp.src([
            'vendor/**/*',
            '!vendor/**/{demo,docs,examples,test,tests,extras,language,license,LICENSE}{,/**}',
            '!vendor/**/{composer.json,composer.lock,.gitignore}',
            '!vendor/**/{*.yml,*.xml,*.md,*phpunit*,*.mdown}',
            '!vendor/bin{,/**}',
            '!vendor/codeigniter{,/**}',
            '!vendor/doctrine{,/**}',
            '!vendor/myclabs{,/**}',
            '!vendor/phpdocumentor{,/**}',
            '!vendor/phpspec{,/**}',
            '!vendor/phpunit{,/**}',
            '!vendor/sebastian{,/**}',
            '!vendor/symfony{,/**}',
            '!vendor/phar-io{,/**}',
            '!vendor/webmozart{,/**}'
        ])
            .pipe(gulp.dest('./src/vendor/'));
    };
};
