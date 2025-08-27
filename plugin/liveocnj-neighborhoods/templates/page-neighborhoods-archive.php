<?php
/**
 * Template Name: Neighborhoods Archive
 * Description: Custom template for displaying the neighborhoods archive
 */

defined('ABSPATH') || exit;
get_header();
require_once __DIR__ . '/partials/image-helpers.php';

// Fetch the archive meta options
$meta = get_option('locnj_archive_meta', []);

// Debug output (only visible to admins)
if (current_user_can('manage_options')) {
    echo '<!-- DEBUG: Archive meta data: ' . esc_html(json_encode($meta)) . ' -->';
    echo '<!-- DEBUG: Body classes: ' . esc_html(implode(' ', get_body_class())) . ' -->';
    
    // Debug CSS files
    global $wp_styles;
    echo '<!-- DEBUG: Enqueued styles: ';
    foreach ($wp_styles->queue as $handle) {
        echo esc_html($handle) . ', ';
    }
    echo ' -->';
}

// Extract values with fallbacks
$hero_title = $meta['hero_title'] ?? 'Ocean City, NJ Real Estate: A Complete Neighborhood & FAQ Guide';
$hero_sub = $meta['hero_sub'] ?? $meta['hero_subtitle'] ?? 'Discover your perfect Ocean City neighborhood';
$stats = $meta['stats'] ?? null;
$hero_img = $meta['hero_image'] ?? '';
$map_img = $meta['map_image'] ?? '';

