/**
 * Webpack Config
 *
 * @package ConstantContactForms
 */
const pluginConfig = require( '../../plugin-config' );
const isProduction = 'production' === process.env.NODE_ENV;
const host = isProduction ? pluginConfig.localURL : pluginConfig.watchURL;
const defaultConfig = require('@wordpress/scripts/config/webpack.config.js');

const config = {
	mode: isProduction ? 'production' : 'development',
	entry: {
		'ctct-plugin-admin': [ './assets/js/ctct-plugin-admin/index.js' ],
		'ctct-plugin-frontend': [ './assets/js/ctct-plugin-frontend/index.js' ],
		'ctct-plugin-recaptcha': [ './assets/js/ctct-plugin-recaptcha/index.js' ],
		'ctct-plugin-recaptcha-v2': [ './assets/js/ctct-plugin-recaptcha-v2/index.js' ],
		'ctct-plugin-hcaptcha': [ './assets/js/ctct-plugin-hcaptcha/index.js' ],
	},
	output: {
		filename: isProduction ? './[name].min.js' : './[name].js',
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
module.exports = config;
