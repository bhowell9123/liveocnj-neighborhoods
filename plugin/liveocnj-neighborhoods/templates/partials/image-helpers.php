<?php
defined('ABSPATH') || exit;

if (!function_exists('locnj_get_plugin_asset')) {
  function locnj_get_plugin_asset($rel) {
    // Use the plugin's main file to build a correct URL
    if (defined('LOCNJ_NEIGHBORHOODS_FILE')) {
      return trailingslashit(plugin_dir_url(LOCNJ_NEIGHBORHOODS_FILE)) . ltrim($rel, '/');
    }
    // Safe fallback if constant is missing
    return plugin_dir_url(dirname(__FILE__, 3)) . ltrim($rel, '/');
  }
}

if (!function_exists('locnj_get_hero_url')) {
  function locnj_get_hero_url($post_id) {
    $val = function_exists('get_field') ? get_field('neighborhood_hero_image', $post_id) : null;
    $url = '';

    if (is_array($val)) {
      if (!empty($val['url'])) { $url = $val['url']; }
      elseif (!empty($val['ID'])) { $url = wp_get_attachment_image_url((int)$val['ID'], 'large'); }
    } elseif (is_numeric($val)) {
      $url = wp_get_attachment_image_url((int)$val, 'large');
    } elseif (is_string($val) && $val !== '') {
      $url = $val; // ACF set to "URL" return type
    }

    if (!$url && has_post_thumbnail($post_id)) {
      $url = get_the_post_thumbnail_url($post_id, 'large');
    }
    if (!$url) {
      $url = locnj_get_plugin_asset('assets/img/card-fallback.jpg');
    }
    
    // Normalize protocol-relative URLs
    if ($url && str_starts_with($url, '//')) {
      $url = (is_ssl() ? 'https:' : 'http:') . $url;
    }
    
    // Guard against non-http(s) values
    if ($url && !preg_match('#^https?://#i', $url)) {
      $url = esc_url_raw($url);
    }
    
    // Add a hook for monitoring fallbacks
    if (!$url || strpos($url, 'card-fallback.jpg') !== false) {
        do_action('locnj_image_fallback_used', $post_id);
    }
    
    return esc_url($url);
  }
}