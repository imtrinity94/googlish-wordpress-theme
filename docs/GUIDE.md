# Googlish theme guide: set it up, then make it yours

This guide takes you from a fresh install to a blog that carries your own name, links, colours and writing style. It assumes no coding. The last sections cover file-level changes for people who want them.

Examples use placeholders such as **Your Name**, **your-site.com** and **@yourhandle**. Replace them with your own details.

---

## Contents

1. [What you need](#1-what-you-need)
2. [Install the theme](#2-install-the-theme)
3. [Set up WordPress for the theme](#3-set-up-wordpress-for-the-theme)
4. [Find your way around the Site Editor](#4-find-your-way-around-the-site-editor)
5. [Make the header and footer yours](#5-make-the-header-and-footer-yours)
6. [Make the home page yours](#6-make-the-home-page-yours)
7. [Colours, fonts and dark mode](#7-colours-fonts-and-dark-mode)
8. [Writing posts](#8-writing-posts)
9. [Stats, likes, feedback, comments and subscribers](#9-stats-likes-feedback-comments-and-subscribers)
10. [Custom blocks reference](#10-custom-blocks-reference)
11. [Changing theme files](#11-changing-theme-files)
12. [Updating the theme](#12-updating-the-theme)
13. [Troubleshooting](#13-troubleshooting)
14. [Removing the theme](#14-removing-the-theme)

---

## 1. What you need

| Need | Details |
| --- | --- |
| WordPress | 6.5 or newer (block themes and the Site Editor) |
| PHP | 7.4 or newer |
| Hosting | Any WordPress host. On WordPress.com you need a plan that allows uploading themes (Business or higher). |
| Jetpack | Optional but recommended: Stats (views, "Most read"), Likes (existing like counts), Subscriptions and Comments. Built in on WordPress.com. |

Without Jetpack the theme still works. View counts and "Most read" fall back to the most-commented posts, and the like count only includes likes from the theme's own button.

---

## 2. Install the theme

### Get the zip

On the repository page choose **Code → Download ZIP**. You don't need to unzip it.

### Try it before switching (recommended)

1. Go to **Appearance → Themes → Add New Theme → Upload Theme**.
2. Choose the zip and select **Install Now**.
3. Select **Live Preview** instead of Activate. You can browse your site with the new theme while visitors keep seeing the old one.
4. When you're happy, select **Activate**.

Your old theme stays installed, so you can switch back at any time from **Appearance → Themes**.

### Updating an installed copy

Upload the new zip the same way. WordPress asks whether to replace the current version; choose **Replace current with uploaded**. See [Updating the theme](#12-updating-the-theme) for what to check afterwards.

---

## 3. Set up WordPress for the theme

Work through this list once after activating.

### Reading

**Settings → Reading → Your homepage displays: Your latest posts.** The home page template is the blog home, so it needs this setting.

### Permalinks

**Settings → Permalinks:** any "pretty" structure works (for example *Post name* or *Day and name*). Avoid *Plain*, because the theme's like and feedback features use the REST API, which needs pretty links on some hosts.

### Featured images (banners)

- The theme shows every banner at **1200 × 630** proportions (the standard social-card shape) and never crops it.
- Upload banners at **2400 × 1260** for sharp results on high-resolution screens.
- Posts without a featured image get a soft gradient instead.

### Excerpts

The excerpt appears under the post title as a short summary (the "standfirst"), on cards and in the featured story. Write it in the post sidebar under **Excerpt**. One or two sentences works best.

### Categories and tags

- The first category appears as the small label on cards and in the breadcrumb.
- Tags appear at the end of each post and in the "Explore topics" cloud on the home page.

### Sticky posts

Mark your best posts as sticky (**Post sidebar → Status → Stick to the top of the blog**). They lead the **Must read** carousel on the home page and are marked "Pick". Sticky posts are *not* repeated at the top of every page of the post list.

### Jetpack (if you use it)

Turn on these modules in **Jetpack → Settings**:

| Module | Used for |
| --- | --- |
| Stats | View counts on posts and the home page, "Most read" |
| Likes | Existing like counts, added to the theme's own Like button |
| Subscriptions | The "Subscribe" card at the end of posts |
| Comments | The comment box (the theme styles it) |

Jetpack's own **sharing buttons** and **Like button** at the end of posts are hidden by the theme, which has its own Share menu and Like button. You can switch them off in **Jetpack → Settings → Sharing** to keep things tidy.

### Discussion

In **Settings → Discussion**, tick **"Email me whenever a comment is held for moderation"** if you want an email whenever a reader sends written feedback through "Send feedback".

---

## 4. Find your way around the Site Editor

Almost everything you'll want to change is in **Appearance → Editor** (the Site Editor).

| Where | What's there |
| --- | --- |
| **Navigation** | The links in the top bar |
| **Styles** | Colours, fonts, sizes for the whole site |
| **Pages / Templates** | The layout of each kind of page |
| **Patterns → Template parts** | **Header** and **Footer** |
| **Patterns** | Reusable sections, including the home page sections |

Templates included with the theme:

| Template | Used for |
| --- | --- |
| Blog Home | The home page |
| Single Posts | Every post, with the table of contents |
| Post without table of contents | Optional per-post template for short posts |
| Pages | Static pages |
| Archives | Category, tag, author and date lists |
| Search Results | Search |
| Index | Fallback |
| 404 | Page not found |

> **Important:** when you save a template or template part in the Site Editor, WordPress keeps *your* copy and stops using the theme's copy. That's how your changes survive theme updates, but it also means theme updates won't change that template. See [Updating the theme](#12-updating-the-theme).

---

## 5. Make the header and footer yours

### Site name in the top bar

The top bar shows a text wordmark: your site name, with the last part (for example the domain ending) in grey.

1. **Appearance → Editor → Patterns → Template parts → Header.**
2. Select the wordmark at the top left. It's a **Custom HTML** block.
3. Edit the text. It looks like this:

```html
<a class="cb-wordmark" href="/" rel="home" aria-label="YourSite.com, home">YourSite<span>.com</span></a>
```

The part inside `<span>` is shown in grey. Leave it out for a single-colour name.

Prefer an image logo? Delete the Custom HTML block and insert a **Site Logo** block in its place.

### Top bar links

1. In the Header, select the **Navigation** block.
2. Add, remove, rename or drag links. Links to categories work well (for example *Articles*, *Quick posts*).

Keep it to about five short links. On screens narrower than 1100px the links collapse into a menu icon automatically.

### Search placeholder

Select the search icon in the Header and change **Placeholder** in the block settings (for example "Search").

### Footer

**Patterns → Template parts → Footer.** Edit the site title, the short line under it, the link groups and the **Social Icons** block (select it, then use the **+** to add a service and paste your profile link). The copyright line is a normal paragraph.

---

## 5b. Keep your details outside the theme (optional)

The header, footer, author section and tools section are patterns. A companion plugin can replace them with your own content, so you never edit the theme and every update is a plain "Replace current with uploaded".

The plugin reads everything from one file, `data/profile.json`:

| Key | What it holds |
| --- | --- |
| `site` | Wordmark text and grey ending, search placeholder, X handle for shares |
| `nav` | Top bar links (`label`, `url`, optional `newTab`) |
| `author` | Kicker, name, role, bio, photo (plugin file or full URL), social links, badges |
| `tools` | Section title and line, button, and cards (`name`, `url`, `description`, `color`) |
| `footer` | Links, social icons, copyright line |

The plugin swaps these theme patterns: `cloudblogger/header`, `cloudblogger/footer`, `cloudblogger/home-author`, `cloudblogger/home-tools`.

## 6. Make the home page yours

The home page is made of seven sections, each a pattern you can edit, reorder or remove.

Open **Appearance → Editor → Templates → Blog Home**. Use **List View** (the icon with three lines, top left) to see the sections and drag them into another order. To remove a section, select it and choose **Delete** from its menu.

### 6.1 Meet the author

The author spotlight: photo, greeting, name, role, a short bio, live blog stats, social links and certifications.

| Part | How to change it |
| --- | --- |
| Photo | Custom HTML block. Replace the `src` with your image's URL (Media Library → select image → Copy URL). A square image of at least 320 × 320 looks best. Update `alt` to your name. |
| Greeting, name, role, bio | Normal heading and paragraphs. Click and type. |
| Blog stats | The **Blog stats** block counts posts, views, comments and years on its own. Nothing to edit. |
| Social links | Custom HTML block with a list of links. Each item looks like the example below. Change the link and label; delete items you don't need. |
| Certifications and honors | Custom HTML block with one card per badge (example below). Upload badge images to the Media Library first and use their URLs. Delete the whole block if you have none. |

Social link item:

```html
<li><a href="https://www.linkedin.com/in/yourhandle/" rel="me noopener" target="_blank"><svg …></svg>LinkedIn</a></li>
```

Keep the `<svg …>` that's already there for that service; just change the `href` and the label.

Certification card:

```html
<a class="cb-badge" href="https://link-to-verify-the-badge" target="_blank" rel="noopener">
  <img src="https://your-site.com/wp-content/uploads/badge.png" alt="" loading="lazy" width="88" height="88">
  <span class="cb-badge__name">Certification name</span>
  <span class="cb-badge__by">Issuer</span>
</a>
```

### 6.2 Featured story

Your newest post as a large card, with the next three posts as cards below it. It updates by itself when you publish. To feature a different set, select the query loop and change its settings (category, order, offset).

### 6.3 Must read

A swipeable row: sticky posts first, then the most-viewed posts.

| Setting | Default | Meaning |
| --- | --- | --- |
| `count` | 10 | Cards in total |
| `days` | 90 | How far back "most viewed" looks |

To change them, open the editor's **Options (⋮) → Code editor**, find `<!-- wp:cloudblogger/must-read {"count":10,"days":90} /-->` and edit the numbers. Switch back with **Exit code editor**.

The heading and the one-line description above it are normal text.

### 6.4 Tools I've built

Coloured cards for your own projects. Each card is a group with a name (linked) and a one-line description.

- **Edit:** click the text; select the name and use the link button to change where it goes.
- **Add:** select a card, choose **Duplicate**, then edit the copy.
- **Colour:** each card has a class under **Block settings → Advanced → Additional CSS class(es)**: `cb-tool cb-tool--blue`. Swap `blue` for `violet`, `teal`, `orange`, `pink` or `green`.
- **Button** at the top right ("All projects…"): change the text and link, or delete it.

Not a builder? Rename the section (for example "Start here") and use the cards for your most useful guides, or delete the section.

### 6.5 Just in

The latest posts as a list, with topic chips above it. It starts from the fifth newest post, because the four newest are already shown in the featured section.

- **Chips:** a Custom HTML block with links to categories. Edit the labels and links to match your categories.
- **Posts per page:** select the query loop and change **Items per page**.

### 6.6 From the community

Two panels side by side: **Recent conversations** (the latest comments, each commenter with a friendly generated mascot) and **Explore topics** (a tag cloud). To show more or fewer comments, edit `{"count":4}` on the `cloudblogger/recent-comments` line in the **Code editor** (see Must read above).

### 6.7 From the archive

Three random older posts (published more than three months ago). A different three on every page view.

---

## 7. Colours, fonts and dark mode

### Quick changes in the Site Editor

**Appearance → Editor → Styles** lets you change colours, fonts and sizes without code.

The theme's palette uses these names:

| Colour | Used for |
| --- | --- |
| Canvas | Page background |
| Surface, Surface 2 | Cards and panels |
| Ink, Ink 2, Muted | Text, from strongest to softest |
| Line | Borders and dividers |
| Accent, Accent soft | Links, buttons, highlights |
| Note, Tip, Important, Critical | Callout colours |

The palette values follow light or dark mode automatically, so pick from the palette rather than typing fixed colours where you can.

### Full control: the colour tokens

Every colour comes from a small set of variables at the top of `assets/css/theme.css`. Light values are in `:root`; dark values are repeated further down for dark mode. To rebrand, change these two blocks and nothing else:

```css
:root {
  --cb-accent: #1a6ae8;        /* links and buttons */
  --cb-accent-ink: #174ea6;    /* accent text on light backgrounds */
  --cb-accent-soft: #e8f0fe;   /* light accent backgrounds */
  --cb-gradient: linear-gradient(100deg, #2f7bf6 0%, #7c5cfa 52%, #e2659f 100%);
  /* … */
}
```

Search the file for `--cb-accent:` to find both the light and the dark values.

Callout colours use `--cb-note-*`, `--cb-tip-*`, `--cb-important-*`, `--cb-caution-*` and `--cb-critical-*`. Code colours use `--cb-code-*` and `--cb-syn-*`.

### Fonts

The theme bundles **Google Sans Flex** (text) and **Google Sans Code** (code), so no font is loaded from outside your site. Change them in **Styles → Typography**, or edit `fontFamilies` in `theme.json`.

### Dark mode

- The site follows the reader's system setting.
- The moon/sun button in the top bar overrides it, and the browser remembers the choice.
- Nothing to set up.

---

## 8. Writing posts

### Headings and the table of contents

- Use **Heading 2** for sections and **Heading 3** for sub-sections. They become the "In this article" table of contents.
- The table of contents appears when a post has at least two sections. Otherwise the article is centred.
- For short posts you can also pick **Post sidebar → Template → Post without table of contents**.

### Code

Use the normal **Code** block. Every code block gets a header with the language, a **Copy** button, line numbers (4 lines or more) and **Show all** for long scripts. The language is detected automatically. To set it yourself, or to show a file name, add classes under **Block settings → Advanced → Additional CSS class(es)**:

| Class | Effect |
| --- | --- |
| `lang-javascript` | Language label and colours (also `lang-python`, `lang-powershell`, `lang-bash`, `lang-json`, `lang-yaml`, `lang-xml`, `lang-sql`, `lang-typescript` …) |
| `file-deploy.js` | Shows "deploy.js" in the header |
| `lang-output` | Labels the block as command output or a log |
| `no-lines` | Hides line numbers |

Example: `lang-javascript file-deploy.js`

Short forms work too: `js`, `ts`, `py`, `sh`, `ps1`, `yml`.

If you use the **Code Block Pro** plugin, its blocks are shown in the theme's style. Add the class `cb-keep-cbp` to a block to keep Code Block Pro's own look.

### Code tabs

To show the same task several ways (for example *UI / CLI / API*), insert the pattern **Tabs: same task, different tools**. Each tab is a group with the class `cb-tab`. Its first paragraph (class `cb-tab__label`) is the tab name. Duplicate a tab to add another.

### Callouts

Select any **Paragraph** or **Group**, open **Styles** in the block settings, and pick **Note**, **Pro tip**, **Important**, **Caution** or **Warning**. Or insert a ready-made one from **Patterns → Googlish: callouts**.

| Style | Use it for |
| --- | --- |
| Note | Background the reader should know |
| Pro tip | A faster or better way |
| Important | Something that affects the result |
| Caution | Something that can go wrong |
| Warning | Data loss, outages, security |

Older posts that used the classes `note`, `tip`, `important`, `caution`, `warning` or `critical` on a paragraph are styled as callouts automatically.

### Ready-made patterns

Open the inserter (**+**) → **Patterns**:

| Category | Patterns |
| --- | --- |
| Googlish: callouts | Note, Pro tip, Important, Caution, Warning |
| Googlish: docs | Tabs, Before you begin, What's next |
| Googlish: home page | Every home page section, to rebuild or reuse them |
| Googlish: demos | Tested on, Numbered steps, Before and after, Video in a window frame, Screenshot in a window frame, Code with language and file name, Download from GitHub, Standard post outline |

### Images

- Every image in a post opens in a full-screen viewer with previous/next arrows, keyboard arrows and swipe.
- To make an image open a web page instead, give it a link (**Link** button in the image toolbar). Linked images skip the viewer.
- Captions show in the viewer.

### Videos

YouTube, Vimeo, VideoPress and uploaded videos fill the column width at 16:9 with rounded corners. Just paste the video link into the post.

---

## 9. Stats, likes, feedback, comments and subscribers

### Post stats

Each post shows date, last updated date, read time and views at the top, and read time, views, comments and likes in the right panel. Views come from Jetpack Stats and are cached for a few hours.

### Like button

At the end of every post. One like per reader per post (remembered in the browser, and limited to once a day per network address). The number shown includes likes the post already had through Jetpack.

### Share menu

**Share** opens a menu with Copy link, LinkedIn, X, WhatsApp, Telegram, Reddit and Email. On phones it uses the phone's own share sheet when available.

### "Was this helpful?"

Readers vote **Yes** or **No** and can send a written note. It sits in the right panel on wide screens and under the post on phones and tablets.

| What | Where to find it |
| --- | --- |
| Yes/No counts | The **Helpful** column in the classic Posts list: `your-site.com/wp-admin/edit.php` |
| Written notes | Held comments, never published: `your-site.com/wp-admin/edit-comments.php?comment_status=moderated` (shown as from "Reader feedback") |

Don't **Approve** a feedback note, because that would publish it under the post. Use **Trash** once you've read it.

### Comments

The comment box and comment list are styled to match the theme. Readers without a profile picture get a friendly generated mascot. People who can write posts on your site keep their real profile picture.

### Subscribe

If Jetpack Subscriptions is on, the "Subscribe" card at the end of posts is styled to match the theme. Change its wording in **Jetpack → Newsletter** settings.

---

## 10. Custom blocks reference

All are in the inserter under the **Theme** category. Post stats has checkboxes in the block settings. For the others, change settings in **Options (⋮) → Code editor**, where each block appears as one line such as `<!-- wp:cloudblogger/random-posts {"count":3,"pool":12} /-->`.

| Block | What it does | Settings (default) |
| --- | --- | --- |
| Post stats | Author, date, updated, read time, views, comments, likes | Tick the items to show |
| Blog stats | Posts, views, comments, years writing | — |
| Must read | Sticky posts, then most viewed | `count` (10), `days` (90) |
| Random picks | Random post cards, re-picked on every page view | `count` (3), `pool` (12), `anyAge` (off: only posts older than 3 months) |
| Recent conversations | Latest comments as cards with mascots | `count` (4) |
| Share post | Share menu | — |
| Like button | The Like button | — |
| Was this helpful? | Feedback | — |

---

## 11. Changing theme files

Most people never need this. Prefer the Site Editor: its changes survive theme updates.

### Theme map

| Path | Contents |
| --- | --- |
| `style.css` | Theme name and version (no styles) |
| `theme.json` | Palette, fonts, sizes, spacing, layout widths (720px text, 1280px wide) |
| `assets/css/theme.css` | All styles, with colour tokens at the top |
| `assets/js/theme.js` | Code blocks, table of contents, image viewer, likes, share, feedback, dark mode |
| `assets/js/editor.js` | Previews of the custom blocks in the editor |
| `inc/setup.php` | Scripts, block styles (callouts), pattern categories, wording used by scripts |
| `inc/blocks.php` | The custom blocks, stats, likes and feedback |
| `inc/mascots.php` | Generated mascot avatars and the Recent comments block |
| `templates/`, `parts/`, `patterns/` | Layouts and sections |
| `assets/fonts/`, `assets/vendor/` | Bundled fonts and highlight.js |

### Rename the theme

To install it next to the original, change **Theme Name** and **Text Domain** in `style.css`, and rename the folder inside the zip.

### Your X handle on shares

To add "via @yourhandle" when readers share to X, add this to a small plugin or your child theme's `functions.php`:

```php
add_filter( 'cb_share_x_via', function () { return 'yourhandle'; } );
```

### Wording

Labels used by scripts ("In this article", "Copy", "Show all", feedback messages and so on) are in `inc/setup.php` under `i18n`. Other text is in templates and patterns.

### Bump the version

When you change files, raise the version in three places so browsers fetch the new files: `style.css` (Version), `functions.php` (`CB_THEME_VERSION`) and `readme.txt`. Add a line to `CHANGELOG.md`.

---

## 12. Updating the theme

1. Download the new zip.
2. **Appearance → Themes → Add New Theme → Upload Theme**, then choose **Replace current with uploaded**.
3. Check the templates you've customised. Open **Appearance → Editor → Templates**. A template you've saved shows a **Reset** option in its menu. Reset it to get the new version, then redo your changes, or leave it as is to keep your version.

Template parts (Header, Footer) work the same way.

On hosts with page caching, logged-out visitors may see the old page for a short while after an update.

---

## 13. Troubleshooting

| Problem | Fix |
| --- | --- |
| A change from an update doesn't show | That template or part has saved changes. Reset it (see above), or clear the page cache. |
| Views show nothing | Turn on Jetpack Stats. New posts show views once Stats has data (cached for about 6 hours). |
| Like count looks lower than before | The count adds Jetpack likes, which needs Jetpack Likes on. |
| The table of contents is missing | The post needs at least two Heading 2 sections. |
| Wrong language label on a code block | Add a `lang-…` class to the Code block. |
| An image opens in the viewer instead of its link | Link the image itself (image toolbar → Link), not the caption. |
| Top bar links wrap or overlap | Shorten or remove a link. Under 1100px they collapse into a menu. |
| Feedback emails don't arrive | Tick "Email me whenever a comment is held for moderation" in Settings → Discussion. |

---

## 14. Removing the theme

Switch to another theme in **Appearance → Themes**, then delete this one. Your posts are untouched. The theme leaves a little data behind, which you can ignore or remove:

| Data | Stored as |
| --- | --- |
| Like counts | Post meta `_cb_likes` |
| Yes/No votes | Post meta `_cb_fb_yes`, `_cb_fb_no` |
| Written feedback | Held comments of type `cb_feedback` |
| Cached stats | Short-lived transients that expire on their own |
