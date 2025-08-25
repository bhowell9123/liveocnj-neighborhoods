#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
PLUGIN_DIR="$ROOT/plugin/liveocnj-neighborhoods"

echo "Checking JS file presence"
test -f "$PLUGIN_DIR/assets/js/ocnj-neighborhoods.js"

echo "Checking jQuery dependency in Plugin.php"
grep -q "wp_enqueue_script(.*\\['jquery'\\]" "$PLUGIN_DIR/src/Plugin.php" || {
  echo "Missing ['jquery'] dependency"; exit 1;
}

echo "Curl archive page and check key selectors"
curl -fsSL "http://localhost:8888/ocean-city-neighborhoods/" | grep -q "ocnj-card-grid"
curl -fsSL "http://localhost:8888/ocean-city-neighborhoods/" | grep -q "ocnj-stats"

echo "Smoke test OK ✅"