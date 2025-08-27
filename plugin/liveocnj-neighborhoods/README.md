# LiveOCNJ Neighborhoods Plugin

## Overview
This plugin creates a custom post type for Ocean City NJ neighborhoods with improved image handling, structured data, and SEO features.

## Features
- Custom post type for neighborhoods
- ACF integration for structured data
- Responsive neighborhood cards
- Archive and single templates
- SEO optimization
- FAQ section with accordion functionality

## Common Issues and Solutions

### Individual Neighborhood Pages Not Working

**Symptom:** When clicking on individual neighborhood links like `/ocean-city-neighborhoods/gold-coast/`, the URL changes but you remain on the archive page instead of loading the individual neighborhood page.

**Cause:** WordPress rewrite rules need to be flushed to recognize the custom URL pattern.

**Solution:**

1. **Immediate Fix:** Flush WordPress rewrite rules using WP-CLI:
   ```bash
   wp rewrite flush
   ```
   
   Or use the plugin's custom command:
   ```bash
   wp ocnj flush-rewrite
   ```

2. **Force Regenerate Rewrite Rules:** If the regular flush doesn't work, use:
   ```bash
   wp ocnj force-flush
   ```
   
   This will delete the existing rewrite rules and regenerate them.

3. **Verify Fix:** Check if a neighborhood post URL is working:
   ```bash
   wp ocnj test-urls
   ```

### Debugging Rewrite Rules

If you need to debug rewrite rules, use:
```bash
wp ocnj debug-rewrite
```

This will show:
- Whether the post type is registered
- Total number of rewrite rules
- When rules were last flushed
- Neighborhood-specific rules

## Maintenance

### Plugin Updates

When updating the plugin:

1. The plugin automatically flushes rewrite rules on activation
2. If you update the plugin version in `liveocnj-neighborhoods.php`, rewrite rules will be automatically flushed
3. If you make changes to the post type registration or rewrite rules, manually flush the rules:
   ```bash
   wp ocnj flush-rewrite
   ```

### Version History

- 1.0.0: Initial release
- 1.0.1: Added force flush rewrite rules functionality to fix individual neighborhood page issues
- 1.0.2: Enhanced template handling and rewrite rules for more robust neighborhood page routing

### Documentation

For detailed information about the rewrite rules fix, see:
- [Emergency Fix Documentation](../../EMERGENCY_FIX_DOCUMENTATION.md) - Detailed process of implementing the fix
- [Neighborhood Pages Fix Summary](../../NEIGHBORHOOD_PAGES_FIX_SUMMARY.md) - Concise overview of the issue and fix
- [Rewrite Rules Fix](../../FIX/REWRITE_RULES_FIX.md) - Technical details of the rewrite rules implementation
- [Fix Script](../../fix_neighborhood_pages.sh) - Automated script for applying the fix

## Development

### File Structure

- `liveocnj-neighborhoods.php` - Main plugin file
- `src/` - Plugin classes
  - `Plugin.php` - Main plugin class
  - `PostType_Neighborhood.php` - Neighborhood post type registration
  - `ACF_Fields.php` - ACF field registration
  - `Rewrite_Rules_Fix.php` - Rewrite rules fix
  - `Force_Flush_Rewrite_Rules.php` - Force flush rewrite rules
- `templates/` - Template files
  - `archive-neighborhood.php` - Archive template
  - `single-neighborhood.php` - Single neighborhood template
  - `partials/` - Partial templates
- `assets/` - CSS, JS, and images
- `wp-cli-commands.php` - WP-CLI commands

### WP-CLI Commands

The plugin provides several WP-CLI commands for maintenance:

- `wp ocnj flush-rewrite` - Flush rewrite rules
- `wp ocnj force-flush` - Force flush rewrite rules (deletes rules first)
- `wp ocnj test-urls` - Test neighborhood URLs
- `wp ocnj debug-rewrite` - Debug rewrite rules