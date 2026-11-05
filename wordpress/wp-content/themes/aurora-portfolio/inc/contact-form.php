<?php
/**
 * WordPress contact form handler.
 *
 * The form posts to admin-post.php with the aurora_contact action. All fields
 * are validated server-side and delivered through wp_mail(), so SMTP
 * credentials never reach the browser.
 *
 * @package Aurora_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the request expects a JSON response.
 *
 * @return bool
 */
function aurora_contact_is_ajax() {
	$requested_with = isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) ) : '';
	$accept         = isset( $_SERVER['HTTP_ACCEPT'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT'] ) ) ) : '';

	return 'xmlhttprequest' === $requested_with || false !== strpos( $accept, 'application/json' );
}

/**
 * Send a success or error response.
 *
 * @param bool   $success Success state.
 * @param string $message Human-readable message.
 * @param int    $status  HTTP status code.
 * @param string $code    Short code used for non-JavaScript redirects.
 * @return void
 */
function aurora_contact_respond( $success, $message, $status = 200, $code = 'validation' ) {
	if ( aurora_contact_is_ajax() ) {
		status_header( $status );
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'X-Content-Type-Options: nosniff' );

		echo wp_json_encode(
			array(
				'success' => (bool) $success,
				'message' => (string) $message,
			),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		);
		exit;
	}

	$target = wp_get_referer();
	if ( ! $target ) {
		$target = home_url( '/' );
	}

	$target = remove_query_arg( array( 'sent', 'error' ), $target );
	$target = add_query_arg( $success ? 'sent' : 'error', $success ? '1' : $code, $target ) . '#contact';

	wp_safe_redirect( $target, 303 );
	exit;
}

/**
 * Multibyte-safe string length.
 *
 * @param string $value Value.
 * @return int
 */
function aurora_contact_length( $value ) {
	return function_exists( 'mb_strlen' ) ? mb_strlen( $value ) : strlen( $value );
}

/**
 * Read a posted text field.
 *
 * @param string $key Field name.
 * @return string
 */
function aurora_contact_field( $key ) {
	return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
}

/**
 * Rate limit by IP address.
 *
 * @return bool True when the request is allowed.
 */
function aurora_contact_rate_allowed() {
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$key = 'aurora_contact_' . md5( $ip );

	$attempts = get_transient( $key );
	$attempts = is_array( $attempts ) ? $attempts : array();
	$now      = time();

	$attempts = array_values(
		array_filter(
			$attempts,
			static function ( $time ) use ( $now ) {
				return is_int( $time ) && ( $now - $time ) < 600;
			}
		)
	);

	if ( count( $attempts ) >= 3 ) {
		return false;
	}

	$attempts[] = $now;
	set_transient( $key, $attempts, 600 );

	return true;
}

/**
 * Process the contact form.
 *
 * @return void
 */
