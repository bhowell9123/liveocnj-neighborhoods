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
     * ACF Fields instance
     *
     * @var LOCNJ_Neighborhoods_ACF_Fields
     */
    private $acf_fields;

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
        require_once __DIR__ . '/ACF_Fields.php';
        
        // Initialize ACF Fields integration
        $this->init_acf_fields();
        
        // Add actions and filters
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'add_custom_rewrite_rules'), 20);
        
        // Add filter to modify the main query for neighborhood URLs
        add_action('pre_get_posts', array($this, 'modify_neighborhood_query'), 5);
        
        // Add body classes for better CSS targeting
        add_filter('body_class', array($this, 'add_body_classes'));
        
        // Enqueue assets with high priority to ensure our CSS overrides theme styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'), 100);
        
        // Add template filter to use our custom templates
        add_filter('template_include', array($this, 'template_include'), 99);
        
        // Add page templates
        add_filter('theme_page_templates', array($this, 'add_page_templates'));
        
        // Register activation hook to flush rewrite rules
        register_activation_hook(LOCNJ_NEIGHBORHOODS_FILE, array($this, 'flush_rewrite_rules_on_activation'));
    }
    
    /**
     * Add body classes for better CSS targeting
     *
     * @param array $classes Existing body classes
     * @return array Modified body classes
     */
    public function add_body_classes($classes) {
        $q = get_queried_object();
        
        // Debug output
        if (current_user_can('manage_options')) {
            error_log('Add body classes called');
            error_log('Current queried object: ' . (is_object($q) ? get_class($q) : 'not an object'));
            if (is_object($q) && isset($q->post_name)) {
                error_log('Post name: ' . $q->post_name);
            }
            error_log('Is post type archive: ' . (is_post_type_archive('neighborhood') ? 'yes' : 'no'));
            error_log('Original classes: ' . implode(' ', $classes));
        }
        
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
            if (current_user_can('manage_options')) {
                error_log('Added ocnj-landing class');
            }
        }
        
        if (is_post_type_archive('neighborhood')) {
            $classes[] = 'ocnj-landing-archive';
            $classes[] = 'post-type-archive-neighborhood'; // Ensure this class is present
            if (current_user_can('manage_options')) {
                error_log('Added ocnj-landing-archive class');
            }
        }
        
        if (current_user_can('manage_options')) {
            error_log('Final classes: ' . implode(' ', $classes));
        }
        
        return $classes;
    }

    /**
     * Enqueue plugin assets
     */
    public function enqueue_assets() {
        // Debug output
        if (current_user_can('manage_options')) {
            error_log('Enqueue assets called - DEBUG: Checking if this method is still being called');
            error_log('Is archive: ' . (is_archive() ? 'yes' : 'no'));
            error_log('Is post type archive: ' . (is_post_type_archive('neighborhood') ? 'yes' : 'no'));
            error_log('Body classes: ' . implode(' ', get_body_class()));
        }
        
        // Main CSS
        $css_path = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'assets/css/ocnj-neighborhoods.css';
        wp_enqueue_style(
            'locnj-neighborhoods',
            plugins_url('assets/css/ocnj-neighborhoods.css', LOCNJ_NEIGHBORHOODS_FILE),
            [],
            file_exists($css_path) ? filemtime($css_path) : time()
        );
        
        if (current_user_can('manage_options')) {
            error_log('Main CSS enqueued: ' . plugins_url('assets/css/ocnj-neighborhoods.css', LOCNJ_NEIGHBORHOODS_FILE));
        }
        
        // Landing fixes CSS - load last to override other styles
        $fixes_path = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'assets/css/ocnj-landing-fixes.css';
        if (file_exists($fixes_path)) {
            wp_enqueue_style(
                'ocnj-landing-fixes',
                plugins_url('assets/css/ocnj-landing-fixes.css', LOCNJ_NEIGHBORHOODS_FILE),
                ['locnj-neighborhoods'],
                filemtime($fixes_path)
            );
            
            if (current_user_can('manage_options')) {
                error_log('Fixes CSS enqueued: ' . plugins_url('assets/css/ocnj-landing-fixes.css', LOCNJ_NEIGHBORHOODS_FILE));
            }
        } else {
            if (current_user_can('manage_options')) {
                error_log('Fixes CSS file not found: ' . $fixes_path);
            }
        }
        
        // JavaScript is enqueued in the main plugin file to prevent duplicate loading
        // This prevents the issue of the same script being loaded twice with different handles
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
    
    /**
     * Filter to use custom templates for neighborhood post type
     *
     * @param string $template The path of the template to include
     * @return string The path of the template to include
     */
    public function template_include($template) {
        // Enhanced debug output
        if (current_user_can('manage_options')) {
            error_log('Template include called for: ' . $_SERVER['REQUEST_URI']);
            error_log('Is archive: ' . (is_archive() ? 'yes' : 'no'));
            error_log('Is post type archive: ' . (is_post_type_archive('neighborhood') ? 'yes' : 'no'));
            error_log('Is singular neighborhood: ' . (is_singular('neighborhood') ? 'yes' : 'no'));
            
            // Get current post info if available
            global $post;
            if ($post) {
                error_log('Current post ID: ' . $post->ID);
                error_log('Current post type: ' . $post->post_type);
                error_log('Current post name: ' . $post->post_name);
            } else {
                error_log('No current post object available');
            }
            
            error_log('Original template: ' . $template);
            error_log('REQUEST_URI: ' . $_SERVER['REQUEST_URI']);
            error_log('QUERY_STRING: ' . $_SERVER['QUERY_STRING']);
            error_log('Current query vars: ' . print_r($GLOBALS['wp_query']->query_vars, true));
        }
        
        // DIRECT URL HANDLING - Check for individual neighborhood URLs first
        // This is the most specific check and should take precedence over everything else
        $request_uri = $_SERVER['REQUEST_URI'];
        if (strpos($request_uri, '/ocean-city-neighborhoods/') !== false &&
            substr_count($request_uri, '/') >= 2 &&
            strpos($request_uri, '/ocean-city-neighborhoods/') === 0) {
            
            // Extract the slug from the URL
            $slug = trim(str_replace('/ocean-city-neighborhoods/', '', $request_uri), '/');
            $slug = strtok($slug, '?'); // Remove any query parameters
            
            // If there's a slug and it's not empty, try to find the post
            if (!empty($slug)) {
                if (current_user_can('manage_options')) {
                    error_log('Extracted slug from URL: ' . $slug);
                }
                
                // Try to find the post using get_page_by_path first
                $neighborhood = get_page_by_path($slug, OBJECT, 'neighborhood');
                
                // If not found, try to find it using WP_Query
                if (!$neighborhood) {
                    $query = new WP_Query([
                        'post_type' => 'neighborhood',
                        'name' => $slug,
                        'posts_per_page' => 1
                    ]);
                    
                    if ($query->have_posts()) {
                        $neighborhood = $query->posts[0];
                    }
                }
                
                // If still not found, try to find it using get_posts
                if (!$neighborhood) {
                    $posts = get_posts([
                        'post_type' => 'neighborhood',
                        'name' => $slug,
                        'posts_per_page' => 1
                    ]);
                    
                    if (!empty($posts)) {
                        $neighborhood = $posts[0];
                    }
                }
                
                if ($neighborhood) {
                    $single_template = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'templates/single-neighborhood.php';
                    if (file_exists($single_template)) {
                        if (current_user_can('manage_options')) {
                            error_log('DIRECT URL MATCH: Using single neighborhood template for slug: ' . $slug);
                            error_log('Post ID: ' . $neighborhood->ID);
                            error_log('Template: ' . $single_template);
                        }
                        
                        // Force WordPress to recognize this as a single post
                        $GLOBALS['wp_query']->is_single = true;
                        $GLOBALS['wp_query']->is_singular = true;
                        $GLOBALS['wp_query']->is_archive = false;
                        $GLOBALS['wp_query']->is_post_type_archive = false;
                        
                        // Set the global post to this neighborhood
                        global $post;
                        $post = $neighborhood;
                        setup_postdata($post);
                        
                        return $single_template;
                    }
                } else if (current_user_can('manage_options')) {
                    error_log('No neighborhood found with slug: ' . $slug);
                    
                    // Debug: List all neighborhood posts
                    $all_neighborhoods = get_posts([
                        'post_type' => 'neighborhood',
                        'posts_per_page' => -1
                    ]);
                    
                    error_log('All neighborhood posts:');
                    foreach ($all_neighborhoods as $n) {
                        error_log('ID: ' . $n->ID . ', Slug: ' . $n->post_name . ', Title: ' . $n->post_title);
                    }
                }
            }
        }
        
        // Check if this is a page using our custom template
        if (is_page()) {
            $template_file = get_post_meta(get_the_ID(), '_wp_page_template', true);
            if ('page-neighborhoods-archive.php' === $template_file) {
                $page_template = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'templates/page-neighborhoods-archive.php';
                if (file_exists($page_template)) {
                    if (current_user_can('manage_options')) {
                        error_log('Using neighborhoods archive page template: ' . $page_template);
                    }
                    return $page_template;
                }
            }
            
            // Check if this is a child page of the neighborhoods parent page
            // This is to handle the conflict between regular pages and neighborhood custom post types
            global $post;
            if ($post && $post->post_parent) {
                // Get the parent page
                $parent = get_post($post->post_parent);
                
                // Check if the parent is the neighborhoods page
                if ($parent && $parent->post_name === 'ocean-city-neighborhoods-page') {
                    // This is a child page of the neighborhoods parent
                    // Check if there's a neighborhood post with the same slug
                    $neighborhood = get_page_by_path($post->post_name, OBJECT, 'neighborhood');
                    
                    if ($neighborhood) {
                        // We found a neighborhood post with the same slug
                        // Override the global post with the neighborhood post
                        $single_template = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'templates/single-neighborhood.php';
                        if (file_exists($single_template)) {
                            if (current_user_can('manage_options')) {
                                error_log('CONFLICT RESOLUTION: Using single neighborhood template for slug: ' . $post->post_name);
                                error_log('Original page ID: ' . $post->ID);
                                error_log('Neighborhood post ID: ' . $neighborhood->ID);
                                error_log('Template: ' . $single_template);
                            }
                            
                            // Force WordPress to recognize this as a single post
                            $GLOBALS['wp_query']->is_single = true;
                            $GLOBALS['wp_query']->is_singular = true;
                            $GLOBALS['wp_query']->is_archive = false;
                            $GLOBALS['wp_query']->is_post_type_archive = false;
                            
                            // Set the global post to this neighborhood
                            $post = $neighborhood;
                            setup_postdata($post);
                            
                            return $single_template;
                        }
                    }
                }
            }
        }
        
        // Fallback to standard WordPress conditional
        // Use our single template for neighborhood posts
        global $post;
        if ($post && $post->post_type === 'neighborhood' && !is_archive() && !is_home()) {
            $single_template = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'templates/single-neighborhood.php';
            if (file_exists($single_template)) {
                if (current_user_can('manage_options')) {
                    error_log('Using single neighborhood template for post ID: ' . $post->ID);
                    error_log('Post name: ' . $post->post_name);
                    error_log('Template: ' . $single_template);
                }
                return $single_template;
            }
        }
        
        // Use our archive template for neighborhood archives
        if (is_post_type_archive('neighborhood')) {
            $archive_template = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'templates/archive-neighborhood.php';
            if (file_exists($archive_template)) {
                if (current_user_can('manage_options')) {
                    error_log('Using custom archive template: ' . $archive_template);
                }
                return $archive_template;
            }
        }
        
        // Check if this is the neighborhood archive page by URL
        // Only use this if not already matched as a single neighborhood
        // This should only match the exact '/ocean-city-neighborhoods/' URL
        if ($_SERVER['REQUEST_URI'] === '/ocean-city-neighborhoods/' ||
            $_SERVER['REQUEST_URI'] === '/ocean-city-neighborhoods') {
            $archive_template = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'templates/archive-neighborhood.php';
            if (file_exists($archive_template)) {
                if (current_user_can('manage_options')) {
                    error_log('Using custom archive template for URL match: ' . $archive_template);
                }
                return $archive_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Add custom page templates
     *
     * @param array $templates Existing page templates
     * @return array Modified page templates
     */
    public function add_page_templates($templates) {
        $templates['page-neighborhoods-archive.php'] = 'Neighborhoods Archive';
        return $templates;
    }
    /**
     * Initialize ACF Fields integration
     *
     * Sets up the ACF Fields instance and initializes field registration
     */
    private function init_acf_fields() {
        // Check if ACF is active before initializing
        if (class_exists('ACF')) {
            // Initialize ACF Fields
            $this->acf_fields = LOCNJ_Neighborhoods_ACF_Fields::init();
            
            if (current_user_can('manage_options')) {
                error_log('ACF Fields initialized successfully');
            }
        } else {
            // Log error if ACF is not active
            if (current_user_can('manage_options')) {
                error_log('ACF plugin is not active - field registration skipped');
            }
        }
    }
    
    /**
     * Add custom rewrite rules for neighborhood URLs
     */
    public function add_custom_rewrite_rules() {
        // Debug output
        if (current_user_can('manage_options')) {
            error_log('Adding custom rewrite rules for neighborhood URLs');
        }
        
        // Add a custom rewrite rule for neighborhood URLs - more specific pattern
        add_rewrite_rule(
            '^ocean-city-neighborhoods/([^/]+)/?$',
            'index.php?post_type=neighborhood&name=$matches[1]',
            'top'
        );
        
        // Add a custom rewrite rule for neighborhood pagination
        add_rewrite_rule(
            '^ocean-city-neighborhoods/([^/]+)/page/?([0-9]{1,})/?$',
            'index.php?post_type=neighborhood&name=$matches[1]&paged=$matches[2]',
            'top'
        );
        
        // Add a custom rewrite rule for neighborhood feeds
        add_rewrite_rule(
            '^ocean-city-neighborhoods/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$',
            'index.php?post_type=neighborhood&name=$matches[1]&feed=$matches[2]',
            'top'
        );
        
        // Add a custom rewrite rule for neighborhood feeds (alternate format)
        add_rewrite_rule(
            '^ocean-city-neighborhoods/([^/]+)/(feed|rdf|rss|rss2|atom)/?$',
            'index.php?post_type=neighborhood&name=$matches[1]&feed=$matches[2]',
            'top'
        );
        
        // Add a fallback rule for any neighborhood URL
        add_rewrite_rule(
            'ocean-city-neighborhoods/(.+)/?$',
            'index.php?post_type=neighborhood&name=$matches[1]',
            'top'
        );
        
        // Debug output
        if (current_user_can('manage_options')) {
            global $wp_rewrite;
            error_log('Rewrite rules after adding custom rules: ' . print_r($wp_rewrite->rules, true));
        }
    }
    
    /**
     * Flush rewrite rules on plugin activation
     */
    public function flush_rewrite_rules_on_activation() {
        // Register post type first
        $this->register_post_types();
        
        // Add custom rewrite rules
        $this->add_custom_rewrite_rules();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        error_log('LOCNJ Neighborhoods: Rewrite rules flushed on activation');
    }
}

// Plugin is initialized via the plugins_loaded hook in the main plugin file