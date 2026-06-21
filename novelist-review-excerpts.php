<?php
/**
 * Plugin Name: Novelist Review Excerpts
 * Plugin URI: https://novelistplugin.com/downloads/review-excerpts/
 * Description: Allows you to include excerpts of reviews on your book pages.
 * Version: 1.0.2-beta1
 * Author: Nose Graze
 * Author URI: https://www.nosegraze.com
 * License: GPL2
 * Text Domain: novelist-review-excerpts
 * Domain Path: languages
 *
 * @package   novelist-review-excerpts
 * @copyright Copyright (c) 2026, Nose Graze Ltd.
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (! version_compare(PHP_VERSION, '8.0', '>=')) {
    return;
}

// Plugin version.
if (! defined('NOVELIST_REVIEW_EXCERPTS_VERSION')) {
    define('NOVELIST_REVIEW_EXCERPTS_VERSION', '1.0.2-beta1');
}

// Plugin Folder Path.
if (! defined('NOVELIST_REVIEW_EXCERPTS_PLUGIN_DIR')) {
    define('NOVELIST_REVIEW_EXCERPTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

// Plugin Folder URL.
if (! defined('NOVELIST_REVIEW_EXCERPTS_PLUGIN_URL')) {
    define('NOVELIST_REVIEW_EXCERPTS_PLUGIN_URL', plugin_dir_url(__FILE__));
}

// Plugin Root File.
if (! defined('NOVELIST_REVIEW_EXCERPTS_PLUGIN_FILE')) {
    define('NOVELIST_REVIEW_EXCERPTS_PLUGIN_FILE', __FILE__);
}

/**
 * Get the add-on up and running.
 *
 * @since 1.0.0
 * @return Novelist_Review_Excerpts_Addon|void
 */
function Novelist_Review_Excerpts()
{
    require_once NOVELIST_REVIEW_EXCERPTS_PLUGIN_DIR.'vendor/autoload.php';

    if (! class_exists('Novelist_Extension_Activation')) {
        require_once 'includes/class-extension-activation.php';
    }

    $required_version = '1.0.3';
    $activation       = new Novelist_Extension_Activation('Novelist Review Excerpts', $required_version);

    if (! $activation->is_compatible()) {
        $activation->run();
    } else {
        require_once NOVELIST_REVIEW_EXCERPTS_PLUGIN_DIR.'Novelist_Review_Excerpts_Addon.php';

        return Novelist_Review_Excerpts_Addon::instance();
    }
}

add_action( 'plugins_loaded', 'Novelist_Review_Excerpts' );
