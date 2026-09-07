<?php
/**
 * Site header — nav is auto-generated from of_get_cluster_pages(), never hand-maintained.
 * Visual design (logo, sticky blurred nav, progress bar, submit button) carried
 * over from the original cluster template; the link list itself is generated,
 * not hardcoded, so a new page can never go unlinked again.
 */
$of_hub    = of_get_hub_page();
$of_submit = of_get_submit_page();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/favicon-32.png' ); ?>" sizes="32x32">
<link rel="icon" href="https://theinfluencersnetwork.com/wp-content/uploads/2025/10/cropped-cropped-2-192x192.png" sizes="192x192">
<link rel="apple-touch-icon" href="https://theinfluencersnetwork.com/wp-content/uploads/2025/10/cropped-cropped-2-180x180.png">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="of-progress" id="ofProgress"></div>

<nav class="of-nav" aria-label="Site navigation">
	<div class="of-nav-inner">
		<a href="<?php echo esc_url( $of_hub ? get_permalink( $of_hub ) : home_url( '/' ) ); ?>" class="of-nav-logo" aria-label="OnlyFans Influencers Directory">
			<span>TIN</span>
			<span class="of-dot">·</span>
			<span>OnlyFans</span>
			<span class="of-site-name">by theinfluencersnetwork.com</span>
		</a>

		<div class="of-nav-links" role="list">
			<?php of_render_nav_links( get_the_ID() ); ?>
		</div>

		<div class="of-nav-actions">
			<a href="https://theinfluencersnetwork.com" class="of-nav-home" title="Main site">&larr; Main site</a>
			<?php if ( $of_submit ) : ?>
				<a href="<?php echo esc_url( get_permalink( $of_submit ) ); ?>" class="of-nav-submit">Submit profile</a>
			<?php endif; ?>
		</div>
	</div>
</nav>
