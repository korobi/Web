var gulp = require('gulp');
var git = require('gulp-git');
var gutil = require('gulp-util');


gulp.task('deploy', function(){
  git.pull('origin', 'laravel', {args: '--rebase'}, function (err) {
    if (err) throw err;
  });
});
