<?php
/**
 * ACF Integration Test File
 *
 * This file provides testing and verification for the ACF integration
 * in the LiveOCNJ Neighborhoods plugin.
 *
 * @package LiveOCNJ_Neighborhoods
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ACF Integration Test Class
 *
 * Provides methods to test and verify the ACF integration.
 */
class LOCNJ_Neighborhoods_ACF_Test {

    /**
     * ACF field group key
     *
     * @var string
     */
    private $field_group_key = 'group_neighborhood_locnj';

    /**
     * Constructor
     */
    public function __construct() {
        // Add admin menu item for the test
        add_action('admin_menu', array($this, 'add_test_menu'));
        
        // Add admin notice to display test results
        add_action('admin_notices', array($this, 'display_test_notice'));
    }

    /**
     * Add test menu item under Tools
     */
    public function add_test_menu() {
        // Only visible to administrators
        if (!current_user_can('manage_options')) {
            return;
        }

        add_management_page(
            'ACF Integration Test',
            'ACF Integration Test',
            'manage_options',
            'locnj-acf-test',
            array($this, 'render_test_page')
        );
    }

    /**
     * Render the test page
     */
    public function render_test_page() {
        // Security check
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        echo '<div class="wrap">';
        echo '<h1>' . __('ACF Integration Test', 'liveocnj-neighborhoods') . '</h1>';
        
        // Run the tests
        $this->run_tests();
        
        echo '</div>';
    }

    /**
     * Run all tests and display results
     */
    public function run_tests() {
        echo '<div class="notice notice-info">';
        echo '<p><strong>' . __('Running ACF Integration Tests...', 'liveocnj-neighborhoods') . '</strong></p>';
        
        // Test 1: Check if ACF is active
        $this->test_acf_active();
        
        // Test 2: Verify field group registration
        $this->test_field_group_registration();
        
        // Test 3: Test field access
        $this->test_field_access();
        
        echo '</div>';
    }

    /**
     * Test if ACF is active
     */
    public function test_acf_active() {
        echo '<h3>' . __('Test 1: ACF Plugin Status', 'liveocnj-neighborhoods') . '</h3>';
        
        if (class_exists('ACF')) {
            echo '<p class="success">' . __('✅ SUCCESS: Advanced Custom Fields is active.', 'liveocnj-neighborhoods') . '</p>';
            echo '<p>' . __('ACF Version: ', 'liveocnj-neighborhoods') . ACF()->version . '</p>';
        } else {
            echo '<p class="error">' . __('❌ ERROR: Advanced Custom Fields is not active.', 'liveocnj-neighborhoods') . '</p>';
            echo '<p>' . __('Please activate the ACF plugin to use the LiveOCNJ Neighborhoods plugin.', 'liveocnj-neighborhoods') . '</p>';
        }
    }

    /**
     * Test field group registration
     */
    public function test_field_group_registration() {
        echo '<h3>' . __('Test 2: Field Group Registration', 'liveocnj-neighborhoods') . '</h3>';
        
        if (!class_exists('ACF')) {
            echo '<p class="error">' . __('❌ SKIPPED: ACF is not active.', 'liveocnj-neighborhoods') . '</p>';
            return;
        }
        
        if (!function_exists('acf_get_field_group')) {
            echo '<p class="error">' . __('❌ ERROR: ACF function acf_get_field_group() not found.', 'liveocnj-neighborhoods') . '</p>';
            return;
        }
        
        $field_group = acf_get_field_group($this->field_group_key);
        
        if ($field_group) {
            echo '<p class="success">' . __('✅ SUCCESS: Field group is registered.', 'liveocnj-neighborhoods') . '</p>';
            echo '<p>' . __('Field Group Title: ', 'liveocnj-neighborhoods') . esc_html($field_group['title']) . '</p>';
            
            // Check if the field group is active
            if (isset($field_group['active']) && $field_group['active']) {
                echo '<p class="success">' . __('✅ Field group is active.', 'liveocnj-neighborhoods') . '</p>';
            } else {
                echo '<p class="warning">' . __('⚠️ WARNING: Field group is registered but not active.', 'liveocnj-neighborhoods') . '</p>';
            }
            
            // Check location rules
            if (isset($field_group['location']) && !empty($field_group['location'])) {
                echo '<p class="success">' . __('✅ Field group has location rules.', 'liveocnj-neighborhoods') . '</p>';
                
                // Display location rules
                echo '<p>' . __('Location Rules:', 'liveocnj-neighborhoods') . '</p>';
                echo '<ul>';
                foreach ($field_group['location'] as $location_group) {
                    foreach ($location_group as $location_rule) {
                        echo '<li>' . esc_html($location_rule['param']) . ' ' . esc_html($location_rule['operator']) . ' ' . esc_html($location_rule['value']) . '</li>';
                    }
                }
                echo '</ul>';
            } else {
                echo '<p class="warning">' . __('⚠️ WARNING: Field group has no location rules.', 'liveocnj-neighborhoods') . '</p>';
            }
        } else {
            echo '<p class="error">' . __('❌ ERROR: Field group is not registered.', 'liveocnj-neighborhoods') . '</p>';
            
            // Check if the field group JSON file exists
            $acf_json_dir = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'acf-json';
            $json_file = $acf_json_dir . '/' . $this->field_group_key . '.json';
            
            if (file_exists($json_file)) {
                echo '<p>' . __('JSON file exists at: ', 'liveocnj-neighborhoods') . esc_html($json_file) . '</p>';
                echo '<p>' . __('But the field group was not loaded. Check ACF JSON sync settings.', 'liveocnj-neighborhoods') . '</p>';
            } else {
                echo '<p>' . __('JSON file does not exist at: ', 'liveocnj-neighborhoods') . esc_html($json_file) . '</p>';
            }
        }
    }

