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

module.exports = (gulp, plugins) => {
    return () => {
        gulp.src([
            'src/assets/js/**/*.js',
            '!src/assets/js/**/*.min.js'
        ])
            .pipe(plugins.changed('src/assets/js/**/*'))
            .pipe(plugins.uglify().on('error', plugins.util.log))
            .pipe(plugins.rename({suffix: '.min'}))
            .pipe(gulp.dest('src/assets/js'));
    };
};

