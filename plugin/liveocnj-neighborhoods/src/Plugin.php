<?php
/**
 * Main plugin class file
 *
 * @package LiveOCNJ_Neighborhoods
 */

defined('ABSPATH') || exit;

/**
 * Main plugin class
 */
class LOCNJ_Neighborhoods_Plugin {

    /**
     * Static initialization method called by the plugins_loaded hook
     */
    public static function init() {
        // Create an instance of this class
        new self();
    }

    /**
     * Constructor
     */
    public function __construct() {
        // Initialize plugin components
        $this->setup();
    }

    /**
     * Initialize plugin components
     */
    private function setup() {
        // Include helper files
        require_once __DIR__ . '/Image_Helpers.php';
        require_once __DIR__ . '/PostType_Neighborhood.php';
        
        // Add actions and filters
        add_action('init', array($this, 'register_post_types'));
        
        // Add body classes for better CSS targeting
        add_filter('body_class', array($this, 'add_body_classes'));
        
        // Enqueue assets with high priority to ensure our CSS overrides theme styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'), 100);
    }
    
    /**
     * Add body classes for better CSS targeting
     *
     * @param array $classes Existing body classes
     * @return array Modified body classes
     */
    public function add_body_classes($classes) {
        $q = get_queried_object();
        
        // Safer than hardcoding IDs - check for specific page/post slugs
        $is_landing = ($q && isset($q->post_name) && in_array($q->post_name, [
            'ocean-city-neighborhoods',               // landing page slug
            'exploring-ocean-city-nj-08226-your-complete-neighborhood-guide' // post slug
        ], true));
        
        // Also check for specific post IDs as fallback
        if (!$is_landing && (is_page(169371) || is_single(175056))) {
            $is_landing = true;
        }
        
        // Add appropriate classes
        if ($is_landing) {
            $classes[] = 'ocnj-landing';
        }
        
        if (is_post_type_archive('neighborhood')) {
            $classes[] = 'ocnj-landing-archive';
        }
        
        return $classes;
    }

    /**
     * Enqueue plugin assets
     */
    public function enqueue_assets() {
        // Main CSS
        $css_path = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'assets/css/ocnj-neighborhoods.css';
        wp_enqueue_style(
            'locnj-neighborhoods',
            plugins_url('assets/css/ocnj-neighborhoods.css', LOCNJ_NEIGHBORHOODS_FILE),
            [],
            file_exists($css_path) ? filemtime($css_path) : time()
        );
        
        // Landing fixes CSS - load last to override other styles
        $fixes_path = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'assets/css/ocnj-landing-fixes.css';
        if (file_exists($fixes_path)) {
            wp_enqueue_style(
                'ocnj-landing-fixes',
                plugins_url('assets/css/ocnj-landing-fixes.css', LOCNJ_NEIGHBORHOODS_FILE),
                ['locnj-neighborhoods'],
                filemtime($fixes_path)
            );
        }
        
        // JavaScript for functionality
        $js_path = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'assets/js/ocnj-neighborhoods.js';
        if (file_exists($js_path)) {
            wp_enqueue_script(
                'ocnj-neighborhoods',
                plugins_url('assets/js/ocnj-neighborhoods.js', LOCNJ_NEIGHBORHOODS_FILE),
                ['jquery'],
                filemtime($js_path),
                true
            );
            wp_script_add_data('ocnj-neighborhoods', 'defer', true);
        }
    }

    /**
     * Register custom post types
     */
    public function register_post_types() {
        // Register the neighborhood post type
        LOCNJ_Neighborhoods_PostType_Neighborhood::register();
    }
    
    /**
     * Quick smoke test function to verify CSS is loaded
     * Can be called via AJAX or WP CLI
     */
    public static function verify_css_loaded() {
        $url = home_url('/ocean-city-neighborhoods/');
        $response = wp_remote_get($url);
        
        if (is_wp_error($response)) {
            return "Error: " . $response->get_error_message();
        }
        
        $body = wp_remote_retrieve_body($response);
        if (strpos($body, 'ocnj-landing-fixes.css') === false) {
            return "Warning: Fixes CSS not found in page source";
        }
        
        return "Success: Fixes CSS found in page source";
    }
}

// Plugin is initialized via the plugins_loaded hook in the main plugin file