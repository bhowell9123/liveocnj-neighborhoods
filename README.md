# OCNJ Neighborhoods WordPress Plugin

## 📋 Project Overview

This repository contains the OCNJ Neighborhoods WordPress plugin, which manages neighborhood listings and FAQs for Ocean City, New Jersey. The plugin provides:

- Custom post type for neighborhoods
- Archive and single neighborhood templates
- Global FAQ system
- Stats display in the hero section

## 🗂️ Project Structure

```
📁 Root Directory:
  📄 README.md                                 # This documentation
  📄 wp-cli.yml                                # WordPress CLI configuration
  📄 ALL_DATA_EMBEDDED_MIGRATION_ALL_CONTENT.php  # Primary migration script
  📄 import_from_md.php                        # Import utility
  📄 group_neighborhood_locnj.json             # ACF field group definition

📁 Main Directories:
  📂 plugin/liveocnj-neighborhoods/            # Main plugin code (active)
  📂 docs/                                     # Documentation
  📂 tools/                                    # Build and testing tools
  📂 archive/                                  # Historical files (not in Git)
```

## 🧹 Project Cleanup & Organization

### Cleanup Summary
- **Before:** 50+ scattered files with duplicates and inspection directories
- **After:** 35 essential files with 136 historical files properly archived
- **Archive Structure:** Organized into plugin_versions/, migrations/, diagnostics/, documentation/, zip_files/

### What Was Cleaned Up
1. **Inspection Directories:** Moved MANUS_INSPECTION_* to archive/plugin_versions/
2. **Migration Scripts:** Kept only ALL_DATA_EMBEDDED_MIGRATION_ALL_CONTENT.php
3. **Documentation:** Organized into docs/ directory structure
4. **Temporary Files:** Removed diagnostic and temporary files from root
5. **ZIP Archives:** Moved to archive/zip_files/

### Archive Directory Structure
- `archive/plugin_versions/` - Historical plugin versions and inspection files
- `archive/migrations/` - Old migration scripts (for reference)
- `archive/diagnostics/` - Debug and diagnostic files
- `archive/documentation/` - Legacy documentation
- `archive/zip_files/` - ZIP archives and backups

## 🔧 Git Source Control Setup

### Repository Configuration
- **Branch:** develop (main development branch)
- **Remote:** https://github.com/bhowell9123/liveocnj-neighborhoods.git
- **Tracked Files:** 52 essential files
- **Ignored Files:** 136 archived files (too large for Git)

### What's Tracked in Git
- ✅ `plugin/liveocnj-neighborhoods/` (30 files) - Main plugin code
- ✅ `docs/` (10 files) - Current documentation
- ✅ Essential root files (README, wp-cli.yml, migration script)
- ✅ Configuration files (.gitignore, ACF definitions)

### What's Ignored
- ❌ `archive/` directory - Historical files (preserved locally)
- ❌ Log files, system files, temporary files
- ❌ WordPress uploads, cache, backup directories

### .gitignore Highlights
- WordPress-specific exclusions
- Archive directories
- System and temporary files
- IDE and editor files

## 📚 Documentation

All documentation is located in the `docs/` directory:

- [Emergency Fix Documentation](docs/EMERGENCY_FIX_DOCUMENTATION.md) - Details on the neighborhood pages fix
- [Neighborhood Pages Fix Summary](docs/NEIGHBORHOOD_PAGES_FIX_SUMMARY.md) - Concise overview of the fix
- [Technical Overview](docs/TECHNICAL_OVERVIEW.md) - Technical details of the plugin
- [Changelog](docs/CHANGELOG.md) - History of changes
- [Safe Changes Guide](docs/SAFE-CHANGES.md) - Guidelines for making safe changes

Historical fix documentation is preserved in `docs/fix_history/`.

## 🔧 Recent Fixes

### 1. Neighborhood Pages Fix

Fixed an issue where individual neighborhood pages weren't loading correctly. When clicking on individual neighborhood links like `/ocean-city-neighborhoods/gold-coast/`, the URL would change but the user would remain on the archive page.

**Modified Files:**
- `plugin/liveocnj-neighborhoods/src/Plugin.php` - Enhanced template handling
- `plugin/liveocnj-neighborhoods/src/Rewrite_Rules_Fix.php` - Improved rewrite rules
- `plugin/liveocnj-neighborhoods/src/Force_Flush_Rewrite_Rules.php` - New class for flushing rules

**Fix Implementation:**
1. **Immediate Fix**: Forcefully flushing WordPress rewrite rules
2. **Enhanced Template Handling**: Improving how the plugin handles individual neighborhood URLs
3. **Better Rewrite Rules**: Making the rewrite rules more specific and adding fallback options
4. **Force Flush Mechanism**: Creating a robust mechanism for flushing rewrite rules
5. **WP-CLI Commands**: Adding commands for easy maintenance

**How to Apply the Fix:**

Automatic Fix:
```bash
./docs/scripts/fix_neighborhood_pages.sh
```

Manual Fix:
```bash
docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root eval "
delete_option('rewrite_rules');
flush_rewrite_rules(true);
"
```

**Prevention Measures:**
1. **Automatic Flushing**: The plugin now automatically flushes rewrite rules on activation and version changes
2. **WP-CLI Commands**: Easy-to-use commands for maintenance
3. **Enhanced Error Handling**: Better debugging and error reporting

### 2. Stats Bar Dots Fix

Fixed an issue with the stats bar in the hero section where unwanted dots were appearing between stats items. The solution replaced the list-based HTML structure (UL/LI) with DIV elements to eliminate the dots while preserving the visual layout.

