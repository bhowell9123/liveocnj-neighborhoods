<?php
/**
 * ACF Integration Test Loader
 *
 * This file provides a simple way to load all ACF integration tests.
 * Include this file in the main plugin file to enable all tests.
 *
 * Usage: require_once __DIR__ . '/tests/load-tests.php';
 *
 * @package LiveOCNJ_Neighborhoods
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Only load tests for administrators
if (!function_exists('current_user_can') || !current_user_can('manage_options')) {
    return;
}

// Load the test files
require_once __DIR__ . '/acf-test.php';
require_once __DIR__ . '/acf-admin-notice.php';

// Add a notice that tests are active
add_action('admin_notices', function() {
    echo '<div class="notice notice-warning is-dismissible">';
    echo '<p><strong>' . __('LiveOCNJ Neighborhoods Test Mode:', 'liveocnj-neighborhoods') . '</strong> ';
    echo __('ACF integration tests are currently active. These should be disabled in production.', 'liveocnj-neighborhoods') . '</p>';
    echo '<p><code>// Remove this line before deploying to production<br>';
    echo 'require_once __DIR__ . \'/tests/load-tests.php\';</code></p>';
    echo '</div>';
});

// Add a dashboard widget to show test status
add_action('wp_dashboard_setup', function() {
    wp_add_dashboard_widget(
        'locnj_neighborhoods_test_status',
        __('LiveOCNJ Neighborhoods Test Mode', 'liveocnj-neighborhoods'),
        function() {
            echo '<div style="background-color: #fef8e8; border-left: 4px solid #ffb900; padding: 10px; margin-bottom: 15px;">';
            echo '<p style="margin: 0;"><strong>' . __('Test Mode Active', 'liveocnj-neighborhoods') . '</strong></p>';
            echo '<p style="margin: 5px 0 0;">' . __('ACF integration tests are currently active. These should be disabled in production.', 'liveocnj-neighborhoods') . '</p>';
            echo '</div>';
            
            echo '<p><a href="' . admin_url('tools.php?page=locnj-acf-test') . '" class="button button-primary">';
            echo __('Run ACF Integration Test', 'liveocnj-neighborhoods') . '</a></p>';
        }
    );
});