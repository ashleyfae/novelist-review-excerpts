<?php

/**
 * Activation Handler
 *
 * Checks to make sure the Novelist plugin is activated and the necessary version is installed.
 *
 * @package   novelist-review-excerpts
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Novelist_Extension_Activation {

	public $plugin_name, $has_novelist, $novelist_version, $required_novelist_version, $novelist_base;

	/**
	 * Setup the activation class
	 *
	 * @param string $plugin_name Name of the extension plugin.
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function __construct( $plugin_name, $required_version = '1.0.0' ) {
		$this->plugin_name               = $plugin_name;
		$this->required_novelist_version = $required_version;
	}

	/**
	 * Checks to see if this extension should be run.
	 *
	 * Returns true if:
	 *      Novelist is installed, and
	 *      the Novelist version is greater than or equal to the required version.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return bool
	 */
	public function is_compatible() {
		if ( ! class_exists( 'Novelist' ) || ! defined( 'NOVELIST_VERSION' ) ) {
			return false;
		}

		return ( version_compare( NOVELIST_VERSION, $this->required_novelist_version, '>=' ) );
	}

	/**
	 * Add Admin Notice
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function run() {
		// We need plugin.php!
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$plugins = get_plugins();

		// Is Novelist installed?
		foreach ( $plugins as $plugin_path => $plugin ) {
			if ( $plugin['Name'] == 'Novelist' ) {
				$this->has_novelist     = true;
				$this->novelist_base    = $plugin_path;
				$this->novelist_version = $plugin['Version'];
				break;
			}
		}

		add_action( 'admin_notices', array( $this, 'requirements_not_met_notice' ) );
	}

	/**
	 * Display Notice
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function requirements_not_met_notice() {
		if ( class_exists( 'Novelist' ) && version_compare( $this->novelist_version, $this->required_novelist_version, '<' ) ) {

			// Novelist is installed, but not up to date.
			$message = sprintf(
				__( "%s requires Novelist verrsion %s or higher. You're using version %s. Please update to the latest version.", 'novelist-review-excerpts' ),
				$this->plugin_name,
				$this->required_novelist_version,
				$this->novelist_version
			);

		} else {
			if ( $this->has_novelist ) {
				// Has Novelist installed, but not activated.
				$url  = esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $this->novelist_base ), 'activate-plugin_' . $this->novelist_base ) );
				$link = '<a href="' . $url . '">' . __( 'activate it', 'novelist-review-excerpts' ) . '</a>';

				$message = sprintf(
					__( '%s requires the Novelist plugin. Please %s to continue!', 'novelist-review-excerpts' ),
					$this->plugin_name,
					$link
				);
			} else {
				// Novelist not installed at all.
				$url  = esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=novelist' ), 'install-plugin_novelist' ) );
				$link = '<a href="' . $url . '">' . __( 'install it', 'novelist-review-excerpts' ) . '</a>';

				$message = sprintf(
					__( '%s requires the Novelist plugin. Please %s to continue!', 'novelist-review-excerpts' ),
					$this->plugin_name,
					$link
				);
			}
		}

		echo '<div class="error"><p>' . $message . '</p></div>';
	}

}