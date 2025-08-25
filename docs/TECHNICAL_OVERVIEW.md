# LiveOCNJ Neighborhoods Plugin Technical Overview

## Request Lifecycle

### Archive Page (`/ocean-city-neighborhoods/`)

1. **Request Received**: WordPress receives request for `/ocean-city-neighborhoods/`
2. **Query Processing**:
   - WordPress identifies this as a post type archive for 'neighborhood'
   - `WP_Query` is created with `post_type=neighborhood`
3. **Hook Execution**:
   - `init` hook: `LOCNJ_Neighborhoods_PostType_Neighborhood::register()` ensures post type is registered
   - `wp_enqueue_scripts` hook: `LOCNJ_Neighborhoods_Plugin::enqueue_assets()` loads CSS/JS assets
   - `body_class` hook: `LOCNJ_Neighborhoods_Plugin::add_body_classes()` adds body classes
   - `template_include` filter: WordPress selects archive template
4. **Template Loading**:
   - WordPress loads `archive-neighborhood.php` from the plugin's templates directory
5. **Template Rendering**:
   - Template executes custom WP_Query to fetch neighborhoods
   - Template renders hero, map, grid, and FAQ sections
   - Template includes partials: `global-faq.php` and `expert-block.php`

### Single Neighborhood Page (`/ocean-city-neighborhoods/[slug]/`)

1. **Request Received**: WordPress receives request for `/ocean-city-neighborhoods/[slug]/`
2. **Query Processing**:
   - WordPress identifies this as a single post of type 'neighborhood'
   - `WP_Query` is created with `post_type=neighborhood` and `name=[slug]`
3. **Hook Execution**:
   - `init` hook: `LOCNJ_Neighborhoods_PostType_Neighborhood::register()` ensures post type is registered
   - `wp_enqueue_scripts` hook: `LOCNJ_Neighborhoods_Plugin::enqueue_assets()` loads CSS/JS assets
   - `body_class` hook: `LOCNJ_Neighborhoods_Plugin::add_body_classes()` adds body classes
   - `template_include` filter: WordPress selects single template
4. **Template Loading**:
   - WordPress loads `single-neighborhood.php` from the plugin's templates directory
5. **Template Rendering**:
   - Template uses the global post object to display neighborhood data
   - Template renders hero, facts, content, FAQ, and related neighborhoods sections

### Landing Page (Static Page with Archive-like Appearance)

1. **Request Received**: WordPress receives request for a static page
2. **Query Processing**:
   - WordPress identifies this as a page (not a post type archive)
3. **Hook Execution**:
   - `body_class` hook: MU plugin `ocnj-neighborhood-landing-body-classes.php` adds archive-like classes
   - `wp_enqueue_scripts` hook: MU plugin ensures neighborhood CSS is loaded
4. **Template Loading**:
   - WordPress loads the theme's page template
5. **Template Rendering**:
   - Page content is rendered with the theme's template
   - CSS styling matches the archive page due to the added body classes

## Asset Loading

### CSS Files

| File | Handle | Dependencies | Purpose |
|------|--------|--------------|---------|
| `assets/css/ocnj-neighborhoods.css` | `locnj-neighborhoods` | `[]` | Main styling for all neighborhood pages |
| `assets/css/ocnj-landing-fixes.css` | `ocnj-landing-fixes` | `['locnj-neighborhoods']` | Fixes for landing page styling |
| `assets/css/ocnj-modal-faq.css` | `locnj-neighborhoods-faq` | `[]` | Styling for FAQ accordions |

### JavaScript Files

| File | Handle | Dependencies | Purpose |
|------|--------|--------------|---------|
| `assets/js/ocnj-neighborhoods.js` | `ocnj-neighborhoods` | `['jquery']` | Interactive elements (modals, accordions) |

## Query Audit

### Archive Page Query

```php
$q = new WP_Query([
  'post_type'           => 'neighborhood',
  'posts_per_page'      => -1,
  // Order so the latest version appears first within a family
  'orderby'             => ['menu_order' => 'ASC', 'date' => 'DESC'],
  'no_found_rows'       => true,
  'ignore_sticky_posts' => true,
  'post_status'         => 'publish',
]);
```

**Key Points**:
- Retrieves all published neighborhood posts (`posts_per_page=-1`)
- Orders by menu_order (ASC) and then date (DESC)
- Performance optimizations: `no_found_rows=true`, `ignore_sticky_posts=true`
- No pre_get_posts filters affecting this query

**Post-Query Processing**:
- Template applies custom deduplication logic to show only the latest post per "family"
- Family is determined by stripping `-live-ocnj` and `-live-ocnj-#` suffixes from the slug

## Data Sources (ACF Fields)

