<?php
/**
 * Content providers with graceful fallbacks.
 *
 * When the companion plugin is active, projects, services and testimonials are
 * pulled from WordPress. When it is not, the theme still renders a complete
 * front page using the concept content below.
 *
 * @package Aurora_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fallback service cards.
 *
 * @return array
 */
function aurora_fallback_services() {
	return array(
		array(
			'number'  => '01',
			'icon'    => 'layout',
			'title'   => 'Website Design',
			'text'    => 'Distinctive, conversion-minded design that gives your brand a confident digital presence — from wireframe to polished UI.',
			'items'   => array( 'UX research & wireframes', 'Visual identity & UI design', 'Interactive prototypes' ),
			'subject' => 'Website Design',
		),
		array(
			'number'  => '02',
			'icon'    => 'globe',
			'title'   => 'WordPress Website Development',
			'text'    => 'Fast, secure and easy-to-manage WordPress websites tailored to your goals — built to grow with your business.',
			'items'   => array( 'Theme setup & customization', 'Elementor & Gutenberg builds', 'Editor-friendly content systems' ),
			'subject' => 'WordPress Website Development',
		),
		array(
			'number'  => '03',
			'icon'    => 'code',
			'title'   => 'Custom WordPress Theme Development',
			'text'    => 'A bespoke theme built from scratch around your brand and workflow. No bloat, no compromises — just clean, maintainable code.',
			'items'   => array( 'Custom template hierarchy', 'ACF-powered content models', 'Performance-first architecture' ),
			'subject' => 'Custom WordPress Theme',
		),
		array(
			'number'  => '04',
			'icon'    => 'plug',
			'title'   => 'WordPress Plugin Development',
			'text'    => 'Purpose-built plugins that solve specific problems — from custom post types and dashboards to API integrations.',
			'items'   => array( 'Custom functionality', 'Third-party integrations', 'Secure, standards-based code' ),
			'subject' => 'WordPress Plugin Development',
		),
		array(
			'number'  => '05',
			'icon'    => 'devices',
			'title'   => 'Responsive Website Development',
			'text'    => 'Experiences that feel native on every screen. Mobile-first layouts, fluid typography and interactions that adapt gracefully.',
			'items'   => array( 'Mobile-first front-end', 'Cross-browser testing', 'Accessible interactions' ),
			'subject' => 'Responsive Website Development',
		),
		array(
			'number'  => '06',
			'icon'    => 'refresh',
			'title'   => 'Website Redesign',
			'text'    => "Give an outdated site a new lease of life. I audit what's holding you back, then redesign and rebuild for clarity and performance.",
			'items'   => array( 'UX & performance audit', 'Modern visual refresh', 'Zero-downtime migration' ),
			'subject' => 'Website Redesign',
		),
		array(
			'number'  => '07',
			'icon'    => 'cart',
			'title'   => 'E-commerce Website Development',
			'text'    => 'Sell online with a WooCommerce store that stays fast and clear — from product setup and checkout to order management.',
			'items'   => array( 'WooCommerce setup & styling', 'Payment & shipping flows', 'Store performance & UX' ),
			'subject' => 'E-commerce Website',
		),
		array(
			'number'  => '08',
			'icon'    => 'briefcase',
			'title'   => 'Business Website Development',
			'text'    => 'A professional business website that explains what you do, earns trust and turns visitors into enquiries.',
			'items'   => array( 'Clear page structure', 'Lead-focused contact flows', 'SEO-ready foundations' ),
			'subject' => 'Business Website',
		),
	);
}

/**
 * Return services from the CPT when available, otherwise fallback content.
 *
 * @return array
 */
function aurora_get_services() {
	$fallback = aurora_fallback_services();

	if ( ! post_type_exists( 'aurora_service' ) ) {
		return apply_filters( 'aurora_services', $fallback );
	}

	$posts = get_posts(
		array(
			'post_type'      => 'aurora_service',
			'posts_per_page' => 12,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			'post_status'    => 'publish',
		)
	);

	if ( empty( $posts ) ) {
		return apply_filters( 'aurora_services', $fallback );
	}

	$services = array();

	foreach ( $posts as $index => $post ) {
		$items_raw = get_post_meta( $post->ID, '_aurora_service_items', true );
		$items     = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $items_raw ) ) );
		$excerpt   = get_the_excerpt( $post );

		$services[] = array(
			'number'  => get_post_meta( $post->ID, '_aurora_service_number', true ) ?: sprintf( '%02d', $index + 1 ),
			'icon'    => get_post_meta( $post->ID, '_aurora_service_icon', true ) ?: 'sparkle',
			'title'   => get_the_title( $post ),
			'text'    => $excerpt ? $excerpt : wp_trim_words( wp_strip_all_tags( $post->post_content ), 28 ),
			'items'   => $items,
			'subject' => get_post_meta( $post->ID, '_aurora_service_subject', true ) ?: get_the_title( $post ),
		);
	}

	return apply_filters( 'aurora_services', $services );
}

