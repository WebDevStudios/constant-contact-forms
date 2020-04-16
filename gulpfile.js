const autoprefixer = require('autoprefixer');
const browserSync = require('browser-sync');
const cssnano = require('cssnano');
const del = require('del');
const fs = require('fs'); // node file system manipulation
const gulp = require('gulp');
const gutil = require('gulp-util');
const mqpacker = require('css-mqpacker');
const notify = require('gulp-notify');
const path = require('path'); // node path module
const plumber = require('gulp-plumber');
const postcss = require('gulp-postcss');
const rename = require('gulp-rename');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const webpack = require('webpack');
const webpackStream = require('webpack-stream');
const wpPot = require('gulp-wp-pot');

const pluginConfig = require('./plugin-config');
const webpackConfig = require( './webpack.config' );

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
* Read directory name for filename
*/
function getFolders(dir) {
	return fs.readdirSync(dir)
		.filter(function(file) {
			return fs.statSync(path.join(dir, file)).isDirectory();
		});
}

/**
 * Use webpack to transpile and bundle scripts.
 */
gulp.task('scripts', function(done) {
	gulp.src( [
		'./assets/js/ctct-plugin-frontend/index.js',
		'./assets/js/ctct-plugin-gutenberg/index.js',
		'./assets/js/ctct-plugin-admin/index.js',
		'./assets/js/ctct-plugin-recaptcha/index.js',
		'./assets/js/ctct-plugin-recaptcha-v2/index.js'
	] )
		.pipe( webpackStream( webpackConfig ), webpack )
		.pipe( gulp.dest( './assets/js' ) );
	done();
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
gulp.task('postcss', gulp.series('sass', function() {
	return gulp.src('assets/css/style.css')

	// Wrap tasks in a sourcemap.
	.pipe(sourcemaps.init())

		// Deal with errors.
		.pipe(plumber({ errorHandler: handleErrors }))

		// Parse with PostCSS plugins.
		.pipe(postcss([
			autoprefixer(),
			mqpacker({
				sort: true
			}),
			cssnano(),
		]))

	// Create sourcemap.
	.pipe(sourcemaps.write())

	// Create style.css.
	.pipe(gulp.dest('./assets/css'))
}));

/**
* Minify and optimize style.css.
*
* https://www.npmjs.com/package/cssnano
*/
gulp.task('cssnano', gulp.series('postcss', function(done) {
	gulp.src('assets/css/style.css')

	// handle any errors
	.pipe(plumber({ errorHandler: handleErrors }))

	// rename file from style.css to style.min.css
	.pipe(rename('style.min.css'))

	.pipe(gulp.dest('./assets/css'));

	done();
}));

/**
* Define default Gulp watch task
*/
gulp.task('watch', function() {

	browserSync( {
		open: false,
		injectChanges: true,
		proxy: pluginConfig.localURL
	});

	gulp.watch( './assets/sass/**/*.scss', gulp.series('cssnano') );
	gulp.watch( './assets/js/ctct-plugin-admin/**/*.js', gulp.series('scripts') );
	gulp.watch( './assets/js/ctct-plugin-gutenberg/**/*.js', gulp.series('scripts') );
	gulp.watch( './assets/js/ctct-plugin-frontend/**/*.js', gulp.series('scripts') );
	gulp.watch( './assets/js/ctct-plugin-recaptcha/**/*.js', gulp.series('scripts') );
	gulp.watch( './assets/js/ctct-plugin-recaptcha-v2/**/*.js', gulp.series('scripts') );
	gulp.watch( './assets/sass/**/*.scss' ).on( 'change', browserSync.reload );
	gulp.watch( './assets/js/ctct-plugin-admin/**/*.js' ).on( 'change', browserSync.reload );
	gulp.watch( './assets/js/ctct-plugin-gutenberg/**/*.js' ).on( 'change', browserSync.reload );
	gulp.watch( './assets/js/ctct-plugin-frontend/**/*.js' ).on( 'change', browserSync.reload );
	gulp.watch( './assets/js/ctct-plugin-recaptcha/**/*.js').on('change', browserSync.reload );
	gulp.watch( './assets/js/ctct-plugin-recaptcha-v2/**/*.js').on('change', browserSync.reload );
});

gulp.task('makepot', function() {
    return gulp.src( [ './constant-contact-forms.php', './includes/*.php' ] ).pipe( wpPot({
		domain: 'constant-contact-forms',
		package: 'Constant Contact Forms'
	}) ).pipe( gulp.dest( './languages/constant-contact-forms.pot' ) );
});

/**
* Create individual tasks.
*/
gulp.task('js', gulp.series('scripts'));
gulp.task('styles', gulp.series('cssnano'));
gulp.task('default', gulp.parallel('styles', 'js', 'makepot' ));
