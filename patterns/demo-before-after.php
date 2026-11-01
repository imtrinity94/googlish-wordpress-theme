<?php
/**
 * Title: Demo: before and after
 * Slug: cloudblogger/demo-before-after
 * Categories: cloudblogger-demo
 * Description: Two side-by-side panels comparing the old and new way.
 *
 * @package CloudBlogger
 */

?>
<!-- wp:columns {"className":"cb-compare"} -->
<div class="wp-block-columns cb-compare"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p>Before</p>
<!-- /wp:paragraph -->

<!-- wp:code {"className":"lang-javascript"} -->
<pre class="wp-block-code lang-javascript"><code>var runtime = java.lang.Runtime.getRuntime();
var process = runtime.exec("uname -n");</code></pre>
<!-- /wp:code --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p>After</p>
<!-- /wp:paragraph -->

<!-- wp:code {"className":"lang-javascript"} -->
<pre class="wp-block-code lang-javascript"><code>var result = System.getModule("com.example.util").runCommand("uname -n");
System.log(result);</code></pre>
<!-- /wp:code --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
