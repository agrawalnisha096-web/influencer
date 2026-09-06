<?php
/**
 * OF Directory theme functions.
 */

add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );

/**
 * Every page on this site is fully-formed HTML (embedded <style>/<script>
 * blocks included) migrated as-is, never plain prose meant for automatic
 * paragraph breaks. WordPress's default wpautop filter doesn't know about
 * <style>/<script> context and injects <p>/<br> tags into the middle of
 * CSS rules and JS statements, corrupting both. Remove it globally — no
 * page here relies on it.
 */
remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_excerpt', 'wpautop' );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'of-directory-style', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
} );

/**
 * Returns every published page using either the hub or category directory
 * template, ordered hub-first then by menu_order/title. This is the single
 * source of truth for site navigation — add a new page with one of these
 * templates and it appears in the nav automatically. No page can go
 * unlinked again because someone forgot to edit a nav array by hand.
 */
function of_get_cluster_pages() {
	static $pages = null;
	if ( null !== $pages ) {
		return $pages;
	}

	$templates = array( 'template-hub.php', 'template-category.php' );
	$pages     = array();

	foreach ( $templates as $template ) {
		$found = get_pages( array(
			'meta_key'    => '_wp_page_template',
			'meta_value'  => $template,
			'post_status' => 'publish',
			'sort_column' => 'menu_order,post_title',
		) );
		foreach ( $found as $p ) {
			$p->of_is_hub = ( 'template-hub.php' === $template );
			$pages[]      = $p;
		}
	}

	// Hub page(s) first, then categories in menu_order/title order.
	usort( $pages, function ( $a, $b ) {
		if ( $a->of_is_hub !== $b->of_is_hub ) {
			return $a->of_is_hub ? -1 : 1;
		}
		return strcmp( $a->post_title, $b->post_title );
	} );

	return $pages;
}

function of_render_nav_links( $current_id ) {
	foreach ( of_get_cluster_pages() as $p ) {
		$active = ( $p->ID === $current_id ) ? ' is-active' : '';
		printf(
			'<a class="of-nav-link%s" href="%s">%s</a>',
			$active,
			esc_url( get_permalink( $p ) ),
			esc_html( $p->of_is_hub ? 'Directory' : get_the_title( $p ) )
		);
	}
}