/**
 * Fallback project cards. These are clearly labelled as concept work.
 *
 * @return array
 */
function aurora_fallback_projects() {
	$base = AURORA_URI . '/assets/images/projects/';

	return array(
		array(
			'id'              => 'lumen',
			'name'            => 'Lumen Studio',
			'category'        => 'wordpress',
			'category_label'  => 'WordPress',
			'category_detail' => 'Brand · Web · WordPress',
			'year'            => '2026',
			'description'     => 'A refined WordPress concept for a boutique creative studio, with a custom editorial layout and a flexible case-study system.',
			'image'           => $base . 'project-lumen.svg',
			'tech'            => array( 'WordPress', 'PHP', 'ACF', 'CSS3' ),
			'demo_url'        => '',
			'source_url'      => '',
			'concept'         => true,
		),
		array(
			'id'              => 'nomad',
			'name'            => 'Nomad Supply',
			'category'        => 'ecommerce',
			'category_label'  => 'E-Commerce',
			'category_detail' => 'WooCommerce · Storefront',
			'year'            => '2025',
			'description'     => 'A conversion-focused WooCommerce storefront concept for outdoor gear, with smart filtering and a frictionless checkout flow.',
			'image'           => $base . 'project-nomad.svg',
			'tech'            => array( 'WooCommerce', 'WordPress', 'PHP', 'Stripe' ),
			'demo_url'        => '',
			'source_url'      => '',
			'concept'         => true,
		),
		array(
			'id'              => 'pulseboard',
			'name'            => 'Pulseboard',
			'category'        => 'web-app',
			'category_label'  => 'Web App',
			'category_detail' => 'SaaS · Product UI',
			'year'            => '2025',
			'description'     => 'A data-rich SaaS landing page and dashboard UI concept built for clarity, speed, and a confident product story.',
			'image'           => $base . 'project-pulseboard.svg',
			'tech'            => array( 'HTML5', 'CSS3', 'JavaScript', 'SVG' ),
			'demo_url'        => '',
			'source_url'      => '',
			'concept'         => true,
		),
		array(
			'id'              => 'verdant',
			'name'            => 'Verdant',
			'category'        => 'custom-theme',
			'category_label'  => 'Custom Theme',
			'category_detail' => 'Custom Theme · Brand',
			'year'            => '2025',
			'description'     => 'A custom WordPress theme concept for an organic skincare brand, pairing considered art direction with lightweight, subtle motion.',
			'image'           => $base . 'project-verdant.svg',
			'tech'            => array( 'WordPress', 'Custom Theme', 'PHP', 'JavaScript' ),
			'demo_url'        => '',
			'source_url'      => '',
			'concept'         => true,
		),
		array(
			'id'              => 'framecast',
			'name'            => 'FrameCast',
			'category'        => 'wordpress',
			'category_label'  => 'WordPress',
			'category_detail' => 'Plugin · Product',
			'year'            => '2024',
			'description'     => 'A WordPress plugin concept that turns complex export workflows into a clean, one-click experience for creative teams.',
			'image'           => $base . 'project-framecast.svg',
			'tech'            => array( 'WordPress Plugin API', 'PHP', 'REST API', 'JavaScript' ),
			'demo_url'        => '',
			'source_url'      => '',
			'concept'         => true,
		),
		array(
			'id'              => 'atelier',
			'name'            => 'Atelier North',
			'category'        => 'ui-ux',
			'category_label'  => 'UI/UX',
			'category_detail' => 'Editorial · Art Direction',
			'year'            => '2024',
			'description'     => 'An editorial portfolio concept pairing oversized type with a quiet, image-led browsing experience.',
			'image'           => $base . 'project-atelier.svg',
			'tech'            => array( 'Figma', 'HTML5', 'CSS3', 'JavaScript' ),
			'demo_url'        => '',
			'source_url'      => '',
			'concept'         => true,
		),
	);
}

/**
 * Return projects from the CPT when available, otherwise fallback content.
 *
 * @return array
 */
