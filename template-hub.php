<?php
/**
 * Template Name: Directory Hub
 *
 * The single hub page linking out to every category page. Assign this
 * template to exactly one page — the site's main directory index.
 */
get_header();
?>
<main class="of-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<h1><?php the_title(); ?></h1>
		<?php the_content(); ?>
	<?php endwhile; ?>

	<div class="of-hub-grid">
		<?php foreach ( of_get_cluster_pages() as $p ) :
			if ( $p->of_is_hub ) {
				continue;
			}
			?>
			<a class="of-hub-card" href="<?php echo esc_url( get_permalink( $p ) ); ?>">
				<h3><?php echo esc_html( get_the_title( $p ) ); ?></h3>
				<p><?php echo esc_html( wp_trim_words( get_the_excerpt( $p ), 16 ) ); ?></p>
			</a>
		<?php endforeach; ?>
	</div>
</main>
<?php get_footer(); ?>
