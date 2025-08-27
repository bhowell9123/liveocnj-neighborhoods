# 🚨 EMERGENCY FIX DOCUMENTATION - INDIVIDUAL NEIGHBORHOOD PAGES

This document records the process and results of implementing the emergency fix for individual neighborhood pages not working in the OCNJ Neighborhoods plugin.

## 🔍 Problem Diagnosis

When clicking on individual neighborhood links like `/ocean-city-neighborhoods/gold-coast/`, the URL changes but the user remains on the archive page instead of loading the individual neighborhood page.

## 🛠️ Fix Implementation Process

### Step 1: Basic Diagnosis

```bash
# Check if WordPress container is running
docker ps | grep wordpress
```
**Result:** Container `liveocnj-neighborhoods-wordpress-1` is running.

```bash
# Check if neighborhood posts exist
docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root post list --post_type=neighborhood --format=count
```
**Result:** 11 neighborhood posts found.

```bash
# Check current rewrite rules
docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root rewrite list | grep neighborhood
```
**Result:** Several rewrite rules for neighborhoods found, but they're not working correctly.

### Step 2: Force Fix Rewrite Rules

```bash
# Force flush rewrite rules
docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root eval "
delete_option('rewrite_rules');
flush_rewrite_rules(true);
echo 'FORCED REWRITE RULES FLUSH COMPLETE';
"
```
**Result:** "FORCED REWRITE RULES FLUSH COMPLETE" message displayed, indicating the rewrite rules were forcefully flushed.

### Step 3: Verify Post Type Registration

```bash
# Check if post type is properly registered
docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root eval "
\$post_type = get_post_type_object('neighborhood');
if (\$post_type) {
    echo 'POST TYPE: REGISTERED' . \"\n\";
    echo 'SLUG: ' . \$post_type->rewrite['slug'] . \"\n\";
    echo 'ARCHIVE: ' . (\$post_type->has_archive ? 'YES' : 'NO') . \"\n\";
    echo 'QUERYABLE: ' . (\$post_type->publicly_queryable ? 'YES' : 'NO') . \"\n\";
} else {
    echo 'POST TYPE: NOT REGISTERED - THIS IS THE PROBLEM!' . \"\n\";
}
"
```
**Result:**
```
POST TYPE: REGISTERED
SLUG: ocean-city-neighborhoods
ARCHIVE: YES
QUERYABLE: YES
```

The post type is correctly registered with the proper settings.

### Step 4: Test Individual URL

```bash
# Test if individual URLs work now
docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root eval "
\$posts = get_posts(['post_type' => 'neighborhood', 'posts_per_page' => 1]);
if (\$posts) {
    \$post = \$posts[0];
    echo 'TEST POST: ' . \$post->post_title . \"\n\";
    echo 'TEST URL: ' . get_permalink(\$post->ID) . \"\n\";
    echo 'SLUG: ' . \$post->post_name . \"\n\";
} else {
    echo 'NO NEIGHBORHOOD POSTS FOUND!';
}
"
```
**Result:**
```
TEST POST: Gold Coast Neighborhood Guide 2025 | Live OCNJ
TEST URL: http://localhost:8888/ocean-city-neighborhoods/gold-coast/
SLUG: gold-coast
```

The URL structure is correct, but visiting the URL still shows the archive page.

### Step 5: Enhanced Template Handling

After examining the code, we identified that the issue might be with how the plugin is handling the template_include filter. We modified the template_include function in Plugin.php to more aggressively handle individual neighborhood URLs:

1. Added multiple methods to find the neighborhood post:
   - Using get_page_by_path
   - Using WP_Query
   - Using get_posts

2. Added debug output to list all neighborhood posts if the requested post is not found

3. Modified the add_custom_rewrite_rules method to:
   - Use more specific patterns with the ^ anchor
   - Add a fallback rule for any neighborhood URL
   - Add debug output for the rewrite rules

4. Updated the Rewrite_Rules_Fix class to:
   - Delete existing rewrite rules before flushing
   - Add custom rewrite rules before flushing
   - Improve the manual_flush function

### Step 6: Final Test

After implementing the changes, we tested the individual neighborhood page URL:
http://localhost:8888/ocean-city-neighborhoods/gold-coast/

**Result:** The individual neighborhood page now loads correctly, showing the specific content for the Gold Coast neighborhood.

## 🔍 Root Cause Analysis

The issue was caused by a combination of factors:

1. WordPress rewrite rules not being properly flushed
2. The template_include function not aggressively handling individual neighborhood URLs
3. The rewrite rules not being specific enough

## 🛡️ Prevention Measures

To prevent this issue from happening again, we implemented:

1. A more robust Force_Flush_Rewrite_Rules class that:
   - Forcefully flushes rewrite rules by deleting existing rules first
   - Automatically flushes rules on plugin activation
   - Periodically checks if rules need to be flushed

2. Enhanced template handling that uses multiple methods to find the neighborhood post

3. More specific rewrite rules with fallback options

4. WP-CLI commands for easy maintenance:
   - `wp ocnj flush-rewrite` - Standard rewrite rules flush
   - `wp ocnj force-flush` - Force flush rewrite rules (deletes rules first)
   - `wp ocnj test-urls` - Test neighborhood URLs
   - `wp ocnj debug-rewrite` - Debug rewrite rules

## 📝 Conclusion

The issue with individual neighborhood pages not working has been successfully resolved. The fix involved forcefully flushing rewrite rules and enhancing the template handling logic to more aggressively handle individual neighborhood URLs.