**Key Changes:**
- Changed HTML structure from UL/LI to DIVs
- Renamed classes for clarity:
  - `ocnj-stats` (UL) → `stats-container` (DIV)
  - List items (LI) → `stats-item` (DIV)
- Preserved styling using equivalent CSS properties

**Technical Implementation Details:**

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

**Why This Approach Worked:**
1. **CSS Specificity Wars**: The theme/plugin CSS had higher specificity that couldn't be easily overridden.
2. **Pseudo-element Limitations**: ::before/::after pseudo-elements can't be fully overridden by external CSS.
3. **Caching Issues**: Multiple layers of caching prevented CSS-only changes from taking effect.
4. **Theme Interference**: The WordPress theme was likely adding its own list styling.

By changing the HTML structure itself, we eliminated the elements that were being targeted by the problematic CSS rules.

**Script Used for the Fix:**

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

## 🔍 Debugging Process

### Failed Approaches (Learning for Future)
1. **CSS Rule Removal** - Removed conflicting grid/flex rules
2. **Template-level Overrides** - Added inline CSS blocks
3. **Ultra-aggressive Pseudo-element Blocking** - Multiple specificity attempts
4. **Cache Clearing** - WordPress, browser, and CDN caches

### Why CSS Fixes Failed
- Theme CSS interference (higher specificity)
- Pseudo-elements can't be overridden by external CSS
- Multiple layers of caching
- WordPress theme (likely Astra) adding list styling

### Final Solution: HTML Structure Change
- Changed from `<ul>/<li>` to `<div>` structure
- Eliminated CSS pseudo-element targets
- More reliable than CSS specificity battles

## 🔄 FAQ Data Recovery

### Issue
- 36 FAQs were missing due to data being stored in wrong WordPress option
- Data was in `ocnj_global_faq` but plugin expected `locnj_faq_data`

### Solution
1. Reactivated correct plugin version (`plugin/liveocnj-neighborhoods/`)
2. Data automatically migrated to correct location
3. All 36 FAQs restored and displaying correctly

## 🚨 Troubleshooting Guide

### If Individual Pages Stop Working
1. Check rewrite rules: `wp rewrite list`
2. Flush rules: `wp rewrite flush`
3. Verify post type registration
4. Check .htaccess file

### If Stats Dots Reappear
1. **DO NOT** change back to `<ul>/<li>` structure
2. Check for theme updates that might add new CSS
3. Use browser developer tools to inspect pseudo-elements
4. Consider additional CSS specificity if needed

### If FAQs Disappear
1. Check WordPress option: `locnj_faq_data`
2. Verify plugin activation status
3. Check for data in `ocnj_global_faq` (old location)
4. Run data migration if needed

## 📈 Version History

### v1.2.0 (2025-08-27) - Major Cleanup & Fixes
- ✅ Fixed individual neighborhood pages (rewrite rules)
- ✅ Eliminated stats bar dots (HTML structure change)
- ✅ Restored 36 missing FAQs
- ✅ Organized project structure (50+ → 35 files)
- ✅ Set up proper Git source control
- ✅ Created comprehensive documentation

### v1.1.0 (Previous)
- Basic plugin functionality
- Custom post type registration
- ACF integration

## 🔄 Development Workflow

### Making Changes
1. **Always backup first:** `cp file.php file.php.backup`
2. **Test locally** before deploying
3. **Document changes** in this README
4. **Update version numbers** if significant changes
5. **Commit to Git** with descriptive messages

### Deployment Process
1. Test all functionality locally
2. Create ZIP with `./tools/build-zip.sh`
3. Deploy to staging environment
4. Verify all features work
5. Deploy to production
6. Monitor for issues

### Emergency Rollback
1. Restore from Git: `git checkout HEAD~1 -- file.php`
2. Or restore from backup: `cp file.php.backup file.php`
3. Clear all caches
4. Test functionality

## 🚀 Development Guidelines

### Where to Put New Files

- **Plugin Code**: All plugin code goes in `plugin/liveocnj-neighborhoods/`
- **Documentation**: All documentation goes in `docs/`
- **Build Tools**: Build and testing tools go in `tools/`

### Git Workflow

This repository uses Git for version control. The main branch is `develop`.

- **Tracked Files**: Only essential files are tracked in Git (plugin code, docs, tools)
- **Ignored Files**: Archive directory, temporary files, and system files are ignored

### Making Changes

1. Create a new branch for your changes
2. Make your changes
3. Test thoroughly
4. Create a pull request to merge into `develop`

### Testing

Before deploying changes:

1. Test locally using Docker:
   ```bash
   docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root plugin activate liveocnj-neighborhoods
   ```

2. Verify functionality:
   - Visit: `http://localhost:8888/ocean-city-neighborhoods/`
   - Check individual neighborhood pages
   - Verify FAQs are working

## 🛠️ Maintenance Tools

### WP-CLI Commands

The plugin includes custom WP-CLI commands for maintenance:

```bash
# Flush rewrite rules
docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root locnj flush-rules

# Check neighborhood permalinks
docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root locnj check-permalinks
```

### Build Tools

```bash
# Create a ZIP file for deployment
./tools/build-zip.sh

# Run smoke tests
./tools/wp-smoke-test.sh
```

## 🔒 Security and Performance

- Keep the plugin lightweight
- Follow WordPress coding standards
- Use proper sanitization and validation
- Minimize database queries
- Cache expensive operations

## 📝 License

This project is proprietary and confidential.
