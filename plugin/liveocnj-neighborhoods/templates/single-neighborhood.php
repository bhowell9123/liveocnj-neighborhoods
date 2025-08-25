<?php
/* Template: Single Neighborhood (plugin) */
get_header();
require_once __DIR__ . '/partials/image-helpers.php';
?>
<div id="primary" class="content-area">
<main id="main" class="site-main">
<?php while ( have_posts() ) : the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('ocnj-neighborhood-detail'); ?>>

<?php
if ( function_exists('yoast_breadcrumb') ) {
  yoast_breadcrumb('<p id="breadcrumbs">','</p>');
}
?>

<?php
// Check if ACF functions exist before using them
if (!function_exists('get_field')) {
  $heading = get_the_title();
  $intro = get_the_excerpt();
  $facts = [];
  $market_data = [];
  $hero_image = null;
  $hero_url = get_the_post_thumbnail_url(get_the_ID(), 'full') ?: '';
  $price_badge = '';
} else {
  $heading = get_field('neighborhood_heading') ?: get_the_title();
  $intro = get_field('neighborhood_about') ?: get_the_excerpt();
  $facts = get_field('neighborhood_facts') ?: [];
  $market_data = get_field('neighborhood_market_data') ?: [];
  
  // Get hero image URL using helper function
  $hero_url = locnj_get_hero_url(get_the_ID());
  
  $price_badge = isset($market_data['price_badge']) ? $market_data['price_badge'] : '';
}
?>

<section class="ocnj-hero">
  <div class="ocnj-hero-bg">
    <img src="<?php echo esc_url($hero_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy" decoding="async" />
  </div>
  <div class="ocnj-hero-shade"></div>
  <div class="ocnj-hero__inner">
    <h1><?php echo esc_html($heading); ?></h1>
    <p class="ocnj-hero-subtitle"><?php echo esc_html($intro); ?></p>
    <?php if ($price_badge) : ?>
      <p class="ocnj-hero-price">Typical asking prices: <?php echo esc_html($price_badge); ?></p>
    <?php endif; ?>
    
    <?php
    // Build stats only if we actually have values
    $stats = [];

    if (!empty($market_data['median_price']) && is_numeric($market_data['median_price'])) {
      $stats[] = ['value' => '$'.number_format((float)$market_data['median_price']), 'label' => 'Median Price'];
    } elseif (!empty($market_data['median_list_price']) && is_numeric($market_data['median_list_price'])) {
      $stats[] = ['value' => '$'.number_format((float)$market_data['median_list_price']), 'label' => 'Median List Price'];
    }
    
    if (!empty($market_data['dom'])) {
      $stats[] = ['value' => (int)$market_data['dom'], 'label' => 'Days on Market'];
    } elseif (!empty($market_data['dom_median'])) {
      $stats[] = ['value' => (int)$market_data['dom_median'], 'label' => 'Days on Market'];
    }
    
    if (!empty($market_data['active_listings'])) {
      $stats[] = ['value' => (int)$market_data['active_listings'], 'label' => 'Homes for Sale'];
    } elseif (!empty($market_data['active_inventory'])) {
      $stats[] = ['value' => (int)$market_data['active_inventory'], 'label' => 'Homes for Sale'];
    }
    
    if (isset($market_data['yoy']) && $market_data['yoy'] !== '') {
      $stats[] = ['value' => (float)$market_data['yoy'], 'label' => 'Year-over-Year Change', 'suffix' => '%'];
    }

    if (!empty($stats)): ?>
      <ul class="ocnj-stats">
        <?php foreach ($stats as $s): ?>
          <li class="ocnj-stat">
            <span class="ocnj-stat-value">
              <?php echo esc_html($s['value']); ?>
              <?php if (!empty($s['suffix'])) echo esc_html($s['suffix']); ?>
            </span>
            <span class="ocnj-stat-label"><?php echo esc_html($s['label']); ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</section>

<div class="ocnj-section">
  <?php if (!empty($facts)) : ?>
  <div class="ocnj-facts-container">
    <h2>Key Facts</h2>
    <ul class="ocnj-facts-list">
      <?php foreach ($facts as $fact) : ?>
        <?php
        $fact_text = '';
        if (is_array($fact)) {
          if (!empty($fact['text'])) {
            $fact_text = $fact['text'];
          } elseif (!empty($fact['fact'])) {
            $fact_text = $fact['fact'];
          }
        } else {
          $fact_text = $fact;
        }
        
        if (!empty($fact_text)) :
        ?>
          <li><?php echo esc_html($fact_text); ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <div class="ocnj-content"><?php the_content(); ?></div>

  <?php
// Initialize FAQ array

