<?php
/**
 * Theme setup.
 *
 * @package Aurora_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme supports, menus, image sizes and editor styles.
 */
function aurora_setup() {
	load_theme_textdomain( 'aurora-portfolio', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'custom-logo', array(
		'height'      => 64,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	add_editor_style( 'assets/css/editor.css' );

	add_image_size( 'aurora-project-card', 900, 620, true );
	add_image_size( 'aurora-project-cover', 1400, 900, true );

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'aurora-portfolio' ),
			'footer'  => __( 'Footer navigation', 'aurora-portfolio' ),
		)
	);
}
add_action( 'after_setup_theme', 'aurora_setup' );

/**
 * Content width for embedded media.
 */
function aurora_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'aurora_content_width', 760 );
}
add_action( 'after_setup_theme', 'aurora_content_width', 0 );

/**
 * Register widget areas.
 */
function aurora_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Blog sidebar', 'aurora-portfolio' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Shown beside single posts and archives.', 'aurora-portfolio' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	for ( $i = 1; $i <= 3; $i++ ) {
		register_sidebar(
			array(
				'name'          => sprintf( /* translators: %d: footer column number. */ __( 'Footer column %d', 'aurora-portfolio' ), $i ),
				'id'            => 'footer-' . $i,
				'description'   => __( 'Optional widget above the footer navigation.', 'aurora-portfolio' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action( 'widgets_init', 'aurora_widgets_init' );

/**
 * Shorten excerpts.
 *
 * @param int $length Default length.
 * @return int
 */
function aurora_excerpt_length( $length ) {
	return 26;
}
add_filter( 'excerpt_length', 'aurora_excerpt_length' );

/**
 * Excerpt ellipsis.
 *
 * @return string
 */
function aurora_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'aurora_excerpt_more' );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 * @return array
 */
function aurora_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'aurora_resource_hints', 10, 2 );
