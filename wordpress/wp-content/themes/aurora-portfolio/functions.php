<?php
/**
 * Aurora Portfolio functions and definitions.
 *
 * @package Aurora_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AURORA_VERSION', '1.0.0' );
define( 'AURORA_DIR', get_template_directory() );
define( 'AURORA_URI', get_template_directory_uri() );

require_once AURORA_DIR . '/inc/defaults.php';
require_once AURORA_DIR . '/inc/helpers.php';
require_once AURORA_DIR . '/inc/setup.php';
require_once AURORA_DIR . '/inc/customizer.php';
require_once AURORA_DIR . '/inc/fallback-content.php';
require_once AURORA_DIR . '/inc/contact-form.php';

/**
 * Front-end styles and scripts.
 *
 * @return void
 */
function aurora_enqueue_assets() {
	$fonts = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&family=Sora:wght@400;500;600;700&display=swap';

	wp_enqueue_style( 'aurora-fonts', $fonts, array(), null );

	$styles = array( 'core', 'sections', 'content', 'responsive', 'upgrade' );
	foreach ( $styles as $handle ) {
		wp_enqueue_style(
			'aurora-' . $handle,
			AURORA_URI . '/assets/css/' . $handle . '.css',
			array(),
			AURORA_VERSION
		);
	}

	wp_enqueue_style( 'aurora-style', get_stylesheet_uri(), array( 'aurora-core', 'aurora-sections', 'aurora-content', 'aurora-responsive', 'aurora-upgrade' ), AURORA_VERSION );

	wp_enqueue_script(
		'aurora-main',
		AURORA_URI . '/assets/js/main.js',
		array(),
		AURORA_VERSION,
		true
	);

	wp_localize_script(
		'aurora-main',
		'auroraPortfolio',
		array(
			'profile'  => aurora_get_profile(),
			'projects' => aurora_get_projects(),
			'homeUrl'  => home_url( '/' ),
			'isWordPress' => true,
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'aurora_enqueue_assets' );

/**
 * Add the nav-link class to primary menu items.
 *
 * @param array   $atts Link attributes.
 * @param WP_Post $item Menu item.
 * @param object  $args Menu arguments.
 * @return array
 */
function aurora_nav_link_attributes( $atts, $item, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
		$classes         = isset( $atts['class'] ) ? explode( ' ', $atts['class'] ) : array();
		$classes[]       = 'nav-link';
		$atts['class']   = implode( ' ', array_unique( array_filter( $classes ) ) );

		$current = array_intersect( (array) $item->classes, array( 'current-menu-item', 'current_page_item', 'current-menu-parent' ) );
		if ( ! empty( $current ) ) {
			$atts['aria-current'] = 'page';
		}
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'aurora_nav_link_attributes', 10, 3 );

/**
 * Accessibility: add a screen-reader label to the primary menu container.
 *
 * @param array $args Menu arguments.
 * @return array
 */
function aurora_primary_menu_args( $args ) {
	if ( isset( $args['theme_location'] ) && 'primary' === $args['theme_location'] ) {
		$args['container']      = false;
		$args['menu_class']     = 'nav-links';
		$args['menu_id']        = 'primary-menu';
		$args['fallback_cb']    = 'aurora_primary_menu_fallback';
		$args['depth']          = 1;
	}

	return $args;
}
add_filter( 'wp_nav_menu_args', 'aurora_primary_menu_args' );

/**
 * Output SEO meta tags, social cards and structured data.
 *
 * Skips the description/OG tags when a dedicated SEO plugin is active.
 *
 * @return void
 */
function aurora_seo_meta() {
	$seo_plugin = defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' );

	$profile     = aurora_get_profile();
	$title       = wp_get_document_title();
	$description = (string) aurora_mod( 'hero_subtitle' );
	$image       = AURORA_URI . '/assets/images/og-image.png';

	if ( is_singular() && ! is_front_page() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			$excerpt = get_the_excerpt( $post );
			if ( $excerpt ) {
				$description = wp_strip_all_tags( $excerpt );
			}
			if ( has_post_thumbnail( $post ) ) {
				$image = (string) get_the_post_thumbnail_url( $post, 'full' );
			}
		}
	}

	$description = wp_trim_words( wp_strip_all_tags( $description ), 40, '…' );
	$canonical   = is_singular() ? get_permalink() : home_url( '/' );
	$og_type     = is_singular() && ! is_front_page() ? 'article' : 'website';

	echo "\n<!-- Aurora Portfolio SEO -->\n";
	echo '<meta name="theme-color" content="#080B16">' . "\n";

	if ( ! $seo_plugin ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
		echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '">' . "\n";
		echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $canonical ) . '">' . "\n";
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	}

	if ( is_front_page() ) {
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Person',
			'name'        => $profile['full_name'],
			'jobTitle'    => $profile['title'],
			'description' => $description,
			'url'         => home_url( '/' ),
			'email'       => 'mailto:' . $profile['email'],
			'telephone'   => $profile['phone'],
			'address'     => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => 'Mirpur 10',
				'addressLocality' => 'Dhaka',
				'addressCountry'  => 'BD',
			),
			'knowsAbout'  => array( 'Web Design', 'WordPress Development', 'Custom WordPress Themes', 'WordPress Plugins', 'WooCommerce', 'Elementor', 'PHP', 'JavaScript', 'Responsive Web Design', 'SEO Fundamentals' ),
			'sameAs'      => array_values( array_filter( array( $profile['linkedin'], $profile['github'], $profile['facebook'], $profile['instagram'] ) ) ),
		);

		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'aurora_seo_meta', 2 );

/**
 * Apply customizer colours as CSS variables.
 *
 * @return void
 */
function aurora_custom_colors() {
	$accent = sanitize_hex_color( (string) aurora_mod( 'accent_color' ) );
	$cyan   = sanitize_hex_color( (string) aurora_mod( 'cyan_color' ) );

	if ( ! $accent && ! $cyan ) {
		return;
	}

	echo '<style id="aurora-custom-colors">:root{';
	if ( $accent ) {
		echo '--violet:' . esc_attr( $accent ) . ';';
	}
	if ( $cyan ) {
		echo '--cyan:' . esc_attr( $cyan ) . ';';
	}
	echo '}</style>' . "\n";
}
add_action( 'wp_head', 'aurora_custom_colors', 20 );

/**
 * Add a pingback header and feed links (WordPress core handles most of this).
 *
 * @return void
 */
function aurora_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'aurora_pingback_header' );
