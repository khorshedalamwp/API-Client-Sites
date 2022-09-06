<?php
/**
 * Plugin Name: Browsing Statistics - Client Site
 * Plugin URI: https://pluginbazar.com/plugin/visitors
 * Description: This plugin for count websites visitors.
 * Version: 1.0.0
 * Author: Pluginbazar
 * Text Domain: bs-client
 * Domain Path: /languages/
 * Author URI: https://pluginbazar.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit;

/**
 * All hooks.
 */

add_action( 'load-plugins.php', 'bs_client_send_requested_data' );

if ( ! function_exists( 'bs_client_send_requested_data' ) ) {
	/**
     *
	 * Send requested data.
	 *
	 * @return void
	 *
	 */
	function bs_client_send_requested_data() {
		$date_time = date( 'U' );
		$userName  = wp_get_current_user()->user_nicename;
		$url       = get_site_url();

		$args = array(
			'method' => 'POST',
			'body'   => array(
				'user_name' => sanitize_text_field( $userName ),
				'url'       => sanitize_url( $url ),
				'date_time' => sanitize_text_field( $date_time ),
			),
		);

		$transient_key = 'data_sent_' . date( 'd-m-y' );
		$get_transient = get_transient( $transient_key );
		$request       = array();

		if ( false === $get_transient ) {
			set_transient( $transient_key, date( 'h:i:s' ), 24 * HOUR_IN_SECONDS );
		}
		if ( empty( $get_transient ) ) {
			$sent_data = wp_remote_post( 'http://jaed.pro/wp-json/server/v1/pluginbazar', $args );
			$request   = json_decode( wp_remote_retrieve_body( $sent_data ), true );
		}

		if ( isset( $request['status'] ) && $request['status'] == 'success' ) {
			add_action( 'admin_notices', 'bs_client_admin_page_notice_success' );
		} else {
			add_action( 'admin_notices', 'bs_client_admin_page_notice_failed' );
		}
	}
}

if ( ! function_exists( 'bs_client_admin_page_notice_success' ) ) {
	/**
     *
	 *  Data sent successfully notification in admin page.
	 *
	 * @return void
     *
	 */
	function bs_client_admin_page_notice_success() {
		$notice = esc_html__( 'Data Sent Successfully!' );
		?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( $notice, 'bs-client' ); ?></p>
        </div>
		<?php
	}
}

if ( ! function_exists( 'bs_client_admin_page_notice_failed' ) ) {
	/**
	 *
	 * Data sent failed notification in admin page.
	 *
	 * @return void
	 *
	 */
	function bs_client_admin_page_notice_failed() {
		$notice = esc_html__( 'Data Sent Failed!' );
		?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( $notice, 'bs-client' ); ?></p>
        </div>
		<?php
	}
}
