<?php
/**
 * ACF Fields integration for Ocean City Neighborhoods
 *
 * @package LiveOCNJ_Neighborhoods
 */

defined('ABSPATH') || exit;

/**
 * ACF Fields integration class
 * 
 * Handles registration of ACF fields for the Neighborhood post type
 * Supports both JSON and PHP registration methods
 */
class LOCNJ_Neighborhoods_ACF_Fields {

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
        // Check if ACF is active before setting up hooks
        if (!$this->is_acf_active()) {
            add_action('admin_notices', array($this, 'acf_missing_notice'));
            return;
        }

        // Set up ACF JSON save/load paths
        add_filter('acf/settings/save_json', array($this, 'acf_json_save_path'));
        add_filter('acf/settings/load_json', array($this, 'acf_json_load_path'));

        // Register field group via PHP if JSON method fails
        add_action('acf/init', array($this, 'register_field_group'));

        // Add admin notice if field group registration fails
        add_action('admin_notices', array($this, 'field_group_notice'));
    }

    /**
     * Check if ACF plugin is active
     *
     * @return bool True if ACF is active, false otherwise
     */
    public function is_acf_active() {
        return class_exists('ACF');
    }

    /**
     * Display admin notice if ACF is not active
     */
    public function acf_missing_notice() {
        if (current_user_can('activate_plugins')) {
            ?>
            <div class="notice notice-error">
                <p><?php _e('The LiveOCNJ Neighborhoods plugin requires Advanced Custom Fields to be installed and activated.', 'liveocnj-neighborhoods'); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Set ACF JSON save path
     *
     * @param string $path The default save path
     * @return string The custom save path
     */
    public function acf_json_save_path($path) {
        // Set the save path to the plugin directory
        return plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE);
    }

    /**
     * Add plugin directory to ACF JSON load paths
     *
     * @param array $paths The default load paths
     * @return array The modified load paths
     */
    public function acf_json_load_path($paths) {
        // Add the plugin directory to the load paths
        $paths[] = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE);
        return $paths;
    }

    /**
     * Check if field group exists
     *
     * @return bool True if field group exists, false otherwise
     */
    public function field_group_exists() {
        if (!function_exists('acf_get_field_group')) {
            return false;
        }

        return acf_get_field_group($this->field_group_key) !== false;
    }

    /**
     * Display admin notice if field group registration fails
     */
    public function field_group_notice() {
        // Only show notice if ACF is active but field group is missing
        if ($this->is_acf_active() && !$this->field_group_exists() && current_user_can('manage_options')) {
            ?>
            <div class="notice notice-warning">
                <p><?php _e('The Neighborhood field group could not be loaded from JSON. Using PHP registration as fallback.', 'liveocnj-neighborhoods'); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Register field group via PHP
     * 
     * This is a fallback method if the JSON registration fails
     */
    public function register_field_group() {
        // Only register if ACF is active and field group doesn't already exist
        if (!$this->is_acf_active() || $this->field_group_exists()) {
            return;
        }

        // Register the field group
        acf_add_local_field_group(array(
            'key' => 'group_neighborhood_locnj',
            'title' => 'Neighborhood Information',
            'fields' => array(
                array(
                    'key' => 'field_neighborhood_heading',
                    'label' => 'Heading (H1)',
                    'name' => 'neighborhood_heading',
                    'type' => 'text',
                    'instructions' => 'Main heading for the neighborhood page',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_neighborhood_about',
                    'label' => 'About',
                    'name' => 'neighborhood_about',
                    'type' => 'textarea',
                    'instructions' => 'Short description for cards and excerpts',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_neighborhood_meta_description',
                    'label' => 'Meta Description',
                    'name' => 'neighborhood_meta_description',
                    'type' => 'textarea',
                    'instructions' => 'SEO description (Yoast will use this as a fallback)',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_neighborhood_facts',
                    'label' => 'Facts',
                    'name' => 'neighborhood_facts',
                    'type' => 'repeater',
                    'layout' => 'table',
                    'button_label' => 'Add Fact',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_neighborhood_fact_text',
                            'label' => 'Fact',
                            'name' => 'text',
                            'type' => 'text',
                            'required' => 0,
                        ),
                    ),
                ),
                array(
                    'key' => 'field_neighborhood_faq',
                    'label' => 'FAQ',
                    'name' => 'neighborhood_faq',
                    'type' => 'repeater',
                    'layout' => 'row',
                    'button_label' => 'Add FAQ',
                    'collapsed' => 'field_neighborhood_faq_question',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_neighborhood_faq_question',
                            'label' => 'Question',
                            'name' => 'question',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_neighborhood_faq_answer',
                            'label' => 'Answer',
                            'name' => 'answer',
                            'type' => 'wysiwyg',
                            'tabs' => 'all',
                            'toolbar' => 'full',
                            'media_upload' => 1,
                        ),
                    ),
                ),
                array(
                    'key' => 'field_neighborhood_market_data',
                    'label' => 'Market Data',
                    'name' => 'neighborhood_market_data',
                    'type' => 'group',
                    'layout' => 'row',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_neighborhood_median_price',
                            'label' => 'Median Price',
                            'name' => 'median_price',
                            'type' => 'number',
                        ),
                        array(
                            'key' => 'field_neighborhood_dom_median',
                            'label' => 'Days on Market (Median)',
                            'name' => 'dom_median',
                            'type' => 'number',
                        ),
                        array(
                            'key' => 'field_neighborhood_active_inventory',
                            'label' => 'Active Inventory',
                            'name' => 'active_inventory',
                            'type' => 'number',
                        ),
                        array(
                            'key' => 'field_neighborhood_price_badge',
                            'label' => 'Price Badge',
                            'name' => 'price_badge',
                            'type' => 'text',
                            'instructions' => 'Short price text for cards, e.g., "$1.2M+"',
                        ),
                    ),
                ),
                array(
                    'key' => 'field_neighborhood_hero_image',
                    'label' => 'Hero Image',
                    'name' => 'neighborhood_hero_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ),
                array(
                    'key' => 'field_neighborhood_idx_links',
                    'label' => 'IDX Links',
                    'name' => 'neighborhood_idx_links',
                    'type' => 'group',
                    'layout' => 'row',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_neighborhood_idx_default',
                            'label' => 'Default Link',
                            'name' => 'default',
                            'type' => 'url',
                        ),
                        array(
                            'key' => 'field_neighborhood_idx_for_sale',
                            'label' => 'For Sale',
                            'name' => 'for_sale',
                            'type' => 'group',
                            'layout' => 'table',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_neighborhood_idx_for_sale_condos',
                                    'label' => 'Condos',
                                    'name' => 'condos',
                                    'type' => 'url',
                                ),
                                array(
                                    'key' => 'field_neighborhood_idx_for_sale_sf',
                                    'label' => 'Single Family',
                                    'name' => 'sf',
                                    'type' => 'url',
                                ),
                            ),
                        ),
                        array(
                            'key' => 'field_neighborhood_idx_sold',
                            'label' => 'Sold',
                            'name' => 'sold',
                            'type' => 'group',
                            'layout' => 'table',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_neighborhood_idx_sold_condos',
                                    'label' => 'Condos',
                                    'name' => 'condos',
                                    'type' => 'url',
                                ),
                                array(
                                    'key' => 'field_neighborhood_idx_sold_sf',
                                    'label' => 'Single Family',
                                    'name' => 'sf',
                                    'type' => 'url',
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_neighborhood_related',
                    'label' => 'Related Neighborhoods',
                    'name' => 'neighborhood_related',
                    'type' => 'relationship',
                    'post_type' => array('neighborhood'),
                    'filters' => array('search'),
                    'elements' => array('featured_image'),
                    'min' => 0,
                    'max' => 5,
                    'return_format' => 'object',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'neighborhood',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
        ));
    }

    /**
     * Static initialization method
     * 
     * @return LOCNJ_Neighborhoods_ACF_Fields Instance of this class
     */
    public static function init() {
        return new self();
    }
}