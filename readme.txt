=== Googlish ===
Version: 2.8.1
Requires at least: 6.5
Requires PHP: 7.4
License: GPLv2 or later

A technical block theme, in the style of Google's blogs and Google Cloud docs.

== Home page ==

Meet the author, Featured story (plus the next three posts) (live blog stats, social links, certifications), Must read carousel,
Tools I've built, Just in (latest posts with topic chips) and From the community. Each is a pattern you can
rearrange in Appearance > Editor > Templates > Blog Home.

* From the community: recent conversations (commenters get a mascot) beside Explore topics, then three random picks from the archive.
* Must read shows your sticky posts first (marked "Pick"), then the most-viewed posts of the last 90 days
  from Jetpack Stats (marked "Most read"), 10 cards in total. Make a post sticky in Post settings >
  "Stick to the top of the blog". Change the count or the 90-day window in the block's settings.
* Blog stats (posts, views, comments, years) update on their own. Views come from Jetpack Stats.

== Posts ==

* Hero band with breadcrumb, large title, date, updated date, read time, standfirst (the excerpt), author and Share menu.
* "In this article" table of contents, drawn as a workflow, with reading progress.
* Right panel: this post's read time, views, comments and likes, Share this post, and "Was this helpful?" / Send feedback.
  On phones and tablets "Was this helpful?" shows under the post instead.
* Like button at the end of every post, in the theme's style. Its count includes likes the post already
  had on WordPress.com. Turn off Jetpack's own Like button in Jetpack > Settings > Sharing if you like.
* Yes/No votes show in the Posts list (Helpful column).
  Written feedback arrives as a pending comment in Comments, marked "Reader feedback". It is never published.
* More from the blog: three random posts, different on every page view.
* Use the "Post without table of contents" template for short posts.

== Code ==

Every Code block gets a header with the language, a Copy button, line numbers (4+ lines) and "Show all" for long scripts.
The language is detected automatically. To set it, or show a file name, add CSS classes under Advanced:

    lang-javascript file-getVmByName.js
    lang-python / lang-powershell / lang-bash / lang-json / lang-yaml / lang-xml / lang-sql
    lang-output   (log or command output)
    no-lines      (hide line numbers)

Tabs (like Console / gcloud / API in Google Cloud docs): insert the "Tabs: same task, different tools" pattern.
Each tab is a group with class cb-tab whose first paragraph (class cb-tab__label) is the tab name.

== Callouts ==

Pick a style on any Paragraph or Group: Note, Pro tip, Important, Caution, Warning.
Old paragraphs with the class "critical" (also note, tip, important, caution, warning) are restyled automatically.

== Patterns (Inserter > Patterns) ==

* CloudBlogger: callouts — Note, Pro tip, Important, Caution, Warning
* CloudBlogger: docs — Tabs, Before you begin, What's next
* CloudBlogger: demos — Tested on, Numbered steps, Before and after, Video and screenshot in a window frame,
  Code with language and file name, Download from GitHub, Standard post outline
* CloudBlogger: home page — all home sections

== Blocks ==

Post stats, Blog stats, Must read, Random posts, Recent comments, Share post, Like button and Was this helpful? (category: Theme).

== Credits ==

* Google Sans Flex and Google Sans Code, Google LLC, SIL Open Font License 1.1 (assets/fonts/OFL.txt).
* highlight.js 11.12.0, BSD-3-Clause (assets/vendor/highlight-LICENSE.txt).
