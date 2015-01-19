var gulp = require('gulp');
var git = require('gulp-git');


gulp.task('pull', function(){
  git.pull('origin', 'master', {args: '--rebase'}, function (err) {
    if (err) throw err;
  });
});
