<?php
/**
 * BlogHash Child theme functions.
 */

add_action( 'wp_enqueue_scripts', 'bloghash_child_enqueue_styles' );

function bloghash_child_enqueue_styles() {
	wp_enqueue_style(
		'bloghash-child-styles',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'bloghash-styles' ),
		wp_get_theme()->get( 'Version' )
	);
}
