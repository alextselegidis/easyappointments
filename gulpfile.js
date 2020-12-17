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
const fs = require('fs-extra');
const zip = require('zip-dir');
const plugins = require('gulp-load-plugins')();
const {execSync} = require('child_process');
const del = require('del');

// Gulp error handling.
const source = gulp.src;
gulp.src = function () {
    return source.apply(gulp, arguments)
        .pipe(plugins.plumber({
            errorHandler: plugins.notify.onError('Error: <%= error.message %>')
        }));
};

gulp.task('package', (done) => {
    const archive = 'easyappointments-0.0.0.zip';

    fs.removeSync('build');
    fs.removeSync(archive);

    fs.mkdirsSync('build');
    fs.copySync('application', 'build/application');
    fs.copySync('assets', 'build/assets');
    fs.copySync('engine', 'build/engine');

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

    execSync('cd build && composer install --no-interaction --no-dev --no-scripts --optimize-autoloader', function (err, stdout, stderr) {
        console.log(stdout);
        console.log(stderr);
    });

    del.sync('**/.DS_Store');

    fs.removeSync('build/composer.lock');

    del.sync('**/.DS_Store');

    del.sync('build/vendor/codeigniter/framework/user_guide');

    zip('build', {saveTo: archive}, function (err) {
        if (err) {
            console.log('Zip Error', err);
        }

        done();
    });
});

gulp.task('clean', (done) => {
    fs.removeSync('assets/js/**/*.min.js');
    fs.removeSync('assets/css/**/*.min.css');
    done();
});

gulp.task('docs', (done) => {
    fs.removeSync('docs/apigen/html');
    fs.removeSync('docs/jsdoc/html');
    fs.removeSync('docs/plato/html');

    fs.mkdirSync('docs/apigen/html');
    fs.mkdirSync('docs/jsdoc/html');
    fs.mkdirSync('docs/plato/html');

    const commands = [
        'php docs/apigen/apigen.phar generate ' +
        '-s "application/controllers,application/models,application/libraries" ' +
        '-d "docs/apigen/html" --exclude "*external*" --tree --todo --template-theme "bootstrap"',

        'npx jsdoc "assets/js" -d "docs/jsdoc/html"',

        'npx plato -r -d "docs/plato/html" "assets/js"'
    ];

    commands.forEach(function (command) {
        execSync(command, function (err, stdout, stderr) {
            console.log(stdout);
            console.log(stderr);
        });
    });

    done();
});

gulp.task('scripts', (done) => {
    return gulp.src([
        'assets/js/**/*.js',
        '!assets/js/**/*.min.js'
    ])
        .pipe(plugins.changed('assets/js/**/*'))
        .pipe(plugins.uglify().on('error', console.log))
        .pipe(plugins.rename({suffix: '.min'}))
        .pipe(gulp.dest('assets/js'));
});

gulp.task('styles', () => {
    return gulp.src([
        'assets/css/**/*.css',
        '!assets/css/**/*.min.css'
    ])
        .pipe(plugins.changed('assets/css/**/*'))
        .pipe(plugins.cleanCss())
        .pipe(plugins.rename({suffix: '.min'}))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('watch', (done) => {
    gulp.watch([
        'assets/js/**/*.js',
        '!assets/js/**/*.min.js'
    ], gulp.parallel('scripts'));

    gulp.watch([
        'assets/css/**/*.css',
        '!assets/css/**/*.min.css'
    ], gulp.parallel('styles'));

    done();
});

gulp.task('dev', gulp.series('clean', 'scripts', 'styles', 'watch'));

gulp.task('build', gulp.series('clean', 'scripts', 'styles', 'package'));

gulp.task('default', gulp.parallel('dev'));