function aurora_get_projects() {
	$fallback = aurora_fallback_projects();

	if ( ! post_type_exists( 'aurora_project' ) ) {
		return apply_filters( 'aurora_projects', $fallback );
	}

	$posts = get_posts(
		array(
			'post_type'      => 'aurora_project',
			'posts_per_page' => 12,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			'post_status'    => 'publish',
		)
	);

	if ( empty( $posts ) ) {
		return apply_filters( 'aurora_projects', $fallback );
	}

	$covers = array(
		'lumen'      => AURORA_URI . '/assets/images/projects/project-lumen.svg',
		'nomad'      => AURORA_URI . '/assets/images/projects/project-nomad.svg',
		'pulseboard' => AURORA_URI . '/assets/images/projects/project-pulseboard.svg',
		'verdant'    => AURORA_URI . '/assets/images/projects/project-verdant.svg',
		'framecast'  => AURORA_URI . '/assets/images/projects/project-framecast.svg',
		'atelier'    => AURORA_URI . '/assets/images/projects/project-atelier.svg',
	);

	$projects = array();

	foreach ( $posts as $post ) {
		$terms     = get_the_terms( $post->ID, 'aurora_project_type' );
		$term      = ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? $terms[0] : null;
		$image     = get_the_post_thumbnail_url( $post->ID, 'aurora-project-card' );
		$excerpt   = get_the_excerpt( $post );
		$cover_key = sanitize_title( $post->post_name );
		$tech_raw  = (string) get_post_meta( $post->ID, '_aurora_project_tech', true );
		$tech      = array_filter( array_map( 'trim', preg_split( '/[\r\n,]+/', $tech_raw ) ) );
		$detail    = (string) get_post_meta( $post->ID, '_aurora_project_category_detail', true );

		if ( ! $image && isset( $covers[ $cover_key ] ) ) {
			$image = $covers[ $cover_key ];
		}

		if ( ! $image ) {
			$image = AURORA_URI . '/assets/images/og-image.png';
		}

		$projects[] = array(
			'id'              => $post->post_name,
			'name'            => get_the_title( $post ),
			'category'        => $term ? $term->slug : 'concept',
			'category_label'  => $term ? $term->name : __( 'Concept', 'aurora-portfolio' ),
			'category_detail' => $detail ? $detail : ( $term ? $term->name : __( 'Concept project', 'aurora-portfolio' ) ),
			'year'            => (string) get_post_meta( $post->ID, '_aurora_project_year', true ),
			'description'     => $excerpt ? $excerpt : wp_trim_words( wp_strip_all_tags( $post->post_content ), 30 ),
			'image'           => $image,
			'tech'            => array_values( $tech ),
			'demo_url'        => esc_url_raw( (string) get_post_meta( $post->ID, '_aurora_project_demo_url', true ) ),
			'source_url'      => esc_url_raw( (string) get_post_meta( $post->ID, '_aurora_project_source_url', true ) ),
			'concept'         => (bool) get_post_meta( $post->ID, '_aurora_project_concept', true ),
		);
	}

	return apply_filters( 'aurora_projects', $projects );
}

/**
 * Build the filter list for a set of projects.
 *
 * @param array $projects Projects.
 * @return array
 */
function aurora_get_project_filters( $projects ) {
	$filters = array();

	foreach ( $projects as $project ) {
		$key = sanitize_title( $project['category'] );

		if ( ! isset( $filters[ $key ] ) ) {
			$filters[ $key ] = array(
				'label' => $project['category_label'],
				'count' => 0,
			);
		}

		$filters[ $key ]['count']++;
	}

	return $filters;
}

/**
 * Render one project card.
 *
 * @param array $project Project data.
 * @return string
 */
