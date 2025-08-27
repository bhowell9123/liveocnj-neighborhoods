<?php
/**
 * Rewrite Rules Fix
 * 
 * This file provides functionality to automatically flush rewrite rules
 * when the plugin is activated, preventing navigation issues with neighborhood URLs.
 */

if (!defined('ABSPATH')) {
    exit;
}

class LOCNJ_Neighborhoods_Rewrite_Rules_Fix {
    
    /**
     * Initialize the rewrite rules fix
     */
    public static function init() {
        // Hook into plugin activation
        register_activation_hook(LOCNJ_NEIGHBORHOODS_FILE, [__CLASS__, 'on_activation']);
        
        // Hook into admin init to check if rewrite rules need flushing
        add_action('admin_init', [__CLASS__, 'maybe_flush_rewrite_rules']);
        
        // Add admin notice if rewrite rules need attention
        add_action('admin_notices', [__CLASS__, 'rewrite_rules_notice']);
        
        // Add custom rewrite rules for neighborhood URLs
        add_action('init', [__CLASS__, 'add_custom_rewrite_rules'], 10);
    }
    
    /**
     * Run when plugin is activated
     */
    public static function on_activation() {
        // Register the post type first
        LOCNJ_Neighborhoods_PostType_Neighborhood::register();
        
        // Add custom rewrite rules
        if (method_exists('LOCNJ_Neighborhoods_Plugin', 'add_custom_rewrite_rules')) {
            $plugin = new LOCNJ_Neighborhoods_Plugin();
            $plugin->add_custom_rewrite_rules();
        }
        
        // Delete existing rewrite rules
        delete_option('rewrite_rules');
        
        // Flush rewrite rules to ensure URLs work
        flush_rewrite_rules(true);
        
        // Set a flag that we've flushed rules
        update_option('locnj_rewrite_rules_flushed', time());
        
        error_log('LOCNJ Neighborhoods: Rewrite rules forcefully flushed on activation');
    }
    
    /**
     * Check if rewrite rules need to be flushed
     */
    public static function maybe_flush_rewrite_rules() {
        // Check if we need to flush rewrite rules
        $last_flushed = get_option('locnj_rewrite_rules_flushed', 0);
        $plugin_version = get_option('locnj_plugin_version', '1.0.0');
        
        // If it's been more than a day since last flush, or version changed
        if ((time() - $last_flushed) > DAY_IN_SECONDS || $plugin_version !== '1.0.2') {
            // Register post type first
            LOCNJ_Neighborhoods_PostType_Neighborhood::register();
            
            // Flush rewrite rules
            flush_rewrite_rules();
            
            // Update timestamps
            update_option('locnj_rewrite_rules_flushed', time());
            update_option('locnj_plugin_version', '1.0.2');
            
            error_log('LOCNJ Neighborhoods: Rewrite rules auto-flushed');
        }
    }
    
    /**
     * Show admin notice if there are rewrite rule issues
     */
    public static function rewrite_rules_notice() {
        // Check if individual neighborhood URLs are working
        $test_post = get_posts([
            'post_type' => 'neighborhood',
            'posts_per_page' => 1,
            'post_status' => 'publish'
        ]);
        
        if (!empty($test_post)) {
            $permalink = get_permalink($test_post[0]->ID);
            $expected_pattern = '/ocean-city-neighborhoods/';
            
            if (!strpos($permalink, $expected_pattern)) {
                ?>
                <div class="notice notice-warning is-dismissible">
                    <p>
                        <strong>OCNJ Neighborhoods:</strong> 
                        Individual neighborhood URLs may not be working correctly. 
                        <a href="<?php echo admin_url('options-permalink.php'); ?>">
                            Visit Permalinks settings
                        </a> and click "Save Changes" to fix this issue.
                    </p>
                </div>
                <?php
            }
        }
    }
    
    /**
     * Manual flush function for troubleshooting
     */
    public static function manual_flush() {
        // Register the post type first
        LOCNJ_Neighborhoods_PostType_Neighborhood::register();
        
        // Add custom rewrite rules
        if (method_exists('LOCNJ_Neighborhoods_Plugin', 'add_custom_rewrite_rules')) {
            $plugin = new LOCNJ_Neighborhoods_Plugin();
            $plugin->add_custom_rewrite_rules();
        }
        
        // Delete existing rewrite rules
        delete_option('rewrite_rules');
        
        // Flush rewrite rules
        flush_rewrite_rules(true);
        
        // Update timestamp
        update_option('locnj_rewrite_rules_flushed', time());
        
        return [
            'success' => true,
            'message' => 'Rewrite rules forcefully flushed manually',
            'timestamp' => time()
        ];
    }
    
