var autoprefixer = require('autoprefixer');
var cssnano = require('gulp-cssnano');
var del = require('del');
var gulp = require('gulp');
var gutil = require('gulp-util');
var mqpacker = require('css-mqpacker');
var notify = require('gulp-notify');
var plumber = require('gulp-plumber');
var postcss = require('gulp-postcss');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var sort = require('gulp-sort');
var sourcemaps = require('gulp-sourcemaps');

/**
 * Handle errors and alert the user.
 */
function handleErrors () {
	var args = Array.prototype.slice.call(arguments);

	notify.onError({
		title: 'Task Failed [<%= error.message %>',
		message: 'See console.',
		sound: 'Sosumi' // See: https://github.com/mikaelbr/node-notifier#all-notification-options-with-their-defaults
	}).apply(this, args);

	gutil.beep(); // Beep 'sosumi' again

	// Prevent the 'watch' task from stopping
	this.emit('end');
}
/**
 * Compile Sass
 *
 * https://www.npmjs.com/package/gulp-sass
 */
gulp.task('sass', function() {
	return gulp.src('assets/sass/*.scss')
	
	// Deal with errors.
	.pipe(plumber({ errorHandler: handleErrors }))

	// Compile Sass using LibSass.
	.pipe(sass({
		outputStyle: 'expanded' // Options: nested, expanded, compact, compressed
	}))

	// Create style.css.
	.pipe(gulp.dest('./assets/css'))
});
