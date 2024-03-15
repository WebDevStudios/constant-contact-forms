/**
 * Webpack Config
 *
 * @package ConstantContactForms
 */
const pluginConfig = require( './plugin-config' );
const isProduction = 'production' === process.env.NODE_ENV;
const host = isProduction ? pluginConfig.localURL : pluginConfig.watchURL;
const defaultConfig = require('@wordpress/scripts/config/webpack.config.js');

const config = {
	mode: isProduction ? 'production' : 'development',
	entry: {
		'ctct-plugin-admin': [ './src/ctct-plugin-admin/index.js' ],
		'ctct-plugin-gutenberg': [ './src/ctct-plugin-gutenberg/index.js' ],
		'ctct-plugin-frontend': [ './src/ctct-plugin-frontend/index.js' ],
		'ctct-plugin-recaptcha': [ './src/ctct-plugin-recaptcha/index.js' ],
		'ctct-plugin-recaptcha-v2': [ './src/ctct-plugin-recaptcha-v2/index.js' ]
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
	devtool: isProduction ? 'source-map' : 'eval-cheap-module-source-map',
	externals: {
		$: 'jQuery',
		jQuery: 'jQuery',
		jquery: 'jQuery',
		lodash: 'lodash'
	}
};

module.exports = {
	...defaultConfig,
	...config
}
