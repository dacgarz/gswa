var gulp = require('gulp');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');
var pump = require('pump');
var concat = require('gulp-concat');
var concatCss = require('gulp-concat-css');
var cleanCSS = require('gulp-clean-css');

gulp.task('scss', function() {
  gulp.src([
    './bower_components/jquery-selectric/public/selectric.css',
    './sass/global.scss'
  ])
    .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
    .pipe(concatCss('global.css'))
    .pipe(cleanCSS({compatibility: 'ie8'}))
    .pipe(gulp.dest('./compiled/css'));
});


gulp.task('js', function(cb) {
  pump([
      gulp.src([
        './bower_components/jquery-selectric/public/jquery.selectric.min.js',
        './bower_components/matchHeight/jquery.matchHeight.js',
        './js/*.js'
      ]),
      uglify(),
      concat('global.js'),
      gulp.dest('./compiled/js')
    ],
    cb
  );
});

gulp.task('default', function() {
  gulp.run("scss");
  gulp.run("js");
});

gulp.task('watch', function() {
  gulp.watch('./sass/**/*.scss', function(event) {
    gulp.run('scss');
  });
});