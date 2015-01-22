var compass = require('gulp-compass'),
    minifycss = require('gulp-minify-css'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    concat = require('gulp-concat'),
    livereload = require('gulp-livereload'),
    path = require('path'),
    gulp = require('gulp'),
    git = require('gulp-git'),
    gutil = require('gulp-util');


gulp.task('deploy', function () {
    git.pull('origin', 'laravel', {args: '--rebase'}, function (err) {
        if (err) throw err;
    });

});

//styles
gulp.task('styles', ['deploy', 'scripts'],  function () {
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
gulp.task('scripts', ['deploy'],  function () {
    return gulp.src('resources/assets/js/**/*.js')
        .pipe(concat('app.js'))
        .pipe(gulp.dest('public/js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('public/js'));
});

//watch
gulp.task('live', function () {
    livereload.listen();

//watch .scss files
    gulp.watch('resources/assets/sass/**/*.scss', ['styles']);

//watch .js files
    gulp.watch('resources/assets/js/**/*.js', ['scripts']);

});

gulp.task('default', ['styles']);