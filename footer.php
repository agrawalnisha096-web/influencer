<?php
$of_hub    = of_get_hub_page();
$of_submit = of_get_submit_page();
?>
<footer class="of-footer" aria-label="OnlyFans directory footer">
	<div class="of-footer-inner">

		<div class="of-footer-brand">
			<div class="of-footer-logo">TIN <span>&middot;</span> OnlyFans</div>
			<p class="of-footer-desc">The most comprehensive OnlyFans influencer directory. Every ranking built from verified profile data &mdash; no paid placements, no guesswork.</p>
			<span class="of-footer-badge">Updated monthly &middot; <?php echo esc_html( gmdate( 'F Y' ) ); ?></span>
		</div>

		<div>
			<div class="of-footer-col-title">Rankings</div>
			<ul class="of-footer-links">
				<?php of_render_footer_links( get_the_ID() ); ?>
			</ul>
		</div>

		<div>
			<div class="of-footer-col-title">Directory</div>
			<ul class="of-footer-links">
				<?php if ( $of_hub ) : ?>
					<li><a href="<?php echo esc_url( get_permalink( $of_hub ) ); ?>">Hub</a></li>
				<?php endif; ?>
				<?php if ( $of_submit ) : ?>
					<li><a href="<?php echo esc_url( get_permalink( $of_submit ) ); ?>">Submit a Profile</a></li>
				<?php endif; ?>
				<li><a href="https://theinfluencersnetwork.com">Main Site</a></li>
			</ul>
		</div>

	</div>

	<div class="of-footer-bottom">
		<p class="of-footer-disclaimer">Independently operated. Not affiliated with OnlyFans or Fenix International Limited. All stats sourced from public OnlyFans profile data and re-verified monthly. All featured creators are verified adults 18+. No paid placements in ranked positions.</p>
		<span class="of-footer-copy">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> theinfluencersnetwork.com</span>
	</div>
</footer>

<script>
const ofProg = document.getElementById('ofProgress');
if (ofProg) {
	window.addEventListener('scroll', () => {
		const pct = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
		ofProg.style.width = Math.min(pct, 100) + '%';
	}, {passive: true});
}
</script>

<?php wp_footer(); ?>
</body>
</html>
