<?php
/**
 * Force Flush Rewrite Rules
 * 
 * This file provides functionality to forcefully flush rewrite rules
 * when needed, preventing navigation issues with neighborhood URLs.
 */

if (!defined('ABSPATH')) {
    exit;
}

class LOCNJ_Neighborhoods_Force_Flush_Rewrite_Rules {
    
    /**
     * Initialize the force flush functionality
     */
    public static function init() {
        // Register activation hook to ensure rules are flushed on activation
        register_activation_hook(LOCNJ_NEIGHBORHOODS_FILE, [__CLASS__, 'force_flush_on_activation']);
        
        // Add admin action for manual flushing
        add_action('admin_init', [__CLASS__, 'maybe_force_flush_rewrite_rules']);
    }
    
    /**
     * Force flush rewrite rules on plugin activation
     */
    public static function force_flush_on_activation() {
        // Register the post type first
        LOCNJ_Neighborhoods_PostType_Neighborhood::register();
        
        // Delete existing rewrite rules
        delete_option('rewrite_rules');
        
        // Flush rewrite rules
        flush_rewrite_rules(true);
        
        // Set a flag that we've flushed rules
        update_option('locnj_rewrite_rules_force_flushed', time());
        
        error_log('LOCNJ Neighborhoods: Rewrite rules forcefully flushed on activation');
    }
    
    /**
     * Check if rewrite rules need to be forcefully flushed
     */
    public static function maybe_force_flush_rewrite_rules() {
        // Check if we need to flush rewrite rules
        $last_flushed = get_option('locnj_rewrite_rules_force_flushed', 0);
        $plugin_version = get_option('locnj_plugin_version', '1.0.0');
        
        // If it's been more than a week since last flush, or version changed
        if ((time() - $last_flushed) > WEEK_IN_SECONDS || $plugin_version !== LOCNJ_NEIGHBORHOODS_VERSION) {
            // Register post type first
            LOCNJ_Neighborhoods_PostType_Neighborhood::register();
            
            // Delete existing rewrite rules
            delete_option('rewrite_rules');
            
            // Flush rewrite rules
            flush_rewrite_rules(true);
            
            // Update timestamps
            update_option('locnj_rewrite_rules_force_flushed', time());
            update_option('locnj_plugin_version', LOCNJ_NEIGHBORHOODS_VERSION);
            
            error_log('LOCNJ Neighborhoods: Rewrite rules forcefully auto-flushed');
        }
    }
    
    /**
     * Manual force flush function for troubleshooting
     */
    public static function manual_force_flush() {
        // Register post type first
        LOCNJ_Neighborhoods_PostType_Neighborhood::register();
        
        // Delete existing rewrite rules
        delete_option('rewrite_rules');
        
        // Flush rewrite rules
        flush_rewrite_rules(true);
        
        // Update timestamp
        update_option('locnj_rewrite_rules_force_flushed', time());
        
        return [
            'success' => true,
            'message' => 'Rewrite rules forcefully flushed',
            'timestamp' => time()
        ];
    }
}

// WP-CLI Commands for troubleshooting
if (defined('WP_CLI') && WP_CLI) {
    
    /**
     * Add a command to force flush rewrite rules
     */
    WP_CLI::add_command('ocnj force-flush', function() {
        $result = LOCNJ_Neighborhoods_Force_Flush_Rewrite_Rules::manual_force_flush();
        WP_CLI::success($result['message']);
    });
}