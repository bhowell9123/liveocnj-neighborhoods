# LiveOCNJ Neighborhoods Plugin

Custom post type for Ocean City NJ neighborhoods with improved image handling.

## Repository Structure

```
.
├── plugin/
│   └── liveocnj-neighborhoods/       # Main plugin code
│       ├── assets/                   # CSS, JS, and images
│       ├── templates/                # Template files
│       ├── src/                      # PHP source files
│       ├── acf-json/                 # ACF field definitions
│       └── liveocnj-neighborhoods.php # Plugin bootstrap
├── mu-plugins/
│   └── ocnj-neighborhood-landing-body-classes.php # MU helper plugin
├── tools/
│   ├── build-zip.sh                  # Build release zip
│   └── wp-smoke-test.sh              # Test script
├── docs/
│   ├── TECHNICAL_OVERVIEW.md         # Technical documentation
│   ├── SAFE-CHANGES.md               # Guidelines for safe modifications
│   └── CHANGELOG.md                  # Version history
└── archive/                          # Historical files (not in git)
```

## Building and Testing Locally

### Build Plugin Zip

```bash
# From the repo root
./tools/build-zip.sh
```

This will create `liveocnj-neighborhoods.zip` in the repo root.

### Run Smoke Tests

```bash
# From the repo root, with WordPress running at http://localhost:8888
./tools/wp-smoke-test.sh
```

## Development Workflow

1. Make changes to files in `plugin/liveocnj-neighborhoods/`
2. Test changes locally
3. Run smoke tests to verify functionality
4. Build zip for deployment

## Important Notes

- Always maintain jQuery dependency in JS enqueue
- Use canonical selectors as documented in SAFE-CHANGES.md
- Keep ACF field definitions in acf-json/ directory