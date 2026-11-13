# Changelog

All notable changes to this theme. Versions follow [Semantic Versioning](https://semver.org/).
The version lives in three places: `style.css` (Version), `functions.php` (`CB_THEME_VERSION`) and `readme.txt`.

## [Tooling] - 2026-11-13

### Added
- GitHub Actions pipeline (`.github/workflows/release.yml`). It checks every push and pull request (PHP and JS syntax, JSON, matching versions, CHANGELOG entry), builds WordPress-ready zips, and publishes a GitHub Release for each new theme or plugin version, with the CHANGELOG section as release notes.
- `scripts/build.sh` to build the same zips locally, and `scripts/release-notes.sh`.

## [2.9.3] - 2026-11-12

### Fixed
- New posts showed no view count for hours. The theme saved "no views yet" from the first moments after publishing for up to an hour, and zero for six hours, and zero counts are hidden. Now no answer or zero is rechecked every 15 minutes, posts from the last week every 30 minutes, and older posts every 6 hours. Counts cached by earlier versions are ignored, so the fix shows straight after updating.

## [Docs] - 2026-11-12

### Added
- `docs/SETUP.md`: step-by-step setup with the theme plus the Googlish Personal plugin, every field in `profile.json`, and what the theme takes from WordPress on its own.
- `plugin/googlish-personal`: the companion plugin (1.0.1) with sample placeholder data, ready to fill in.
- README quick start for theme + plugin.

## [2.9.2] - 2026-11-12

Googlish now renders cloudblogger.co.in exactly, with all personal content in the separate Googlish Personal plugin. Version numbers jump to 2.9.2 so Googlish and the old CloudBlogger releases share one line from here on.

### Added
- Header and footer are now patterns (`cloudblogger/header`, `cloudblogger/footer`) that the template parts load, so a plugin can replace them without editing the theme.
- Portrait card styling for the home author section (`.cb-portrait`).
- Labels use the site's own name: "More from <site name>" after posts and "Search <site name>" in search boxes. Filters: `googlish_related_title`, `googlish_search_placeholder`.

### Changed
- Release zips unpack to a folder named `googlish`, so updates replace the installed theme.

## [2.8.2] - 2026-11-12

### Changed
- Theme author set to Mayank Goyal (style.css, readme.txt, README credits).

## [2.8.1] - 2026-11-12

### Fixed
- Home page, wide screens: when the newest post has no featured image, the featured story collapsed to a thin strip showing only its category label. A soft gradient placeholder now takes the banner's place, so the card shows in full. Post cards without an image get the same placeholder.

## [2.8.0] - 2026-11-12

### Added
- "Load more" for post lists: the home page "Just in" list and category, tag, archive and search pages add the next set of posts in place, without reloading the page (like the "Load more" button on Google's blogs). The page links stay in the HTML for search engines and fall back automatically if loading fails.

## [2.7.14] - 2026-11-07

### Fixed
- Desktop too: images resized in the editor to a width larger than the text column were squashed. They now keep their proportions on every screen size.

## [2.7.13] - 2026-11-07

### Fixed
- Phones and tablets: long unbroken words in posts (package names, file names, URLs) ran off the screen. They now wrap.
- Phones and tablets: images resized in the editor were squashed, because their saved height stayed fixed while the width shrank. They now keep their proportions.

## [2.7.12] - 2026-11-01

### Fixed
- On posts with a Jetpack subscribe box saved inside the post content, the whole article was moved below the Like button and feedback, so "Enjoyed this post?" appeared before the post started. The post text now always comes first.
- Those posts also showed two subscribe boxes. The one inside the post is hidden when Jetpack adds its own after the post.

## [2.7.11] - 2026-11-01

### Fixed
- On posts where Jetpack places the subscribe box inside the post content, the whole article was styled as the subscribe card (gradient background and padding). Only the subscribe box is styled now.

### Changed
- Tables in posts are more compact and wrap long words, so wide comparison tables fit the column.

## [2.7.10] - 2026-11-01

### Fixed
- Between about 900px and 1200px wide, the featured story's title could shrink to nothing. The text card now sizes to its content below 1200px, and the title never shrinks.

### Changed
- Public release named Googlish, with placeholder author details, links and sample tools.
- The X share link only adds a "via" handle when one is set with the `cb_share_x_via` filter.

## [2.7.9] - 2026-11-01

### Fixed
- Jetpack's "Discover more" subscribe box showed with square, unstyled fields when the theme script hadn't tagged it yet. The card style (soft gradient card, pill email field and Subscribe button) now comes from CSS alone.

## [2.7.8] - 2026-11-01

### Changed
- "Leave a Reply" restyled to match the theme: rounded card, soft focus ring, borderless text area, divider above the actions, pill-shaped Comment button. Works with Jetpack's inline comment form and the core form.
- Comment list: comments as rounded cards, replies indented with a guide line, pill-shaped Reply links.

## [2.7.7] - 2026-11-01

### Fixed
- Wide screens showed the menu icon and the links at the same time, making the top bar tall. The menu icon now appears only under 1100px.
- The "In this article" table of contents no longer picks up Jetpack's "Like this:" heading.

## [2.7.6] - 2026-11-01

### Changed
- "More from the blog" at the end of a post is now random on every page view. The page carries a random pool of 18 posts from the whole archive and the browser shows a fresh 3, so it stays random even with WordPress.com page caching. The post being read is never included.
- "From the archive" on the home page uses the same random pool approach (12 posts, 3 shown).
- `cloudblogger/random-posts` block gained `pool` and `anyAge` attributes.

### Fixed
- The three cards under the featured story cut titles off at two lines. They now show the full title.

## [2.7.5] - 2026-11-01

### Changed
- Phones and tablets (under 1100px) get a menu icon instead of the links wrapping onto a second line. The icon matches the search and theme buttons and sits beside them.
- Full-screen menu: large left-aligned links with dividers.

## [2.7.4] - 2026-11-01

### Fixed
- On phones the featured story's text card collapsed under the banner, leaving only a sliver of the category tag.

## [2.7.3] - 2026-11-01

### Added
- The next three posts appear as cards under the featured story on the home page.

### Changed
- "Just in" now starts from the fifth newest post so nothing repeats.

## [2.7.2] - 2026-11-01

### Fixed
- Vimeo embeds saved without an aspect ratio stayed 500px wide. All video embeds now fill the column at 16:9.
- VideoPress, Vimeo and uploaded videos get the same rounded corners as everything else.

## [2.7.1] - 2026-11-01

### Fixed
- White gap between the top bar and the page.

### Changed
- Reading progress bar is thicker (5px) with a rounded end and soft glow.

## [2.7.0] - 2026-11-01

### Added
- Theme's own Like button (`cloudblogger/like`) at the end of each post, replacing Jetpack's unstylable Like iframe. Its count includes existing WordPress.com likes. REST route `cloudblogger/v1/like`.

### Changed
- "Was this helpful?" and Send feedback moved to the right panel on wide screens. Phones and tablets still show them under the post.
- Tighter spacing in the right panel cards.

## [2.1.0 – 2.6.0]

Iterations on the 2.0 redesign, driven by the live site:

- Text wordmark ("CloudBlogger" with ".co.in" in grey) instead of an image logo; authoritative header with larger nav.
- Author block moved to the top of the home page; certifications shown as readable cards.
- Must read: sticky posts first (marked "Pick"), then the most-viewed posts of the last 90 days from Jetpack Stats.
- Sticky posts no longer repeat on every page of a query loop.
- Community section: recent conversations with generated mascot avatars for commenters, beside an Explore topics tag cloud.
- Image viewer: every post image opens enlarged with previous/next, keyboard and swipe, unless it links elsewhere.
- Code: Code Block Pro blocks converted to the theme's code style, per-language colours, stricter language detection, Link and Output labels.
- Google-style shimmer on hover for cards and rows.
- Banners shown at their true 1200×630 ratio everywhere, never cropped.
- Clean post ending: duplicated Jetpack share, like and subscribe pieces hidden or restyled.
- Posts without headings are centred instead of shifting right.
- Author block removed from the end of posts.

## [2.0.0]

Redesign in the style of Google's blogs and Google Cloud docs:

- Bundled Google Sans Flex and Google Sans Code fonts.
- Home page: Meet the author (live stats, social links, certifications), featured story, Must read carousel, Tools I've built, Just in with topic chips, community and archive picks.
- Post page: hero wash with breadcrumb, standfirst, post stats (read time, views, comments, likes), share menu, workflow-style sticky table of contents with reading progress, right panel with post stats.
- Docs-style callouts: Note, Pro tip, Important, Caution, Warning.
- Code blocks with language label, file name, copy button, line numbers and "Show all" for long scripts (highlight.js bundled).
- Code tabs and demo patterns (steps, before and after, video and screenshot frames, download from GitHub, tested on).
- "Was this helpful?" feedback with votes per post and private written feedback.
- Light and dark modes with a toggle.

## [1.0.0]

- First version of the block theme for cloudblogger.co.in.

