<?php
/**
 * ACF Integration Admin Notice
 *
 * This file provides a simple admin notice function that can be temporarily
 * added to the plugin to verify the ACF integration.
 *
 * Usage: Include this file in the main plugin file to display the admin notice.
 * Example: require_once __DIR__ . '/tests/acf-admin-notice.php';
 *
 * @package LiveOCNJ_Neighborhoods
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add admin notice to verify ACF integration
 */
function locnj_neighborhoods_acf_verification_notice() {
    // Only show to administrators
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Check if ACF is active
    $acf_active = class_exists('ACF');
    
    // Check if field group is registered
    $field_group_registered = false;
    if ($acf_active && function_exists('acf_get_field_group')) {
        $field_group_registered = acf_get_field_group('group_neighborhood_locnj') !== false;
    }
    
    // Check if we have neighborhood posts
    $neighborhood_posts = get_posts(array(
        'post_type' => 'neighborhood',
        'posts_per_page' => 1,
    ));
    
    // Display notice
    echo '<div class="notice notice-info is-dismissible">';
    echo '<h3>' . __('LiveOCNJ Neighborhoods ACF Integration Status', 'liveocnj-neighborhoods') . '</h3>';
    echo '<ul style="list-style-type: disc; padding-left: 20px;">';
    
    // ACF Status
    if ($acf_active) {
        echo '<li style="color: #46b450;">' . __('✅ ACF Plugin: Active (Version: ', 'liveocnj-neighborhoods') . ACF()->version . ')</li>';
    } else {
        echo '<li style="color: #dc3232;">' . __('❌ ACF Plugin: Not active', 'liveocnj-neighborhoods') . '</li>';
    }
    
    // Field Group Status
    if ($field_group_registered) {
        echo '<li style="color: #46b450;">' . __('✅ Neighborhood Field Group: Registered', 'liveocnj-neighborhoods') . '</li>';
    } else {
        echo '<li style="color: #dc3232;">' . __('❌ Neighborhood Field Group: Not registered', 'liveocnj-neighborhoods') . '</li>';
    }
    
    // JSON Directory Status
    $acf_json_dir = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'acf-json';
    if (is_dir($acf_json_dir)) {
        echo '<li style="color: #46b450;">' . __('✅ ACF JSON Directory: Exists', 'liveocnj-neighborhoods') . '</li>';
    } else {
        echo '<li style="color: #dc3232;">' . __('❌ ACF JSON Directory: Does not exist', 'liveocnj-neighborhoods') . '</li>';
    }
    
    // JSON File Status
    $json_file = $acf_json_dir . '/group_neighborhood_locnj.json';
    if (file_exists($json_file)) {
        echo '<li style="color: #46b450;">' . __('✅ ACF JSON File: Exists', 'liveocnj-neighborhoods') . '</li>';
    } else {
        echo '<li style="color: #dc3232;">' . __('❌ ACF JSON File: Does not exist', 'liveocnj-neighborhoods') . '</li>';
    }
    
    // Neighborhood Posts Status
    if (!empty($neighborhood_posts)) {
        echo '<li style="color: #46b450;">' . __('✅ Neighborhood Posts: Found', 'liveocnj-neighborhoods') . '</li>';
    } else {
        echo '<li style="color: #ffb900;">' . __('⚠️ Neighborhood Posts: None found', 'liveocnj-neighborhoods') . '</li>';
    }
    
    echo '</ul>';
    
    // Add link to full test page if available
    if (file_exists(plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'tests/acf-test.php')) {
        echo '<p><a href="' . admin_url('tools.php?page=locnj-acf-test') . '" class="button button-primary">' . __('Run Full ACF Integration Test', 'liveocnj-neighborhoods') . '</a></p>';
    }
    
    echo '<p><em>' . __('This notice is for development and testing purposes only. Remove it from production by commenting out the include statement in the main plugin file.', 'liveocnj-neighborhoods') . '</em></p>';
    echo '</div>';
}

// Add the admin notice
add_action('admin_notices', 'locnj_neighborhoods_acf_verification_notice');

/**
 * Add a dashboard widget for ACF integration status
 */
function locnj_neighborhoods_add_acf_dashboard_widget() {
    // Only show to administrators
    if (!current_user_can('manage_options')) {
        return;
    }
    
    wp_add_dashboard_widget(
        'locnj_neighborhoods_acf_status',
        __('LiveOCNJ Neighborhoods ACF Status', 'liveocnj-neighborhoods'),
        'locnj_neighborhoods_acf_dashboard_widget_callback'
    );
}
add_action('wp_dashboard_setup', 'locnj_neighborhoods_add_acf_dashboard_widget');

/**
 * Dashboard widget callback
 */
function locnj_neighborhoods_acf_dashboard_widget_callback() {
    // Check if ACF is active
    $acf_active = class_exists('ACF');
    
    // Check if field group is registered
    $field_group_registered = false;
    if ($acf_active && function_exists('acf_get_field_group')) {
        $field_group_registered = acf_get_field_group('group_neighborhood_locnj') !== false;
    }
    
    // Display status
    echo '<div style="margin-bottom: 15px;">';
    
    if ($acf_active && $field_group_registered) {
        echo '<div style="background-color: #f0f9e8; border-left: 4px solid #46b450; padding: 10px;">';
        echo '<p style="margin: 0;"><strong>' . __('ACF Integration Status:', 'liveocnj-neighborhoods') . '</strong> ' . __('Working correctly', 'liveocnj-neighborhoods') . '</p>';
        echo '</div>';
    } elseif ($acf_active && !$field_group_registered) {
        echo '<div style="background-color: #fef8e8; border-left: 4px solid #ffb900; padding: 10px;">';
        echo '<p style="margin: 0;"><strong>' . __('ACF Integration Status:', 'liveocnj-neighborhoods') . '</strong> ' . __('ACF is active, but field group is not registered', 'liveocnj-neighborhoods') . '</p>';
        echo '</div>';
    } else {
        echo '<div style="background-color: #fef7f7; border-left: 4px solid #dc3232; padding: 10px;">';
        echo '<p style="margin: 0;"><strong>' . __('ACF Integration Status:', 'liveocnj-neighborhoods') . '</strong> ' . __('ACF is not active', 'liveocnj-neighborhoods') . '</p>';
        echo '</div>';
    }
    
    echo '</div>';
    
    // Add link to full test page if available
    if (file_exists(plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'tests/acf-test.php')) {
        echo '<p><a href="' . admin_url('tools.php?page=locnj-acf-test') . '" class="button">' . __('Run Full ACF Integration Test', 'liveocnj-neighborhoods') . '</a></p>';
    }
}