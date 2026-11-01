<?php
/**
 * Title: Standard post outline
 * Slug: cloudblogger/post-outline
 * Categories: cloudblogger-demo
 * Description: The standard structure for a technical post: opening, tested on, problem, cause, solution, testing, download, summary and closing.
 * Block Types: core/post-content
 * Post Types: post
 *
 * @package CloudBlogger
 */

?>
<!-- wp:paragraph -->
<p>Opening: what problem this post solves, who hits it, and what the reader will have by the end.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"cb-tested"} -->
<p class="cb-tested"><strong>Tested on</strong> <code>Ubuntu 24.04</code> <code>Node.js 22</code></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">The problem</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>What you see: the error, the symptom or the requirement.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Why it happens</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The root cause or the idea behind the approach.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">The solution</h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true,"className":"cb-steps"} -->
<ol class="wp-block-list cb-steps"><!-- wp:list-item -->
<li><strong>Create the action</strong>Create a new action in your module and set its runtime.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Add the inputs</strong>Add the inputs and the return type the workflow needs.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Run it</strong>Run the action and check the log output.</li>
<!-- /wp:list-item --></ol>
<!-- /wp:list -->

<!-- wp:group {"className":"is-style-cb-tip","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-cb-tip"><!-- wp:paragraph -->
<p>A shortcut or a better way, if there is one.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Testing it</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Run it and show the output.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Download from GitHub</h2>
<!-- /wp:heading -->

<!-- wp:group {"className":"cb-download","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group cb-download"><!-- wp:group {"style":{"spacing":{"blockGap":"0.2rem"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"cb-download__title"} -->
<p class="cb-download__title">com.cloudblogger.example.package</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"cb-download__desc"} -->
<p class="cb-download__desc">The scripts and examples from this post, ready to use.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="https://github.com/yourhandle/your-repo/">Download from GitHub</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Summary</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>The one thing to remember.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>Closing: what to try next, a related post, or an invitation to comment.</p>
<!-- /wp:paragraph -->
