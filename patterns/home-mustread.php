<?php
/**
 * Title: Must read
 * Slug: cloudblogger/home-mustread
 * Categories: cloudblogger-home
 * Description: Your sticky posts first, then the most-viewed posts of the last 90 days.
 *
 * @package CloudBlogger
 */

?>
<!-- wp:group {"align":"wide","className":"cb-mustread","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide cb-mustread"><!-- wp:html -->
<div class="cb-rail-head"><div><h2 class="cb-section-title">Must read</h2><p class="cb-section-lede">My picks, then what readers opened most in the last 90 days.</p></div><div class="cb-carousel__nav"><div class="cb-carousel__track" aria-hidden="true"><div class="cb-carousel__bar"></div></div><button type="button" class="cb-carousel__btn" data-dir="-1" aria-label="Previous posts"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6"/></svg></button><button type="button" class="cb-carousel__btn" data-dir="1" aria-label="Next posts"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button></div></div>
<!-- /wp:html -->

<!-- wp:cloudblogger/must-read {"count":10,"days":90} /--></div>
<!-- /wp:group -->
