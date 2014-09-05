var gulp = require('gulp');
var phpspec = require('gulp-phpspec');
var run = require('gulp-run');
var notify = require('gulp-notify');

gulp.task('test', function() {
    gulp.src('spec/**/*.php')
        .pipe(phpspec( '', {
            clear: true,
            notify: true,
            verbose: 'v'
        }))
        .on('error', notify.onError({
            title: 'PHP: FAILED!',
            message: 'Your tests have failed!',
            icon: __dirname + '/spec/failed.jpg'
        }))
        .pipe(notify({
            title: 'PHP: Success',
            message: 'All tests have returned green!',
            icon: __dirname + '/spec/success.jpg'
        }));
});

gulp.task('watch', function() {
    gulp.watch(['spec/**/*.php', '*.php'], ['test']);
});

gulp.task('default', ['test', 'watch']);
