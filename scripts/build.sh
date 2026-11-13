#!/usr/bin/env bash
# Builds WordPress-ready zips into dist/:
#   googlish-<version>.zip           -> unpacks to googlish/            (Appearance > Themes > Upload)
#   googlish-personal-<version>.zip  -> unpacks to googlish-personal/   (Plugins > Add New > Upload)
# Checks first: versions agree everywhere, PHP and JS have no syntax errors, JSON is valid.
set -euo pipefail
cd "$(dirname "$0")/.."

fail() { echo "::error::$*" >&2; echo "ERROR: $*" >&2; exit 1; }

# ---- Versions --------------------------------------------------------------
THEME_VERSION=$(sed -n 's/^Version: *//p' style.css | tr -d '\r')
FUNC_VERSION=$(sed -n "s/.*define( *'CB_THEME_VERSION', *'\([^']*\)'.*/\1/p" functions.php)
README_VERSION=$(sed -n 's/^Version: *//p' readme.txt | head -1 | tr -d '\r')
[ -n "$THEME_VERSION" ] || fail "No Version in style.css"
[ "$THEME_VERSION" = "$FUNC_VERSION" ] || fail "style.css ($THEME_VERSION) and functions.php ($FUNC_VERSION) versions differ"
[ "$THEME_VERSION" = "$README_VERSION" ] || fail "style.css ($THEME_VERSION) and readme.txt ($README_VERSION) versions differ"
grep -q "^## \[$THEME_VERSION\]" CHANGELOG.md || fail "CHANGELOG.md has no entry for theme $THEME_VERSION"

PLUGIN_DIR=plugin/googlish-personal
PLUGIN_VERSION=$(sed -n 's/^ \* Version: *//p' "$PLUGIN_DIR/googlish-personal.php" | tr -d '\r')
PLUGIN_CONST=$(sed -n "s/.*define( *'GOOGLISH_PERSONAL_VERSION', *'\([^']*\)'.*/\1/p" "$PLUGIN_DIR/googlish-personal.php")
PLUGIN_README=$(sed -n 's/^Version: *//p' "$PLUGIN_DIR/readme.txt" | head -1 | tr -d '\r')
[ "$PLUGIN_VERSION" = "$PLUGIN_CONST" ] || fail "Plugin header ($PLUGIN_VERSION) and GOOGLISH_PERSONAL_VERSION ($PLUGIN_CONST) differ"
[ "$PLUGIN_VERSION" = "$PLUGIN_README" ] || fail "Plugin header ($PLUGIN_VERSION) and plugin readme.txt ($PLUGIN_README) differ"
grep -q "^## \[$PLUGIN_VERSION\]" "$PLUGIN_DIR/CHANGELOG.md" || fail "Plugin CHANGELOG.md has no entry for $PLUGIN_VERSION"

echo "Theme  : $THEME_VERSION"
echo "Plugin : $PLUGIN_VERSION"

# ---- Code checks -----------------------------------------------------------
while IFS= read -r -d '' f; do php -l "$f" >/dev/null || fail "PHP syntax error in $f"; done < <(find . -name '*.php' -not -path './dist/*' -not -path './.git/*' -print0)
while IFS= read -r -d '' f; do node --check "$f" || fail "JS syntax error in $f"; done < <(find assets/js -name '*.js' -not -name '*.min.js' -print0)
for f in theme.json "$PLUGIN_DIR/data/profile.json" "$PLUGIN_DIR/inc/icons.json" styles/*.json; do
  [ -f "$f" ] || continue
  python3 -c "import json,sys; json.load(open(sys.argv[1]))" "$f" || fail "Invalid JSON in $f"
done
echo "Checks passed"

# ---- Build -----------------------------------------------------------------
rm -rf dist && mkdir -p dist/stage
STAGE=dist/stage

# Theme: everything except repo-only folders
mkdir -p "$STAGE/googlish"
tar --exclude=./.git --exclude=./.github --exclude=./dist --exclude=./docs --exclude=./plugin --exclude=./scripts \
    --exclude=./.gitignore --exclude='*.DS_Store' -cf - . | tar -xf - -C "$STAGE/googlish"
(cd "$STAGE" && zip -qr "../googlish-$THEME_VERSION.zip" googlish)

# Plugin
cp -r "$PLUGIN_DIR" "$STAGE/googlish-personal"
(cd "$STAGE" && zip -qr "../googlish-personal-$PLUGIN_VERSION.zip" googlish-personal)

rm -rf "$STAGE"

# ---- Verify the zips unpack to the right folder names ----------------------
check_zip() {
  local zip=$1 folder=$2 must=$3
  local tops; tops=$(unzip -Z1 "$zip" | cut -d/ -f1 | sort -u)
  [ "$tops" = "$folder" ] || fail "$zip must contain only the folder $folder/ (found: $tops)"
  unzip -Z1 "$zip" | grep -qx "$folder/$must" || fail "$zip is missing $folder/$must"
}
check_zip "dist/googlish-$THEME_VERSION.zip" googlish style.css
check_zip "dist/googlish-personal-$PLUGIN_VERSION.zip" googlish-personal googlish-personal.php

ls -la dist
if [ -n "${GITHUB_OUTPUT:-}" ]; then
  { echo "theme_version=$THEME_VERSION"; echo "plugin_version=$PLUGIN_VERSION"; } >> "$GITHUB_OUTPUT"
fi
