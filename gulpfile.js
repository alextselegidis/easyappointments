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

// Gulp instance and plugins.
const gulp = require('gulp');
const plugins = require('gulp-load-plugins')();

// Gulp error handling.
const source = gulp.src;
gulp.src = function() {
    return source.apply(gulp, arguments)
        .pipe(plugins.plumber({
            errorHandler: plugins.notify.onError('Error: <%= error.message %>')
        }));
};

// Define gulp tasks.
gulp.task('build', require('./tools/gulp/build')(gulp, plugins));
gulp.task('clean', require('./tools/gulp/clean')(gulp, plugins));
gulp.task('composer', require('./tools/gulp/composer')(gulp, plugins));
gulp.task('dev', require('./tools/gulp/dev')(gulp, plugins));
gulp.task('docs', require('./tools/gulp/docs')(gulp, plugins));
gulp.task('scripts', require('./tools/gulp/scripts')(gulp, plugins));
gulp.task('styles', require('./tools/gulp/styles')(gulp, plugins));
gulp.task('tests', require('./tools/gulp/tests')(gulp, plugins));
gulp.task('watch', require('./tools/gulp/watch')(gulp, plugins));
gulp.task('default', gulp.parallel('dev'));
