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
 * Single source of truth for the OnlyFans content that moved to its own
 * subdomain (best.theinfluencersnetwork.com): old main-domain path => new
 * subdomain URL. Both the redirect hook and the sitemap-exclusion filter
 * below read from this one map, so adding a URL here automatically
 * redirects it AND drops it from the XML sitemap — nothing to remember
 * to update in two places.
 */
function bloghash_child_moved_to_subdomain_map() {
	return array(
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
}

/**
 * 301 redirects for the moved OnlyFans content, to avoid duplicate content
 * between the old main-domain URLs and the new subdomain copies.
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
	$map  = bloghash_child_moved_to_subdomain_map();
	$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

	if ( isset( $map[ $path ] ) ) {
		wp_redirect( $map[ $path ], 301 );
		exit;
	}
}, 1 );

/**
 * Drop the same moved pages out of Yoast's XML sitemap — they 301 away on
 * every visit (including search-engine crawls), so listing them in the
 * sitemap only invites Google to keep crawling a redirect chain instead of
 * the real subdomain URL.
 */
add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', function ( $excluded_ids ) {
	foreach ( array_keys( bloghash_child_moved_to_subdomain_map() ) as $path ) {
		$slug = trim( $path, '/' );
		if ( '' === $slug ) {
			continue;
		}
		$post = get_page_by_path( $slug );
		if ( $post ) {
			$excluded_ids[] = $post->ID;
		}
	}
	return $excluded_ids;
} );