function aurora_project_card( $project ) {
	$name        = isset( $project['name'] ) ? $project['name'] : __( 'Untitled project', 'aurora-portfolio' );
	$category    = isset( $project['category'] ) ? sanitize_title( $project['category'] ) : 'concept';
	$label       = isset( $project['category_label'] ) ? $project['category_label'] : __( 'Project', 'aurora-portfolio' );
	$detail      = isset( $project['category_detail'] ) ? $project['category_detail'] : $label;
	$description = isset( $project['description'] ) ? $project['description'] : '';
	$image       = isset( $project['image'] ) ? $project['image'] : '';
	$year        = isset( $project['year'] ) ? $project['year'] : '';
	$tech        = isset( $project['tech'] ) && is_array( $project['tech'] ) ? $project['tech'] : array();
	$demo        = isset( $project['demo_url'] ) ? $project['demo_url'] : '';
	$source      = isset( $project['source_url'] ) ? $project['source_url'] : '';

	$html  = '<article class="project-card" data-category="' . esc_attr( $category ) . '" data-reveal>';
	$html .= '<div class="project-card__media">';
	$html .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( sprintf( /* translators: %s: project name. */ __( '%s project preview', 'aurora-portfolio' ), $name ) ) . '" width="1200" height="800" loading="lazy" decoding="async">';
	$html .= '<span class="project-card__shade" aria-hidden="true"></span>';
	$html .= '<span class="project-card__chip">' . esc_html( $label ) . '</span>';
	$html .= '<span class="project-card__open">' . aurora_icon( 'arrow-up-right' ) . '</span>';
	$html .= '</div>';
	$html .= '<div class="project-card__body">';
	$html .= '<div class="project-card__meta"><span>' . esc_html( $detail ) . '</span><span>' . esc_html( $year ) . '</span></div>';
	$html .= '<h3>' . esc_html( $name ) . '</h3>';
	$html .= '<p>' . esc_html( $description ) . '</p>';

	if ( ! empty( $tech ) ) {
		$html .= '<ul class="tech-list" aria-label="' . esc_attr__( 'Technologies used', 'aurora-portfolio' ) . '">';
		foreach ( $tech as $item ) {
			$html .= '<li>' . esc_html( $item ) . '</li>';
		}
		$html .= '</ul>';
	}

	$html .= '<div class="project-card__actions">';

	if ( $demo ) {
		$external = (bool) preg_match( '#^https?://#i', $demo );
		$html    .= '<a class="btn btn--primary btn--sm" href="' . esc_url( $demo ) . '"' . ( $external ? ' target="_blank" rel="noopener noreferrer"' : '' ) . '><span>' . esc_html__( 'Live demo', 'aurora-portfolio' ) . '</span>' . aurora_icon( 'arrow-up-right' ) . '</a>';
	}

	if ( $source ) {
		$external = (bool) preg_match( '#^https?://#i', $source );
		$html    .= '<a class="btn btn--ghost btn--sm" href="' . esc_url( $source ) . '"' . ( $external ? ' target="_blank" rel="noopener noreferrer"' : '' ) . '><span>' . esc_html__( 'Source code', 'aurora-portfolio' ) . '</span>' . aurora_icon( 'code' ) . '</a>';
	}

	if ( ! $demo && ! $source ) {
		$html .= '<a class="btn btn--ghost btn--sm" href="' . esc_url( aurora_section_url( 'contact' ) ) . '"><span>' . esc_html__( 'Discuss a project', 'aurora-portfolio' ) . '</span>' . aurora_icon( 'arrow-up-right' ) . '</a>';
	}

	$html .= '</div></div></article>';

	return $html;
}

/**
 * Testimonials. The theme deliberately ships with no invented reviews.
 *
 * @return array
 */
function aurora_get_testimonials() {
	$testimonials = array();

	if ( post_type_exists( 'aurora_testimonial' ) ) {
		$posts = get_posts(
			array(
				'post_type'      => 'aurora_testimonial',
				'posts_per_page' => 6,
				'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
				'post_status'    => 'publish',
			)
		);

		foreach ( $posts as $post ) {
			$name    = get_the_title( $post );
			$role    = (string) get_post_meta( $post->ID, '_aurora_testimonial_role', true );
			$company = (string) get_post_meta( $post->ID, '_aurora_testimonial_company', true );

			if ( $company ) {
				$role = trim( $role . ' · ' . $company, ' ·' );
			}

			$words    = preg_split( '/\s+/', trim( $name ) );
			$initials = '';

			foreach ( array_slice( (array) $words, 0, 2 ) as $word ) {
				$initials .= strtoupper( substr( $word, 0, 1 ) );
			}

			$testimonials[] = array(
				'quote'     => wp_strip_all_tags( $post->post_content ),
				'name'      => $name ? $name : __( 'Client name', 'aurora-portfolio' ),
				'role'      => $role,
				'initials'  => $initials ? $initials : 'CN',
				'is_sample' => (bool) get_post_meta( $post->ID, '_aurora_testimonial_is_sample', true ),
			);
		}
	}

	return apply_filters( 'aurora_testimonials', $testimonials );
}

/**
 * Technical skills rendered as logo cards.
 *
 * @return array
 */
