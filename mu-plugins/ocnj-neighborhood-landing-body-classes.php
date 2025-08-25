<?php
/**
 * Plugin Name: OCNJ Neighborhood Landing Body Classes
 * Description: Adds archive-like body classes to specific pages to make them appear like the neighborhood archive
 * Version: 1.0.0
 * Author: LiveOCNJ
 */

if (!defined('ABSPATH')) exit;

/**
 * Add archive-like body classes to specific pages
 * This makes static pages appear with the same styling as the neighborhood archive
 */
add_filter('body_class', function($classes) {
    // Current queried object
    $q = get_queried_object();
    
    // Check if this is one of our target pages
    $is_target = false;
    
    // Check by ID
    if ($q && isset($q->ID) && in_array($q->ID, [167843, 169371, 175056])) {
        $is_target = true;
    }
    
    // Check by slug
    if ($q && isset($q->post_name) && in_array($q->post_name, [
        'ocean-city-neighborhoods',
        'neighborhoods',
        'home-content'
    ])) {
        $is_target = true;
    }
    
    // Check by URL pattern
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/ocean-city-neighborhoods') !== false) {
        $is_target = true;
    }
    
    // If this is a target page, add the archive-like classes
    if ($is_target) {
        $force = array(
            "archive",
            "post-type-archive",
            "post-type-archive-neighborhood",
            "ocnj-neighborhood-page",
            "ocnj-neighborhood-landing",
        );
        
        $classes = array_merge($classes, $force);
    }
    
    return $classes;
});