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
        '.tmp-package',
        'easyappointments.zip'
    ]);

    fs.copySync('src', '.tmp-package');
    fs.copySync('.tmp-package/config-sample.php', '.tmp-package/config.php');
    fs.removeSync('.tmp-package/config-sample.php');
    fs.copySync('CHANGELOG.md', '.tmp-package/CHANGELOG.md');
    fs.copySync('README.md', '.tmp-package/README.md');
    fs.copySync('LICENSE', '.tmp-package/LICENSE');

    del.sync([
        '.tmp-package/storage/uploads/*',
        '!.tmp-package/storage/uploads/index.html'
    ]);

    del.sync([
        '.tmp-package/storage/logs/*',
        '!.tmp-package/storage/logs/index.html'
    ]);

    del.sync([
        '.tmp-package/storage/sessions/*',
        '!.tmp-package/storage/sessions/.htaccess',
        '!.tmp-package/storage/sessions/index.html'
    ]);

    del.sync([
        '.tmp-package/storage/cache/*',
        '!.tmp-package/storage/cache/.htaccess',
        '!.tmp-package/storage/cache/index.html'
    ]);

    zip('.tmp-package', { saveTo: 'easyappointments.zip' }, function (err, buffer) {
        if (err)
            console.log('Zip Error', err);

        done();
    });
});

/**
 * Generate code documentation.
 */
gulp.task('doc', function(done) {
    fs.removeSync('doc/apigen');
    fs.mkdirSync('doc/apigen');
    fs.removeSync('doc/jsdoc');
    fs.mkdirSync('doc/jsdoc');
    fs.removeSync('doc/plato');
    fs.mkdirSync('doc/plato');

    const commands = [
        'php rsc/apigen.phar generate ' +
            '-s "src/application/controllers,src/application/models,src/application/libraries" ' +
            '-d "doc/apigen" --exclude "*external*" --tree --todo --template-theme "bootstrap"',

        path.join('.', 'node_modules', '.bin', 'jsdoc') + ' "src/assets/js" -d "doc/jsdoc"',

        path.join('.', 'node_modules', '.bin', 'plato') + ' -r -d "doc/plato" "src/assets/js"'
    ];

    commands.forEach(function(command) {
        exec(command, function (err, stdout, stderr) {
            console.log(stdout);
            console.log(stderr);
        });
    });

    done();
});
