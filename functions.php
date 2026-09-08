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

/**
 * 301 redirects for the OnlyFans content that moved to its own subdomain
 * (best.theinfluencersnetwork.com), to avoid duplicate content between
 * the old main-domain URLs and the new subdomain copies.
 *
 * Hooked at template_redirect priority 1 — earlier than WordPress's own
 * redirect_canonical() (priority 10) — so every URL variant (including
 * ones that would otherwise 404, like /onlyfans/) redirects in a single
 * hop straight to its final destination, rather than chaining through
 * WordPress's own canonical redirect first.
 *
 * wp_redirect() (not wp_safe_redirect()) is deliberate here: the targets
 * are hardcoded by us, not derived from user input, and wp_safe_redirect()
 * would otherwise block or alter a redirect to a different host.
 */
add_action( 'template_redirect', function () {
	$map = array(
		'/onlyfans'                           => 'https://best.theinfluencersnetwork.com/',
		'/onlyfans/'                          => 'https://best.theinfluencersnetwork.com/',
		'/onlyfans/top-onlyfans-influencers/' => 'https://best.theinfluencersnetwork.com/top-onlyfans-influencers/',
		'/onlyfans/latina-onlyfans/'          => 'https://best.theinfluencersnetwork.com/latina-onlyfans-influencers/',
		'/onlyfans/celebrity-onlyfans/'       => 'https://best.theinfluencersnetwork.com/celebrity-onlyfans-influencers/',
		'/best-asian-onlyfans-influencers/'   => 'https://best.theinfluencersnetwork.com/asian-onlyfans-influencers/',
		'/best-onlyfans-agencies/'            => 'https://best.theinfluencersnetwork.com/onlyfans-agencies/',
		'/onlyfans/submit-profile/'           => 'https://best.theinfluencersnetwork.com/submit-profile/',
		'/submit-profile/'                    => 'https://best.theinfluencersnetwork.com/submit-profile/',
	);

	$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

	if ( isset( $map[ $path ] ) ) {
		wp_redirect( $map[ $path ], 301 );
		exit;
	}
}, 1 );