    /**
     * Test field access
     */
    public function test_field_access() {
        echo '<h3>' . __('Test 3: Field Access', 'liveocnj-neighborhoods') . '</h3>';
        
        if (!class_exists('ACF')) {
            echo '<p class="error">' . __('❌ SKIPPED: ACF is not active.', 'liveocnj-neighborhoods') . '</p>';
            return;
        }
        
        if (!function_exists('acf_get_fields')) {
            echo '<p class="error">' . __('❌ ERROR: ACF function acf_get_fields() not found.', 'liveocnj-neighborhoods') . '</p>';
            return;
        }
        
        $fields = acf_get_fields($this->field_group_key);
        
        if ($fields) {
            echo '<p class="success">' . __('✅ SUCCESS: Fields are accessible.', 'liveocnj-neighborhoods') . '</p>';
            echo '<p>' . __('Found ', 'liveocnj-neighborhoods') . count($fields) . __(' fields in the field group.', 'liveocnj-neighborhoods') . '</p>';
            
            // Display field types
            echo '<table class="widefat">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>' . __('Field Name', 'liveocnj-neighborhoods') . '</th>';
            echo '<th>' . __('Field Type', 'liveocnj-neighborhoods') . '</th>';
            echo '<th>' . __('Field Key', 'liveocnj-neighborhoods') . '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            foreach ($fields as $field) {
                echo '<tr>';
                echo '<td>' . esc_html($field['label']) . '</td>';
                echo '<td>' . esc_html($field['type']) . '</td>';
                echo '<td>' . esc_html($field['key']) . '</td>';
                echo '</tr>';
                
                // If this is a repeater or group field, show sub fields
                if (in_array($field['type'], array('repeater', 'group')) && isset($field['sub_fields'])) {
                    foreach ($field['sub_fields'] as $sub_field) {
                        echo '<tr>';
                        echo '<td> — ' . esc_html($sub_field['label']) . '</td>';
                        echo '<td>' . esc_html($sub_field['type']) . '</td>';
                        echo '<td>' . esc_html($sub_field['key']) . '</td>';
                        echo '</tr>';
                    }
                }
            }
            
            echo '</tbody>';
            echo '</table>';
            
            // Test field data retrieval with a sample post
            $this->test_field_data_retrieval();
        } else {
            echo '<p class="error">' . __('❌ ERROR: Fields are not accessible.', 'liveocnj-neighborhoods') . '</p>';
        }
    }

    /**
     * Test field data retrieval with a sample post
     */
    private function test_field_data_retrieval() {
        echo '<h3>' . __('Test 4: Field Data Retrieval', 'liveocnj-neighborhoods') . '</h3>';
        
        // Get a sample neighborhood post
        $posts = get_posts(array(
            'post_type' => 'neighborhood',
            'posts_per_page' => 1,
        ));
        
        if (empty($posts)) {
            echo '<p class="warning">' . __('⚠️ WARNING: No neighborhood posts found for testing field data retrieval.', 'liveocnj-neighborhoods') . '</p>';
            return;
        }
        
        $post_id = $posts[0]->ID;
        echo '<p>' . __('Testing with neighborhood post: ', 'liveocnj-neighborhoods') . esc_html($posts[0]->post_title) . ' (ID: ' . $post_id . ')</p>';
        
        // Test basic field retrieval
        $heading = get_field('neighborhood_heading', $post_id);
        $about = get_field('neighborhood_about', $post_id);
        
        echo '<h4>' . __('Basic Fields', 'liveocnj-neighborhoods') . '</h4>';
        echo '<ul>';
        echo '<li><strong>Heading:</strong> ' . (empty($heading) ? '(empty)' : esc_html($heading)) . '</li>';
        echo '<li><strong>About:</strong> ' . (empty($about) ? '(empty)' : esc_html($about)) . '</li>';
        echo '</ul>';
        
        // Test repeater field
        $facts = get_field('neighborhood_facts', $post_id);
        echo '<h4>' . __('Repeater Field (Facts)', 'liveocnj-neighborhoods') . '</h4>';
        
        if (is_array($facts) && !empty($facts)) {
            echo '<p class="success">' . __('✅ Repeater field data retrieved successfully.', 'liveocnj-neighborhoods') . '</p>';
            echo '<ul>';
            foreach ($facts as $fact) {
                echo '<li>' . esc_html($fact['text']) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="warning">' . __('⚠️ No data in repeater field or field not set.', 'liveocnj-neighborhoods') . '</p>';
        }
        
        // Test group field
        $market_data = get_field('neighborhood_market_data', $post_id);
        echo '<h4>' . __('Group Field (Market Data)', 'liveocnj-neighborhoods') . '</h4>';
        
        if (is_array($market_data) && !empty($market_data)) {
            echo '<p class="success">' . __('✅ Group field data retrieved successfully.', 'liveocnj-neighborhoods') . '</p>';
            echo '<ul>';
            foreach ($market_data as $key => $value) {
                echo '<li><strong>' . esc_html($key) . ':</strong> ' . esc_html($value) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="warning">' . __('⚠️ No data in group field or field not set.', 'liveocnj-neighborhoods') . '</p>';
        }
        
        // Test image field
        $hero_image = get_field('neighborhood_hero_image', $post_id);
        echo '<h4>' . __('Image Field (Hero Image)', 'liveocnj-neighborhoods') . '</h4>';
        
        if (is_array($hero_image) && !empty($hero_image)) {
            echo '<p class="success">' . __('✅ Image field data retrieved successfully.', 'liveocnj-neighborhoods') . '</p>';
            echo '<p><img src="' . esc_url($hero_image['sizes']['thumbnail']) . '" alt="' . esc_attr($hero_image['alt']) . '" /></p>';
            echo '<ul>';
            echo '<li><strong>URL:</strong> ' . esc_html($hero_image['url']) . '</li>';
            echo '<li><strong>Title:</strong> ' . esc_html($hero_image['title']) . '</li>';
            echo '<li><strong>Alt:</strong> ' . esc_html($hero_image['alt']) . '</li>';
            echo '</ul>';
        } else {
            echo '<p class="warning">' . __('⚠️ No data in image field or field not set.', 'liveocnj-neighborhoods') . '</p>';
        }
    }

    /**
     * Display admin notice with test results
     */
    public function display_test_notice() {
        // Only show to administrators
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Only show on specific pages
        $screen = get_current_screen();
        if (!$screen || !in_array($screen->id, array('dashboard', 'plugins', 'edit-neighborhood'))) {
            return;
        }
        
        // Check if ACF is active
        $acf_active = class_exists('ACF');
        
        // Check if field group is registered
        $field_group_registered = false;
        if ($acf_active && function_exists('acf_get_field_group')) {
            $field_group_registered = acf_get_field_group($this->field_group_key) !== false;
        }
        
        // Display notice
        if (!$acf_active) {
            echo '<div class="notice notice-error">';
            echo '<p><strong>' . __('LiveOCNJ Neighborhoods ACF Integration:', 'liveocnj-neighborhoods') . '</strong> ' . __('Advanced Custom Fields is not active. Please activate the ACF plugin.', 'liveocnj-neighborhoods') . '</p>';
            echo '</div>';
        } elseif (!$field_group_registered) {
            echo '<div class="notice notice-warning">';
            echo '<p><strong>' . __('LiveOCNJ Neighborhoods ACF Integration:', 'liveocnj-neighborhoods') . '</strong> ' . __('ACF is active, but the Neighborhood field group is not registered.', 'liveocnj-neighborhoods') . '</p>';
            echo '<p><a href="' . admin_url('tools.php?page=locnj-acf-test') . '">' . __('Run ACF Integration Test', 'liveocnj-neighborhoods') . '</a></p>';
            echo '</div>';
        } else {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p><strong>' . __('LiveOCNJ Neighborhoods ACF Integration:', 'liveocnj-neighborhoods') . '</strong> ' . __('ACF integration is working correctly.', 'liveocnj-neighborhoods') . '</p>';
            echo '<p><a href="' . admin_url('tools.php?page=locnj-acf-test') . '">' . __('Run ACF Integration Test', 'liveocnj-neighborhoods') . '</a></p>';
            echo '</div>';
        }
    }

    /**
     * Static initialization method
     * 
     * @return LOCNJ_Neighborhoods_ACF_Test Instance of this class
     */
    public static function init() {
        return new self();
    }
}

// Add some CSS for the test page
add_action('admin_head', function() {
    ?>
    <style>
        .success {
            color: #46b450;
            font-weight: bold;
        }
        .error {
            color: #dc3232;
            font-weight: bold;
        }
        .warning {
            color: #ffb900;
            font-weight: bold;
        }
    </style>
    <?php
});

// Initialize the test class if we're in the admin area
if (is_admin()) {
    LOCNJ_Neighborhoods_ACF_Test::init();
}