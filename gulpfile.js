var runSequence = require('run-sequence'),
    compass = require('gulp-compass'),
    minifycss = require('gulp-minify-css'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    concat = require('gulp-concat'),
    livereload = require('gulp-livereload'),
    path = require('path'),
    gulp = require('gulp'),
    git = require('gulp-git'),
    gutil = require('gulp-util'),
    shell = require('gulp-shell');


gulp.task('deploy', function (cb) {
    git.pull('origin', 'laravel', {args: '--rebase'}, function (err) {
        if (err) throw err;
        cb(err);
    });

});

//styles
gulp.task('styles', ['scripts'], function () {
    return gulp.src(['resources/assets/sass/**/*.scss'])
        .pipe(compass({
            css: 'public/css',
            sass: 'resources/assets/sass/'
        }))
        .pipe(gulp.dest('public/css'))
        .pipe(rename({suffix: '.min'}))
        .pipe(minifycss())
        .pipe(gulp.dest('public/css'));
});

//scripts
gulp.task('scripts', function () {
    return gulp.src('resources/assets/js/**/*.js')
        .pipe(concat('app.js'))
        .pipe(gulp.dest('public/js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('public/js'));
});

gulp.task('serve', shell.task('php artisan serve'));

//watch
gulp.task('watcher', function () {
    livereload.listen();

    //watch .scss files
    gulp.watch('resources/assets/sass/**/*.scss', ['styles']);

    //watch .js files
    gulp.watch('resources/assets/js/**/*.js', ['scripts']);
    gutil.log(gutil.colors.green('You may now change SASS and JS files!'));

});

gulp.task('default', function() {
    runSequence("deploy", ["styles", "scripts"]);
});

gulp.task('live', function() {
    runSequence(["watcher", "serve"]);
});