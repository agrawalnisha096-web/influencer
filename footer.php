<footer class="of-footer">
	<div class="of-footer-inner">
		<div class="of-footer-links">
			<?php of_render_nav_links( get_the_ID() ); ?>
		</div>
		<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> The Influencers Network</span>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
