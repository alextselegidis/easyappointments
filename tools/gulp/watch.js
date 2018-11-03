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
        gulp.watch([
            'src/assets/js/**/*.js',
            '!src/assets/js/**/*.min.js'
        ], ['scripts']);

        gulp.watch([
            'src/assets/css/**/*.css',
            '!src/assets/css/**/*.min.css'
        ], ['styles']);
    };
};
