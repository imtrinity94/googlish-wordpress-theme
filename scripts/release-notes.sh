#!/usr/bin/env bash
# Prints the CHANGELOG section for one version: scripts/release-notes.sh <CHANGELOG.md> <version>
set -euo pipefail
awk -v v="$2" '
  $0 ~ "^## \\[" v "\\]" { on=1; next }
  on && /^## \[/ { exit }
  on { print }
' "$1" | sed -e '/./,$!d'
