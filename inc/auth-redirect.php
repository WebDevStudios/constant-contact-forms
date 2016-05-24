<?php

function constantcontact_rewrite_add_var( $vars ) {
	$vars[] = 'constantcontact';
    $vars[] = 'code';
    $vars[] = 'username';
	return $vars;
}
add_filter( 'query_vars', 'constantcontact_rewrite_add_var' );

function add_analytic_rewrite_rule() {

	add_rewrite_tag( '%constantcontact%', '([^&]+)' );

	add_rewrite_rule(
		'^constantcontact/([^/]*)/?',
		'index.php?constantcontact=$matches[1]',
		'top'
	);
}
add_action( 'init', 'add_analytic_rewrite_rule' );
add_action( 'template_redirect', 'constantcontact_rewrite_catch' );


function constantcontact_rewrite_catch() {
	global $wp_query;
    //var_dump( $_SERVER );
	if ( 'constantcontact' === $wp_query->query_vars['name'] ) {
		echo 'token: ' . $wp_query->query_vars['code'];
		echo '</br>user: ' . $wp_query->query_vars['username'];
        // $path = add_query_arg(
        //
        // );
        // wp_safe_redirect( admin_url( $path ) );
		exit;
	}
}
