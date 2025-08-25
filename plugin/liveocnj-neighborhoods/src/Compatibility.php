<?php
/**
 * Compatibility file for backward compatibility
 */

defined('ABSPATH') || exit;

// Only define the class if it doesn't already exist
if (!class_exists('LOCNJ_CPT_Plugin')) {
    /**
     * Compatibility class for backward compatibility
     */
    class LOCNJ_CPT_Plugin {
        /**
         * Static initialization method called by the plugins_loaded hook
         */
        public static function init() {
            // Forward to the new class
            if (class_exists('LOCNJ_Neighborhoods_Plugin')) {
                LOCNJ_Neighborhoods_Plugin::init();
            }
        }
    }
}