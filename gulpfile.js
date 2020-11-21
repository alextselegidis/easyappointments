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

// Gulp error handling.
const source = gulp.src;
gulp.src = function () {
    return source.apply(gulp, arguments)
        .pipe(plugins.plumber({
            errorHandler: plugins.notify.onError('Error: <%= error.message %>')
        }));
};

gulp.task('build', (done) => {
    const archive = 'easyappointments-0.0.0.zip';

    fs.removeSync('build');
    fs.removeSync(archive);

    fs.mkdirsSync('build');

    fs.copySync('application', 'build/application');
    fs.copySync('assets', 'build/assets');
    fs.copySync('engine', 'build/engine');
    fs.copySync('storage', 'build/storage');
    fs.copySync('index.php', 'build/index.php');
    fs.copySync('composer.json', 'build/composer.json');
    fs.copySync('composer.lock', 'build/composer.lock');
    fs.copySync('config-sample.php', 'build/config-sample.php');
    fs.copySync('CHANGELOG.md', 'build/CHANGELOG.md');
    fs.copySync('README.md', 'build/README.md');
    fs.copySync('LICENSE', 'build/LICENSE');

    execSync('cd build && composer install --no-interaction --no-scripts --optimize-autoloader', function (err, stdout, stderr) {
        console.log(stdout);
        console.log(stderr);
    });

    fs.removeSync('build/composer.json');
    fs.removeSync('build/composer.lock');
    fs.removeSync('build/storage/uploads/*');
    fs.removeSync('!build/storage/uploads/index.html');
    fs.removeSync('build/storage/logs/*');
    fs.removeSync('!build/storage/logs/index.html');
    fs.removeSync('build/storage/sessions/*');
    fs.removeSync('!build/storage/sessions/.htaccess');
    fs.removeSync('!build/storage/sessions/index.html');
    fs.removeSync('build/storage/cache/*');
    fs.removeSync('!build/storage/cache/.htaccess');
    fs.removeSync('!build/storage/cache/index.html');

    zip('build', {saveTo: archive}, function (err) {
        if (err)
            console.log('Zip Error', err);

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
    gulp.src([
        'assets/js/**/*.js',
        '!assets/js/**/*.min.js'
    ])
        .pipe(plugins.changed('assets/js/**/*'))
        .pipe(plugins.uglify().on('error', console.log))
        .pipe(plugins.rename({suffix: '.min'}))
        .pipe(gulp.dest('assets/js'));

    done();
});

gulp.task('styles', (done) => {
    gulp.src([
        'assets/css/**/*.css',
        '!assets/css/**/*.min.css'
    ])
        .pipe(plugins.changed('assets/css/**/*'))
        .pipe(plugins.cleanCss())
        .pipe(plugins.rename({suffix: '.min'}))
        .pipe(gulp.dest('assets/css'));

    done();
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

gulp.task('default', gulp.parallel('dev'));
