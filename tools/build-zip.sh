#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
PLUGIN_DIR="$ROOT/plugin/liveocnj-neighborhoods"
OUT="$ROOT/liveocnj-neighborhoods.zip"

rm -f "$OUT"
(cd "$PLUGIN_DIR/.." && zip -r "$OUT" "liveocnj-neighborhoods" \
  -x "*.DS_Store" -x "*.git*" -x "*node_modules/*" -x "*vendor/*")
echo "Built: $OUT"