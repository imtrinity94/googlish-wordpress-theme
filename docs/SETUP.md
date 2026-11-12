# Set up Googlish: a Google-style tech blog in about 30 minutes

Googlish comes in two pieces:

| Piece | What it is | Who changes it |
| --- | --- | --- |
| **Googlish theme** (`googlish`) | Layout, design, every feature. The same for everyone. | Nobody. You only update it. |
| **Googlish Personal plugin** (`googlish-personal`) | Your name, photo, bio, links, badges, projects, header and footer, in one data file. | You, once, then whenever your details change. |

Everything else comes from what you already write in WordPress: posts, excerpts, featured images, categories, tags, comments and stats.

Keeping your details in the plugin means theme updates never touch them. Every theme update is a plain **Replace current with uploaded**.

Live example: [cloudblogger.co.in](https://cloudblogger.co.in/) runs exactly this setup.

---

## 1. What you need

- WordPress 6.5 or newer, PHP 7.4 or newer.
- A host that lets you upload themes and plugins. On WordPress.com that's the Business plan or higher.
- Recommended: **Jetpack**, built in on WordPress.com. It provides Stats (views and "Most read"), Likes, Subscriptions and Comments. Without it, the theme still works and simply hides those numbers.

## 2. Get the two zips

**Theme:**
1. Download this repository (**Code → Download ZIP**) and unzip it.
2. Rename the unzipped folder to `googlish`.
3. Delete `plugin/` and `docs/` from it (optional; it works either way).
4. Zip the folder again.

The folder name matters. WordPress recognises a theme by its folder, so always use `googlish` and updates will replace it cleanly.

**Plugin:** zip the folder `plugin/googlish-personal` on its own, so the zip contains a `googlish-personal` folder at the top.

## 3. Install

1. **Plugins → Add New Plugin → Upload Plugin**, choose the plugin zip, then **Activate**.
2. **Appearance → Themes → Add New Theme → Upload Theme**, choose the theme zip.
3. Click **Live Preview** first, look around, then **Activate**.

Order doesn't matter, but installing the plugin first means your details show from the first look.

## 4. Make it yours: edit `data/profile.json`

All your content lives in `googlish-personal/data/profile.json`. Edit it before zipping the plugin, or edit it on your computer and upload the plugin again (**Replace current with uploaded**).

### `site`

| Field | Example | Shows up |
| --- | --- | --- |
| `wordmark` | `"YourSite"` | Top left of every page, in dark text |
| `wordmark_suffix` | `".com"` | Right after it, in grey. Use `""` for none. |
| `search_placeholder` | `"Search YourSite"` | Inside search boxes. Leave `""` and the theme uses "Search *your site title*". |
| `x_handle` | `"yourhandle"` | Adds "via @yourhandle" when readers share a post to X. Leave `""` for none. |

### `nav`: links in the top bar

```json
"nav": [
  { "label": "Latest", "url": "/" },
  { "label": "Articles", "url": "/category/article/" },
  { "label": "My other site", "url": "https://example.com/", "newTab": true }
]
```

Keep it to about five short links. Under 1100px wide they collapse into a menu icon automatically.

### `author`: the "Meet the author" section at the top of the home page

| Field | What to put |
| --- | --- |
| `kicker` | The small line above your name, e.g. "Hi, I'm" |
| `name` | Your name |
| `role` | One line under your name, e.g. "Cloud architect and writer" (shown in the gradient colour) |
| `bio` | Two or three sentences |
| `photo` | `"assets/author-portrait.jpg"`. Put your photo in the plugin's `assets/` folder, or use a full URL from your Media Library. Portrait shape (4:5) works best, at least 800 × 1000. |
| `photo_small` | A 400 × 500 copy for phones (optional, makes the page lighter) |
| `photo_alt` | Your name, for screen readers |
| `show_site_stats` | `true` shows live blog stats (posts, views, comments, years writing) |
| `socials` | Buttons under the bio. Each has `key`, `label` and `url`. Keys with icons: `linkedin`, `github`, `youtube`, `x`, `gitlab`, `spotify`, `buy-me-a-coffee`. Any other key gets a simple round icon. |
| `badges_label` | Heading above the badges, e.g. "Certifications and honors" |
| `badges` | Each has `name`, `issuer`, `url` (where to verify it) and `image` (a square image, 88 × 88 or larger). Use `[]` to hide the whole row. |

### `tools`: the coloured cards on the home page

| Field | What to put |
| --- | --- |
| `title`, `lede` | Section heading and the line under it, e.g. "Things I've built" |
| `button_label`, `button_url` | The outline button on the right, e.g. "All projects on GitHub". Leave `button_url` empty to hide it. |
| `items` | Each card: `name`, `url`, `description` and `color` (`blue`, `violet`, `teal`, `orange`, `pink` or `green`) |

Not a builder? Use the cards for your best guides instead.

### `footer`

| Field | What to put |
| --- | --- |
| `links` | Small text links next to your site title (`label`, `url`) |
| `socials` | Icon-only links. Each needs `url` and `service` (`linkedin`, `x`, `youtube`, `github`, `spotify`, `mastodon`, `instagram`… any WordPress social icon). For a site without its own icon, use `"service": "chain"` and add a `label`. |
| `copyright` | The small line at the bottom |

## 5. What comes from WordPress on its own

You don't configure any of this. The theme reads it from your site.

| On the page | Comes from |
| --- | --- |
| Site name in the footer and in "More from …" | **Settings → General → Site Title** |
| Featured story, the three cards under it, "Just in" list | Your published posts, newest first |
| Banners on cards and posts | Each post's **Featured image**. 1200 × 630 shape; upload at 2400 × 1260 for sharp screens. A post without one gets a soft gradient. |
| Summary under titles and on cards | Each post's **Excerpt**. Write 30–40 words. |
| Small label on cards, breadcrumb | The post's first **Category** |
| Topic chips, "Explore topics" cloud, tags under posts | **Tags** and **Categories** |
| "Must read" carousel | **Sticky posts** first (marked "Pick"), then the most-viewed posts of the last 90 days from Jetpack Stats |
| View counts on posts and in blog stats | Jetpack Stats |
| Like count | The theme's Like button, plus any likes from Jetpack Likes |
| Read time | Worked out from the post's word count |
| Author name, picture and one-line role in the post byline | The post author's **profile** (Users → Profile: display name and Biographical Info) and Gravatar |
| Comments, "Recent conversations" | WordPress comments. Readers without a picture get a friendly generated mascot. |
| Blog stats (posts, views, comments, years) | Live counts from your site; "years" counts from your first post |
| "From the archive" and "More from …" | Random posts, a fresh set on every page view |
| Table of contents | The post's **Heading 2** and **Heading 3** blocks |
| Light and dark mode | The reader's device setting, plus the moon/sun button |

## 6. WordPress settings to check once

- **Settings → Reading → Your homepage displays: Your latest posts.**
- **Settings → Permalinks:** any option except *Plain*.
- **Settings → General → Site Title:** your blog's name (used in labels).
- **Settings → Discussion:** tick "Email me whenever a comment is held for moderation" to get an email for each "Send feedback" note.
- **Jetpack → Settings:** turn on Stats, Likes, Subscriptions and Comments. You can turn off Jetpack's own sharing buttons; the theme has its own.

## 7. Updating

- **Theme update:** download the new release, make sure the folder is named `googlish`, then upload with **Replace current with uploaded**. Your details are untouched because they live in the plugin.
- **Changing your details:** edit `profile.json`, zip the plugin, upload with **Replace current with uploaded**.
- If a page doesn't change after a theme update, you've customised that template in **Appearance → Editor**. Open it and choose **Reset**.

## 8. Writing posts

See the [full guide](GUIDE.md) for code blocks, callouts, tabs, patterns, images and videos.

## 9. Checklist

- [ ] Plugin installed and activated
- [ ] Theme installed as `googlish` and activated
- [ ] `profile.json`: wordmark, nav, name, role, bio, photo, socials
- [ ] `profile.json`: badges and tools, or set them to `[]`
- [ ] `profile.json`: footer links, socials and copyright
- [ ] Site title set; homepage shows latest posts; pretty permalinks
- [ ] Every post has a featured image and a 30–40 word excerpt
- [ ] A few best posts marked sticky for "Must read"
- [ ] Jetpack Stats, Likes, Subscriptions and Comments on
