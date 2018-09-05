var gulp = require('gulp'),
sass = require('gulp-sass'),
uglify = require('gulp-uglify'),
cleanCSS = require('gulp-clean-css'),
concat = require('gulp-concat'),
sourcemaps = require('gulp-sourcemaps'),
modernizr = require('gulp-modernizr'),
browserSync = require('browser-sync').create(),
plumber = require('gulp-plumber'),
rename = require('gulp-rename'),
notify = require('gulp-notify');

// Styles Task
// Uglifies
gulp.task('styles', function() {
	gulp.src('./src/scss/**/**.scss')
	.pipe(plumber({errorHandler: function(err){
		notify.onError({
			title: "Gulp Error in" + err.plugin,
			message: err.toString()
		})(err)
	}}))
	.pipe(sourcemaps.init())
	.pipe(sass({outputStyle: 'nested'}))
	.pipe(gulp.dest('./assets/css'));
	
});

// Task Task
// Watches
gulp.task('watch', function() {
	gulp.watch('assets/sass/**/**.scss', ['sass']);
});

// Default Task
// Gulp
gulp.task('default', ['styles', 'watch']);
