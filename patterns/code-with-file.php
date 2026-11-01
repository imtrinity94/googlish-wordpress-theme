<?php
/**
 * Title: Code with language and file name
 * Slug: cloudblogger/code-with-file
 * Categories: cloudblogger-demo
 * Description: A code block that shows its language and file name in the header. Set them in Advanced > Additional CSS class(es) as lang-javascript file-myAction.js.
 *
 * @package CloudBlogger
 */

?>
<!-- wp:code {"className":"lang-javascript file-getVmByName.js"} -->
<pre class="wp-block-code lang-javascript file-getVmByName.js"><code>var vms = VcPlugin.getAllVirtualMachines(null, "xpath:name='" + vmName + "'");
if (vms.length === 0) {
    throw "No VM found with name " + vmName;
}
return vms[0];</code></pre>
<!-- /wp:code -->
