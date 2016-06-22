<?php
/**
 * ConstantContact_Admin_Pages class
 *
 * @package ConstantContactProcessForm
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Class ConstantContact_Admin_Pages
 */
class ConstantContact_Admin_Pages {

	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.0.1
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Parse multidemntional args
	 *
	 * Borrowed from: http://mekshq.com/recursive-wp-parse-args-wordpress-function/
	 *
	 * @internal
	 * @since 1.0.0
	 * @param  array $args args to parse.
	 * @param  array $defaults default array.
	 * @return array the parsed array
	 */
	function parse_multidimensional_array_args( &$args, $defaults ) {

		$args = (array) $args;
		$result = $defaults = (array) $defaults;

		foreach ( $args as $key => &$value ) {
			if ( is_array( $value ) && isset( $result[ $key ] ) ) {
				$result[ $key ] = $this->parse_multidimensional_array_args( $value, $result[ $key ] );
			} else {
				$result[ $key ] = $value;
			}
		}
		return $result;
	}
}

