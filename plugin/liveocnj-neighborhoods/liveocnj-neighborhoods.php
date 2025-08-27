<?php
/**
 * Plugin Name: LiveOCNJ Neighborhoods
 * Description: Custom post type for Ocean City NJ neighborhoods with improved image handling
 * Version: 1.0.2
 * Author: LiveOCNJ
 * Text Domain: liveocnj-neighborhoods
 */
if (!defined('ABSPATH')) exit;

// Define plugin constants
if (!defined('LOCNJ_NEIGHBORHOODS_FILE')) define('LOCNJ_NEIGHBORHOODS_FILE', __FILE__);
if (!defined('LOCNJ_NEIGHBORHOODS_VERSION')) define('LOCNJ_NEIGHBORHOODS_VERSION', '1.0.2');

// Include compatibility file first for backward compatibility
if (file_exists(__DIR__ . '/src/Compatibility.php')) {
    require_once __DIR__ . '/src/Compatibility.php';
}

// Include core plugin files
if (file_exists(__DIR__ . '/src/Plugin.php')) {
    require_once __DIR__ . '/src/Plugin.php';
}

// Include rewrite rules fix
if (file_exists(__DIR__ . '/src/Rewrite_Rules_Fix.php')) {
    require_once __DIR__ . '/src/Rewrite_Rules_Fix.php';
}

// Include force flush rewrite rules
if (file_exists(__DIR__ . '/src/Force_Flush_Rewrite_Rules.php')) {
    require_once __DIR__ . '/src/Force_Flush_Rewrite_Rules.php';
}

// Include WP-CLI commands if WP-CLI is available
if (defined('WP_CLI') && WP_CLI && file_exists(__DIR__ . '/wp-cli-commands.php')) {
    require_once __DIR__ . '/wp-cli-commands.php';
}

// Make sure the plugin really boots
// This will also initialize ACF integration through the Plugin class
add_action('plugins_loaded', ['LOCNJ_Neighborhoods_Plugin', 'init'], 5);

// Initialize rewrite rules fix
add_action('plugins_loaded', ['LOCNJ_Neighborhoods_Rewrite_Rules_Fix', 'init'], 10);

// Initialize force flush rewrite rules
add_action('plugins_loaded', ['LOCNJ_Neighborhoods_Force_Flush_Rewrite_Rules', 'init'], 11);

// Add shortcode for rendering neighborhoods
add_action('init', function () {
  add_shortcode('ocnj_neighborhoods', function () {
    ob_start();
    include __DIR__ . '/templates/partials/global-faq.php';      // or your archive renderer
    include __DIR__ . '/templates/archive-neighborhood.php';     // if you want to render the grid here
    return ob_get_clean();
  });
});

// Ensure CSS is properly enqueued
add_action('wp_enqueue_scripts', function() {
    if (is_post_type_archive('neighborhood') || is_singular('neighborhood')) {
        wp_enqueue_style(
            'ocnj-neighborhoods-css',
            plugins_url('assets/css/ocnj-neighborhoods.css', LOCNJ_NEIGHBORHOODS_FILE),
            [],
            LOCNJ_NEIGHBORHOODS_VERSION
        );
        wp_enqueue_style(
            'ocnj-landing-fixes-css',
            plugins_url('assets/css/ocnj-landing-fixes.css', LOCNJ_NEIGHBORHOODS_FILE),
            ['ocnj-neighborhoods-css'],
            LOCNJ_NEIGHBORHOODS_VERSION
        );
    }
}, 20);

// Ensure JavaScript is properly enqueued
add_action('wp_enqueue_scripts', function() {
    if (is_post_type_archive('neighborhood') || is_singular('neighborhood')) {
        // Main neighborhoods script
        wp_enqueue_script(
            'ocnj-neighborhoods-js',
            plugins_url('assets/js/ocnj-neighborhoods.js', LOCNJ_NEIGHBORHOODS_FILE),
            ['jquery'],
            LOCNJ_NEIGHBORHOODS_VERSION,
            true
        );
        
        // FAQ accordion script - properly enqueued instead of direct script tag
        wp_enqueue_script(
            'ocnj-faq-accordion-js',
            plugins_url('assets/js/ocnj-faq-accordion.js', LOCNJ_NEIGHBORHOODS_FILE),
            ['jquery'],
            LOCNJ_NEIGHBORHOODS_VERSION,
            true
        );
    }
}, 20);