$faqs = [];
if ( function_exists('have_rows') && have_rows('neighborhood_faq') ) {
  while ( have_rows('neighborhood_faq') ) { the_row();
    $q = trim((string) get_sub_field('question'));
    $a = (string) get_sub_field('answer');
    if ( $q !== '' && $a !== '' ) $faqs[] = ['question'=>$q, 'answer'=>$a];
  }
} elseif ( function_exists('get_field') ) {
  $raw = get_field('neighborhood_faq'); // could be array, number, empty
  
  if ( is_array($raw) ) {
    foreach ($raw as $index => $row) {
      $q = isset($row['question']) ? trim((string)$row['question']) : '';
      $a = isset($row['answer'])   ? (string)$row['answer']         : '';
      if ($q !== '' && $a !== '') $faqs[] = ['question'=>$q, 'answer'=>$a];
    }
  } else {
    // If $raw is not an array or is empty, try to rebuild from raw meta
    
    // First try individual ACF fields
    $i = 0;
    $has_more_items = true;
    
    while ($has_more_items) {
      $q = get_field("neighborhood_faq_{$i}_question");
      $a = get_field("neighborhood_faq_{$i}_answer");
      
      
      if ($q && $a) {
        $faqs[] = ['question' => $q, 'answer' => $a];
        $i++;
      } else {
        $has_more_items = false;
      }
    }
    
    // If still no FAQs, try direct post meta access as a fallback
    if (empty($faqs)) {
      $count = (int) get_post_meta(get_the_ID(), 'neighborhood_faq', true);
      
      for ($i = 0; $i < $count; $i++) {
        $q = get_post_meta(get_the_ID(), "neighborhood_faq_{$i}_question", true);
        $a = get_post_meta(get_the_ID(), "neighborhood_faq_{$i}_answer", true);
        
        
        if ($q && $a) {
          $faqs[] = ['question' => $q, 'answer' => $a];
        }
      }
    }
    
    // If still no FAQs, try rebuilding from raw post meta
    if (empty($faqs)) {
      $meta = get_post_meta(get_the_ID());
      $rows = [];
      
      // First check for standard ACF pattern
      foreach ($meta as $k => $vals) {
        if (preg_match('/^neighborhood_faq_(\d+)_(question|answer)$/', $k, $m)) {
          $rows[(int)$m[1]][$m[2]] = $vals[0] ?? '';
        }
      }
      
      ksort($rows);
      
      foreach ($rows as $index => $row) {
        if (!empty($row['question']) && !empty($row['answer'])) {
          $faqs[] = ['question' => $row['question'], 'answer' => $row['answer']];
        }
      }
      
      // If still no FAQs, check for serialized data
      if (empty($faqs) && isset($meta['neighborhood_faq'][0])) {
        $serialized_data = $meta['neighborhood_faq'][0];
        $unserialized = maybe_unserialize($serialized_data);
        
        if (is_array($unserialized)) {
          
          // Handle different possible structures
          if (isset($unserialized[0]) && is_array($unserialized[0])) {
            // Array of arrays
            foreach ($unserialized as $item) {
              if (isset($item['question']) && isset($item['answer'])) {
                $faqs[] = [
                  'question' => $item['question'],
                  'answer' => $item['answer']
                ];
              }
            }
          } elseif (isset($unserialized['question']) && isset($unserialized['answer'])) {
            // Single FAQ item
            $faqs[] = [
              'question' => $unserialized['question'],
              'answer' => $unserialized['answer']
            ];
          }
        }
      }
      
      // If still no FAQs, check for any meta key containing 'faq'
      if (empty($faqs)) {
        foreach ($meta as $k => $vals) {
          if (stripos($k, 'faq') !== false && !empty($vals[0])) {
            $potential_data = maybe_unserialize($vals[0]);
            
            if (is_array($potential_data)) {
              // Try to extract Q&A pairs
              foreach ($potential_data as $key => $value) {
                if (is_array($value) && isset($value['question']) && isset($value['answer'])) {
                  $faqs[] = [
                    'question' => $value['question'],
                    'answer' => $value['answer']
                  ];
                }
              }
            }
          }
        }
      }
      
    }
    
    // Last resort: check for global FAQ data if this is the Riviera neighborhood
    if (empty($faqs) && (strpos(strtolower(get_the_title()), 'riviera') !== false ||
                         strpos(get_post_field('post_name', get_the_ID()), 'riviera') !== false)) {
      
      // Hardcoded Riviera FAQs from the YAML file
      $faqs = [
        [
          'question' => 'What types of homes are available in Riviera?',
          'answer' => 'Riviera offers single-family homes, townhouses, and some multi-family properties. Many feature bayfront access, private docks, or expansive water views.'
        ],
        [
          'question' => 'Is Riviera family-friendly?',
          'answer' => "Yes. Riviera's quiet streets, bay access, and proximity to parks and schools make it a welcoming neighborhood for families."
        ],
        [
          'question' => 'How is parking in Riviera?',
          'answer' => 'Many homes have off-street parking, including garages or driveways. Street parking is also available, though demand increases during summer.'
        ],
        [
          'question' => 'Is Riviera a good investment?',
          'answer' => "Yes. Riviera's bayfront setting and limited supply support strong long-term value. Pricing changes with market conditions; request a comp set or see our Market Monitor."
        ],
        [
          'question' => 'What kind of lifestyle does Riviera offer?',
          'answer' => "Life in Riviera centers on the water. Residents enjoy boating, fishing, kayaking, and sunsets, balanced with easy access to Ocean City's beaches and boardwalk."
        ],
        [
          'question' => 'What unique features define Riviera?',
          'answer' => 'Many homes include private docks and panoramic bay views, making it especially attractive to boating and water sports enthusiasts.'
        ]
      ];
      
    }
  }
}
// Only render if we have clean items
if ( ! empty($faqs) ) : ?>
  <div class="ocnj-faq-container">
    <h2>Frequently Asked Questions</h2>
    <div class="ocnj-faq-list">
      <?php foreach ($faqs as $faq): ?>
        <div class="ocnj-faq-item">
          <h3><?php echo esc_html($faq['question']); ?></h3>
          <div class="ocnj-faq-answer" style="display:none;"><?php echo wp_kses_post($faq['answer']); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

  <?php $idx_links = get_field('neighborhood_idx_links'); if (!empty($idx_links)) : ?>
  <div class="ocnj-cta-group">
    <?php if (!empty($idx_links['for_sale']['condos'])) : ?>
      <a class="ocnj-btn" href="<?php echo esc_url($idx_links['for_sale']['condos']); ?>" target="_blank" rel="noopener">Condos for Sale</a>
    <?php endif; ?>
    <?php if (!empty($idx_links['for_sale']['sf'])) : ?>
      <a class="ocnj-btn" href="<?php echo esc_url($idx_links['for_sale']['sf']); ?>" target="_blank" rel="noopener">Single-Family for Sale</a>
    <?php endif; ?>
    <?php if (!empty($idx_links['sold']['condos'])) : ?>
      <a class="ocnj-btn" href="<?php echo esc_url($idx_links['sold']['condos']); ?>" target="_blank" rel="noopener">Recently Sold Condos</a>
    <?php endif; ?>
    <?php if (!empty($idx_links['sold']['sf'])) : ?>
      <a class="ocnj-btn" href="<?php echo esc_url($idx_links['sold']['sf']); ?>" target="_blank" rel="noopener">Recently Sold Single-Family</a>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php $related = get_field('neighborhood_related'); if (!empty($related)) : ?>
  <div class="ocnj-related-neighborhoods">
    <h2>Related Neighborhoods</h2>
    <div class="ocnj-neighborhoods-grid">
      <?php foreach ($related as $neighborhood) :
        $related_market_data = function_exists('get_field') ? get_field('neighborhood_market_data', $neighborhood->ID) : [];
        $related_price_badge = '';
        
        if (is_array($related_market_data)) {
          if (!empty($related_market_data['price_badge'])) {
            $related_price_badge = $related_market_data['price_badge'];
          } elseif (!empty($related_market_data['median_price']) && is_numeric($related_market_data['median_price'])) {
            $related_price_badge = '$' . number_format((float)$related_market_data['median_price']);
          } elseif (!empty($related_market_data['median_list_price']) && is_numeric($related_market_data['median_list_price'])) {
            $related_price_badge = '$' . number_format((float)$related_market_data['median_list_price']);
          }
        }
      ?>
      <article class="ocnj-neighborhood-card">
        <a href="<?php echo get_permalink($neighborhood->ID); ?>" class="ocnj-card-link">
          <div class="ocnj-card-image-container">
            <?php
            // Get hero image URL using helper function
            $img_url = function_exists('locnj_get_hero_url') ? locnj_get_hero_url($neighborhood->ID) : '';
            
            // Default dimensions for the image
            $width = 800;
            $height = 600;

            if ($img_url) : ?>
              <img
                src="<?php echo esc_url($img_url); ?>"
                alt="<?php echo esc_attr(get_the_title($neighborhood->ID)); ?>"
                class="ocnj-card-image"
                loading="lazy"
                decoding="async"
                width="<?php echo esc_attr($width); ?>"
                height="<?php echo esc_attr($height); ?>"
              />
            <?php endif; ?>
            <?php if ($related_price_badge) : ?><div class="ocnj-price-badge"><?php echo esc_html($related_price_badge); ?></div><?php endif; ?>
          </div>
          <div class="ocnj-card-content">
            <h3 class="ocnj-card-title"><?php echo get_the_title($neighborhood->ID); ?></h3>
            <p class="ocnj-card-description"><?php echo get_the_excerpt($neighborhood->ID); ?></p>
          </div>
        </a>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

</div>
</article>
<?php endwhile; ?>
</main>
</div>

<?php
// SEO Schema is now included via wp_head action in Plugin.php
?>

<?php get_footer();