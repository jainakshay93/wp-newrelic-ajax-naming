<?php
/**
 * Plugin Name: New Relic AJAX Naming
 * Description: Names WordPress admin-ajax.php transactions in New Relic by their action
 *              parameter, so each AJAX action appears as a separate transaction instead of
 *              all being grouped under admin-ajax.php.
 * Version:     1.0.0
 * Author:      WisdmLabs
 * License:     GPL-2.0-or-later
 *
 * @package NewRelicAjaxNaming
 *
 * Drop this file into wp-content/mu-plugins/.
 * Requires the New Relic PHP agent (newrelic_name_transaction must be available).
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_init',
	function () {
		if ( ! wp_doing_ajax() ) {
			return;
		}
		if ( ! function_exists( 'newrelic_name_transaction' ) ) {
			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended -- reading the AJAX routing key only, not processing form submission data.
		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		// harness-ignore: wp-input-sanitization -- action is a routing key, not user-submitted content.
		$raw_action = isset( $_POST['action'] ) ? sanitize_key( wp_unslash( $_POST['action'] ) ) : ( isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : 'unknown' );
		// phpcs:enable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$action = $raw_action;

		newrelic_name_transaction( "AJAX/{$action}" );
	},
	1
);
