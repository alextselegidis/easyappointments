var gulp = require('gulp'),
    exec = require('child_process').execSync,
    del = require('del');

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
        '!composer/**/*.yml',
        '!composer/**/*.md'
    ])
        .pipe(gulp.dest('./src/application/third_party/'));
});

/**
 * Build the project and create an easyappointments.zip file ready for distribution.
 */
gulp.task('build', function() {

});

/**
 * Generate code documentation.
 */
gulp.task('doc', function() {

});
