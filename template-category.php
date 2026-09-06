<?php
/**
 * Template Name: Category Directory
 *
 * Used for every profile-listing page (asian, latina, celebrity, top, agencies, ...).
 * Automatically picked up by of_get_cluster_pages() for nav — no manual linking needed.
 */
get_header();
?>
<main class="of-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<h1><?php the_title(); ?></h1>
		<?php the_content(); ?>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
