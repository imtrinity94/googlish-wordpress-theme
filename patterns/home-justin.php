<?php
/**
 * Title: Just in
 * Slug: cloudblogger/home-justin
 * Categories: cloudblogger-home
 * Description: The latest posts in a large list, with topic chips on the left.
 *
 * @package CloudBlogger
 */

?>
<!-- wp:group {"align":"wide","className":"cb-justin","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide cb-justin"><!-- wp:group {"className":"cb-justin__side","layout":{"type":"default"}} -->
<div class="wp-block-group cb-justin__side"><!-- wp:heading {"className":"cb-section-title"} -->
<h2 class="wp-block-heading cb-section-title">Just in</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"cb-justin__filter"} -->
<p class="cb-justin__filter">Browse by topic</p>
<!-- /wp:paragraph -->

<!-- wp:html -->
<nav class="cb-chips" aria-label="Topics"><a href="/" aria-current="page">All posts</a><a href="/category/article/">Article</a><a href="/category/quick-post/">Quick post</a><a href="/category/tutorial/">Tutorial</a><a href="/category/ai/">AI</a><a href="/category/extension/">Extension</a><a href="/category/hacks/">Hacks</a><a href="/category/list/">List</a></nav>
<!-- /wp:html --></div>
<!-- /wp:group -->

<!-- wp:query {"queryId":21,"query":{"perPage":8,"pages":0,"offset":4,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"cb-postlist"} -->
<div class="wp-block-query cb-postlist"><!-- wp:post-template -->
<!-- wp:group {"className":"cb-row","layout":{"type":"default"}} -->
<div class="wp-block-group cb-row"><!-- wp:post-featured-image {"isLink":true,"sizeSlug":"medium_large"} /-->

<!-- wp:group {"className":"cb-row__body","layout":{"type":"default"}} -->
<div class="wp-block-group cb-row__body"><!-- wp:post-terms {"term":"category"} /-->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-excerpt {"excerptLength":26} /-->

<!-- wp:cloudblogger/post-stats {"items":["author","date","readtime","comments","likes"]} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"paginationArrow":"arrow","layout":{"type":"flex","justifyContent":"left"}} -->
<!-- wp:query-pagination-previous {"label":"Newer posts"} /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next {"label":"Explore more articles"} /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>No posts yet.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
