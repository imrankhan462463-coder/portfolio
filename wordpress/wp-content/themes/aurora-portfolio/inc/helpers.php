<?php
/**
 * Small helpers shared by the theme templates.
 *
 * @package Aurora_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read a theme setting with its default value.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function aurora_mod( $key ) {
	return get_theme_mod( $key, aurora_default( $key ) );
}

/**
 * Build the editable profile array shared by templates and JavaScript.
 *
 * @return array
 */
function aurora_get_profile() {
	$email    = (string) aurora_mod( 'profile_email' );
	$phone    = (string) aurora_mod( 'profile_phone' );
	$whatsapp = (string) aurora_mod( 'contact_whatsapp' );

	if ( '' === $email ) {
		$email = (string) aurora_mod( 'contact_email' );
	}

	$whatsapp_digits = preg_replace( '/\D+/', '', $whatsapp );
	if ( '' === $whatsapp_digits ) {
		$whatsapp_digits = preg_replace( '/\D+/', '', $phone );
	}

	$linkedin = (string) aurora_mod( 'profile_linkedin' );
	if ( '' === $linkedin ) {
		$linkedin = (string) aurora_mod( 'social_linkedin' );
	}

	$about_text = (string) aurora_mod( 'about_text' );
	$bio_short  = trim( (string) strtok( $about_text, "\n" ) );

	return array(
		'full_name'       => (string) aurora_mod( 'profile_full_name' ),
		'brand_name'      => (string) aurora_mod( 'brand_name' ),
		'logo_text'        => (string) aurora_mod( 'logo_text' ),
		'title'            => (string) aurora_mod( 'brand_role' ),
		'identity'         => (string) aurora_mod( 'brand_role' ),
		'email'            => $email,
		'phone'            => $phone,
		'whatsapp'         => $whatsapp,
		'whatsapp_url'     => '' !== $whatsapp_digits ? 'https://wa.me/' . $whatsapp_digits : '',
		'location'         => (string) aurora_mod( 'profile_location' ),
		'linkedin'         => $linkedin,
		'github'           => (string) aurora_mod( 'social_github' ),
		'facebook'         => (string) aurora_mod( 'social_facebook' ),
		'instagram'        => (string) aurora_mod( 'social_instagram' ),
		'profile_image'    => (string) aurora_mod( 'profile_image' ),
		'resume'           => (string) aurora_mod( 'profile_cv' ),
		'availability'     => (string) aurora_mod( 'profile_availability' ),
		'response_time'    => (string) aurora_mod( 'profile_response_time' ),
		'hero_heading'     => (string) aurora_mod( 'hero_heading' ),
		'hero_description' => (string) aurora_mod( 'hero_subtitle' ),
		'bio_short'        => $bio_short,
		'bio_long'         => $about_text,
	);
}

/**
 * WhatsApp URL for the current profile.
 *
 * @return string
 */
function aurora_whatsapp_url() {
	$profile = aurora_get_profile();

	return $profile['whatsapp_url'];
}

/**
 * Email link for the current profile.
 *
 * @return string
 */
function aurora_email_url() {
	$profile = aurora_get_profile();

	return $profile['email'] ? 'mailto:' . $profile['email'] : '';
}

/**
 * Telephone link for the current profile.
 *
 * @return string
 */
function aurora_phone_url() {
	$profile = aurora_get_profile();
	$phone   = preg_replace( '/[^\d+]/', '', $profile['phone'] );

	return $phone ? 'tel:' . $phone : '';
}

/**
 * Brand mark markup with the dot highlighted.
 *
 * @return string
 */
function aurora_logo_text_html() {
	$text = (string) aurora_mod( 'logo_text' );
	$pos  = strpos( $text, '.' );

	if ( false === $pos ) {
		return esc_html( $text );
	}

	return esc_html( substr( $text, 0, $pos ) ) . '<span class="brand__dot">' . esc_html( substr( $text, $pos ) ) . '</span>';
}

