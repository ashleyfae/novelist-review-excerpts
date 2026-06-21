<?php

use AshleyFae\NovelistLicensing\License;

/**
 * Novelist_Review_Excerpts_Addon.php
 *
 * @package   novelist-review-excerpts
 * @copyright Copyright (c) 2026, Ashley Gibson
 * @license   MIT
 */
class Novelist_Review_Excerpts_Addon
{
    /**
     * Instance of the ARC Requests Add-On class
     *
     * @var Novelist_Review_Excerpts_Addon
     * @since 1.0.0
     */
    private static Novelist_Review_Excerpts_Addon $instance;

    /**
     * Novelist_Review_Excerpts_Addon instance.
     *
     * Insures that only one instance of Novelist_Review_Excerpts_Addon exists at any one time.
     *
     * @access public
     * @since  1.0.0
     * @return Novelist_Review_Excerpts_Addon Instance of Novelist_Review_Excerpts_Addon class
     */
    public static function instance(): Novelist_Review_Excerpts_Addon
    {
        if (! isset(self::$instance)) {
            self::$instance = new Novelist_Review_Excerpts_Addon;
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
     * @since  1.0.0
     * @return void
     */
    public function __clone()
    {
        // Cloning instances of the class is forbidden.
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'novelist-review-excerpts'), '1.0.0');
    }

    /**
     * Disable unserializing of the class.
     *
     * @since  1.0.0
     * @return void
     */
    public function __wakeup()
    {
        // Unserializing instances of the class is forbidden.
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'novelist-review-excerpts'), '1.0.0');
    }

    /**
     * Include Required Files
     *
     * @since  1.0.0
     */
    private function includes() : void
    {
        require_once NOVELIST_REVIEW_EXCERPTS_PLUGIN_DIR.'vendor/autoload.php';
        require_once NOVELIST_REVIEW_EXCERPTS_PLUGIN_DIR.'includes/field-functions.php';
        require_once NOVELIST_REVIEW_EXCERPTS_PLUGIN_DIR.'includes/load-assets.php';
    }

    /**
     * Hooks
     *
     * Set up license key field and automatic updates.
     *
     * @since  1.0.0
     */
    private function hooks(): void
    {
        if (! class_exists(License::class)) {
            return;
        }

        new License(
            pluginName: 'Review Excerpts',
            pluginFile: NOVELIST_REVIEW_EXCERPTS_PLUGIN_FILE,
            productUuid: 'da0e5577-412c-4c8e-a5b8-9c84b9e63ff4',
            currentPluginVersion: NOVELIST_REVIEW_EXCERPTS_VERSION,
            optionName: 'novelist_review_excerpts_license_key'
        );
    }

    /**
     * Loads the plugin language files.
     *
     * @since  1.0.0
     */
    public function load_textdomain(): void
    {
        $lang_dir = NOVELIST_REVIEW_EXCERPTS_PLUGIN_DIR.'/languages/';
        $lang_dir = apply_filters('novelist-review-excerpts/languages-directory', $lang_dir);
        load_plugin_textdomain('novelist-review-excerpts', false, $lang_dir);
    }
}
