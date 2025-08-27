<?php
/**
 * Global FAQ (legacy layout w/ TOC + 6 categories).
 * Works with either:
 *  A) flat JSON array of 36 Q&As (legacy order), or
 *  B) category-grouped JSON as shown below.
 *
 * JSON (preferred):
 * [
 *   {"category":"buying","title":"Buying a Home in Ocean City, NJ","items":[{"q":"Q1...","a":"<p>...</p>"}, ... 6 items ...]},
 *   {"category":"selling","title":"Selling in OCNJ","items":[ ... 6 items ... ]},
 *   {"category":"investing","title":"Investing in OCNJ","items":[ ... ]},
 *   {"category":"living","title":"Living in OCNJ","items":[ ... ]},
 *   {"category":"agent","title":"Working with an Agent","items":[ ... ]},
 *   {"category":"process","title":"The Real Estate Process","items":[ ... ]}
 * ]
 */
defined('ABSPATH') || exit;

if (!function_exists('locnj_render_global_faq')) {
  function locnj_render_global_faq() {
    // First try to get data from WordPress option
    $option_data = get_option('locnj_faq_data');
    
    if ($option_data) {
      $json = json_decode($option_data, true);
    } else {
      // Fallback to file-based JSON if option is not set
      $faq_file = dirname(LOCNJ_NEIGHBORHOODS_FILE) . '/assets/data/faq.json';
      if (!file_exists($faq_file)) {
        echo '<!-- FAQ data not found (neither option nor file) -->';
        return;
      }

      $json = json_decode(file_get_contents($faq_file), true);
    }
    
    if (!$json) {
      echo '<!-- Invalid FAQ JSON data -->';
      return;
    }

    // Normalize: accept grouped JSON OR flat JSON of 36 in legacy order.
    $groups = [];

    // Helper to clean slugs
    $slugify = function ($s) {
      $s = strtolower(trim($s));
      $s = preg_replace('/[^a-z0-9]+/','-',$s);
      return trim($s,'-');
    };

    // Check if this is the new format with categories
    if (isset($json['categories']) && is_array($json['categories'])) {
      foreach ($json['categories'] as $category) {
        if (empty($category['questions']) || !is_array($category['questions'])) continue;
        $key = $slugify($category['title']);
        $groups[$key] = [
          'title' => $category['title'],
          'items' => []
        ];
        
        // Convert questions format
        foreach ($category['questions'] as $question) {
          if (!empty($question['question']) && !empty($question['answer'])) {
            $groups[$key]['items'][] = [
              'q' => $question['question'],
              'a' => $question['answer']
            ];
          }
        }
      }
    }
    // Fallback to legacy formats
    else if (isset($json[0]['items']) && is_array($json[0]['items'])) {
      // Legacy grouped format
      foreach ($json as $group) {
        if (empty($group['items']) || !is_array($group['items'])) continue;
        $key   = !empty($group['category']) ? $slugify($group['category']) : $slugify($group['title']);
        $title = !empty($group['title']) ? $group['title'] : ucfirst($key);
        $groups[$key] = [
          'title' => $title,
          'items' => $group['items'],
        ];
      }
    } else {
      // Flat JSON -> map by index into 6 buckets of 6 (legacy order)
      $titles = [
        'buying'   => 'Buying a Home in Ocean City, NJ',
        'selling'  => 'Selling in OCNJ',
        'investing'=> 'Investing in OCNJ',
        'living'   => 'Living in OCNJ',
        'agent'    => 'Working with an Agent',
        'process'  => 'The Real Estate Process',
      ];
      $buckets = ['buying','selling','investing','living','agent','process'];
      foreach ($buckets as $b) {
        $groups[$b] = ['title' => $titles[$b], 'items' => []];
      }

      $i = 0;
      foreach ($json as $row) {
        $bucket = $buckets[min(intval($i / 6), 5)]; // 0–5, 6–11, ..., 30–35
        $q = isset($row['q']) ? $row['q'] : '';
        $a = isset($row['a']) ? $row['a'] : '';
        if ($q && $a) {
          $groups[$bucket]['items'][] = ['q' => $q, 'a' => $a];
        }
        $i++;
      }
    }

    if (empty($groups)) {
      echo '<!-- No FAQ groups -->';
      return;
    }

    // ——— Render ———
    echo '<section class="ocnj-faq-section">';
    echo '  <h2>Ocean City Real Estate FAQ</h2>';
    echo '  <p class="section-intro">Your on-island expert answers to common questions about buying, selling, investing, and living in Ocean City, NJ.</p>';

    // Table of contents
    echo '  <nav class="table-of-contents" aria-label="Table of contents">';
    echo '    <h3>Table of Contents</h3>';
    echo '    <ul>';
    foreach ($groups as $key => $group) {
      echo '      <li><a href="#faq-' . esc_attr($key) . '">' . esc_html($group['title']) . '</a></li>';
    }
    echo '    </ul>';
    echo '  </nav>';

    // Groups + questions
    echo '  <div class="ocnj-faq-grid">';
    foreach ($groups as $key => $group) {
      echo '    <h2 id="faq-' . esc_attr($key) . '">' . esc_html($group['title']) . '</h2>';

      $n = 1;
      foreach ($group['items'] as $item) {
        $qid = 'q-' . $key . '-' . $n;
        $q = isset($item['q']) ? $item['q'] : '';
        $a = isset($item['a']) ? $item['a'] : '';
        if (!$q || !$a) { $n++; continue; }

        echo '    <div class="ocnj-faq-item" id="' . esc_attr($qid) . '">';
        echo '      <h3>' . esc_html($q) . '</h3>';
        // answers may contain links/markup
        echo '      <div>' . wp_kses_post($a) . '</div>';
        echo '    </div>';
        $n++;
      }
    }
    echo '  </div>';
    
    // FAQ accordion JavaScript is now properly enqueued in the main plugin file
    
    echo '</section>';
  }
}