/**
 * Inline SVG from the sprite.
 *
 * @param string $name Icon name without the icon- prefix.
 * @return string
 */
function aurora_icon( $name ) {
	$name = sanitize_key( $name );

	if ( '' === $name ) {
		return '';
	}

	return '<svg aria-hidden="true" focusable="false"><use href="#icon-' . esc_attr( $name ) . '"></use></svg>';
}

/**
 * URL for an on-page section, from any page in the site.
 *
 * @param string $key Section ID without the hash.
 * @return string
 */
function aurora_section_url( $key ) {
	$key = sanitize_title( $key );

	if ( is_front_page() ) {
		return '#' . $key;
	}

	return home_url( '/' ) . '#' . $key;
}

/**
 * Primary navigation fallback.
 *
 * @return void
 */
function aurora_primary_menu_fallback() {
	$links = array(
		'home'     => __( 'Home', 'aurora-portfolio' ),
		'about'    => __( 'About', 'aurora-portfolio' ),
		'services' => __( 'Services', 'aurora-portfolio' ),
		'skills'   => __( 'Skills', 'aurora-portfolio' ),
		'projects' => __( 'Projects', 'aurora-portfolio' ),
		'why-me'   => __( 'Why me', 'aurora-portfolio' ),
		'contact'  => __( 'Contact', 'aurora-portfolio' ),
	);

	echo '<ul class="nav-links" id="primary-menu">';
	foreach ( $links as $key => $label ) {
		printf(
			'<li><a class="nav-link" href="%1$s">%2$s</a></li>',
			esc_url( aurora_section_url( $key ) ),
			esc_html( $label )
		);
	}
	echo '</ul>';
}

/**
 * Render the hero heading exactly the way main.js does.
 *
 * @param string $heading Full heading text.
 * @return string
 */
function aurora_hero_heading_html( $heading ) {
	$words = preg_split( '/\s+/', trim( (string) $heading ) );
	$words = array_values( array_filter( (array) $words ) );

	if ( empty( $words ) ) {
		return '';
	}

	$accent = array_pop( $words );
	$middle = (int) ceil( count( $words ) / 2 );
	$lines  = array(
		implode( ' ', array_slice( $words, 0, $middle ) ),
		implode( ' ', array_slice( $words, $middle ) ),
	);
	$lines  = array_values( array_filter( $lines, 'strlen' ) );

	$html = '';
	foreach ( $lines as $line ) {
		$html .= '<span class="hero__line">' . esc_html( $line ) . '</span>';
	}

	$html .= '<span class="hero__line hero__line--accent"><em class="serif">' . esc_html( $accent ) . '</em></span>';

	return $html;
}

/**
 * Output paragraph-wrapped rich text.
 *
 * @param string $text Text.
 * @return string
 */
function aurora_paragraphs( $text ) {
	return wp_kses_post( wpautop( (string) $text ) );
}

/**
 * Split a comma/newline separated setting into a clean array.
 *
 * @param string $value Raw value.
 * @return array
 */
function aurora_split_list( $value ) {
	$parts = preg_split( '/[\r\n,]+/', (string) $value );
	$parts = array_map( 'trim', (array) $parts );

	return array_values( array_filter( $parts, 'strlen' ) );
}

/**
 * Allowed contact form project types.
 *
 * @return array
 */
function aurora_contact_project_types() {
	return array(
		'Website Design',
		'WordPress Website Development',
		'Custom WordPress Theme',
		'Custom WordPress Plugin',
		'Business Website',
		'E-commerce Website',
		'Responsive Website Design',
		'Website Redesign & Maintenance',
		'Other',
	);
}

/**
 * Allowed contact form budget ranges.
 *
 * @return array
 */
function aurora_contact_budgets() {
	return array( '', 'Under $500', '$500 – $1,500', '$1,500 – $3,000', '$3,000+', 'Not sure yet' );
}

/**
 * Profile social links that have a URL.
 *
 * @return array
 */
