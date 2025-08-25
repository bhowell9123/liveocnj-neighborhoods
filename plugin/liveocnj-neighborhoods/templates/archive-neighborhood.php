<?php
/**
 * Archive: Neighborhood landing page
 * - Shows ONE entry per neighborhood family (latest by date)
 * - Renders a card grid with proper classes
 */

defined('ABSPATH') || exit;
get_header();
require_once __DIR__ . '/partials/image-helpers.php';
?>
<main id="primary" class="ocnj-archive">

  <!-- HERO -->
  <?php
  // Get the hero image URL using a more dynamic approach
  $hero_image_id = get_option('locnj_neighborhoods_archive_hero_id');
  if ($hero_image_id) {
    $hero_url = wp_get_attachment_image_url($hero_image_id, 'full');
  } else {
    // Fallback to the plugin's default image
    $hero_url = plugin_dir_url(LOCNJ_NEIGHBORHOODS_FILE) . 'assets/img/ocean-city-nj-bay-aerial-neighborhoods-hero.png';
  }
  ?>
  <section class="ocnj-hero ocnj-hero--spaced">
    <div class="ocnj-hero-bg" aria-hidden="true">
      <img src="<?php echo esc_url($hero_url); ?>" alt="" loading="eager" decoding="async">
    </div>
    <div class="ocnj-hero-shade" aria-hidden="true"></div>
    <div class="ocnj-hero__inner">
      <h1>Ocean City, NJ Real Estate: A Complete Neighborhood &amp; FAQ Guide</h1>
      <p class="ocnj-hero__sub">Discover your perfect Ocean City neighborhood</p>
      <div class="ocnj-hero__cta">
        <a class="ocnj-btn ocnj-btn--primary" href="#ocnj-profiles">Explore Neighborhoods</a>
        <a class="ocnj-btn ocnj-btn--ghost" href="#ocnj-faq">Read the FAQ</a>
      </div>
      <ul class="ocnj-stats">
        <li><span class="ocnj-stat-value">$1,045,659</span><span class="ocnj-stat-label">Median Price</span></li>
        <li><span class="ocnj-stat-value">-0.2%</span><span class="ocnj-stat-label">1-Year Change</span></li>
        <li><span class="ocnj-stat-value">35</span><span class="ocnj-stat-label">Days on Market</span></li>
        <li><span class="ocnj-stat-value">187</span><span class="ocnj-stat-label">Homes for Sale</span></li>
      </ul>
    </div>
  </section>

  <!-- MAP INTRO -->
  <section class="ocnj-map-intro" aria-labelledby="ocnj-map-title">
    <div class="ocnj-section__inner">
      <h2 id="ocnj-map-title">Map of all Areas in Ocean City NJ</h2>
      <p>Explore Ocean City's 11 distinct neighborhoods. Click on any area to learn more about its unique character, market data, and lifestyle offerings.</p>
      <div class="ocnj-map-container">
        <img src="<?php echo esc_url(plugin_dir_url(LOCNJ_NEIGHBORHOODS_FILE) . 'assets/img/Ocean_City_NJ_Map.jpg'); ?>" alt="Map of Ocean City neighborhoods" class="ocnj-map-image" width="1600" height="900" loading="lazy" decoding="async">
      </div>
    </div>
  </section>

  <!-- PROFILES GRID -->
  <section id="ocnj-profiles" class="ocnj-profiles" aria-labelledby="ocnj-profiles-title">
    <div class="ocnj-section__inner">
      <h2 id="ocnj-profiles-title">Comprehensive Neighborhood Profiles</h2>
      <p>Detailed insights into each of Ocean City's unique neighborhoods, including market data, lifestyle information, and expert recommendations.</p>

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
          <article class="ocnj-card">
            <a class="ocnj-card__media" href="<?php the_permalink(); ?>">
<?php
$img_url = locnj_get_hero_url($id);
?>
  <img src="<?php echo esc_url($img_url); ?>"
       alt="<?php echo esc_attr(get_the_title($id)); ?>"
       loading="lazy" decoding="async" />

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

              if ($price_badge !== '') : ?>
                <div class="ocnj-price-badge"><?php echo esc_html($price_badge); ?></div>
              <?php endif; ?>
            </a>

            <div class="ocnj-card__body">
              <h3 class="ocnj-card__title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              </h3>

              <p class="ocnj-card__sub">
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
              if ($chips) : ?>
                <ul class="ocnj-card-chips" aria-label="Highlights">
                  <?php foreach (array_slice($chips, 0, 3) as $chip) : ?>
                    <li><?php echo esc_html($chip); ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>

              <p class="ocnj-card__link"><a href="<?php the_permalink(); ?>">Read the guide →</a></p>
            </div>
          </article>
          <?php
          $cards_html[] = ob_get_clean();
        }
        wp_reset_postdata();
      }

      // 3) Output grid once (no theme fallback loop, no duplicates)
      if ($cards_html) {
        echo '<div class="ocnj-card-grid">', implode('', $cards_html), '</div>';
      }
      ?>
    </div>
  </section>

  <!-- GLOBAL FAQ (safe, optional) -->
  <section id="ocnj-faq" class="ocnj-faq" aria-labelledby="ocnj-faq-title">
    <div class="ocnj-section__inner">
      <h2 id="ocnj-faq-title">Ocean City Real Estate FAQ</h2>
      <p>Your on-island expert answers to common questions about buying, selling, investing, and living in Ocean City, NJ.</p>

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