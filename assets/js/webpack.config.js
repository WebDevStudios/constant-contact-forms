/**
 * Webpack Config
 *
 * @package ConstantContactForms
 */
const pluginConfig = require( '../../plugin-config' );
const isProduction = 'production' === process.env.NODE_ENV;
const host = isProduction ? pluginConfig.localURL : pluginConfig.watchURL;

const sharedConfig = {
	mode: isProduction ? 'production' : 'development',
	entry: {
		'ctct-plugin-admin': [ './assets/js/ctct-plugin-admin/index.js' ],
		'ctct-plugin-attached-lists': ['./assets/js/ctct-plugin-attached-lists/index.js'],
		'ctct-plugin-frontend': [ './assets/js/ctct-plugin-frontend/index.js' ],
		'ctct-plugin-recaptcha': [ './assets/js/ctct-plugin-recaptcha/index.js' ],
		'ctct-plugin-recaptcha-v2': [ './assets/js/ctct-plugin-recaptcha-v2/index.js' ],
		'ctct-plugin-hcaptcha': [ './assets/js/ctct-plugin-hcaptcha/index.js' ],
		'ctct-plugin-turnstile': [ './assets/js/ctct-plugin-turnstile/index.js' ],
	},
	module: {
		rules: [
			{
				test: /\.jsx?$/,
				exclude: /(node_modules)/,
				use: {
					loader: 'babel-loader',
					options: {
						presets: [
							[
								'@babel/preset-env',
								{
									'targets': {
										'browsers': [ 'last 2 versions', 'ie 11' ]
									}
								}
							],
							'@babel/preset-react'
						]
					}
				}
			}
		]
	},
	plugins: [],
	devtool: isProduction ? 'source-map' : 'eval-cheap-module-source-map',
	externals: {
		$: 'jQuery',
		jQuery: 'jQuery',
		jquery: 'jQuery',
		lodash: 'lodash'
	}
};

// Unminified build. PHP falls back to this filename whenever SCRIPT_DEBUG is enabled.
const unminifiedConfig = {
	...sharedConfig,
	output: {
		filename: './[name].js',
		publicPath: host + pluginConfig.publicJS
	},
	optimization: {
		minimize: false
	}
};

// Minified build, used by PHP whenever SCRIPT_DEBUG is off (the default).
const minifiedConfig = {
	...sharedConfig,
	output: {
		filename: './[name].min.js',
		publicPath: host + pluginConfig.publicJS
	},
	optimization: {
		minimize: true
	}
};

// Dev only needs the unminified build. The release build needs both filenames
// to exist for every entry, since PHP picks between them based on SCRIPT_DEBUG
// regardless of how the plugin was built.
module.exports = isProduction ? [ unminifiedConfig, minifiedConfig ] : unminifiedConfig;
