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

const babel = require('gulp-babel');
const changed = require('gulp-changed');
const childProcess = require('child_process');
const css = require('gulp-clean-css');
const del = require('del');
const dist = require('gulp-npm-dist');
const fs = require('fs-extra');
const gulp = require('gulp');
const plumber = require('gulp-plumber');
const rename = require('gulp-rename');
const sass = require('gulp-sass')(require('sass'));
const zip = require('zip-dir');

function archive(done) {
    const filename = 'easyappointments-0.0.0.zip';

    fs.removeSync('build');
    fs.removeSync(filename);

    fs.mkdirsSync('build');
    fs.copySync('application', 'build/application');
    fs.copySync('assets', 'build/assets');
    fs.copySync('system', 'build/system');

    fs.ensureDirSync('build/storage/backups');
    fs.copySync('storage/backups/.htaccess', 'build/storage/backups/.htaccess');
    fs.copySync('storage/backups/index.html', 'build/storage/backups/index.html');

    fs.ensureDirSync('build/storage/cache');
    fs.copySync('storage/cache/index.html', 'build/storage/cache/index.html');
    fs.copySync('storage/cache/.htaccess', 'build/storage/cache/.htaccess');

    fs.ensureDirSync('build/storage/logs');
    fs.copySync('storage/logs/.htaccess', 'build/storage/logs/.htaccess');
    fs.copySync('storage/logs/index.html', 'build/storage/logs/index.html');

    fs.ensureDirSync('build/storage/sessions');
    fs.copySync('storage/sessions/.htaccess', 'build/storage/sessions/.htaccess');
    fs.copySync('storage/sessions/index.html', 'build/storage/sessions/index.html');

    fs.ensureDirSync('build/storage/uploads');
    fs.copySync('storage/uploads/index.html', 'build/storage/uploads/index.html');

    fs.copySync('index.php', 'build/index.php');
    fs.copySync('composer.json', 'build/composer.json');
    fs.copySync('composer.lock', 'build/composer.lock');
    fs.copySync('config-sample.php', 'build/config-sample.php');
    fs.copySync('CHANGELOG.md', 'build/CHANGELOG.md');
    fs.copySync('README.md', 'build/README.md');
    fs.copySync('LICENSE', 'build/LICENSE');

    childProcess.execSync('cd build && composer install --no-interaction --no-dev --no-scripts --optimize-autoloader');

    del.sync('**/.DS_Store');
    fs.removeSync('build/composer.lock');
    del.sync('**/.DS_Store');

    zip('build', {saveTo: filename}, function (error) {
        if (error) {
            console.log('Zip Error', error);
        }

        done();
    });
}

function clean(done) {
    fs.removeSync('assets/js/**/*.min.js');
    fs.removeSync('assets/css/**/*.min.css');
    done();
}

function scripts() {
    return (
        gulp
            .src(['assets/js/**/*.js', '!assets/js/**/*.min.js'])
            .pipe(plumber())
            .pipe(changed('assets/js/**/*'))
            .pipe(babel())
            // .pipe(uglify().on('error', console.log))
            .pipe(rename({suffix: '.min'}))
            .pipe(gulp.dest('assets/js'))
    );
}

function styles() {
    return gulp
        .src(['assets/css/**/*.scss', '!assets/css/**/*.min.css'])
        .pipe(plumber())
        .pipe(changed('assets/css/**/*'))
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('assets/css'))
        .pipe(css())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('assets/css'));
}

function watch(done) {
    gulp.watch(['assets/js/**/*.js', '!assets/js/**/*.min.js'], gulp.parallel(scripts));
    gulp.watch(['assets/css/**/*.css', '!assets/css/**/*.min.css'], gulp.parallel(styles));
    done();
}

function vendor() {
    del.sync(['assets/vendor/**', '!assets/vendor/index.html']);

    const excludes = [
        'less',
        'metadata',
        'scss',
        'attribution.js',
        'examples',
        'src',
        'test',
        'esm',
        'cjs',
        'external',
        'build'
    ];

    return gulp
        .src(dist({excludes}), {base: './node_modules'})
        .pipe(rename((path) => (path.dirname = path.dirname.replace(/\/dist/, '').replace(/\\dist/, ''))))
        .pipe(gulp.dest('./assets/vendor'));
}

exports.clean = gulp.series(clean);
exports.vendor = gulp.series(vendor);
exports.scripts = gulp.series(scripts);
exports.styles = gulp.series(styles);
exports.dev = gulp.series(clean, vendor, scripts, styles, watch);
exports.build = gulp.series(clean, vendor, scripts, styles, archive);
exports.default = exports.dev;
