<?php
// src/Image_Helpers.php
defined('ABSPATH') || exit;

if (!function_exists('locnj_get_hero_url')) {
  /**
   * Return a usable URL for the neighborhood hero image.
   * Handles ACF image array, attachment ID, and raw URL string.
   * Falls back to featured image, then plugin fallback.
   */
  function locnj_get_hero_url($post_id) {
    $val = function_exists('get_field') ? get_field('neighborhood_hero_image', $post_id) : '';
    $url = '';

    if (is_array($val) && !empty($val['url'])) {
      $url = $val['url'];
    } elseif (is_numeric($val)) {
      $url = wp_get_attachment_image_url((int) $val, 'full');
    } elseif (is_string($val) && $val !== '') {
      $url = esc_url_raw($val);
    }

    if (empty($url) && has_post_thumbnail($post_id)) {
      $url = get_the_post_thumbnail_url($post_id, 'full');
    }

    if (empty($url)) {
      // final fallback bundled with plugin
      $url = plugins_url('assets/img/card-fallback.jpg', LOCNJ_NEIGHBORHOODS_FILE);
    }
    return $url;
  }
}