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
- `wp ocnj debug-rewrite` - Debug rewrite rules-e 

## Stats Bar Dots Fix


# Stats Bar Dots Fix Documentation

## Issue Overview

The OCNJ Neighborhoods plugin's stats bar was displaying unwanted dots between list items. These dots were being added by CSS pseudo-elements (::before or ::after) targeting the list items, likely from the WordPress theme (Astra) or other plugins.

## Solution Implemented

We applied a "nuclear option" fix by completely replacing the list-based HTML structure (UL/LI) with DIV elements. This eliminated the dots by removing the elements that the pseudo-selectors were targeting while preserving the visual layout.

### Technical Implementation Details

1. The original HTML structure used a UL/LI pattern:
   ```html
   <ul class="ocnj-stats">
     <li>...</li>
     <li>...</li>
     <!-- etc. -->
   </ul>
   ```

2. We replaced it with a DIV-based structure:
   ```html
   <div class="stats-container">
     <div class="stats-item">...</div>
     <div class="stats-item">...</div>
     <!-- etc. -->
   </div>
   ```

3. All styling was preserved using equivalent CSS properties on the DIVs.

### Class Name Changes

- `ocnj-stats` (UL) → `stats-container` (DIV)
- List items (LI) → `stats-item` (DIV)

## Why This Approach Worked

1. **CSS Specificity Wars**: The theme/plugin CSS had higher specificity that couldn't be easily overridden.
2. **Pseudo-element Limitations**: ::before/::after pseudo-elements can't be fully overridden by external CSS.
3. **Caching Issues**: Multiple layers of caching prevented CSS-only changes from taking effect.
4. **Theme Interference**: The WordPress theme was likely adding its own list styling.

By changing the HTML structure itself, we eliminated the elements that were being targeted by the problematic CSS rules.

## Future Maintenance Guidelines

### When Editing the Stats Section

1. **Remember the DIV Structure**: The stats section now uses DIVs instead of UL/LI elements.
2. **Use the New Class Names**: 
   - `stats-container` (formerly `ocnj-stats`)
   - `stats-item` (formerly list items)
3. **Preserve the Flexbox Layout**: The layout is maintained using flexbox properties.
4. **Avoid Reverting to Lists**: Do not change back to UL/LI structure as this will reintroduce the dots issue.

### If Similar Issues Occur Elsewhere

1. **Identify the HTML Structure**: Determine if list elements (UL/LI) are being used.
2. **Check for Pseudo-elements**: Use browser developer tools to inspect for ::before or ::after pseudo-elements.
3. **Consider Structure Change**: Instead of fighting CSS specificity battles, consider changing the HTML structure.
4. **Document Changes**: Always document structural changes for future reference.

## Key Lesson

Sometimes the best solution is to change the HTML structure rather than fight CSS specificity battles. This approach is:
- More reliable than CSS overrides
- Future-proof against theme changes
- Cleaner than complex CSS workarounds
- Easier to maintain with clear class names

## Script Used for the Fix

For reference, here's the Python script used to implement the fix:

```python
import re

# Read the template file
with open('plugin/liveocnj-neighborhoods/templates/archive-neighborhood.php', 'r') as f:
    content = f.read()

# Find the stats section
stats_pattern = r'<ul class="ocnj-stats"[^>]*>(.*?)</ul>'

# Extract the stats section content
stats_match = re.search(stats_pattern, content, re.DOTALL)
if stats_match:
    stats_content = stats_match.group(1)
    
    # Replace each <li> with a <div>
    stats_content = re.sub(r'<li[^>]*>', '<div class="stats-item" style="flex: 1; min-width: 120px; text-align: center !important; visibility: visible !important; opacity: 1 !important;">', stats_content)
    stats_content = re.sub(r'</li>', '</div>', stats_content)
    
    # Create the new DIV-based stats section
    new_stats = f'''<div class="stats-container" style="list-style: none !important; padding: 2rem !important; margin: 3rem auto 0 !important; display: flex !important; flex-wrap: wrap !important; gap: 1rem !important; align-items: center !important; max-width: 960px !important; background: rgba(255,255,255,.95) !important; backdrop-filter: blur(10px) !important; border-radius: 16px !important; box-shadow: 0 8px 32px rgba(0,0,0,.1) !important; visibility: visible !important; opacity: 1 !important;">
{stats_content}
</div>'''
    
    # Replace the UL with the new DIV structure
    content = re.sub(stats_pattern, new_stats, content, flags=re.DOTALL)
    
    # Write back
    with open('plugin/liveocnj-neighborhoods/templates/archive-neighborhood.php', 'w') as f:
        f.write(content)
```
