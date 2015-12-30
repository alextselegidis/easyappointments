var gulp = require('gulp'),
    exec = require('child_process').execSync,
    del = require('del'),
    fs = require('fs-extra'),
    zip = require('zip-dir');

/**
 * Install and copy the required files from the "composer" directory.
 *
 * Composer needs to be installed and configured in order for this command to
 * work properly.
 */
gulp.task('composer', function() {
    del.sync([
        './composer',
        './src/application/third_party/**/*',
        '!./src/application/third_party/index.html',
    ]);

    exec('composer install --no-dev --prefer-dist', function (err, stdout, stderr) {
        console.log(stdout);
        console.log(stderr);
    });

    return gulp.src([
        'composer/**/*',
        '!composer/**/demo{,/**}',
        '!composer/**/{demo,docs,examples,test,extras,language}{,/**}',
        '!composer/**/{composer.json,composer.lock,.gitignore}',
        '!composer/**/{*.yml,*.md}'
    ])
        .pipe(gulp.dest('./src/application/third_party/'));
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

    zip('.tmp-package', { saveTo: 'easyappointments.zip' }, function (err, buffer) {
        if (err)
            console.log('Zip Error', err);

        done();
    });
});

/**
 * Generate code documentation.
 */
gulp.task('doc', function() {

});
