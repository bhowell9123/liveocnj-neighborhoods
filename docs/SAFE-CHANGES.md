# Safe Changes Guide for LiveOCNJ Neighborhoods

This document outlines the key contract for making safe changes to the LiveOCNJ Neighborhoods plugin.

## Source of Truth

- One source of truth: `plugin/liveocnj-neighborhoods` + `mu-plugins/ocnj-neighborhood-landing-body-classes.php`
- All development should be done in these directories

## Safe Edit Locations

- Edit CSS layout in `assets/css/ocnj-neighborhoods.css`
- Edit behavior in `assets/js/ocnj-neighborhoods.js` (must enqueue with ['jquery'])
- Archive markup in `templates/archive-neighborhood.php`
- Single markup in `templates/single-neighborhood.php`

## Canonical Selectors

- Grid: `.ocnj-card-grid` (not `.ocnj-grid`)
- Stats: `.ocnj-stats > li` with `li + li::before` separators
- Hero: `.ocnj-hero`, `.ocnj-hero-bg`, `.ocnj-hero__inner`

## ACF Fields

- Keep/commit ACF local JSON in `plugin/liveocnj-neighborhoods/acf-json/`
- When adding new fields, export the JSON to this directory

## Testing Changes

- Run `tools/wp-smoke-test.sh` to verify basic functionality
- Check that jQuery dependency is maintained in `wp_enqueue_script`
- Verify archive page loads with correct grid and stats selectors