| Field | Type | Template Usage | Fallback |
|-------|------|---------------|----------|
| `neighborhood_heading` | Text | Single: Hero heading | Post title |
| `neighborhood_meta_description` | Textarea | SEO meta description | Excerpt |
| `neighborhood_about` | Textarea | Single: Hero subtitle, Archive: Card description | Excerpt |
| `neighborhood_facts` | Repeater | Single: Facts list, Archive: Card chips | Empty array |
| `neighborhood_facts_[n]_text` | Text | Individual fact text | - |
| `neighborhood_faq` | Repeater | Single: FAQ accordion | Empty array |
| `neighborhood_faq_[n]_question` | Text | FAQ question | - |
| `neighborhood_faq_[n]_answer` | Wysiwyg | FAQ answer | - |
| `neighborhood_hero_image` | Image | Single & Archive: Hero image | Featured image → Fallback image |
| `neighborhood_market_data` | Group | Single: Stats display | Empty array |
| `neighborhood_market_data[median_price]` | Number | Single: Stats value | - |
| `neighborhood_market_data[median_list_price]` | Number | Single: Stats value (fallback) | - |
| `neighborhood_market_data[dom]` | Number | Single: Stats value | - |
| `neighborhood_market_data[dom_median]` | Number | Single: Stats value (fallback) | - |
| `neighborhood_market_data[active_listings]` | Number | Single: Stats value | - |
| `neighborhood_market_data[active_inventory]` | Number | Single: Stats value (fallback) | - |
| `neighborhood_market_data[yoy]` | Number | Single: Stats value | - |
| `neighborhood_idx_links` | Group | Single: CTA buttons | - |
| `neighborhood_related` | Relationship | Single: Related neighborhoods | - |

## Selector/Markup Contract

### Hero Section

```html
<section class="ocnj-hero">
  <div class="ocnj-hero-bg">
    <img src="..." alt="" loading="eager" decoding="async">
  </div>
  <div class="ocnj-hero-shade"></div>
  <div class="ocnj-hero__inner">
    <h1>...</h1>
    <p class="ocnj-hero-subtitle">...</p>
    <!-- Optional price badge -->
    <p class="ocnj-hero-price">...</p>
    <!-- Stats section -->
    <ul class="ocnj-stats">
      <li class="ocnj-stat">
        <span class="ocnj-stat-value">...</span>
        <span class="ocnj-stat-label">...</span>
      </li>
      <!-- More stats... -->
    </ul>
  </div>
</section>
```

### Stats Section

```html
<ul class="ocnj-stats">
  <li class="ocnj-stat">
    <span class="ocnj-stat-value">...</span>
    <span class="ocnj-stat-label">...</span>
  </li>
  <!-- More stats... -->
</ul>
```

**CSS Contract**: Stats are displayed as a flex row with dot separators between items. The CSS uses `li + li::before` to create the separators.

### Card Grid

```html
<div class="ocnj-card-grid">
  <article class="ocnj-card">
    <a class="ocnj-card__media" href="...">
      <img src="..." alt="..." loading="lazy" decoding="async">
      <!-- Optional price badge -->
      <div class="ocnj-price-badge">...</div>
    </a>
    <div class="ocnj-card__body">
      <h3 class="ocnj-card__title">
        <a href="...">...</a>
      </h3>
      <p class="ocnj-card__sub">...</p>
      <!-- Optional fact chips -->
      <ul class="ocnj-card-chips" aria-label="Highlights">
        <li>...</li>
        <!-- More chips... -->
      </ul>
      <p class="ocnj-card__link"><a href="...">Read the guide →</a></p>
    </div>
  </article>
  <!-- More cards... -->
</div>
```

### FAQ Section

```html
<div class="ocnj-faq-container">
  <h2>Frequently Asked Questions</h2>
  <div class="ocnj-faq-list">
    <div class="ocnj-faq-item">
      <h3>...</h3>
      <div class="ocnj-faq-answer" style="display:none;">...</div>
    </div>
    <!-- More FAQ items... -->
  </div>
</div>
```

## Body Class Behavior

### Body Classes Added by Plugin

```php
// In Plugin.php
if ($is_landing) {
  $classes[] = 'ocnj-landing';
}

if (is_post_type_archive('neighborhood')) {
  $classes[] = 'ocnj-landing-archive';
}
```

### Body Classes Added by MU Plugin

```php
// In ocnj-neighborhood-landing-body-classes.php
$force = array(
  "archive",
  "post-type-archive",
  "post-type-archive-neighborhood",
  "ocnj-neighborhood-page",
  "ocnj-neighborhood-landing",
);
```

**Target Pages for MU Plugin**:
- Pages with IDs: 167843, 169371, 175056
- Pages with slugs: "ocean-city-neighborhoods", "neighborhoods", "home-content"
- Pages with URLs matching: `/ocean-city-neighborhoods(/|$)`

## Template Section → CSS Selectors → Data Fields Table

| Template Section | CSS Selectors | Data Fields |
|------------------|---------------|-------------|
| Hero | `.ocnj-hero`, `.ocnj-hero-bg`, `.ocnj-hero-shade`, `.ocnj-hero__inner` | `neighborhood_hero_image` |
| Stats | `.ocnj-stats`, `.ocnj-stat`, `.ocnj-stat-value`, `.ocnj-stat-label` | `neighborhood_market_data` |
| Card Grid | `.ocnj-card-grid`, `.ocnj-card`, `.ocnj-card__media`, `.ocnj-card__body` | Post data, `neighborhood_about` |
| Card Chips | `.ocnj-card-chips`, `.ocnj-card-chips li` | `neighborhood_facts` |
| Facts | `.ocnj-facts-container`, `.ocnj-facts-list` | `neighborhood_facts` |
| FAQ | `.ocnj-faq-container`, `.ocnj-faq-list`, `.ocnj-faq-item` | `neighborhood_faq` |
| Related | `.ocnj-related-neighborhoods`, `.ocnj-neighborhoods-grid` | `neighborhood_related` |
| Expert | `.ocnj-expert-section`, `.ocnj-expert-card` | Static content |