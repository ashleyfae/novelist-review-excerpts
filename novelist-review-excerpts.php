<?php
/**
 * Plugin Name: Novelist Review Excerpts
 * Plugin URI: https://novelistplugin.com/downloads/review-excerpts/
 * Description: Allows you to include excerpts of reviews on your book pages.
 * Version: 1.0
 * Author: Nose Graze
 * Author URI: https://www.nosegraze.com
 * License: GPL2
 * Text Domain: novelist-review-excerpts
 * Domain Path: languages
 *
 * @package   novelist-review-excerpts
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Novelist_Review_Excerpts_Addon' ) ) :

	class Novelist_Review_Excerpts_Addon {

		/**
		 * Instance of the ARC Requests Add-On class
		 *
		 * @var Novelist_Review_Excerpts_Addon
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Novelist_Review_Excerpts_Addon instance.
		 *
		 * Insures that only one instance of Novelist_Review_Excerpts_Addon exists at any one time.
		 *
		 * @uses   Novelist::setup_constants() Set up the plugin constants.
		 * @uses   Novelist::includes() Include any required files.
		 * @uses   Novelist::load_textdomain() Load the language files.
		 *
		 * @access public
		 * @since  1.0.0
		 * @return Novelist_Review_Excerpts_Addon Instance of Novelist_Review_Excerpts_Addon class
		 */
		public static function instance() {

			if ( ! self::$instance ) {
				self::$instance = new Novelist_Review_Excerpts_Addon;
				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				self::$instance->includes();
				self::$instance->hooks();
			}

			return self::$instance;

		}

		/**
		 * Throw error on object clone.
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @access protected
		 * @since  1.0.0
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'novelist-review-excerpts' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access protected
		 * @since  1.0.0
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'novelist-review-excerpts' ), '1.0.0' );
		}

		/**
		 * Setup plugin constants.
		 *
		 * @access private
		 * @since  1.0.0
		 * @return void
		 */
		private function setup_constants() {

			// Plugin version.
			if ( ! defined( 'NOVELIST_REVIEW_EXCERPTS_VERSION' ) ) {
				define( 'NOVELIST_REVIEW_EXCERPTS_VERSION', '1.0.0' );
			}

			// Plugin Folder Path.
			if ( ! defined( 'NOVELIST_REVIEW_EXCERPTS_PLUGIN_DIR' ) ) {
				define( 'NOVELIST_REVIEW_EXCERPTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'NOVELIST_REVIEW_EXCERPTS_PLUGIN_URL' ) ) {
				define( 'NOVELIST_REVIEW_EXCERPTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File.
			if ( ! defined( 'NOVELIST_REVIEW_EXCERPTS_PLUGIN_FILE' ) ) {
				define( 'NOVELIST_REVIEW_EXCERPTS_PLUGIN_FILE', __FILE__ );
			}

		}

		/**
		 * Include Required Files
		 *
		 * @access private
		 * @since  1.0.0
		 * @return void
		 */
		private function includes() {

			require_once NOVELIST_REVIEW_EXCERPTS_PLUGIN_DIR . 'includes/field-functions.php';
			require_once NOVELIST_REVIEW_EXCERPTS_PLUGIN_DIR . 'includes/load-assets.php';

		}

		/**
		 * Hooks
		 *
		 * Set up license key field and automatic updates.
		 *
		 * @access private
		 * @since  1.0.0
		 * @return void
		 */
		private function hooks() {

			if ( ! class_exists( 'Novelist_License' ) ) {
				require_once NOVELIST_REVIEW_EXCERPTS_PLUGIN_DIR . 'includes/updater/class-novelist-license.php';
			}

			$novelist_license = new Novelist_License( __FILE__, 'Review Excerpts', NOVELIST_REVIEW_EXCERPTS_VERSION, 'Ashley Gibson', 'novelist_review_excerpts_license_key' );

		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access public
		 * @since  1.0.0
		 * @return void
		 */
		public function load_textdomain() {

			$lang_dir = NOVELIST_REVIEW_EXCERPTS_PLUGIN_DIR . '/languages/';
			$lang_dir = apply_filters( 'novelist-review-excerpts/languages-directory', $lang_dir );
			load_plugin_textdomain( 'novelist-review-excerpts', false, $lang_dir );

		}

	}

endif; // End class check

/**
 * Get the add-on up and running.
 *
 * @since 1.0.0
 * @return Novelist_Review_Excerpts_Addon|void
 */
function Novelist_Review_Excerpts() {
	if ( ! class_exists( 'Novelist_Extension_Activation' ) ) {
		require_once 'includes/class-extension-activation.php';
	}

	$required_version = '1.0.3';
	$activation       = new Novelist_Extension_Activation( __( 'Novelist Review Excerpts', 'novelist-review-excerpts' ), $required_version );

	if ( ! $activation->is_compatible() ) {
		$activation->run();
	} else {
		return Novelist_Review_Excerpts_Addon::instance();
	}
}

add_action( 'plugins_loaded', 'Novelist_Review_Excerpts' );