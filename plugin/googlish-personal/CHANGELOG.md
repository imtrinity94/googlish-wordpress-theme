# Googlish Personal changelog

Companion plugin for the Googlish theme. Newest first. Every release bumps the version in `googlish-personal.php` (plugin header and `GOOGLISH_PERSONAL_VERSION`) and `readme.txt`, and adds an entry here.

## [1.0.1] - 2026-11-12

### Fixed
- Photos and badge images given as inline `data:image/...` addresses were dropped by WordPress's URL cleaner. They now show.

## [1.0.0] - 2026-11-12

### Added
- First release. Replaces the Googlish header, footer, home author section and tools section with personal content read from `data/profile.json`.
- Author section with portrait photo (bundled in `assets/`), kicker, name, role, bio, live blog stats, social links and badges.
- Tools cards, header wordmark and navigation, footer links, social icons and copyright.
- X handle on "Share to X" links and the search box placeholder.
- `googlish_personal_data` filter, so a future settings screen can supply the data.
