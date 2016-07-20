<?php
/**
 * Load Assets
 *
 * @package   novelist-review-excerpts
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

/**
 * Is Admin Page
 *
 * Checks whether or not the current page is a Novelist admin page.
 *
 * @since 1.0.0
 * @return bool
 */
function novelist_review_excerpts_is_admin_page() {
	$screen           = get_current_screen();
	$is_novelist_page = false;

	if ( $screen->base == 'post' && $screen->post_type == 'book' ) {
		$is_novelist_page = true;
	}

	return apply_filters( 'novelist-review-excerpts/is-admin-page', $is_novelist_page, $screen );
}

/**
 * Load Admin Scripts
 *
 * Adds all admin scripts and stylesheets to the admin panel.
 *
 * @param string $hook Currently loaded page
 *
 * @since 1.0.0
 * @return void
 */
function novelist_review_excerpts_load_assets( $hook ) {
	if ( ! apply_filters( 'novelist-review-excerpts/load-admin-scripts', novelist_review_excerpts_is_admin_page(), $hook ) ) {
		return;
	}

	$js_dir  = NOVELIST_REVIEW_EXCERPTS_PLUGIN_URL . 'assets/js/';
	$css_dir = NOVELIST_REVIEW_EXCERPTS_PLUGIN_URL . 'assets/css/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	/*
	 * JavaScript
	 */

	$admin_deps = array(
		'jquery'
	);

	wp_register_script( 'novelist-review-excerpts-admin-scripts', $js_dir . 'admin-scripts' . $suffix . '.js', $admin_deps, NOVELIST_REVIEW_EXCERPTS_VERSION, true );
	wp_enqueue_script( 'novelist-review-excerpts-admin-scripts' );

	/*
	 * Stylesheets
	 */

	wp_register_style( 'novelist-review-excerpts-admin', $css_dir . 'admin-css' . $suffix . '.css', NOVELIST_REVIEW_EXCERPTS_VERSION );
	wp_enqueue_style( 'novelist-review-excerpts-admin' );
}

add_action( 'admin_enqueue_scripts', 'novelist_review_excerpts_load_assets', 100 );