function aurora_profile_social_links() {
	$profile = aurora_get_profile();
	$links   = array(
		array(
			'key'   => 'linkedin',
			'label' => __( 'LinkedIn profile', 'aurora-portfolio' ),
			'url'   => $profile['linkedin'],
			'icon'  => 'linkedin',
		),
		array(
			'key'   => 'email',
			'label' => __( 'Email', 'aurora-portfolio' ),
			'url'   => aurora_email_url(),
			'icon'  => 'mail',
		),
		array(
			'key'   => 'whatsapp',
			'label' => __( 'WhatsApp', 'aurora-portfolio' ),
			'url'   => $profile['whatsapp_url'],
			'icon'  => 'whatsapp',
		),
		array(
			'key'   => 'github',
			'label' => __( 'GitHub', 'aurora-portfolio' ),
			'url'   => $profile['github'],
			'icon'  => 'github',
		),
		array(
			'key'   => 'facebook',
			'label' => __( 'Facebook', 'aurora-portfolio' ),
			'url'   => $profile['facebook'],
			'icon'  => 'facebook',
		),
		array(
			'key'   => 'instagram',
			'label' => __( 'Instagram', 'aurora-portfolio' ),
			'url'   => $profile['instagram'],
			'icon'  => 'instagram',
		),
	);

	return array_values(
		array_filter(
			$links,
			static function ( $link ) {
				return ! empty( $link['url'] );
			}
		)
	);
}

/**
 * Render a technology logo. Markup is fixed theme code, not user input.
 *
 * @param string $key Technology key.
 * @return string
 */
