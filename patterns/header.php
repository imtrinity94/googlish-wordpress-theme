<?php
/**
 * Title: Header
 * Slug: cloudblogger/header
 * Categories: cloudblogger-home
 * Inserter: no
 * Description: Site header: wordmark, navigation, search and the light/dark switch. A personal plugin can replace it.
 *
 * @package CloudBlogger
 */

?>
<!-- wp:group {"tagName":"header","align":"full","className":"cb-header","style":{"spacing":{"padding":{"top":"0.6rem","bottom":"0.6rem","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<header class="wp-block-group alignfull cb-header" style="padding-top:0.6rem;padding-right:var(--wp--preset--spacing--40);padding-bottom:0.6rem;padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"style":{"spacing":{"blockGap":"0.7rem"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:html -->
<a class="cb-wordmark" href="/" rel="home" aria-label="YourSite.com, home">YourSite<span>.com</span></a>
<!-- /wp:html --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cb-header__nav","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group cb-header__nav"><!-- wp:navigation {"overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"blockGap":"1.75rem"}}} -->
<!-- wp:navigation-link {"label":"Latest","url":"/","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"Articles","url":"/category/article/","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"Quick posts","url":"/category/quick-post/","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"About","url":"/about/","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"Projects","url":"/projects/","kind":"custom"} /-->
<!-- /wp:navigation --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"0.25rem"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search","buttonText":"Search","buttonPosition":"button-only","buttonUseIcon":true,"isSearchFieldHidden":true} /-->

<!-- wp:html -->
<button type="button" class="cb-theme-toggle" aria-label="Switch between light and dark mode" aria-pressed="false"><svg class="cb-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5z"/></svg><svg class="cb-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg></button>
<!-- /wp:html --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></header>
<!-- /wp:group -->
