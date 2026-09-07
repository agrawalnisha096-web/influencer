<?php
/**
 * SEO metadata system.
 *
 * Every page — existing or newly published — gets a correct <title>,
 * meta description, Open Graph + Twitter Card tags, and JSON-LD structured
 * data automatically. Nothing here is per-page manual setup: descriptions
 * are auto-generated from the page's own content unless a manual excerpt
 * is set, and the JSON-LD ItemList on the hub is built from the same
 * page-registry function (of_get_category_pages()) that drives navigation,
 * so it can never drift out of sync with what's actually published.
 */

/**
 * Clean, plain-text excerpt for meta descriptions. Strips embedded
 * <style>/<script> blocks first — this content has plenty of both — so
 * an auto-generated description never picks up CSS or JS as "text."
 */
function of_generate_description( $post_id, $length = 155 ) {
	$content = get_post_field( 'post_content', $post_id );
	$content = preg_replace( '#<style[^>]*>.*?</style>#is', '', $content );
	$content = preg_replace( '#<script[^>]*>.*?</script>#is', '', $content );
	$text    = wp_strip_all_tags( $content );
	$text    = preg_replace( '/\s+/', ' ', trim( $text ) );

	if ( strlen( $text ) <= $length ) {
		return $text;
	}
	$text = substr( $text, 0, $length );
	$cut  = strrpos( $text, ' ' );
	return ( false !== $cut ? substr( $text, 0, $cut ) : $text ) . '…';
}

/**
 * A manually-set excerpt always wins; otherwise auto-generate one.
 */
function of_get_meta_description( $post_id ) {
	$excerpt = get_post_field( 'post_excerpt', $post_id );
	if ( $excerpt ) {
		return wp_strip_all_tags( $excerpt );
	}
	return of_generate_description( $post_id );
}

/**
 * Correct, page-specific <title> everywhere, including the front page —
 * which otherwise falls back to just the bare site title with no
 * indication of what the page actually is.
 */
add_filter( 'document_title_parts', function ( $parts ) {
	if ( is_singular() ) {
		$parts['title'] = get_the_title();
	}
	return $parts;
} );

/**
 * Meta description, Open Graph, Twitter Card, and JSON-LD — one hook,
 * every page, automatically.
 */
add_action( 'wp_head', function () {
	if ( ! is_singular() ) {
		return;
	}

	$post_id     = get_the_ID();
	$title       = get_the_title( $post_id );
	$description = of_get_meta_description( $post_id );
	$url         = get_permalink( $post_id );
	$site_name   = get_bloginfo( 'name' );
	$of_hub      = function_exists( 'of_get_hub_page' ) ? of_get_hub_page() : null;
	$is_hub      = $of_hub && (int) $of_hub->ID === (int) $post_id;

	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );

	printf( '<meta property="og:type" content="website">' . "\n" );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $site_name ) );

	printf( '<meta name="twitter:card" content="summary">' . "\n" );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $description ) );

	// Breadcrumb: Home -> Hub -> current page (or just Home -> current on the hub itself).
	$breadcrumb_items   = array();
	$breadcrumb_items[] = array(
		'@type'    => 'ListItem',
		'position' => 1,
		'name'     => 'Home',
		'item'     => home_url( '/' ),
	);
	if ( $of_hub && ! $is_hub ) {
		$breadcrumb_items[] = array(
			'@type'    => 'ListItem',
			'position' => 2,
			'name'     => get_the_title( $of_hub ),
			'item'     => get_permalink( $of_hub ),
		);
		$breadcrumb_items[] = array(
			'@type'    => 'ListItem',
			'position' => 3,
			'name'     => $title,
			'item'     => $url,
		);
	} else {
		$breadcrumb_items[] = array(
			'@type'    => 'ListItem',
			'position' => 2,
			'name'     => $title,
			'item'     => $url,
		);
	}

	$graph = array(
		array(
			'@type'       => 'WebPage',
			'@id'         => $url,
			'url'         => $url,
			'name'        => $title,
			'description' => $description,
			'inLanguage'  => 'en-US',
			'breadcrumb'  => array(
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $breadcrumb_items,
			),
		),
	);

	// ItemList on the hub page only — built from the live page registry,
	// so it always matches what's actually in the nav, automatically.
	if ( $is_hub && function_exists( 'of_get_category_pages' ) ) {
		$items = array();
		$pos   = 1;
		foreach ( of_get_category_pages() as $p ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $pos++,
				'name'     => get_the_title( $p ),
				'url'      => get_permalink( $p ),
			);
		}
		$graph[] = array(
			'@type'           => 'ItemList',
			'name'            => $title,
			'description'     => $description,
			'url'             => $url,
			'itemListElement' => $items,
		);
	}

	echo '<script type="application/ld+json">' . wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_SLASHES
	) . '</script>' . "\n";
} );

/**
 * Lazy-load every content image automatically. Runs at render time via
 * the_content filter rather than touching stored post content, so it
 * applies retroactively to already-published pages too, not just new
 * ones going forward.
 */
add_filter( 'the_content', function ( $content ) {
	return preg_replace_callback(
		'/<img\s+(?![^>]*\bloading=)([^>]*)>/i',
		function ( $m ) {
			return '<img loading="lazy" ' . $m[1] . '>';
		},
		$content
	);
}, 20 );