function aurora_get_skills() {
	$skills = array(
		array( 'key' => 'html5', 'name' => 'HTML5', 'desc' => 'Semantic markup', 'color' => '#e34f26' ),
		array( 'key' => 'css3', 'name' => 'CSS3', 'desc' => 'Layout & motion', 'color' => '#38bdf8' ),
		array( 'key' => 'javascript', 'name' => 'JavaScript', 'desc' => 'Interactions', 'color' => '#f7df1e' ),
		array( 'key' => 'php', 'name' => 'PHP', 'desc' => 'Server-side logic', 'color' => '#a78bfa' ),
		array( 'key' => 'wordpress', 'name' => 'WordPress', 'desc' => 'Themes & plugins', 'color' => '#38bdf8' ),
		array( 'key' => 'woocommerce', 'name' => 'WooCommerce', 'desc' => 'Online stores', 'color' => '#c084fc' ),
		array( 'key' => 'elementor', 'name' => 'Elementor', 'desc' => 'Page building', 'color' => '#f472b6' ),
		array( 'key' => 'figma', 'name' => 'Figma', 'desc' => 'UI design', 'color' => '#f59e0b' ),
		array( 'key' => 'tailwind', 'name' => 'Tailwind CSS', 'desc' => 'Utility styling', 'color' => '#22d3ee' ),
		array( 'key' => 'mysql', 'name' => 'MySQL', 'desc' => 'Databases', 'color' => '#2dd4bf' ),
		array( 'key' => 'git', 'name' => 'Git & GitHub', 'desc' => 'Version control', 'color' => '#fb923c' ),
		array( 'key' => 'acf', 'name' => 'ACF', 'desc' => 'Custom fields', 'color' => '#60a5fa' ),
	);

	return apply_filters( 'aurora_skills', $skills );
}

/**
 * Six-step process.
 *
 * @return array
 */
function aurora_get_process() {
	$process = array(
		array(
			'number' => '01',
			'icon'   => 'search',
			'title'  => 'Research',
			'text'   => 'I learn about your business, audience and competitors to understand what success actually looks like.',
			'tag'    => 'Discovery call · Audit',
		),
		array(
			'number' => '02',
			'icon'   => 'compass',
			'title'  => 'Planning',
			'text'   => 'Content, structure and priorities come together in a clear roadmap before any pixels are pushed.',
			'tag'    => 'Sitemap · Roadmap',
		),
		array(
			'number' => '03',
			'icon'   => 'pen',
			'title'  => 'Design',
			'text'   => 'I explore the visual direction and refine it into an interactive design you can react to early.',
			'tag'    => 'UI design · Prototype',
		),
		array(
			'number' => '04',
			'icon'   => 'code',
			'title'  => 'Development',
			'text'   => 'Design becomes a fast, accessible WordPress build — cleanly coded, responsive by default and easy to manage.',
			'tag'    => 'WordPress · Front-end',
		),
		array(
			'number' => '05',
			'icon'   => 'test',
			'title'  => 'Testing',
			'text'   => 'Every device, browser and edge case gets checked. Performance, accessibility and forms are all verified.',
			'tag'    => 'QA · Accessibility',
		),
		array(
			'number' => '06',
			'icon'   => 'rocket',
			'title'  => 'Launch',
			'text'   => "We deploy, monitor and hand over a site you're confident running. Support continues after go-live.",
			'tag'    => 'Deploy · Handover',
		),
	);

	return apply_filters( 'aurora_process', $process );
}

/**
 * Reasons to work together.
 *
 * @return array
 */
function aurora_get_why() {
	$reasons = array(
		array(
			'icon'  => 'send',
			'title' => 'Clear communication',
			'text'  => 'Plain-language updates, honest timelines and no disappearing acts. You always know what stage the project is at.',
		),
		array(
			'icon'  => 'devices',
			'title' => 'Responsive by default',
			'text'  => 'Every layout is designed for phones, tablets, laptops and desktops from the start — not squeezed in at the end.',
		),
		array(
			'icon'  => 'rocket',
			'title' => 'Performance focused',
			'text'  => 'Lean markup, optimised images and sensible loading choices so your site feels quick on real connections.',
		),
		array(
			'icon'  => 'code',
			'title' => 'Clean, maintainable code',
			'text'  => 'Readable, standards-based code that another developer can pick up — and that you can keep running after launch.',
		),
		array(
			'icon'  => 'search',
			'title' => 'SEO fundamentals',
			'text'  => 'Semantic structure, sensible headings, meta details and fast pages — the groundwork search engines need.',
		),
		array(
			'icon'  => 'refresh',
			'title' => 'Support after launch',
			'text'  => 'Handover is not the end. I stay available for fixes, small changes and questions while you settle in.',
		),
	);

	return apply_filters( 'aurora_why', $reasons );
}

/**
 * Toolbox tags.
 *
 * @return array
 */
function aurora_get_toolbox() {
	return apply_filters( 'aurora_toolbox', aurora_split_list( aurora_mod( 'skills_toolbox' ) ) );
}

/**
 * Hero marquee items.
 *
 * @return array
 */
function aurora_get_marquee() {
	return apply_filters( 'aurora_marquee', aurora_split_list( aurora_mod( 'hero_marquee' ) ) );
}
