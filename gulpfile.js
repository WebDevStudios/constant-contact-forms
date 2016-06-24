var gulp = require('gulp');

var autoprefixer = require('autoprefixer');
var cssnano = require('gulp-cssnano');
var del = require('del');
var gulp = require('gulp');
var concat = require('gulp-concat');
var gutil = require('gulp-util');
var mqpacker = require('css-mqpacker');
var notify = require('gulp-notify');
var plumber = require('gulp-plumber');
var postcss = require('gulp-postcss');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var sort = require('gulp-sort');
var uglify = require('gulp-uglify');
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
 * Concatenate javascripts after they're clobbered.
 * https://www.npmjs.com/package/gulp-concat
 */
gulp.task('concat', function() {
	return gulp.src( 'assets/js/concat/*.js' )
	.pipe(plumber({ errorHandler: handleErrors }))
	.pipe(sourcemaps.init())
	.pipe(concat('plugin.js'))
	.pipe(sourcemaps.write())
	.pipe(gulp.dest('assets/js'));
});


 /**
  * Minify javascripts after they're concatenated.
  * https://www.npmjs.com/package/gulp-uglify
  */
gulp.task('uglify', ['concat'], function() {
	return gulp.src( 'assets/js/plugin.js' )
	.pipe(rename({suffix: '.min'}))
	.pipe(uglify({
		mangle: false
	}))
	.pipe(gulp.dest('assets/js'));
});

/**
* Delete style.css and style.min.css before we minify and optimize
*/
gulp.task('clean:styles', function() {
	return del(['assets/css/style.css', 'assets/css/style.min.css'])
});

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

/**
* Run stylesheet through PostCSS.
*
* https://www.npmjs.com/package/gulp-postcss
* https://www.npmjs.com/package/gulp-autoprefixer
* https://www.npmjs.com/package/css-mqpacker
*/
gulp.task('postcss', ['sass'], function() {
	return gulp.src('assets/css/style.css')

	// Wrap tasks in a sourcemap.
	.pipe(sourcemaps.init())

		// Deal with errors.
		.pipe(plumber({ errorHandler: handleErrors }))

		// Parse with PostCSS plugins.
		.pipe(postcss([
			autoprefixer({
				browsers: ['last 2 version']
			}),
			mqpacker({
				sort: true
			}),
		]))

	// Create sourcemap.
	.pipe(sourcemaps.write())

	// Create style.css.
	.pipe(gulp.dest('./assets/css'))
});

/**
* Minify and optimize style.css.
*
* https://www.npmjs.com/package/gulp-cssnano
*/
gulp.task('cssnano', ['postcss'], function() {
	return gulp.src('assets/css/style.css')

	// handle any errors
	.pipe(plumber({ errorHandler: handleErrors }))

	.pipe(cssnano({
		safe: true // Use safe optimizations
	}))

	// rename file from style.css to style.min.css
	.pipe(rename('style.min.css'))

	.pipe(gulp.dest('./assets/css'))
});

/**
* Define default Gulp watch task
*/
gulp.task('watch', function() {
	gulp.watch('./assets/sass/**/*.scss', ['cssnano']);
	gulp.watch('./assets/js/concat/*.js', ['uglify']);
});

/**
* Create individual tasks.
*/
gulp.task('scripts', ['uglify']);
gulp.task('styles', ['cssnano']);
gulp.task('default', ['styles']);