function aurora_handle_contact_form() {
	if ( 'POST' !== strtoupper( isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '' ) ) {
		aurora_contact_respond( false, __( 'Method not allowed. Please submit the contact form.', 'aurora-portfolio' ), 405, 'method' );
	}

	$nonce = isset( $_POST['aurora_contact_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['aurora_contact_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'aurora_contact' ) ) {
		aurora_contact_respond( false, __( 'Your form session expired. Please refresh the page and try again.', 'aurora-portfolio' ), 419, 'security' );
	}

	// Honeypot: bots fill this hidden field. Pretend success without sending.
	$honeypot = isset( $_POST['website'] ) ? sanitize_text_field( wp_unslash( $_POST['website'] ) ) : '';
	if ( '' !== $honeypot ) {
		aurora_contact_respond( true, __( 'Thanks — your message has been sent.', 'aurora-portfolio' ) );
	}

	$name         = aurora_contact_field( 'name' );
	$email        = aurora_contact_field( 'email' );
	$phone        = aurora_contact_field( 'phone' );
	$location     = aurora_contact_field( 'location' );
	$subject      = aurora_contact_field( 'subject' );
	$project_type = aurora_contact_field( 'project_type' );
	$budget       = aurora_contact_field( 'budget' );
	$message      = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	$name_length = aurora_contact_length( $name );
	if ( $name_length < 2 || $name_length > 100 ) {
		aurora_contact_respond( false, __( 'Please enter your name (2–100 characters).', 'aurora-portfolio' ), 422 );
	}

	if ( aurora_contact_length( $email ) > 190 || ! is_email( $email ) ) {
		aurora_contact_respond( false, __( 'Please enter a valid email address.', 'aurora-portfolio' ), 422 );
	}

	if ( aurora_contact_length( $phone ) < 7 || aurora_contact_length( $phone ) > 24 || ! preg_match( '/^\+?[0-9\s().-]{7,24}$/', $phone ) ) {
		aurora_contact_respond( false, __( 'Please enter a valid phone number.', 'aurora-portfolio' ), 422 );
	}

	if ( aurora_contact_length( $location ) > 120 ) {
		aurora_contact_respond( false, __( 'Please keep the location under 120 characters.', 'aurora-portfolio' ), 422 );
	}

	if ( aurora_contact_length( $subject ) > 180 ) {
		aurora_contact_respond( false, __( 'Please keep the subject under 180 characters.', 'aurora-portfolio' ), 422 );
	}

	if ( ! in_array( $project_type, aurora_contact_project_types(), true ) ) {
		aurora_contact_respond( false, __( 'Please choose a valid project type.', 'aurora-portfolio' ), 422 );
	}

	if ( ! in_array( $budget, aurora_contact_budgets(), true ) ) {
		aurora_contact_respond( false, __( 'Please choose a valid budget range.', 'aurora-portfolio' ), 422 );
	}

	if ( aurora_contact_length( $message ) < 10 || aurora_contact_length( $message ) > 6000 ) {
		aurora_contact_respond( false, __( 'Please write a message between 10 and 6,000 characters.', 'aurora-portfolio' ), 422 );
	}

	if ( preg_match_all( '~https?://~i', $message ) > 6 ) {
		aurora_contact_respond( false, __( 'That message contains too many links. Please shorten it and try again.', 'aurora-portfolio' ), 422 );
	}

	if ( ! aurora_contact_rate_allowed() ) {
		$profile = aurora_get_profile();
		aurora_contact_respond(
			false,
			sprintf(
				/* translators: %s: email address. */
				__( 'Too many messages in a short time. Please wait a few minutes or email %s directly.', 'aurora-portfolio' ),
				$profile['email']
			),
			429,
			'rate'
		);
	}

	$profile   = aurora_get_profile();
	$recipient = (string) aurora_mod( 'contact_recipient' );

	if ( ! is_email( $recipient ) ) {
		$recipient = (string) aurora_mod( 'contact_email' );
	}

	if ( ! is_email( $recipient ) ) {
		$recipient = $profile['email'];
	}

	$subject_line = '' !== $subject ? $subject : __( 'New project enquiry', 'aurora-portfolio' );
	$mail_subject = sprintf( '[Portfolio] %1$s — %2$s', $project_type, $name );

	$body  = "New message from your portfolio contact form\n";
	$body .= str_repeat( '-', 58 ) . "\n\n";
	$body .= 'Name:         ' . $name . "\n";
	$body .= 'Email:        ' . $email . "\n";
	$body .= 'Phone:        ' . $phone . "\n";
	$body .= 'Location:     ' . ( '' !== $location ? $location : 'Not provided' ) . "\n";
	$body .= 'Subject:      ' . $subject_line . "\n";
	$body .= 'Project type: ' . $project_type . "\n";
	$body .= 'Budget:       ' . ( '' !== $budget ? $budget : 'Not provided' ) . "\n\n";
	$body .= "Message:\n" . $message . "\n\n";
	$body .= str_repeat( '-', 58 ) . "\n";
	$body .= 'Sent: ' . gmdate( 'Y-m-d H:i:s' ) . " UTC\n";

	$site_name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$site_name = $site_name ? $site_name : 'Portfolio';
	$admin     = sanitize_email( (string) get_option( 'admin_email' ) );
	$from      = $admin ? $admin : $recipient;

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'From: ' . $site_name . ' <' . $from . '>',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	$sent = wp_mail( $recipient, $mail_subject, $body, $headers );

	if ( ! $sent ) {
		aurora_contact_respond(
			false,
			sprintf(
				/* translators: %s: email address. */
				__( 'The message could not be sent right now. Please email %s directly.', 'aurora-portfolio' ),
				$profile['email']
			),
			500,
			'server'
		);
	}

	aurora_contact_respond(
		true,
		__( 'Thanks for reaching out — your message has been sent. I will reply within one business day.', 'aurora-portfolio' )
	);
}
add_action( 'admin_post_aurora_contact', 'aurora_handle_contact_form' );
add_action( 'admin_post_nopriv_aurora_contact', 'aurora_handle_contact_form' );
