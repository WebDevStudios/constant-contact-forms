/**
 * Webpack Config
 *
 * @package ConstantContactForms
 */
const pluginConfig = require( './plugin-config' );
const isProduction = 'production' === process.env.NODE_ENV;
const host = isProduction ? pluginConfig.localURL : pluginConfig.watchURL;

const config = {
	mode: isProduction ? 'production' : 'development',
	entry: {
		'ctct-plugin-admin': [ './assets/js/ctct-plugin-admin/index.js' ],
		'ctct-plugin-gutenberg': [ './assets/js/ctct-plugin-gutenberg/index.js' ],
		'ctct-plugin-frontend': [ '@babel/polyfill', './assets/js/ctct-plugin-frontend/index.js' ]
	},
	output: {
		filename: isProduction ? '[name].min.js' : '[name].js',
		publicPath: host + pluginConfig.publicJS
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
	devtool: isProduction ? 'source-map' : 'cheap-module-eval-source-map',
	externals: {
		$: 'jQuery',
		jQuery: 'jQuery',
		jquery: 'jQuery'
	}
};

module.exports = config;