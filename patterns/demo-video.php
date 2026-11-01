<?php
/**
 * Title: Demo: video in a window frame
 * Slug: cloudblogger/demo-video
 * Categories: cloudblogger-demo
 * Description: A demo video framed like an app window. Replace the YouTube link with your own.
 *
 * @package CloudBlogger
 */

?>
<!-- wp:group {"className":"cb-window","layout":{"type":"default"}} -->
<div class="wp-block-group cb-window"><!-- wp:paragraph {"className":"cb-window__bar"} -->
<p class="cb-window__bar">Demo</p>
<!-- /wp:paragraph -->

<!-- wp:embed {"url":"https://youtu.be/ATAgRdsLouE","type":"video","providerNameSlug":"youtube","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
https://youtu.be/ATAgRdsLouE
</div></figure>
<!-- /wp:embed --></div>
<!-- /wp:group -->
