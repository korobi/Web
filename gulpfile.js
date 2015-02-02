var elixir = require('laravel-elixir'),
    runSequence = require('run-sequence'),
    minifycss = require('gulp-minify-css'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    concat = require('gulp-concat'),
    livereload = require('gulp-livereload'),
    path = require('path'),
    gulp = require('gulp'),
    git = require('gulp-git'),
    gutil = require('gulp-util'),
    phpunit = require('gulp-phpunit'),
    shell = require('gulp-shell');


gulp.task('deploy', function (cb) {
    git.pull('origin', 'laravel', {args: '--rebase'}, function (err) {
        if (err) throw err;
        cb(err);
    });

});

// styles
gulp.task('styles', ['scripts'], function () {

});

// scripts
gulp.task('scripts', function () {
    return gulp.src('resources/assets/js/**/*.js')
        .pipe(concat('app.js'))
        .pipe(gulp.dest('public/assets/js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('public/assets/js'));
});

gulp.task('serve', shell.task('php artisan serve'));

// tests
gulp.task('tests', function() {
    gulp.src('phpunit.xml').pipe(phpunit());
});

// watch
gulp.task('watcher', function () {
    livereload.listen();

    // watch .scss files
    gulp.watch('resources/assets/sass/**/*.scss', ['styles']);

    // watch .js files
    gulp.watch('resources/assets/js/**/*.js', ['scripts']);
    gutil.log(gutil.colors.green('You may now change SASS and JS files!'));

});

gulp.task('default', function() {
    runSequence("deploy", ["styles", "scripts"]);
});

gulp.task('live', function() {
    runSequence(["watcher", "serve"]);
});