    /**
     * Debug function to check rewrite rules
     */
    public static function debug_rewrite_rules() {
        global $wp_rewrite;
        
        $rules = get_option('rewrite_rules');
        $neighborhood_rules = [];
        
        foreach ($rules as $pattern => $replacement) {
            if (strpos($pattern, 'ocean-city-neighborhoods') !== false) {
                $neighborhood_rules[$pattern] = $replacement;
            }
        }
        
        return [
            'neighborhood_rules' => $neighborhood_rules,
            'total_rules' => count($rules),
            'last_flushed' => get_option('locnj_rewrite_rules_flushed', 'Never'),
            'post_type_registered' => post_type_exists('neighborhood')
        ];
    }
    /**
     * Add custom rewrite rules for neighborhood URLs
     */
    public static function add_custom_rewrite_rules() {
        // Add a custom rewrite rule for neighborhood URLs
        add_rewrite_rule(
            'ocean-city-neighborhoods/([^/]+)/?$',
            'index.php?post_type=neighborhood&name=$matches[1]',
            'top'
        );
        
        // Add a custom rewrite rule for neighborhood pagination
        add_rewrite_rule(
            'ocean-city-neighborhoods/([^/]+)/page/?([0-9]{1,})/?$',
            'index.php?post_type=neighborhood&name=$matches[1]&paged=$matches[2]',
            'top'
        );
        
        // Add a custom rewrite rule for neighborhood feeds
        add_rewrite_rule(
            'ocean-city-neighborhoods/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$',
            'index.php?post_type=neighborhood&name=$matches[1]&feed=$matches[2]',
            'top'
        );
        
        // Add a custom rewrite rule for neighborhood feeds (alternate format)
        add_rewrite_rule(
            'ocean-city-neighborhoods/([^/]+)/(feed|rdf|rss|rss2|atom)/?$',
            'index.php?post_type=neighborhood&name=$matches[1]&feed=$matches[2]',
            'top'
        );
    }
}

// WP-CLI Commands for troubleshooting
if (defined('WP_CLI') && WP_CLI) {
    
    class LOCNJ_Neighborhoods_CLI_Commands {
        
        /**
         * Flush rewrite rules for neighborhoods
         */
        public function flush_rewrite() {
            $result = LOCNJ_Neighborhoods_Rewrite_Rules_Fix::manual_flush();
            WP_CLI::success($result['message']);
        }
        
        /**
         * Debug rewrite rules
         */
        public function debug_rewrite() {
            $debug = LOCNJ_Neighborhoods_Rewrite_Rules_Fix::debug_rewrite_rules();
            
            WP_CLI::line('=== REWRITE RULES DEBUG ===');
            WP_CLI::line('Post type registered: ' . ($debug['post_type_registered'] ? 'Yes' : 'No'));
            WP_CLI::line('Total rewrite rules: ' . $debug['total_rules']);
            WP_CLI::line('Last flushed: ' . date('Y-m-d H:i:s', $debug['last_flushed']));
            WP_CLI::line('');
            WP_CLI::line('Neighborhood-specific rules:');
            
            foreach ($debug['neighborhood_rules'] as $pattern => $replacement) {
                WP_CLI::line("  $pattern => $replacement");
            }
        }
        
        /**
         * Test neighborhood URLs
         */
        public function test_urls() {
            $posts = get_posts([
                'post_type' => 'neighborhood',
                'posts_per_page' => 5,
                'post_status' => 'publish'
            ]);
            
            WP_CLI::line('=== NEIGHBORHOOD URL TEST ===');
            
            foreach ($posts as $post) {
                $permalink = get_permalink($post->ID);
                $slug = $post->post_name;
                
                WP_CLI::line("$post->post_title:");
                WP_CLI::line("  Slug: $slug");
                WP_CLI::line("  URL: $permalink");
                WP_CLI::line("");
            }
        }
    }
    
    WP_CLI::add_command('ocnj', 'LOCNJ_Neighborhoods_CLI_Commands');
}