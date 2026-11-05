<?php
/**
 * Site header.
 *
 * @package Aurora_Portfolio
 */

$aurora_profile = aurora_get_profile();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
	<script>document.documentElement.classList.remove('no-js');document.documentElement.classList.add('js');</script>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'aurora-portfolio' ); ?></a>

<?php get_template_part( 'template-parts/sprite' ); ?>

<div class="scroll-progress" aria-hidden="true"><span id="scroll-progress-bar"></span></div>

<?php if ( is_front_page() ) : ?>
	<div class="preloader" id="preloader" aria-hidden="true">
		<div class="preloader__inner">
			<span class="logo-mark logo-mark--lg" aria-hidden="true"><?php echo aurora_icon( 'sparkle' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<span class="preloader__name">IMRAN<span>.DEV</span></span>
			<span class="preloader__track"><span></span></span>
			<span class="preloader__label"><?php esc_html_e( 'Designing the experience…', 'aurora-portfolio' ); ?></span>
		</div>
	</div>
<?php endif; ?>

<header class="site-header" id="site-header">
	<div class="container">
		<nav class="nav-shell" aria-label="<?php esc_attr_e( 'Primary', 'aurora-portfolio' ); ?>">
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person name. */ __( '%s — back to top', 'aurora-portfolio' ), $aurora_profile['full_name'] ) ); ?>">
				<span class="logo-mark" aria-hidden="true"><?php echo aurora_icon( 'sparkle' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="brand__name"><?php echo wp_kses_post( aurora_logo_text_html() ); ?></span>
			</a>

			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav-links',
					'menu_id'        => 'primary-menu',
					'fallback_cb'    => 'aurora_primary_menu_fallback',
					'depth'          => 1,
				)
			);
			?>

			<div class="nav-actions">
				<a class="btn btn--primary btn--sm nav-cta" href="<?php echo esc_url( aurora_section_url( 'contact' ) ); ?>">
					<span><?php esc_html_e( 'Hire me', 'aurora-portfolio' ); ?></span>
					<?php echo aurora_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<button class="nav-toggle" id="nav-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="<?php esc_attr_e( 'Open menu', 'aurora-portfolio' ); ?>">
					<span></span><span></span><span></span>
				</button>
			</div>
		</nav>
	</div>
</header>

<div class="mobile-menu" id="mobile-menu" aria-hidden="true">
	<div class="mobile-menu__backdrop" data-menu-close></div>
	<div class="mobile-menu__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Mobile navigation', 'aurora-portfolio' ); ?>">
		<div class="mobile-menu__top">
			<span class="brand brand--static">
				<span class="logo-mark" aria-hidden="true"><?php echo aurora_icon( 'sparkle' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="brand__name"><?php echo wp_kses_post( aurora_logo_text_html() ); ?></span>
			</span>
			<button class="menu-close" type="button" data-menu-close aria-label="<?php esc_attr_e( 'Close menu', 'aurora-portfolio' ); ?>">
				<?php echo aurora_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>

		<nav aria-label="<?php esc_attr_e( 'Mobile', 'aurora-portfolio' ); ?>">
			<ul class="mobile-links">
				<?php
				$aurora_mobile_links = array(
					'home'         => __( 'Home', 'aurora-portfolio' ),
					'about'        => __( 'About', 'aurora-portfolio' ),
					'services'     => __( 'Services', 'aurora-portfolio' ),
					'skills'       => __( 'Skills', 'aurora-portfolio' ),
					'projects'     => __( 'Projects', 'aurora-portfolio' ),
					'process'      => __( 'Process', 'aurora-portfolio' ),
					'why-me'       => __( 'Why me', 'aurora-portfolio' ),
					'testimonials' => __( 'Testimonials', 'aurora-portfolio' ),
					'contact'      => __( 'Contact', 'aurora-portfolio' ),
				);
				$aurora_index = 1;
				foreach ( $aurora_mobile_links as $aurora_key => $aurora_label ) :
					?>
					<li>
						<a href="<?php echo esc_url( aurora_section_url( $aurora_key ) ); ?>">
							<span><?php echo esc_html( str_pad( (string) $aurora_index, 2, '0', STR_PAD_LEFT ) ); ?></span><?php echo esc_html( $aurora_label ); ?>
						</a>
					</li>
					<?php
					$aurora_index++;
				endforeach;
				?>
			</ul>
		</nav>

		<div class="mobile-menu__footer">
			<a class="btn btn--primary" href="<?php echo esc_url( aurora_section_url( 'contact' ) ); ?>">
				<?php esc_html_e( 'Start a project', 'aurora-portfolio' ); ?>
				<?php echo aurora_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
			<div class="socials">
				<?php foreach ( aurora_profile_social_links() as $aurora_social ) : ?>
					<a class="social-link" href="<?php echo esc_url( $aurora_social['url'] ); ?>"<?php echo ( 0 === strpos( $aurora_social['url'], 'http' ) ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?> aria-label="<?php echo esc_attr( $aurora_social['label'] ); ?>">
						<?php echo aurora_icon( $aurora_social['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
