<?php
/**
 * Plugin Name: LiveOCNJ Neighborhoods
 * Description: Custom post type for Ocean City NJ neighborhoods with improved image handling
 * Version: 1.0.0
 * Author: LiveOCNJ
 * Text Domain: liveocnj-neighborhoods
 */
if (!defined('ABSPATH')) exit;

// Define the plugin file constant for correct asset URLs
if (!defined('LOCNJ_NEIGHBORHOODS_FILE')) define('LOCNJ_NEIGHBORHOODS_FILE', __FILE__);

// Include compatibility file first for backward compatibility
if (file_exists(__DIR__ . '/src/Compatibility.php')) {
    require_once __DIR__ . '/src/Compatibility.php';
}

// Include core plugin files
if (file_exists(__DIR__ . '/src/Plugin.php')) {
    require_once __DIR__ . '/src/Plugin.php';
}

// Make sure the plugin really boots
add_action('plugins_loaded', ['LOCNJ_Neighborhoods_Plugin', 'init'], 5);