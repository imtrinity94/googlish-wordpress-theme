<?php
/**
 * Title: Demo: numbered steps
 * Slug: cloudblogger/demo-steps
 * Categories: cloudblogger-demo
 * Description: Numbered steps joined by a line, for a procedure the reader follows in order. Bold the first words of each step as its title.
 *
 * @package CloudBlogger
 */

?>
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
