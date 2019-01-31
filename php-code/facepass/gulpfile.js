'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var babel = require('gulp-babel');
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');
var watch = require('gulp-watch');


// список файлов для сборки JS
// добавляем в порядке зависимости друг от друга 1 > 2 > 3 ...
var watchJs = [
    "./public/js/main/index.js",
    "./public/js/configs/cameras.js"
];

gulp.task('js', function() {
   return gulp.src(watchJs)
       .pipe(sourcemaps.init())
       .pipe(babel({
           presets: ['es2015', 'babili']
       }))
       .pipe(concat('build.js'))
       .pipe(gulp.dest('./public/js'))
       .pipe(sourcemaps.write('./map'))
       .pipe(gulp.dest('./public/js'));
});

gulp.task('sass', function () {
    return gulp.src('./public/scss/**/*.scss')
        .pipe(sass.sync().on('error', sass.logError))
        .pipe(gulp.dest('./public/css'));
});

gulp.task('sass:watch', function () {
    gulp.watch('./public/scss/**/*.scss', ['sass']);
});

gulp.task('js:watch', function () {
    gulp.watch('./public/js/*.js', ['js']);
    gulp.watch('./public/js/**/*.js', ['js']);
});

gulp.task('frontend', function() {
    gulp.watch('./public/js/*.js', ['js']);
    gulp.watch('./public/js/**/*.js', ['js']);
    gulp.watch('./public/scss/**/*.scss', ['sass']);
});

// TODO запускаем gulp frontend в консоле для сборки фронтенда