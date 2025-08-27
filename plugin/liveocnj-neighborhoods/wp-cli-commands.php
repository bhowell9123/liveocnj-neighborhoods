<?php
/**
 * WP-CLI Commands for OCNJ Neighborhoods
 * 
 * This file provides WP-CLI commands for managing the OCNJ Neighborhoods plugin.
 * 
 * Usage:
 * wp ocnj flush-rewrite - Flush rewrite rules
 * wp ocnj force-flush - Force flush rewrite rules (deletes rules first)
 * wp ocnj test-urls - Test neighborhood URLs
 * wp ocnj debug-rewrite - Debug rewrite rules
 */

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

/**
 * Manages OCNJ Neighborhoods plugin functionality.
 */
class LOCNJ_Neighborhoods_CLI_Commands {
    
    /**
     * Flush rewrite rules for neighborhoods
     * 
     * ## EXAMPLES
     * 
     *     wp ocnj flush-rewrite
     * 
     * @when after_wp_load
     */
    public function flush_rewrite() {
        // Register post type first
        LOCNJ_Neighborhoods_PostType_Neighborhood::register();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        WP_CLI::success('Rewrite rules flushed');
    }
    
    /**
     * Force flush rewrite rules for neighborhoods (deletes rules first)
     * 
     * ## EXAMPLES
     * 
     *     wp ocnj force-flush
     * 
     * @when after_wp_load
     */
    public function force_flush() {
        if (class_exists('LOCNJ_Neighborhoods_Force_Flush_Rewrite_Rules')) {
            $result = LOCNJ_Neighborhoods_Force_Flush_Rewrite_Rules::manual_force_flush();
            WP_CLI::success($result['message']);
        } else {
            // Fallback if the class doesn't exist
            // Register post type first
            LOCNJ_Neighborhoods_PostType_Neighborhood::register();
            
            // Delete existing rewrite rules
            delete_option('rewrite_rules');
            
            // Flush rewrite rules
            flush_rewrite_rules(true);
            
            WP_CLI::success('Rewrite rules forcefully flushed');
        }
    }
    
    /**
     * Debug rewrite rules
     * 
     * ## EXAMPLES
     * 
     *     wp ocnj debug-rewrite
     * 
     * @when after_wp_load
     */
    public function debug_rewrite() {
        global $wp_rewrite;
        
        $rules = get_option('rewrite_rules');
        $neighborhood_rules = [];
        
        foreach ($rules as $pattern => $replacement) {
            if (strpos($pattern, 'ocean-city-neighborhoods') !== false) {
                $neighborhood_rules[$pattern] = $replacement;
            }
        }
        
        WP_CLI::line('=== REWRITE RULES DEBUG ===');
        WP_CLI::line('Post type registered: ' . (post_type_exists('neighborhood') ? 'Yes' : 'No'));
        WP_CLI::line('Total rewrite rules: ' . count($rules));
        WP_CLI::line('Last flushed: ' . date('Y-m-d H:i:s', get_option('locnj_rewrite_rules_force_flushed', time())));
        WP_CLI::line('');
        WP_CLI::line('Neighborhood-specific rules:');
        
        foreach ($neighborhood_rules as $pattern => $replacement) {
            WP_CLI::line("  $pattern => $replacement");
        }
    }
    
    /**
     * Test neighborhood URLs
     * 
     * ## EXAMPLES
     * 
     *     wp ocnj test-urls
     * 
     * @when after_wp_load
     */
    public function test_urls() {
        $posts = get_posts([
            'post_type' => 'neighborhood',
            'posts_per_page' => 5,
            'post_status' => 'publish'
        ]);
        
        WP_CLI::line('=== NEIGHBORHOOD URL TEST ===');
        
        if (empty($posts)) {
            WP_CLI::warning('No neighborhood posts found');
            return;
        }
        
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

// Register the commands
WP_CLI::add_command('ocnj', 'LOCNJ_Neighborhoods_CLI_Commands');