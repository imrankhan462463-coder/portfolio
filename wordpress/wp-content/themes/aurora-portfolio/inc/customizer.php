<?php
/**
 * Customizer settings for the portfolio content.
 *
 * @package Aurora_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the portfolio settings panel.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 * @return void
 */
function aurora_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'aurora_portfolio',
		array(
			'title'       => __( 'Portfolio settings', 'aurora-portfolio' ),
			'description' => __( 'Edit the profile, section copy and contact details used across the site.', 'aurora-portfolio' ),
			'priority'    => 20,
		)
	);

	$sections = array(
		'profile'          => array( __( 'Profile & contact', 'aurora-portfolio' ), 10 ),
		'hero'             => array( __( 'Hero', 'aurora-portfolio' ), 20 ),
		'about'            => array( __( 'About', 'aurora-portfolio' ), 30 ),
		'services'         => array( __( 'Services', 'aurora-portfolio' ), 40 ),
		'skills'           => array( __( 'Skills', 'aurora-portfolio' ), 50 ),
		'projects'         => array( __( 'Projects', 'aurora-portfolio' ), 60 ),
		'process'          => array( __( 'Process', 'aurora-portfolio' ), 70 ),
		'why'              => array( __( 'Why choose me', 'aurora-portfolio' ), 80 ),
		'testimonials'     => array( __( 'Testimonials', 'aurora-portfolio' ), 90 ),
		'contact_settings' => array( __( 'Contact section', 'aurora-portfolio' ), 100 ),
		'social'           => array( __( 'Social profiles', 'aurora-portfolio' ), 110 ),
		'colours'          => array( __( 'Colours', 'aurora-portfolio' ), 120 ),
		'visibility'       => array( __( 'Section visibility', 'aurora-portfolio' ), 130 ),
	);

	foreach ( $sections as $id => $config ) {
		$wp_customize->add_section(
			'aurora_' . $id,
			array(
				'title'    => $config[0],
				'panel'    => 'aurora_portfolio',
				'priority' => $config[1],
			)
		);
	}

	$fields = array(
		// Profile.
		'brand_name'              => array( 'profile', __( 'Brand name', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'logo_text'               => array( 'profile', __( 'Logo text', 'aurora-portfolio' ), 'text', 'sanitize_text_field', __( 'Use a dot to highlight the suffix, e.g. IMRAN.DEV.', 'aurora-portfolio' ) ),
		'brand_role'              => array( 'profile', __( 'Professional title', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'profile_full_name'       => array( 'profile', __( 'Full name', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'profile_email'           => array( 'profile', __( 'Email address', 'aurora-portfolio' ), 'email', 'sanitize_email' ),
		'profile_phone'           => array( 'profile', __( 'Phone number', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'profile_location'        => array( 'profile', __( 'Location', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'profile_availability'    => array( 'profile', __( 'Availability label', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'profile_response_time'   => array( 'profile', __( 'Response time label', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'profile_linkedin'        => array( 'profile', __( 'LinkedIn URL', 'aurora-portfolio' ), 'url', 'esc_url_raw' ),
		'profile_image'           => array( 'profile', __( 'Profile photo', 'aurora-portfolio' ), 'image', 'esc_url_raw' ),
		'profile_cv'              => array( 'profile', __( 'CV / résumé file', 'aurora-portfolio' ), 'upload', 'esc_url_raw' ),
		'footer_description'      => array( 'profile', __( 'Footer description', 'aurora-portfolio' ), 'textarea', 'sanitize_textarea_field' ),
		'footer_note'             => array( 'profile', __( 'Footer note', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),

		// Hero.
		'hero_heading'            => array( 'hero', __( 'Hero heading', 'aurora-portfolio' ), 'text', 'sanitize_text_field', __( 'The last word is automatically highlighted in italic serif.', 'aurora-portfolio' ) ),
		'hero_typed_roles'        => array( 'hero', __( 'Typed roles', 'aurora-portfolio' ), 'text', 'sanitize_text_field', __( 'Comma-separated list.', 'aurora-portfolio' ) ),
		'hero_subtitle'           => array( 'hero', __( 'Hero description', 'aurora-portfolio' ), 'textarea', 'sanitize_textarea_field' ),
		'hero_note'               => array( 'hero', __( 'Location note', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'hero_primary_label'      => array( 'hero', __( 'Primary button label', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'hero_primary_url'        => array( 'hero', __( 'Primary button URL', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'hero_secondary_label'    => array( 'hero', __( 'Secondary button label', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'hero_secondary_url'      => array( 'hero', __( 'Secondary button URL', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'hero_stat_1_value'       => array( 'hero', __( 'Stat 1 value', 'aurora-portfolio' ), 'number', 'absint' ),
		'hero_stat_1_label'       => array( 'hero', __( 'Stat 1 label', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'hero_stat_1_suffix'      => array( 'hero', __( 'Stat 1 suffix', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'hero_stat_2_value'       => array( 'hero', __( 'Stat 2 value', 'aurora-portfolio' ), 'number', 'absint' ),
		'hero_stat_2_label'       => array( 'hero', __( 'Stat 2 label', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'hero_stat_2_suffix'      => array( 'hero', __( 'Stat 2 suffix', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'hero_stat_3_value'       => array( 'hero', __( 'Stat 3 value', 'aurora-portfolio' ), 'number', 'absint' ),
		'hero_stat_3_label'       => array( 'hero', __( 'Stat 3 label', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'hero_stat_3_suffix'      => array( 'hero', __( 'Stat 3 suffix', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'hero_marquee'            => array( 'hero', __( 'Marquee tools', 'aurora-portfolio' ), 'textarea', 'sanitize_textarea_field', __( 'Comma-separated list.', 'aurora-portfolio' ) ),

		// About.
		'about_eyebrow'           => array( 'about', __( 'Eyebrow', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'about_title'             => array( 'about', __( 'Title', 'aurora-portfolio' ), 'textarea', 'wp_kses_post', __( 'Basic HTML such as <br> and <em> is allowed.', 'aurora-portfolio' ) ),
		'about_text'              => array( 'about', __( 'Bio', 'aurora-portfolio' ), 'textarea', 'wp_kses_post', __( 'Separate paragraphs with a blank line.', 'aurora-portfolio' ) ),
		'about_points'            => array( 'about', __( 'Highlight points', 'aurora-portfolio' ), 'textarea', 'sanitize_textarea_field', __( 'One point per line.', 'aurora-portfolio' ) ),
		'about_signature'         => array( 'about', __( 'Signature name', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'about_signature_role'    => array( 'about', __( 'Signature role', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'about_cv_label'          => array( 'about', __( 'CV button label', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),

		// Services.
		'services_eyebrow'        => array( 'services', __( 'Eyebrow', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'services_title'          => array( 'services', __( 'Title', 'aurora-portfolio' ), 'textarea', 'wp_kses_post' ),
		'services_text'           => array( 'services', __( 'Intro text', 'aurora-portfolio' ), 'textarea', 'sanitize_textarea_field' ),

		// Skills.
		'skills_eyebrow'          => array( 'skills', __( 'Eyebrow', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'skills_title'            => array( 'skills', __( 'Title', 'aurora-portfolio' ), 'textarea', 'wp_kses_post' ),
		'skills_text'             => array( 'skills', __( 'Intro text', 'aurora-portfolio' ), 'textarea', 'sanitize_textarea_field' ),
		'skills_panel_title'      => array( 'skills', __( 'Panel title', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'skills_panel_badge'      => array( 'skills', __( 'Panel badge', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'skills_toolbox'          => array( 'skills', __( 'Toolbox tags', 'aurora-portfolio' ), 'textarea', 'sanitize_textarea_field', __( 'One tag per line.', 'aurora-portfolio' ) ),

		// Projects.
		'projects_eyebrow'        => array( 'projects', __( 'Eyebrow', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'projects_title'          => array( 'projects', __( 'Title', 'aurora-portfolio' ), 'textarea', 'wp_kses_post' ),
		'projects_text'           => array( 'projects', __( 'Intro text', 'aurora-portfolio' ), 'textarea', 'sanitize_textarea_field' ),
		'projects_note'           => array( 'projects', __( 'Concept note', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),

		// Process.
		'process_eyebrow'         => array( 'process', __( 'Eyebrow', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'process_title'           => array( 'process', __( 'Title', 'aurora-portfolio' ), 'textarea', 'wp_kses_post' ),
		'process_text'            => array( 'process', __( 'Intro text', 'aurora-portfolio' ), 'textarea', 'sanitize_textarea_field' ),

		// Why.
		'why_eyebrow'             => array( 'why', __( 'Eyebrow', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'why_title'               => array( 'why', __( 'Title', 'aurora-portfolio' ), 'textarea', 'wp_kses_post' ),
		'why_text'                => array( 'why', __( 'Intro text', 'aurora-portfolio' ), 'textarea', 'sanitize_textarea_field' ),

		// Testimonials.
		'testimonials_eyebrow'    => array( 'testimonials', __( 'Eyebrow', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'testimonials_title'      => array( 'testimonials', __( 'Title', 'aurora-portfolio' ), 'textarea', 'wp_kses_post' ),
		'testimonials_text'       => array( 'testimonials', __( 'Intro text', 'aurora-portfolio' ), 'textarea', 'sanitize_textarea_field' ),
		'testimonials_empty_title'=> array( 'testimonials', __( 'Empty state title', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'testimonials_empty_text' => array( 'testimonials', __( 'Empty state text', 'aurora-portfolio' ), 'textarea', 'sanitize_textarea_field' ),

		// Contact.
		'contact_eyebrow'         => array( 'contact_settings', __( 'Eyebrow', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'contact_title'           => array( 'contact_settings', __( 'Title', 'aurora-portfolio' ), 'textarea', 'wp_kses_post' ),
		'contact_text'            => array( 'contact_settings', __( 'Intro text', 'aurora-portfolio' ), 'textarea', 'sanitize_textarea_field' ),
		'contact_email'           => array( 'contact_settings', __( 'Public email', 'aurora-portfolio' ), 'email', 'sanitize_email' ),
		'contact_recipient'       => array( 'contact_settings', __( 'Form recipient email', 'aurora-portfolio' ), 'email', 'sanitize_email', __( 'Where contact form messages are sent. Defaults to the public email.', 'aurora-portfolio' ) ),
		'contact_phone_display'   => array( 'contact_settings', __( 'Phone display text', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'contact_whatsapp'        => array( 'contact_settings', __( 'WhatsApp number', 'aurora-portfolio' ), 'text', 'sanitize_text_field', __( 'Digits only, including the country code.', 'aurora-portfolio' ) ),
		'contact_location'        => array( 'contact_settings', __( 'Location', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'contact_response'        => array( 'contact_settings', __( 'Response label', 'aurora-portfolio' ), 'text', 'sanitize_text_field' ),
		'contact_form_note'       => array( 'contact_settings', __( 'Form privacy note', 'aurora-portfolio' ), 'textarea', 'sanitize_textarea_field' ),

		// Social.
		'social_linkedin'         => array( 'social', __( 'LinkedIn URL', 'aurora-portfolio' ), 'url', 'esc_url_raw' ),
		'social_github'           => array( 'social', __( 'GitHub URL', 'aurora-portfolio' ), 'url', 'esc_url_raw' ),
		'social_facebook'         => array( 'social', __( 'Facebook URL', 'aurora-portfolio' ), 'url', 'esc_url_raw' ),
		'social_instagram'        => array( 'social', __( 'Instagram URL', 'aurora-portfolio' ), 'url', 'esc_url_raw' ),

		// Colours.
		'accent_color'            => array( 'colours', __( 'Accent colour', 'aurora-portfolio' ), 'color', 'sanitize_hex_color' ),
		'cyan_color'              => array( 'colours', __( 'Secondary accent', 'aurora-portfolio' ), 'color', 'sanitize_hex_color' ),
	);

	foreach ( $fields as $key => $field ) {
		$section     = 'aurora_' . $field[0];
		$label       = $field[1];
		$type        = $field[2];
		$sanitize    = $field[3];
		$description = isset( $field[4] ) ? $field[4] : '';

		$wp_customize->add_setting(
			$key,
			array(
				'default'           => aurora_default( $key ),
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);

		$control_args = array(
			'label'       => $label,
			'section'     => $section,
			'description' => $description,
		);

		if ( 'image' === $type ) {
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					$key,
					array_merge( $control_args, array( 'mime_type' => 'image' ) )
				)
			);
			continue;
		}

		if ( 'upload' === $type ) {
			$wp_customize->add_control(
				new WP_Customize_Upload_Control(
					$wp_customize,
					$key,
					$control_args
				)
			);
			continue;
		}

		if ( 'color' === $type ) {
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$key,
					$control_args
				)
			);
			continue;
		}

		$wp_customize->add_control(
			$key,
			array_merge(
				$control_args,
				array(
					'type' => $type,
				)
			)
		);
	}

	$visibility = array(
		'show_about'        => __( 'Show About section', 'aurora-portfolio' ),
		'show_services'     => __( 'Show Services section', 'aurora-portfolio' ),
		'show_skills'       => __( 'Show Skills section', 'aurora-portfolio' ),
		'show_projects'     => __( 'Show Projects section', 'aurora-portfolio' ),
		'show_process'      => __( 'Show Process section', 'aurora-portfolio' ),
		'show_why'          => __( 'Show Why choose me section', 'aurora-portfolio' ),
		'show_testimonials' => __( 'Show Testimonials section', 'aurora-portfolio' ),
		'show_contact'      => __( 'Show Contact section', 'aurora-portfolio' ),
	);

	foreach ( $visibility as $key => $label ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => aurora_default( $key ),
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			$key,
			array(
				'label'   => $label,
				'section' => 'aurora_visibility',
				'type'    => 'checkbox',
			)
		);
	}
}
add_action( 'customize_register', 'aurora_customize_register' );
