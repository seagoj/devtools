var gulp = require('gulp');
var phpspec = require('gulp-phpspec');
var run = require('gulp-run');
var notify = require('gulp-notify');

gulp.task('test', function() {
    gulp.src('spec/**/*.php')
        .pipe(phpspec( '', { clear: true, notify: true } ))
        .on('error', notify.onError({
            title: 'Dangit',
            message: 'Your tests failed!',
            icon: __dirname + '/fail.png'
        }))
        .pipe(notify({
            title: 'Success',
            message: 'All tests have returned green!'
        }));
});

gulp.task('watch', function() {
    gulp.watch(['spec/**/*.php', '*.php'], ['test']);
});

gulp.task('default', ['test', 'watch']);
