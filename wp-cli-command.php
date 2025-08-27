<?php
/**
 * Ocean City Neighborhoods Markdown Importer WP-CLI Command
 * 
 * This file registers the WP-CLI command for importing Markdown content.
 * 
 * @package LiveOCNJ_Neighborhoods
 */

// Exit if not in WP-CLI context
if (!defined('WP_CLI')) {
    return;
}

// Include the importer script
require_once __DIR__ . '/import_from_md.php';

/**
 * Import Ocean City neighborhoods from Markdown file
 */
class LOCNJ_Import_Command {
    /**
     * Import Ocean City neighborhoods from Markdown file
     * 
     * ## OPTIONS
     * 
     * <file>
     * : Path to the Markdown file
     * 
     * ## EXAMPLES
     * 
     *     wp locnj import_md /path/to/ocean-city-neighborhoods-content.md
     * 
     * @param array $args Command arguments
     * @param array $assoc_args Command options
     */
    public function import_md($args, $assoc_args) {
        if (empty($args[0])) {
            WP_CLI::error('Please provide a path to the Markdown file.');
            return;
        }
        
        $file_path = $args[0];
        
        WP_CLI::log('Starting import from Markdown file: ' . $file_path);
        
        $importer = new LOCNJ_Markdown_Importer($file_path);
        $stats = $importer->import();
        
        if ($stats === false) {
            WP_CLI::error('Import failed.');
            return;
        }
        
        // Display summary table
        WP_CLI::log('Import completed successfully.');
        WP_CLI::log('');
        WP_CLI::log('Summary:');
        
        $table_data = [];
        $table_data[] = ['Item' => 'Neighborhoods Created', 'Count' => $stats['neighborhoods_created']];
        $table_data[] = ['Item' => 'Neighborhoods Updated', 'Count' => $stats['neighborhoods_updated']];
        $table_data[] = ['Item' => 'FAQ Categories', 'Count' => $stats['faq_categories']];
        $table_data[] = ['Item' => 'FAQ Items', 'Count' => $stats['faq_items']];
        $table_data[] = ['Item' => 'Landing Meta Updated', 'Count' => $stats['landing_meta_updated'] ? 'Yes' : 'No'];
        
        WP_CLI\Utils\format_items('table', $table_data, ['Item', 'Count']);
        
        // Check if ACF is active
        if (!function_exists('update_field')) {
            WP_CLI::warning('Advanced Custom Fields (ACF) is not active. Data has been stored as post meta, but ACF fields will not be available until ACF is activated.');
            WP_CLI::log('');
            WP_CLI::log('To activate ACF, run:');
            WP_CLI::log('wp plugin install advanced-custom-fields --activate --allow-root');
            WP_CLI::log('');
            WP_CLI::log('Or for ACF Pro (if you have the zip):');
            WP_CLI::log('wp plugin install /path/to/advanced-custom-fields-pro.zip --activate --allow-root');
        }
    }
}

WP_CLI::add_command('locnj', 'LOCNJ_Import_Command');