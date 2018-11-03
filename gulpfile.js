const gulp = require('gulp');
const exec = require('child_process').execSync;
const del = require('del');
const fs = require('fs-extra');
const path = require('path');
const zip = require('zip-dir');

/**
 * Install and copy the required files from the "composer_modules" directory.
 *
 * Composer needs to be installed and configured in order for this command to
 * work properly.
 */
gulp.task('composer', function() {
    del.sync([
        './src/vendor/**/*',
        '!./src/vendor/index.html'
    ]);

    return gulp.src([
        'vendor/**/*',
        '!vendor/**/demo{,/**}',
        '!vendor/**/{demo,docs,examples,test,tests,extras,language}{,/**}',
        '!vendor/**/{composer.json,composer.lock,.gitignore}',
        '!vendor/**/{*.yml,*.md,*phpunit*,*.mdown}',
        '!vendor/bin{,/**}',
        '!vendor/codeigniter{,/**}',
        '!vendor/doctrine{,/**}',
        '!vendor/myclabs{,/**}',
        '!vendor/phpdocumentor{,/**}',
        '!vendor/phpspec{,/**}',
        '!vendor/phpunit{,/**}',
        '!vendor/sebastian{,/**}',
        '!vendor/symfony{,/**}',
        '!vendor/webmozart{,/**}'
    ])
        .pipe(gulp.dest('./src/vendor/'));
});

/**
 * Build the project and create an easyappointments.zip file ready for distribution.
 */
gulp.task('build', function(done) {
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

    zip('build', { saveTo: 'easyappointments-0.0.0.zip' }, function (err, buffer) {
        if (err)
            console.log('Zip Error', err);

        done();
    });
});

/**
 * Generate code documentation.
 */
gulp.task('doc', function(done) {
    fs.removeSync('doc/apigen/html');
    fs.mkdirSync('doc/apigen/html');
    fs.removeSync('doc/jsdoc/html');
    fs.mkdirSync('doc/jsdoc/html');
    fs.removeSync('doc/plato/html');
    fs.mkdirSync('doc/plato/html');

    const commands = [
        'php doc/apigen/apigen.phar generate ' +
            '-s "src/application/controllers,src/application/models,src/application/libraries" ' +
            '-d "doc/apigen/html" --exclude "*external*" --tree --todo --template-theme "bootstrap"',

        path.join('.', 'node_modules', '.bin', 'jsdoc') + ' "src/assets/js" -d "doc/jsdoc/html"',

        path.join('.', 'node_modules', '.bin', 'plato') + ' -r -d "doc/plato/html" "src/assets/js"'
    ];

    commands.forEach(function(command) {
        exec(command, function (err, stdout, stderr) {
            console.log(stdout);
            console.log(stderr);
        });
    });

    done();
});
