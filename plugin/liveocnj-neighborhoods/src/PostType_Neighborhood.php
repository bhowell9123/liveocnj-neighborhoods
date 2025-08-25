<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class LOCNJ_Neighborhoods_PostType_Neighborhood {

    public static function register() {
        $slug    = sanitize_title(get_option('locnj_cpt_rewrite_slug', 'ocean-city-neighborhoods'));
        $archive = (bool) get_option('locnj_cpt_has_archive', 1);

        $labels = [
            'name' => 'Neighborhoods',
            'singular_name' => 'Neighborhood',
            'add_new_item' => 'Add New Neighborhood',
            'edit_item' => 'Edit Neighborhood',
            'new_item' => 'New Neighborhood',
            'view_item' => 'View Neighborhood',
            'search_items' => 'Search Neighborhoods',
            'not_found' => 'No neighborhoods found',
        ];

        $args = [
            'labels' => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'query_var'          => true,
            'has_archive' => $archive ? $slug : false,
            'rewrite'     => ['slug' => $slug, 'with_front' => false],
            'supports' => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
            'show_in_rest' => true,
        ];

        register_post_type( 'neighborhood', $args );
    }
}