// More debug info for admins
if (current_user_can('manage_options')) {
    echo '<!-- DEBUG: Using hero_title: ' . esc_html($hero_title) . ' -->';
    echo '<!-- DEBUG: Using hero_sub: ' . esc_html($hero_sub) . ' -->';
    echo '<!-- DEBUG: Using hero_img: ' . esc_html($hero_img) . ' -->';
    echo '<!-- DEBUG: Using map_img: ' . esc_html($map_img) . ' -->';
    echo '<!-- DEBUG: Stats data: ' . esc_html(json_encode($stats)) . ' -->';
}
?>
<main id="primary" class="ocnj-archive">

  <?php if (current_user_can('manage_options')): ?>
  <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; margin: 10px 0;">
    <p><strong>Debug Info (Admin Only):</strong></p>
    <p>Template: page-neighborhoods-archive.php</p>
    <p>Body Classes: <?php echo esc_html(implode(' ', get_body_class())); ?></p>
    <p>Hero URL: <?php echo !empty($hero_img) ? esc_html($hero_img) : 'Not set'; ?></p>
    <p>Map URL: <?php echo !empty($map_img) ? esc_html($map_img) : 'Not set'; ?></p>
    <p>Stats: <?php echo is_array($stats) ? esc_html(json_encode($stats)) : 'Not set'; ?></p>
  </div>
  <?php endif; ?>

  <!-- HERO -->
  <?php
  // Get the hero image URL using a more dynamic approach
  if (!empty($hero_img)) {
    $hero_url = $hero_img;
  } else {
    $hero_image_id = get_option('locnj_neighborhoods_archive_hero_id');
    if ($hero_image_id) {
      $hero_url = wp_get_attachment_image_url($hero_image_id, 'full');
    } else {
      // Fallback to the plugin's default image
      $hero_url = plugin_dir_url(LOCNJ_NEIGHBORHOODS_FILE) . 'assets/img/ocean-city-nj-bay-aerial-neighborhoods-hero.png';
    }
  }
  
  // Ensure hero_url is absolute
  if (!empty($hero_url) && strpos($hero_url, 'http') !== 0) {
    $hero_url = site_url($hero_url);
  }
  ?>
  <section class="ocnj-hero ocnj-hero--spaced" style="position: relative; min-height: 80vh; overflow: hidden; display: flex; align-items: center; justify-content: center; text-align: center; color: #fff; margin: 0 0 4rem 0;">
    <div class="ocnj-hero-bg" aria-hidden="true" style="position: absolute; inset: 0; z-index: 0; overflow: hidden;">
      <img src="<?php echo esc_url($hero_url); ?>" alt="" loading="eager" decoding="async" style="display: block; width: 100%; height: 100%; object-fit: cover;">
    </div>
    <div class="ocnj-hero-shade" aria-hidden="true" style="position: absolute; inset: 0; z-index: 1; background: linear-gradient(rgba(0,0,0,.45), rgba(0,0,0,.45));"></div>
    <div class="ocnj-hero__inner" style="position: relative; z-index: 2; max-width: 800px; padding: 3rem 2rem;">
      <h1 style="font-size: clamp(2.5rem,5vw,3.5rem); font-weight: 700; margin-bottom: .5rem; text-shadow: 2px 2px 4px rgba(0,0,0,.7); line-height: 1.1; color: #fff;"><?php echo esc_html($hero_title); ?></h1>
      <p class="ocnj-hero__sub" style="font-size: clamp(1rem,2vw,1.25rem); margin-bottom: 2.5rem; color: rgba(255,255,255,.95); text-shadow: 1px 1px 2px rgba(0,0,0,.7);"><?php echo esc_html($hero_sub); ?></p>
      <div class="ocnj-hero__cta">
        <a class="ocnj-btn ocnj-btn--primary" href="#ocnj-profiles" style="display: inline-block; padding: 14px 28px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 0 10px 1rem 0; border: 2px solid #2563eb;">Explore Neighborhoods</a>
        <a class="ocnj-btn ocnj-btn--ghost" href="#ocnj-faq" style="display: inline-block; padding: 14px 28px; background: transparent; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 0 10px 1rem 0; border: 2px solid #fff;">Read the FAQ</a>
      </div>
      <ul class="ocnj-stats" style="list-style: none; padding: 2rem; margin: 3rem auto 0; display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; max-width: 960px; background: rgba(255,255,255,.95); backdrop-filter: blur(10px); border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,.1);">
        <li style="list-style: none; padding-left: 0; margin-left: 0; text-align: center;"><span class="ocnj-stat-value" style="display: block; font-size: 1.75rem; font-weight: 700; color: #2563eb; margin-bottom: .25rem; white-space: nowrap;"><?php echo esc_html(is_array($stats) && isset($stats['median_price']) ? $stats['median_price'] : '$1,045,659'); ?></span><span class="ocnj-stat-label" style="display: block; font-size: .95rem; color: #666; font-weight: 500;">Median Price</span></li>
        <li style="list-style: none; padding-left: 0; margin-left: 0; text-align: center;"><span class="ocnj-stat-value" style="display: block; font-size: 1.75rem; font-weight: 700; color: #2563eb; margin-bottom: .25rem; white-space: nowrap;"><?php echo esc_html(is_array($stats) && isset($stats['yoy_change']) ? $stats['yoy_change'] : '-0.2%'); ?></span><span class="ocnj-stat-label" style="display: block; font-size: .95rem; color: #666; font-weight: 500;">1-Year Change</span></li>
        <li style="list-style: none; padding-left: 0; margin-left: 0; text-align: center;"><span class="ocnj-stat-value" style="display: block; font-size: 1.75rem; font-weight: 700; color: #2563eb; margin-bottom: .25rem; white-space: nowrap;"><?php echo esc_html(is_array($stats) && isset($stats['dom']) ? $stats['dom'] : '35'); ?></span><span class="ocnj-stat-label" style="display: block; font-size: .95rem; color: #666; font-weight: 500;">Days on Market</span></li>
        <li style="list-style: none; padding-left: 0; margin-left: 0; text-align: center;"><span class="ocnj-stat-value" style="display: block; font-size: 1.75rem; font-weight: 700; color: #2563eb; margin-bottom: .25rem; white-space: nowrap;"><?php echo esc_html(is_array($stats) && isset($stats['active']) ? $stats['active'] : '187'); ?></span><span class="ocnj-stat-label" style="display: block; font-size: .95rem; color: #666; font-weight: 500;">Homes for Sale</span></li>
      </ul>
    </div>
  </section>

  <!-- MAP INTRO -->
  <section class="ocnj-map-intro" aria-labelledby="ocnj-map-title" style="margin: 4rem 0;">
    <div class="ocnj-section__inner" style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
      <h2 id="ocnj-map-title" style="font-size: clamp(2rem,4vw,2.75rem); font-weight: 700; text-align: center; margin-bottom: 1.5rem; color: #1f2937;">Map of all Areas in Ocean City NJ</h2>
      <p style="font-size: 1.125rem; text-align: center; color: #6b7280; max-width: 700px; margin: 0 auto 2rem;">Explore Ocean City's 11 distinct neighborhoods. Click on any area to learn more about its unique character, market data, and lifestyle offerings.</p>
      <div class="ocnj-map-container" style="max-width: 1200px; margin: 2rem auto; text-align: center;">
        <?php
        // Ensure map_img is absolute
        $map_url = !empty($map_img) ? $map_img : plugin_dir_url(LOCNJ_NEIGHBORHOODS_FILE) . 'assets/img/Ocean_City_NJ_Map.jpg';
        if (!empty($map_url) && strpos($map_url, 'http') !== 0) {
          $map_url = site_url($map_url);
        }
        ?>
        <img src="<?php echo esc_url($map_url); ?>" alt="Map of Ocean City neighborhoods" class="ocnj-map-image" width="1600" height="900" loading="lazy" decoding="async" style="max-width: 100%; height: auto; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1);">
      </div>
    </div>
  </section>

  <!-- PROFILES GRID -->
  <section id="ocnj-profiles" class="ocnj-profiles" aria-labelledby="ocnj-profiles-title">
    <div class="ocnj-section__inner" style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
      <h2 id="ocnj-profiles-title" style="font-size: clamp(2rem,4vw,2.75rem); font-weight: 700; text-align: center; margin-bottom: 1.5rem; color: #1f2937;">Comprehensive Neighborhood Profiles</h2>
      <p style="font-size: 1.125rem; text-align: center; color: #6b7280; max-width: 700px; margin: 0 auto 2rem;">Detailed insights into each of Ocean City's unique neighborhoods, including market data, lifestyle information, and expert recommendations.</p>

      <?php
      // 1) Query all neighborhoods (fast flags on)
      $q = new WP_Query([
        'post_type'           => 'neighborhood',
        'posts_per_page'      => -1,
        // Order so the latest version appears first within a family
        'orderby'             => ['menu_order' => 'ASC', 'date' => 'DESC'],
        'no_found_rows'       => true,
        'ignore_sticky_posts' => true,
        'post_status'         => 'publish',
      ]);

      // 2) Keep only the latest post per "family" (strip -live-ocnj and -live-ocnj-# from slug)
      $seen_families = [];
      $cards_html    = [];

      if ($q->have_posts()) {
        while ($q->have_posts()) { $q->the_post();
          $id   = get_the_ID();
          $slug = get_post_field('post_name', $id);
          $family = preg_replace('/-live-ocnj(?:-\d+)?$/', '', $slug);

          // If we've already output this family, skip older duplicates
          if (isset($seen_families[$family])) { continue; }
          $seen_families[$family] = true;

          ob_start(); ?>
          <article class="ocnj-card" style="background: #fff; border-radius: 20px; box-shadow: 0 8px 24px rgba(0,0,0,.08); overflow: hidden; display: flex; flex-direction: column;">
            <a class="ocnj-card__media" href="<?php the_permalink(); ?>" style="display: block; text-decoration: none;">
<?php
$img_url = locnj_get_hero_url($id);
?>
  <img src="<?php echo esc_url($img_url); ?>"
       alt="<?php echo esc_attr(get_the_title($id)); ?>"
       loading="lazy" decoding="async" style="display: block; width: 100%; height: 220px; object-fit: cover;" />

              <?php
              // Price badge (ACF -> fallback)
              $price_badge = '';

              if (function_exists('get_field')) {
                // 1) Explicit badge field if you have it
                $maybe_badge = get_field('neighborhood_price_badge', $id);
                if (is_string($maybe_badge) && $maybe_badge !== '') {
                  $price_badge = $maybe_badge;
                }

                // 2) Else derive from market data (median_price)
                if ($price_badge === '') {
                  $md = get_field('neighborhood_market_data', $id); // group/array
                  if (is_array($md) && !empty($md['median_price'])) {
                    $n = preg_replace('/[^\d.]/', '', (string)$md['median_price']);
                    if ($n !== '') {
                      $num = (float)$n;
                      $price_badge = '$' . number_format($num);
                    }
                  }
                }
              }

              // 3) Last-ditch static hint (optional)
              // if ($price_badge === '') $price_badge = '$1.2M+';

              if ($price_badge !== '') { ?>
                <div class="ocnj-price-badge" style="position: absolute; top: 1rem; right: 1rem; background: #2563eb; color: #fff; padding: .5rem 1rem; border-radius: 8px; font-weight: 600; font-size: .875rem; box-shadow: 0 2px 8px rgba(37,99,235,.3);"><?php echo esc_html($price_badge); ?></div>
              <?php } ?>
            </a>

            <div class="ocnj-card__body" style="padding: 18px 20px;">
              <h3 class="ocnj-card__title" style="font-size: 1.25rem; line-height: 1.25; margin: 0 0 8px;">
                <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: #1f2937;"><?php the_title(); ?></a>
              </h3>

              <p class="ocnj-card__sub" style="color: #4b5563; margin: 0 0 12px;">
                <?php
                $about   = function_exists('get_field') ? (string) get_field('neighborhood_about', $id) : '';
                $snippet = $about ? wp_trim_words(wp_strip_all_tags($about), 26) : get_the_excerpt();
                echo esc_html($snippet);
                ?>
              </p>

              <?php
              // Build up to 3 chips from ACF repeater "neighborhood_facts"
              $chips = [];
              if (function_exists('have_rows') && have_rows('neighborhood_facts', $id)) {
                while (have_rows('neighborhood_facts', $id)) { the_row();
                  $t = get_sub_field('text') ?: get_sub_field('fact') ?: get_sub_field('label');
                  if (is_string($t) && $t !== '') { $chips[] = $t; }
                }
              } elseif (function_exists('get_field')) {
                $raw = get_field('neighborhood_facts', $id);
                if (is_array($raw)) {
                  foreach ($raw as $row) {
                    $t = is_array($row) ? ($row['text'] ?? $row['fact'] ?? $row['label'] ?? '') : (string) $row;
                    if ($t !== '') { $chips[] = $t; }
                  }
                }
              }
              if ($chips) { ?>
                <ul class="ocnj-card-chips" style="display: flex; flex-wrap: wrap; gap: 8px; margin: 0 0 12px; padding: 0; list-style: none;" aria-label="Highlights">
                  <?php foreach (array_slice($chips, 0, 3) as $chip) { ?>
                    <li style="background: #f1f5f9; border-radius: 999px; padding: 6px 10px; font-size: .875rem; color: #111827; list-style: none;"><?php echo esc_html($chip); ?></li>
                  <?php } ?>
                </ul>
              <?php } ?>

              <p class="ocnj-card__link" style="margin-top: 12px;"><a href="<?php the_permalink(); ?>" style="color: #2563eb; text-decoration: none;">Read the guide →</a></p>
            </div>
          </article>
          <?php
          $cards_html[] = ob_get_clean();
        }
        wp_reset_postdata();
      }

      // 3) Output grid once (no theme fallback loop, no duplicates)
      if ($cards_html) {
        echo '<div class="ocnj-card-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 28px; margin-top: 24px;">', implode('', $cards_html), '</div>';
      }
      ?>
    </div>
  </section>

  <!-- GLOBAL FAQ (safe, optional) -->
  <section id="ocnj-faq" class="ocnj-faq" aria-labelledby="ocnj-faq-title">
    <div class="ocnj-section__inner" style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
      <h2 id="ocnj-faq-title" style="font-size: clamp(2rem,4vw,2.75rem); font-weight: 700; text-align: center; margin-bottom: 1.5rem; color: #1f2937;">Ocean City Real Estate FAQ</h2>
      <p style="font-size: 1.125rem; text-align: center; color: #6b7280; max-width: 700px; margin: 0 auto 2rem;">Your on-island expert answers to common questions about buying, selling, investing, and living in Ocean City, NJ.</p>

      <?php
      if (function_exists('locnj_render_global_faq')) {
        locnj_render_global_faq();   // <-- single call
      } else {
        include __DIR__ . '/partials/global-faq.php';
        if (function_exists('locnj_render_global_faq')) {
          locnj_render_global_faq();
        }
      }
      ?>
    </div>
  </section>

  <?php if ( file_exists(__DIR__ . '/partials/expert-block.php') ) {
    include __DIR__ . '/partials/expert-block.php';
  } ?>

</main>
<?php get_footer();