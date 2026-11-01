<?php
/**
 * Title: Tabs: same task, different tools
 * Slug: cloudblogger/code-tabs
 * Categories: cloudblogger-docs
 * Description: Tabs like Google Cloud docs (Console / gcloud / API). Each tab is a group with the class cb-tab whose first paragraph (class cb-tab__label) names the tab.
 *
 * @package CloudBlogger
 */

?>
<!-- wp:group {"className":"cb-tabs","layout":{"type":"default"}} -->
<div class="wp-block-group cb-tabs"><!-- wp:group {"className":"cb-tab","layout":{"type":"default"}} -->
<div class="wp-block-group cb-tab"><!-- wp:paragraph {"className":"cb-tab__label"} -->
<p class="cb-tab__label">Web console</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Run this in a scriptable task or an action.</p>
<!-- /wp:paragraph -->

<!-- wp:code {"className":"lang-javascript"} -->
<pre class="wp-block-code lang-javascript"><code>var vms = VcPlugin.getAllVirtualMachines();
System.log(&quot;Found &quot; + vms.length + &quot; VMs&quot;);</code></pre>
<!-- /wp:code --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cb-tab","layout":{"type":"default"}} -->
<div class="wp-block-group cb-tab"><!-- wp:paragraph {"className":"cb-tab__label"} -->
<p class="cb-tab__label">REST API</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Call the REST API with a bearer token.</p>
<!-- /wp:paragraph -->

<!-- wp:code {"className":"lang-bash"} -->
<pre class="wp-block-code lang-bash"><code>curl -s -H &quot;Authorization: Bearer $TOKEN&quot; \
  https://auto.vmw.lab/vco/api/workflows?maxResult=5</code></pre>
<!-- /wp:code --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cb-tab","layout":{"type":"default"}} -->
<div class="wp-block-group cb-tab"><!-- wp:paragraph {"className":"cb-tab__label"} -->
<p class="cb-tab__label">PowerCLI</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>The same check from PowerCLI.</p>
<!-- /wp:paragraph -->

<!-- wp:code {"className":"lang-powershell"} -->
<pre class="wp-block-code lang-powershell"><code>Connect-VIServer -Server vc.vmw.lab
(Get-VM).Count</code></pre>
<!-- /wp:code --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
