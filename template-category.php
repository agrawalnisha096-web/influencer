<?php
/**
 * Template Name: Category Directory
 *
 * Used for every profile-listing page (asian, latina, celebrity, top, agencies, ...).
 * Automatically picked up by of_get_cluster_pages() for nav — no manual linking needed.
 *
 * No <h1> is rendered here: the migrated content for these pages already
 * carries its own <h1 class="ptitle">, so the template deliberately leaves
 * page-title output out to avoid a duplicate H1.
 */
get_header();
?>
<main class="of-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
