<?php
/**
 * Site header — nav is auto-generated from of_get_cluster_pages(), never hand-maintained.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<nav class="of-nav" aria-label="Site navigation">
	<div class="of-nav-inner">
		<a class="of-nav-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">The Influencers Network · OnlyFans</a>
		<div class="of-nav-links">
			<?php of_render_nav_links( get_the_ID() ); ?>
		</div>
	</div>
</nav>
