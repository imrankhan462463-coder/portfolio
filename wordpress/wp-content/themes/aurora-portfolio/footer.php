<?php
/**
 * Site footer.
 *
 * @package Aurora_Portfolio
 */

$aurora_profile = aurora_get_profile();
?>
<footer class="site-footer" id="footer">
	<div class="container">
		<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) ) : ?>
			<div class="footer__widgets widget-area">
				<?php for ( $aurora_i = 1; $aurora_i <= 3; $aurora_i++ ) : ?>
					<?php if ( is_active_sidebar( 'footer-' . $aurora_i ) ) : ?>
						<?php dynamic_sidebar( 'footer-' . $aurora_i ); ?>
					<?php endif; ?>
				<?php endfor; ?>
			</div>
		<?php endif; ?>

		<div class="footer__top">
			<div class="footer__brand">
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person name. */ __( '%s — back to top', 'aurora-portfolio' ), $aurora_profile['full_name'] ) ); ?>">
					<span class="logo-mark" aria-hidden="true"><?php echo aurora_icon( 'sparkle' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span class="brand__name"><?php echo wp_kses_post( aurora_logo_text_html() ); ?></span>
				</a>
				<p><?php echo esc_html( aurora_mod( 'footer_description' ) ); ?></p>
				<span class="footer__status"><i></i><span data-profile="availability"><?php echo esc_html( $aurora_profile['availability'] ); ?></span></span>
				<div class="socials" aria-label="<?php esc_attr_e( 'Contact and social profiles', 'aurora-portfolio' ); ?>">
					<?php foreach ( aurora_profile_social_links() as $aurora_social ) : ?>
						<a class="social-link" href="<?php echo esc_url( $aurora_social['url'] ); ?>"<?php echo ( 0 === strpos( $aurora_social['url'], 'http' ) ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?> aria-label="<?php echo esc_attr( $aurora_social['label'] ); ?>">
							<?php echo aurora_icon( $aurora_social['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>

			<nav class="footer__nav" aria-label="<?php esc_attr_e( 'Footer navigation', 'aurora-portfolio' ); ?>">
				<div>
					<h2 class="footer__heading"><?php esc_html_e( 'Navigate', 'aurora-portfolio' ); ?></h2>
					<ul>
						<li><a href="<?php echo esc_url( aurora_section_url( 'home' ) ); ?>"><?php esc_html_e( 'Home', 'aurora-portfolio' ); ?></a></li>
						<li><a href="<?php echo esc_url( aurora_section_url( 'about' ) ); ?>"><?php esc_html_e( 'About', 'aurora-portfolio' ); ?></a></li>
						<li><a href="<?php echo esc_url( aurora_section_url( 'services' ) ); ?>"><?php esc_html_e( 'Services', 'aurora-portfolio' ); ?></a></li>
						<li><a href="<?php echo esc_url( aurora_section_url( 'skills' ) ); ?>"><?php esc_html_e( 'Skills', 'aurora-portfolio' ); ?></a></li>
						<li><a href="<?php echo esc_url( aurora_section_url( 'projects' ) ); ?>"><?php esc_html_e( 'Projects', 'aurora-portfolio' ); ?></a></li>
					</ul>
				</div>
				<div>
					<h2 class="footer__heading"><?php esc_html_e( 'Explore', 'aurora-portfolio' ); ?></h2>
					<ul>
						<li><a href="<?php echo esc_url( aurora_section_url( 'why-me' ) ); ?>"><?php esc_html_e( 'Why me', 'aurora-portfolio' ); ?></a></li>
						<li><a href="<?php echo esc_url( aurora_section_url( 'process' ) ); ?>"><?php esc_html_e( 'Process', 'aurora-portfolio' ); ?></a></li>
						<li><a href="<?php echo esc_url( aurora_section_url( 'testimonials' ) ); ?>"><?php esc_html_e( 'Testimonials', 'aurora-portfolio' ); ?></a></li>
						<li><a href="<?php echo esc_url( aurora_section_url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact', 'aurora-portfolio' ); ?></a></li>
						<?php if ( $aurora_profile['resume'] ) : ?>
							<li><a href="<?php echo esc_url( $aurora_profile['resume'] ); ?>" download><?php esc_html_e( 'Download CV', 'aurora-portfolio' ); ?></a></li>
						<?php endif; ?>
					</ul>
				</div>
				<div>
					<h2 class="footer__heading"><?php esc_html_e( 'Connect', 'aurora-portfolio' ); ?></h2>
					<ul>
						<?php if ( $aurora_profile['email'] ) : ?>
							<li><a href="<?php echo esc_url( 'mailto:' . $aurora_profile['email'] ); ?>"><?php echo esc_html( $aurora_profile['email'] ); ?></a></li>
						<?php endif; ?>
						<?php if ( $aurora_profile['phone'] ) : ?>
							<li><a href="<?php echo esc_url( aurora_phone_url() ); ?>"><?php echo esc_html( $aurora_profile['phone'] ); ?></a></li>
						<?php endif; ?>
						<?php if ( $aurora_profile['whatsapp_url'] ) : ?>
							<li><a href="<?php echo esc_url( $aurora_profile['whatsapp_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'WhatsApp', 'aurora-portfolio' ); ?></a></li>
						<?php endif; ?>
						<?php if ( $aurora_profile['linkedin'] ) : ?>
							<li><a href="<?php echo esc_url( $aurora_profile['linkedin'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'LinkedIn', 'aurora-portfolio' ); ?></a></li>
						<?php endif; ?>
						<li><a href="<?php echo esc_url( aurora_section_url( 'contact' ) ); ?>"><?php esc_html_e( 'Start a project', 'aurora-portfolio' ); ?></a></li>
					</ul>
				</div>
			</nav>
		</div>

		<div class="footer__wordmark" aria-hidden="true">IMRAN</div>

		<div class="footer__bottom">
			<p>© <span id="year"><?php echo esc_html( current_time( 'Y' ) ); ?></span> <?php echo esc_html( $aurora_profile['full_name'] ); ?>. <?php esc_html_e( 'All rights reserved.', 'aurora-portfolio' ); ?></p>
			<p class="footer__built"><?php echo wp_kses_post( aurora_mod( 'footer_note' ) ); ?></p>
			<a class="footer__back-top" href="<?php echo esc_url( home_url( '/#home' ) ); ?>"><?php esc_html_e( 'Back to top', 'aurora-portfolio' ); ?> <?php echo aurora_icon( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		</div>
	</div>
</footer>

<button class="back-to-top" id="back-to-top" type="button" aria-label="<?php esc_attr_e( 'Back to top', 'aurora-portfolio' ); ?>">
	<svg class="back-to-top__ring" viewBox="0 0 48 48" aria-hidden="true">
		<circle cx="24" cy="24" r="21" class="back-to-top__track"></circle>
		<circle cx="24" cy="24" r="21" class="back-to-top__value" id="back-to-top-value"></circle>
	</svg>
	<?php echo aurora_icon( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</button>

<div class="toast" id="toast" role="status" aria-live="polite" aria-hidden="true">
	<span class="toast__icon"><?php echo aurora_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
	<span class="toast__message"></span>
</div>

<noscript>
	<style>
		.preloader{display:none!important}
		[data-reveal]{opacity:1!important;translate:none!important}
		.nav-toggle{display:none!important}
	</style>
</noscript>

<?php wp_footer(); ?>
</body>
</html>
