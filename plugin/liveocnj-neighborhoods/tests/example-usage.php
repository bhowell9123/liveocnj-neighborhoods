<?php
/**
 * Example Usage of ACF Integration Tests
 *
 * This file demonstrates how to include the ACF integration tests
 * in the main plugin file. This is for demonstration purposes only
 * and should not be included in the actual plugin.
 *
 * @package LiveOCNJ_Neighborhoods
 */

/**
 * Example 1: Using the test loader (recommended)
 * 
 * This is the simplest way to include all tests. Add this to the main plugin file
 * after the plugin header and before any other code.
 */

// Define the plugin file constant (already in the main plugin file)
if (!defined('LOCNJ_NEIGHBORHOODS_FILE')) define('LOCNJ_NEIGHBORHOODS_FILE', __FILE__);

// Include ACF integration tests (remove in production)
require_once __DIR__ . '/tests/load-tests.php';

/**
 * Example 2: Using individual test files
 * 
 * If you only want to include specific test files, you can include them individually.
 */

// Include only the test page
require_once __DIR__ . '/tests/acf-test.php';

// Or include only the admin notice
require_once __DIR__ . '/tests/acf-admin-notice.php';

/**
 * Example 3: Conditional loading based on environment
 * 
 * This example shows how to conditionally load the tests based on the environment.
 */

// Define a constant to indicate the environment
define('LOCNJ_NEIGHBORHOODS_ENV', 'development'); // Options: 'development', 'staging', 'production'

// Only load tests in development or staging environments
if (defined('LOCNJ_NEIGHBORHOODS_ENV') && in_array(LOCNJ_NEIGHBORHOODS_ENV, array('development', 'staging'))) {
    require_once __DIR__ . '/tests/load-tests.php';
}

/**
 * Example 4: Conditional loading based on user role
 * 
 * This example shows how to conditionally load the tests based on the user role.
 */

// Only load tests for administrators
add_action('init', function() {
    if (is_admin() && current_user_can('manage_options')) {
        require_once __DIR__ . '/tests/load-tests.php';
    }
});

/**
 * Example 5: Loading tests via a query parameter
 * 
 * This example shows how to load the tests only when a specific query parameter is present.
 */

// Only load tests when the 'acf_test' query parameter is present
add_action('init', function() {
    if (isset($_GET['acf_test']) && $_GET['acf_test'] === 'true' && current_user_can('manage_options')) {
        require_once __DIR__ . '/tests/load-tests.php';
    }
});

/**
 * IMPORTANT: This file is for demonstration purposes only.
 * Do not include this file in the actual plugin.
 * Instead, copy the relevant code snippets to the main plugin file as needed.
 */