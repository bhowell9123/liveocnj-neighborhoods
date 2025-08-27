# WordPress Rewrite Rules Fix for Individual Neighborhood Pages

## 🔍 Problem Diagnosis

### Symptoms
- Individual neighborhood pages not loading correctly
- URL changes to `/ocean-city-neighborhoods/[neighborhood-slug]/` but content remains on archive page
- No 404 error, just incorrect content display

### Root Cause Analysis
The issue stems from WordPress's rewrite rules system, which maps pretty URLs to internal query parameters. When these rules are not properly registered or flushed, WordPress cannot correctly route requests for individual neighborhood pages.

Specific issues identified:
1. Rewrite rules not properly flushed after plugin activation
2. Custom post type registration not properly setting up rewrite rules
3. Template handling not aggressively finding the correct template for individual pages

## 🛠️ Technical Solution

### 1. Enhanced Rewrite Rules Registration

```php
/**
 * Register custom rewrite rules for neighborhood pages
 */
public function add_custom_rewrite_rules() {
    // Primary rule for individual neighborhood pages
    add_rewrite_rule(
        'ocean-city-neighborhoods/([^/]+)/?$',
        'index.php?post_type=neighborhood&name=$matches[1]',
        'top'
    );
    
    // Fallback rule with explicit post_type parameter
    add_rewrite_rule(
        'ocean-city-neighborhoods/([^/]+)/?$',
        'index.php?neighborhood=$matches[1]',
        'top'
    );
}
```

### 2. Force Flush Mechanism

Created a dedicated class `Force_Flush_Rewrite_Rules` to handle rewrite rule flushing:

```php
/**
 * Force flush rewrite rules by first deleting them
 */
public static function force_flush() {
    // Delete existing rewrite rules
    delete_option('rewrite_rules');
    
    // Then flush to regenerate them
    flush_rewrite_rules(true);
    
    // Log the flush for debugging
    update_option('locnj_last_rewrite_flush', current_time('mysql'));
}
```

### 3. Version-Based Flushing

Added version tracking to automatically flush rules when the plugin is updated:

```php
/**
 * Check if plugin version has changed and flush rules if needed
 */
public function check_version() {
    $current_version = '1.0.1'; // Current plugin version
    $stored_version = get_option('locnj_neighborhoods_version', '0');
    
    if (version_compare($stored_version, $current_version, '<')) {
        // Version changed, flush rewrite rules
        self::force_flush();
        
        // Update stored version
        update_option('locnj_neighborhoods_version', $current_version);
    }
}
```

### 4. Enhanced Template Handling

Improved the template_include filter to more aggressively find neighborhood posts:

```php
/**
 * Enhanced template handling for neighborhood pages
 */
public function template_include($template) {
    // Check if we're on a neighborhood page
    if (is_singular('neighborhood')) {
        // Try to load the template from the plugin directory
        $plugin_template = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'templates/single-neighborhood.php';
        
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    
    // Additional check for neighborhood URLs that might not be properly recognized
    global $wp;
    $current_url = home_url(add_query_arg(array(), $wp->request));
    
    if (strpos($current_url, '/ocean-city-neighborhoods/') !== false && 
        !is_post_type_archive('neighborhood')) {
        
        // Extract the slug from the URL
        $parts = explode('/ocean-city-neighborhoods/', $current_url);
        if (isset($parts[1])) {
            $slug = trim($parts[1], '/');
            
            // Try to find the post by slug
            $posts = get_posts([
                'name' => $slug,
                'post_type' => 'neighborhood',
                'post_status' => 'publish',
                'numberposts' => 1
            ]);
            
            if (!empty($posts)) {
                // Found a matching post, load the single template
                $plugin_template = plugin_dir_path(LOCNJ_NEIGHBORHOODS_FILE) . 'templates/single-neighborhood.php';
                
                if (file_exists($plugin_template)) {
                    // Set up the global post object
                    global $post;
                    $post = $posts[0];
                    setup_postdata($post);
                    
                    return $plugin_template;
                }
            }
        }
    }
    
    return $template;
}
```

### 5. WP-CLI Commands for Maintenance

Added WP-CLI commands for easy troubleshooting:

```php
/**
 * WP-CLI command to flush rewrite rules
 */
public function flush_rules() {
    Force_Flush_Rewrite_Rules::force_flush();
    WP_CLI::success('Rewrite rules forcefully flushed.');
}

/**
 * WP-CLI command to check rewrite rules
 */
public function check_rules() {
    $rules = get_option('rewrite_rules', []);
    $neighborhood_rules = array_filter($rules, function($rule) {
        return strpos($rule, 'neighborhood') !== false;
    });
    
    WP_CLI::log('Neighborhood rewrite rules:');
    foreach ($neighborhood_rules as $pattern => $query) {
        WP_CLI::log("Pattern: $pattern => $query");
    }
}
```

## 🚀 Implementation Steps

1. **Plugin Activation Hook**:
   ```php
   register_activation_hook(__FILE__, ['Rewrite_Rules_Fix', 'on_activation']);
   ```

2. **Plugin Initialization**:
   ```php
   add_action('init', [$this, 'add_custom_rewrite_rules'], 10);
   add_action('plugins_loaded', [$this, 'check_version'], 20);
   ```

3. **Template Handling**:
   ```php
   add_filter('template_include', [$this, 'template_include'], 99);
   ```

4. **Admin Interface**:
   Added a "Flush Rewrite Rules" button in the admin interface for manual flushing.

## 🧪 Testing and Verification

### Test Cases
1. **Plugin Activation Test**:
   - Deactivate and reactivate the plugin
   - Verify individual neighborhood pages work

2. **URL Structure Test**:
   - Visit `/ocean-city-neighborhoods/`
   - Click on a neighborhood card
   - Verify URL changes to `/ocean-city-neighborhoods/[slug]/`
   - Verify individual content loads

3. **Direct URL Test**:
   - Directly enter `/ocean-city-neighborhoods/[slug]/` in browser
   - Verify correct neighborhood content loads

4. **WP-CLI Test**:
   ```bash
   wp locnj flush-rules
   wp locnj check-rules
   ```

## 🛡️ Prevention Measures

To prevent this issue from recurring:

1. **Automatic Flushing**:
   - On plugin activation
   - On plugin version changes
   - After custom post type registration changes

2. **Robust Template Handling**:
   - Multiple methods to find the correct post
   - Fallback mechanisms for URL parsing

3. **Debugging Tools**:
   - WP-CLI commands for maintenance
   - Admin interface for manual flushing
   - Logging of flush operations

## 📚 WordPress Rewrite Rules Reference

### How WordPress Rewrite Rules Work
1. WordPress receives a request for a pretty URL
2. It checks the rewrite rules to map the URL to query parameters
3. The query parameters determine which content to display
4. The template hierarchy determines which template file to use

### Common Rewrite Rule Issues
- Rules not flushed after changes to post types or permalinks
- Rules overridden by other plugins
- Incorrect rule priority (order matters)
- Missing or incorrect rules for custom URL structures

### Best Practices
- Always flush rules after registering or modifying post types
- Use 'top' priority for critical rules
- Include fallback rules for different URL patterns
- Test thoroughly with direct URL access