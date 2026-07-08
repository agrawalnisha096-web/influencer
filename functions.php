<?php
/**
 * BlogHash Child theme functions.
 */

add_action( 'wp_enqueue_scripts', 'bloghash_child_enqueue_styles' );

function bloghash_child_enqueue_styles() {
style.css wp_enqueue_style(
style.css style.css 'bloghash-child-styles',
style.css style.css get_stylesheet_directory_uri() . '/style.css',
style.css style.css array( 'bloghash-styles' ),
style.css style.css wp_get_theme()->get( 'Version' )
style.css );
}
