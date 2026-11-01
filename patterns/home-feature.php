<?php
/**
 * Title: Featured story
 * Slug: cloudblogger/home-feature
 * Categories: cloudblogger-home
 * Description: The newest post as a large split card, with the next three posts as cards below it.
 *
 * @package CloudBlogger
 */

?>
<!-- wp:group {"align":"wide","className":"cb-feature","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide cb-feature"><!-- wp:query {"queryId":11,"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"cb-feature-q"} -->
<div class="wp-block-query cb-feature-q"><!-- wp:post-template -->
<!-- wp:group {"className":"cb-feature__grid","layout":{"type":"default"}} -->
<div class="wp-block-group cb-feature__grid"><!-- wp:group {"className":"cb-feature__text","layout":{"type":"default"}} -->
<div class="wp-block-group cb-feature__text"><!-- wp:post-terms {"term":"category"} /-->

<!-- wp:post-title {"level":2,"isLink":true} /-->

<!-- wp:post-excerpt {"excerptLength":24} /-->

<!-- wp:group {"className":"cb-feature__by","style":{"spacing":{"blockGap":"1rem"}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group cb-feature__by"><!-- wp:cloudblogger/post-stats {"items":["date","readtime","views"]} /-->

<!-- wp:read-more {"content":"Read the post"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:post-featured-image {"isLink":true,"sizeSlug":"large","className":"cb-feature__media"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query -->

<!-- wp:query {"queryId":12,"query":{"perPage":3,"pages":0,"offset":1,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"cb-feature-more"} -->
<div class="wp-block-query cb-feature-more"><!-- wp:post-template -->
<!-- wp:group {"className":"cb-card-post","layout":{"type":"default"}} -->
<div class="wp-block-group cb-card-post"><!-- wp:post-featured-image {"sizeSlug":"medium_large"} /-->

<!-- wp:post-terms {"term":"category"} /-->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:cloudblogger/post-stats {"items":["date","readtime"]} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
