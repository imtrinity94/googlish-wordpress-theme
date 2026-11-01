<?php
/**
 * Title: Conversations and topics
 * Slug: cloudblogger/home-community
 * Categories: cloudblogger-home
 * Description: Recent comments (with mascots) beside the topic list.
 *
 * @package CloudBlogger
 */

?>
<!-- wp:group {"align":"wide","className":"cb-talk","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide cb-talk"><!-- wp:group {"className":"cb-talk__panel","layout":{"type":"default"}} -->
<div class="wp-block-group cb-talk__panel"><!-- wp:heading {"className":"cb-section-title"} -->
<h2 class="wp-block-heading cb-section-title">Recent conversations</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"cb-section-lede"} -->
<p class="cb-section-lede">What readers are asking and adding.</p>
<!-- /wp:paragraph -->

<!-- wp:cloudblogger/recent-comments {"count":4} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cb-talk__panel","layout":{"type":"default"}} -->
<div class="wp-block-group cb-talk__panel"><!-- wp:heading {"className":"cb-section-title"} -->
<h2 class="wp-block-heading cb-section-title">Explore topics</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"cb-section-lede"} -->
<p class="cb-section-lede">Jump to everything written on a subject.</p>
<!-- /wp:paragraph -->

<!-- wp:tag-cloud {"numberOfTags":40,"smallestFontSize":"14px","largestFontSize":"14px"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
