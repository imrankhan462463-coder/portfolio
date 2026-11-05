<?php
/**
 * Default values for every customizer setting.
 *
 * These defaults mirror the static portfolio so the theme is complete and
 * presentable before anything is changed in the Customizer.
 *
 * @package Aurora_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the default value for a theme setting.
 *
 * @param string $key Setting key without the aurora_ prefix.
 * @return mixed
 */
function aurora_default( $key ) {
	static $defaults = null;

	if ( null === $defaults ) {
		$defaults = array(
			// -----------------------------------------------------------------
			// Brand and profile.
			// -----------------------------------------------------------------
			'brand_name'                 => 'IMRAN HOSAN',
			'logo_text'                  => 'IMRAN.DEV',
			'brand_role'                 => 'Web Designer & WordPress Developer',
			'profile_full_name'          => 'MD Imran Hosan',
			'profile_email'              => 'imransikdar462@gmail.com',
			'profile_phone'              => '+8801623331050',
			'profile_location'           => 'Mirpur 10, Dhaka, Bangladesh',
			'profile_availability'       => 'Available for new projects',
			'profile_response_time'      => 'Replies within 24 hours',
			'profile_linkedin'           => 'https://www.linkedin.com/in/imran-sikdar-02183733b',
			'profile_image'              => AURORA_URI . '/assets/images/imran.jpg',
			'profile_cv'                 => AURORA_URI . '/assets/files/imran-cv.pdf',
			'footer_description'         => 'Web designer and WordPress developer in Dhaka, building modern, responsive websites and custom WordPress solutions for businesses that want to grow online.',
			'footer_note'                => 'Designed & built with care — and a lot of coffee.',

			// -----------------------------------------------------------------
			// Hero.
			// -----------------------------------------------------------------
			'hero_heading'               => 'Building Digital Experiences That Inspire.',
			'hero_typed_roles'           => 'Web Designer, WordPress Developer, Frontend Developer, UI/UX Designer',
			'hero_subtitle'              => 'I am MD Imran Hosan, a Web Designer and WordPress Developer passionate about creating modern, responsive, user-friendly, and visually stunning websites that help businesses grow online.',
			'hero_primary_label'         => 'View My Projects',
			'hero_primary_url'           => '#projects',
			'hero_secondary_label'       => 'Hire Me',
			'hero_secondary_url'         => '#contact',
			'hero_note'                  => 'Mirpur 10, Dhaka · Working worldwide',
			'hero_stat_1_value'          => 8,
			'hero_stat_1_label'          => 'Core services',
			'hero_stat_1_suffix'         => '',
			'hero_stat_2_value'          => 12,
			'hero_stat_2_label'          => 'Tech & skills',
			'hero_stat_2_suffix'         => '',
			'hero_stat_3_value'          => 24,
			'hero_stat_3_label'          => 'Reply window',
			'hero_stat_3_suffix'         => 'h',
			'hero_marquee'               => 'WordPress, PHP, JavaScript, WooCommerce, Figma, Elementor, Tailwind CSS, MySQL',

			// -----------------------------------------------------------------
			// About.
			// -----------------------------------------------------------------
			'about_eyebrow'              => 'About me',
			'about_title'                => 'A designer\'s eye.<br><em class="serif text-gradient">A developer\'s mind.</em>',
			'about_text'                 => "I'm MD Imran Hosan, a Web Designer and WordPress Developer based in Mirpur 10, Dhaka. I design and build modern, responsive websites and custom WordPress solutions that are fast, easy to manage, and focused on real business goals.\n\nMy work covers the full journey — from layout and visual design to custom WordPress themes, plugins and WooCommerce builds. I care about the details most people never notice: clear structure, readable code, sensible loading times, and an editing experience you can actually use after launch.",
			'about_points'               => "Design that tells your story, not just decorates a page\nWordPress builds that are easy to update and hard to break\nPerformance, accessibility and SEO considered from day one",
			'about_signature'            => 'Imran',
			'about_signature_role'       => 'Web Designer & WordPress Developer',
			'about_cv_label'             => 'Download CV',

			// -----------------------------------------------------------------
			// Section headings.
			// -----------------------------------------------------------------
			'services_eyebrow'           => 'What I do',
			'services_title'             => 'Services built for<br><em class="serif text-gradient">real business results.</em>',
			'services_text'              => 'From the first sketch to the final launch, I handle the design and development so you can focus on running your business. Every project is built with intention.',

			'skills_eyebrow'             => 'My toolkit',
			'skills_title'               => 'Skills refined<br><em class="serif text-gradient">through real work.</em>',
			'skills_text'                => "I don't collect tools for the sake of it. Everything in my toolkit has earned its place by helping me ship better work, faster.",
			'skills_panel_title'         => 'Technology stack',
			'skills_panel_badge'         => 'Tools I use daily',
			'skills_toolbox'             => "ACF Pro\nWooCommerce\nElementor\nGutenberg\nTailwind CSS\nGit & GitHub\nFigma\nMySQL\nREST API\nSEO",

			'projects_eyebrow'           => 'Selected work',
			'projects_title'             => 'Work that speaks<br><em class="serif text-gradient">for itself.</em>',
			'projects_text'              => 'A small selection of concept projects and builds. Each one explores a different challenge — from editorial WordPress to product interfaces.',
			'projects_note'              => 'Concept projects — live demos included',

			'process_eyebrow'            => 'How I work',
			'process_title'              => 'A clear process.<br><em class="serif text-gradient">Zero guesswork.</em>',
			'process_text'               => 'Six deliberate steps that keep your project moving — and keep you informed at every stage.',

			'why_eyebrow'                => 'Why work with me',
			'why_title'                  => 'A reliable partner,<br><em class="serif text-gradient">not just a developer.</em>',
			'why_text'                   => 'You get one person who understands both design and code — so fewer things get lost between the idea and the finished website.',

			'testimonials_eyebrow'       => 'Kind words',
			'testimonials_title'         => 'Feedback I\'m looking<br><em class="serif text-gradient">forward to sharing.</em>',
			'testimonials_text'          => "I'm building this portfolio one project at a time. Real client reviews will appear here as projects wrap up — no invented quotes, no borrowed names.",
			'testimonials_empty_title'   => 'No testimonials published yet.',
			'testimonials_empty_text'    => "Rather than fill this space with placeholder praise, I'd rather let the concept work speak for itself. If we work together, your genuine feedback can be the first review shown here — with your permission, of course.",

			// -----------------------------------------------------------------
			// Contact.
			// -----------------------------------------------------------------
			'contact_eyebrow'            => "Let's talk",
			'contact_title'              => 'Have a project in mind?<br><em class="serif text-gradient">Let\'s make it real.</em>',
			'contact_text'               => "Tell me a little about what you're building. I'll get back to you within one business day with honest thoughts and a clear next step.",
			'contact_email'              => 'imransikdar462@gmail.com',
			'contact_recipient'          => 'imransikdar462@gmail.com',
			'contact_phone_display'      => '+880 1623 331050',
			'contact_whatsapp'           => '8801623331050',
			'contact_location'           => 'Mirpur 10, Dhaka, Bangladesh',
			'contact_response'           => 'Replies within 24 hours',
			'contact_form_note'          => 'By sending this message you agree to be contacted about your project. No newsletters, no spam — ever.',

			// -----------------------------------------------------------------
			// Social profiles. Leave a field empty to hide that link.
			// -----------------------------------------------------------------
			'social_linkedin'            => 'https://www.linkedin.com/in/imran-sikdar-02183733b',
			'social_github'              => '',
			'social_facebook'            => '',
			'social_instagram'           => '',

			// -----------------------------------------------------------------
			// Colours.
			// -----------------------------------------------------------------
			'accent_color'               => '#8b5cf6',
			'cyan_color'                 => '#06b6d4',

			// -----------------------------------------------------------------
			// Visibility.
			// -----------------------------------------------------------------
			'show_about'                 => true,
			'show_services'              => true,
			'show_skills'                => true,
			'show_projects'              => true,
			'show_process'               => true,
			'show_why'                   => true,
			'show_testimonials'          => true,
			'show_contact'               => true,
		);
	}

	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}
