<?php
/**
 * Hero section.
 *
 * @package Aurora_Portfolio
 */

$profile      = aurora_get_profile();
$roles        = array_map( 'trim', explode( ',', (string) aurora_mod( 'hero_typed_roles' ) ) );
$roles        = array_values( array_filter( $roles, 'strlen' ) );
$primary_url  = (string) aurora_mod( 'hero_primary_url' );
$secondary_url = (string) aurora_mod( 'hero_secondary_url' );
$marquee      = aurora_get_marquee();
?>
<section class="hero" id="home" aria-label="<?php esc_attr_e( 'Introduction', 'aurora-portfolio' ); ?>">
	<div class="hero__backdrop" aria-hidden="true">
		<span class="orb orb--violet"></span>
		<span class="orb orb--cyan"></span>
		<span class="orb orb--blue"></span>
		<span class="hero__grid"></span>
	</div>

	<div class="container hero__inner">
		<div class="hero__content">
			<p class="availability" data-reveal>
				<span class="availability__dot" aria-hidden="true"></span>
				<span data-profile="availability"><?php echo esc_html( $profile['availability'] ); ?></span>
				<span class="availability__sep" aria-hidden="true"></span>
				<span class="availability__muted" data-profile="location"><?php echo esc_html( $profile['location'] ); ?></span>
			</p>

			<h1 class="hero__title" data-reveal data-reveal-delay="60" data-profile-hero="hero_heading">
				<?php echo wp_kses_post( aurora_hero_heading_html( aurora_mod( 'hero_heading' ) ) ); ?>
			</h1>

			<p class="hero__typed" data-reveal data-reveal-delay="120">
				<span><?php esc_html_e( "I'm a", 'aurora-portfolio' ); ?></span>
				<span class="typed-wrap">
					<span class="typed-text" id="typed-role" data-roles="<?php echo esc_attr( wp_json_encode( $roles ) ); ?>"><?php echo esc_html( $roles ? $roles[0] : '' ); ?></span><span class="typed-caret" aria-hidden="true"></span>
				</span>
			</p>

			<p class="hero__subtitle" data-reveal data-reveal-delay="160" data-profile="hero_description">
				<?php echo esc_html( aurora_mod( 'hero_subtitle' ) ); ?>
			</p>

			<div class="hero__actions" data-reveal data-reveal-delay="220">
				<a class="btn btn--primary" href="<?php echo esc_url( $primary_url ); ?>">
					<span><?php echo esc_html( aurora_mod( 'hero_primary_label' ) ); ?></span>
					<?php echo aurora_icon( 'arrow-down' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<a class="btn btn--glass" href="<?php echo esc_url( $secondary_url ); ?>">
					<span><?php echo esc_html( aurora_mod( 'hero_secondary_label' ) ); ?></span>
					<?php echo aurora_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<?php if ( $profile['resume'] ) : ?>
					<a class="btn btn--ghost" href="<?php echo esc_url( $profile['resume'] ); ?>" download data-profile-link="resume">
						<span><?php esc_html_e( 'Download CV', 'aurora-portfolio' ); ?></span>
						<?php echo aurora_icon( 'download' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				<?php endif; ?>
			</div>

			<div class="hero__meta" data-reveal data-reveal-delay="280">
				<div class="socials" aria-label="<?php esc_attr_e( 'Contact and social profiles', 'aurora-portfolio' ); ?>">
					<?php foreach ( aurora_profile_social_links() as $social ) : ?>
						<a class="social-link" href="<?php echo esc_url( $social['url'] ); ?>"<?php echo ( 0 === strpos( $social['url'], 'http' ) ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?> aria-label="<?php echo esc_attr( $social['label'] ); ?>" data-profile-link="<?php echo esc_attr( $social['key'] ); ?>">
							<?php echo aurora_icon( $social['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endforeach; ?>
				</div>
				<span class="hero__meta-line" aria-hidden="true"></span>
				<span class="hero__meta-note"><?php echo esc_html( aurora_mod( 'hero_note' ) ); ?></span>
			</div>

			<div class="hero__stats" data-reveal data-reveal-delay="340">
				<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
					<?php if ( $i > 1 ) : ?>
						<span class="stat__divider" aria-hidden="true"></span>
					<?php endif; ?>
					<div class="stat">
						<span class="stat__value">
							<span class="counter" data-target="<?php echo esc_attr( absint( aurora_mod( 'hero_stat_' . $i . '_value' ) ) ); ?>">0</span>
							<?php if ( aurora_mod( 'hero_stat_' . $i . '_suffix' ) ) : ?>
								<span class="stat__suffix"><?php echo esc_html( aurora_mod( 'hero_stat_' . $i . '_suffix' ) ); ?></span>
							<?php endif; ?>
						</span>
						<span class="stat__label"><?php echo esc_html( aurora_mod( 'hero_stat_' . $i . '_label' ) ); ?></span>
					</div>
				<?php endfor; ?>
			</div>
		</div>

		<div class="hero__visual" data-reveal data-reveal-delay="200">
			<div class="hero-scene hero-scene--profile" id="hero-scene">
				<span class="hero-scene__glow" aria-hidden="true"></span>
				<span class="hero-scene__ring hero-scene__ring--one" aria-hidden="true"></span>
				<span class="hero-scene__ring hero-scene__ring--two" aria-hidden="true"></span>
				<span class="hero-scene__dot hero-scene__dot--one" aria-hidden="true"></span>
				<span class="hero-scene__dot hero-scene__dot--two" aria-hidden="true"></span>

				<figure class="profile-card">
					<div class="profile-card__media">
						<img src="<?php echo esc_url( $profile['profile_image'] ); ?>" alt="<?php echo esc_attr( sprintf( /* translators: %s: person name. */ __( 'Portrait of %s, Web Designer and WordPress Developer', 'aurora-portfolio' ), $profile['full_name'] ) ); ?>" width="912" height="1168" fetchpriority="high" decoding="async" data-profile-image>
						<span class="profile-card__shade" aria-hidden="true"></span>
						<span class="profile-card__status"><i aria-hidden="true"></i><span data-profile="availability"><?php echo esc_html( $profile['availability'] ); ?></span></span>
						<span class="profile-card__mark" aria-hidden="true">IH</span>
					</div>
					<figcaption class="profile-card__body">
						<p class="profile-card__eyebrow" data-profile="logo_text"><?php echo esc_html( $profile['logo_text'] ); ?></p>
						<h2 data-profile="full_name"><?php echo esc_html( $profile['full_name'] ); ?></h2>
						<p class="profile-card__role" data-profile="title"><?php echo esc_html( $profile['title'] ); ?></p>
						<div class="profile-card__footer">
							<span class="profile-card__location"><?php echo aurora_icon( 'map-pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span data-profile="location"><?php echo esc_html( $profile['location'] ); ?></span></span>
							<?php if ( $profile['linkedin'] ) : ?>
								<a class="profile-card__link" href="<?php echo esc_url( $profile['linkedin'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'LinkedIn profile', 'aurora-portfolio' ); ?>" data-profile-link="linkedin"><?php echo aurora_icon( 'linkedin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
							<?php endif; ?>
						</div>
					</figcaption>
				</figure>

				<div class="float-card float-card--wp">
					<span class="float-card__icon"><?php echo aurora_icon( 'globe' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span><strong>WordPress</strong><small><?php esc_html_e( 'Custom themes & plugins', 'aurora-portfolio' ); ?></small></span>
				</div>

				<div class="float-card float-card--speed">
					<span class="float-card__icon float-card__icon--cyan"><?php echo aurora_icon( 'devices' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span><strong><?php esc_html_e( 'Responsive', 'aurora-portfolio' ); ?></strong><small><?php esc_html_e( 'Every screen, every size', 'aurora-portfolio' ); ?></small></span>
				</div>

				<div class="float-card float-card--figma">
					<span class="float-card__icon float-card__icon--figma"><?php echo aurora_icon( 'code' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span><strong><?php esc_html_e( 'Clean Code', 'aurora-portfolio' ); ?></strong><small><?php esc_html_e( 'Built to last', 'aurora-portfolio' ); ?></small></span>
				</div>

				<div class="float-pill">
					<span class="float-pill__dot" aria-hidden="true"></span>
					<?php esc_html_e( 'Open to work', 'aurora-portfolio' ); ?>
				</div>
			</div>
		</div>
	</div>

	<?php if ( ! empty( $marquee ) ) : ?>
		<div class="container">
			<div class="marquee" aria-label="<?php esc_attr_e( 'Tools and technologies', 'aurora-portfolio' ); ?>" data-reveal>
				<div class="marquee__track">
					<span class="marquee__group">
						<?php foreach ( $marquee as $item ) : ?>
							<span><?php echo esc_html( $item ); ?></span><?php echo aurora_icon( 'sparkle' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endforeach; ?>
					</span>
					<span class="marquee__group" aria-hidden="true">
						<?php foreach ( $marquee as $item ) : ?>
							<span><?php echo esc_html( $item ); ?></span><?php echo aurora_icon( 'sparkle' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endforeach; ?>
					</span>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<a class="hero__scroll" href="<?php echo esc_url( aurora_section_url( 'about' ) ); ?>" aria-label="<?php esc_attr_e( 'Scroll to about section', 'aurora-portfolio' ); ?>">
		<span class="hero__scroll-line"></span>
		<span><?php esc_html_e( 'Scroll to explore', 'aurora-portfolio' ); ?></span>
	</a>
</section>