function aurora_tech_logo( $key ) {
	$logos = array(
		'html5'      => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M8 4h32l-3 36-13 4-13-4L8 4Z" fill="currentColor" opacity=".18"/><path d="M12 8h24l-2.4 29-9.6 3-9.6-3L12 8Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><text x="24" y="31" text-anchor="middle" fill="currentColor" font-family="Arial,Helvetica,sans-serif" font-size="15" font-weight="700">5</text></svg>',
		'css3'       => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M8 4h32l-3 36-13 4-13-4L8 4Z" fill="currentColor" opacity=".18"/><path d="M12 8h24l-2.4 29-9.6 3-9.6-3L12 8Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><text x="24" y="31" text-anchor="middle" fill="currentColor" font-family="Arial,Helvetica,sans-serif" font-size="15" font-weight="700">3</text></svg>',
		'javascript' => '<svg viewBox="0 0 48 48" aria-hidden="true"><rect x="6" y="6" width="36" height="36" rx="7" fill="currentColor"/><text x="24" y="31" text-anchor="middle" fill="#111827" font-family="Arial,Helvetica,sans-serif" font-size="15" font-weight="700">JS</text></svg>',
		'php'        => '<svg viewBox="0 0 48 48" aria-hidden="true"><ellipse cx="24" cy="24" rx="19" ry="15" fill="currentColor" opacity=".22"/><ellipse cx="24" cy="24" rx="19" ry="15" fill="none" stroke="currentColor" stroke-width="2"/><text x="24" y="29" text-anchor="middle" fill="currentColor" font-family="Arial,Helvetica,sans-serif" font-size="11" font-weight="700">php</text></svg>',
		'wordpress'  => '<svg viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="19" fill="currentColor" opacity=".18"/><circle cx="24" cy="24" r="17" fill="none" stroke="currentColor" stroke-width="2.5"/><text x="24" y="31" text-anchor="middle" fill="currentColor" font-family="Georgia,serif" font-size="22" font-weight="700">W</text></svg>',
		'woocommerce'=> '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M7 14a6 6 0 0 1 6-6h22a6 6 0 0 1 6 6v14a6 6 0 0 1-6 6H26l-8 7v-7h-5a6 6 0 0 1-6-6V14Z" fill="currentColor" opacity=".2"/><path d="M7 14a6 6 0 0 1 6-6h22a6 6 0 0 1 6 6v14a6 6 0 0 1-6 6H26l-8 7v-7h-5a6 6 0 0 1-6-6V14Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><text x="24" y="27" text-anchor="middle" fill="currentColor" font-family="Arial,Helvetica,sans-serif" font-size="10" font-weight="700">Woo</text></svg>',
		'elementor'  => '<svg viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="19" fill="currentColor" opacity=".18"/><circle cx="24" cy="24" r="17" fill="none" stroke="currentColor" stroke-width="2"/><text x="24" y="31" text-anchor="middle" fill="currentColor" font-family="Arial,Helvetica,sans-serif" font-size="17" font-weight="700">E</text></svg>',
		'figma'      => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M17 8h7v10h-7a5 5 0 0 1 0-10Z" fill="#f24e1e"/><path d="M24 8h7a5 5 0 0 1 0 10h-7V8Z" fill="#ff7262"/><path d="M17 18h7v10h-7a5 5 0 0 1 0-10Z" fill="#a259ff"/><path d="M24 18h7a5 5 0 1 1-7 5v-5Z" fill="#1abcfe"/><path d="M17 28h7v5a5 5 0 1 1-7-5Z" fill="#0acf83"/></svg>',
		'tailwind'   => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M10 18c5-7 9-7 14 0s9 7 14 0M10 29c5-7 9-7 14 0s9 7 14 0" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round"/></svg>',
		'mysql'      => '<svg viewBox="0 0 48 48" aria-hidden="true"><ellipse cx="24" cy="12" rx="15" ry="6" fill="currentColor" opacity=".25"/><path d="M9 12v10c0 3.3 6.7 6 15 6s15-2.7 15-6V12M9 22v10c0 3.3 6.7 6 15 6s15-2.7 15-6V22" fill="none" stroke="currentColor" stroke-width="2.4"/><text x="24" y="19" text-anchor="middle" fill="currentColor" font-family="Arial,Helvetica,sans-serif" font-size="8" font-weight="700">SQL</text></svg>',
		'git'        => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M16 10v9a8 8 0 0 0 8 8h8" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/><circle cx="16" cy="8" r="4.2" fill="currentColor"/><circle cx="16" cy="28" r="4.2" fill="currentColor"/><circle cx="33" cy="28" r="4.2" fill="currentColor"/></svg>',
		'acf'        => '<svg viewBox="0 0 48 48" aria-hidden="true"><rect x="6" y="6" width="36" height="36" rx="9" fill="currentColor" opacity=".18"/><rect x="6" y="6" width="36" height="36" rx="9" fill="none" stroke="currentColor" stroke-width="2"/><text x="24" y="29" text-anchor="middle" fill="currentColor" font-family="Arial,Helvetica,sans-serif" font-size="11" font-weight="700">ACF</text></svg>',
		'github'     => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M24 4a20 20 0 0 0-6.3 39c1 .2 1.4-.4 1.4-1v-3.5c-5.6 1.2-6.8-2.7-6.8-2.7-.9-2.3-2.2-2.9-2.2-2.9-1.8-1.2.1-1.2.1-1.2 2 .1 3 2 3 2 1.8 3 4.7 2.1 5.8 1.6.2-1.3.7-2.1 1.3-2.6-4.5-.5-9.2-2.2-9.2-9.8 0-2.2.8-4 2-5.4-.2-.5-.9-2.6.2-5.4 0 0 1.7-.5 5.5 2a19 19 0 0 1 10 0c3.8-2.5 5.5-2 5.5-2 1.1 2.8.4 4.9.2 5.4 1.3 1.4 2 3.2 2 5.4 0 7.6-4.7 9.3-9.2 9.8.7.6 1.3 1.8 1.3 3.6v5.4c0 .6.4 1.2 1.4 1A20 20 0 0 0 24 4Z" fill="currentColor"/></svg>',
	);

	return isset( $logos[ $key ] ) ? $logos[ $key ] : aurora_icon( 'code' );
}
