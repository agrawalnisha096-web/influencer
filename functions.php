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
	wp_enqueue_style(
		'of-directory-fonts',
		'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap',
		array(),
		null
	);
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
			$p->of_is_hub    = ( 'template-hub.php' === $template );
			$p->of_is_submit = ( 'submit-profile' === $p->post_name );
			$pages[]         = $p;
		}
	}

	// Hub first, then submit-profile last, categories alphabetical in between.
	usort( $pages, function ( $a, $b ) {
		if ( $a->of_is_hub !== $b->of_is_hub ) {
			return $a->of_is_hub ? -1 : 1;
		}
		if ( $a->of_is_submit !== $b->of_is_submit ) {
			return $a->of_is_submit ? 1 : -1;
		}
		return strcmp( $a->post_title, $b->post_title );
	} );

	return $pages;
}

/**
 * The tab-style links in the main nav bar and footer "Rankings" column —
 * every cluster page except the hub (which is the logo/home link) and
 * submit-profile (which gets its own button, matching the original design).
 */
function of_get_category_pages() {
	return array_values( array_filter( of_get_cluster_pages(), function ( $p ) {
		return ! $p->of_is_hub && ! $p->of_is_submit;
	} ) );
}

function of_get_hub_page() {
	foreach ( of_get_cluster_pages() as $p ) {
		if ( $p->of_is_hub ) {
			return $p;
		}
	}
	return null;
}

function of_get_submit_page() {
	foreach ( of_get_cluster_pages() as $p ) {
		if ( $p->of_is_submit ) {
			return $p;
		}
	}
	return null;
}

function of_render_nav_links( $current_id ) {
	foreach ( of_get_category_pages() as $p ) {
		$active = ( $p->ID === $current_id ) ? ' of-active' : '';
		printf(
			'<a class="of-nav-link%s" href="%s">%s</a>',
			$active,
			esc_url( get_permalink( $p ) ),
			esc_html( get_the_title( $p ) )
		);
	}
}

function of_render_footer_links( $current_id ) {
	foreach ( of_get_category_pages() as $p ) {
		printf(
			'<li><a href="%s">%s</a></li>',
			esc_url( get_permalink( $p ) ),
			esc_html( get_the_title( $p ) )
		);
	}
}
