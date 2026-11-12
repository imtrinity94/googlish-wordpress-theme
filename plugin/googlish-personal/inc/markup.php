<?php
/**
 * Builds the personal header, footer, author and tools sections from the data,
 * using exactly the block markup the Googlish theme's own patterns use.
 *
 * @package GooglishPersonal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block comment attributes, encoded the way WordPress does.
 *
 * @param array $attrs Attributes.
 * @return string
 */
function googlish_personal_attrs( $attrs ) {
	return serialize_block_attributes( $attrs );
}

/**
 * Image address for src attributes. WordPress's URL cleaner drops data: images, so those are only attribute-escaped.
 *
 * @param string $u Address.
 * @return string
 */
function googlish_personal_src( $u ) {
	return 0 === strpos( (string) $u, 'data:image/' ) ? esc_attr( $u ) : esc_url( $u );
}

/**
 * The icon SVG for a social link key (linkedin, github, youtube, x, gitlab, spotify, buy-me-a-coffee…).
 *
 * @param string $key Key.
 * @return string
 */
function googlish_personal_icon( $key ) {
	static $icons = null;
	if ( null === $icons ) {
		$icons = json_decode( (string) file_get_contents( GOOGLISH_PERSONAL_DIR . 'inc/icons.json' ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( ! is_array( $icons ) ) {
			$icons = array();
		}
	}
	return isset( $icons[ $key ] ) ? $icons[ $key ] : '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="2"/></svg>';
}

/**
 * Header: wordmark, navigation, search and the light/dark switch.
 *
 * @param array $d Data.
 * @return string
 */
function googlish_personal_header( $d ) {
	$site   = isset( $d['site'] ) ? $d['site'] : array();
	$mark   = isset( $site['wordmark'] ) ? $site['wordmark'] : get_bloginfo( 'name' );
	$suffix = isset( $site['wordmark_suffix'] ) ? $site['wordmark_suffix'] : '';
	$search = ! empty( $site['search_placeholder'] ) ? $site['search_placeholder'] : sprintf( 'Search %s', get_bloginfo( 'name' ) );
	$nav    = '';
	foreach ( ( isset( $d['nav'] ) ? $d['nav'] : array() ) as $item ) {
		$a = array(
			'label' => $item['label'],
			'url'   => $item['url'],
			'kind'  => 'custom',
		);
		if ( ! empty( $item['newTab'] ) ) {
			$a['opensInNewTab'] = true;
		}
		$nav .= '<!-- wp:navigation-link ' . googlish_personal_attrs( $a ) . " /-->\n";
	}
	$search_attrs = array(
		'label'               => 'Search',
		'showLabel'           => false,
		'placeholder'         => $search,
		'buttonText'          => 'Search',
		'buttonPosition'      => 'button-only',
		'buttonUseIcon'       => true,
		'isSearchFieldHidden' => true,
	);

	return '<!-- wp:group {"tagName":"header","align":"full","className":"cb-header","style":{"spacing":{"padding":{"top":"0.6rem","bottom":"0.6rem","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<header class="wp-block-group alignfull cb-header" style="padding-top:0.6rem;padding-right:var(--wp--preset--spacing--40);padding-bottom:0.6rem;padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"style":{"spacing":{"blockGap":"0.7rem"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:html -->
<a class="cb-wordmark" href="/" rel="home" aria-label="' . esc_attr( $mark . $suffix ) . ', home">' . esc_html( $mark ) . ( '' !== $suffix ? '<span>' . esc_html( $suffix ) . '</span>' : '' ) . '</a>
<!-- /wp:html --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cb-header__nav","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group cb-header__nav"><!-- wp:navigation {"overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"blockGap":"1.75rem"}}} -->
' . $nav . '<!-- /wp:navigation --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"0.25rem"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:search ' . googlish_personal_attrs( $search_attrs ) . ' /-->

<!-- wp:html -->
<button type="button" class="cb-theme-toggle" aria-label="Switch between light and dark mode" aria-pressed="false"><svg class="cb-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5z"/></svg><svg class="cb-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg></button>
<!-- /wp:html --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></header>
<!-- /wp:group -->';
}

/**
 * Footer: site title, links, social icons and copyright.
 *
 * @param array $d Data.
 * @return string
 */
function googlish_personal_footer( $d ) {
	$f     = isset( $d['footer'] ) ? $d['footer'] : array();
	$links = '';
	foreach ( ( isset( $f['links'] ) ? $f['links'] : array() ) as $l ) {
		$links .= "\n\n<!-- wp:paragraph -->\n<p><a href=\"" . esc_url( $l['url'] ) . '">' . esc_html( $l['label'] ) . "</a></p>\n<!-- /wp:paragraph -->";
	}
	$soc = array();
	foreach ( ( isset( $f['socials'] ) ? $f['socials'] : array() ) as $s ) {
		$soc[] = '<!-- wp:social-link ' . googlish_personal_attrs( $s ) . ' /-->';
	}
	$copy = isset( $f['copyright'] ) ? $f['copyright'] : '© ' . get_bloginfo( 'name' );

	return '<!-- wp:group {"tagName":"footer","align":"full","className":"cb-footer","style":{"spacing":{"padding":{"top":"2rem","bottom":"2rem","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<footer class="wp-block-group alignfull cb-footer" style="padding-top:2rem;padding-right:var(--wp--preset--spacing--40);padding-bottom:2rem;padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"1.25rem 2rem"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"style":{"spacing":{"blockGap":"1.5rem"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:site-title {"level":0} /-->' . $links . '</div>
<!-- /wp:group -->

<!-- wp:social-links {"className":"is-style-logos-only","style":{"spacing":{"blockGap":{"left":"1rem"}}}} -->
<ul class="wp-block-social-links is-style-logos-only">' . implode( "\n\n", $soc ) . '</ul>
<!-- /wp:social-links --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"align":"wide","fontSize":"x-small"} -->
<p class="alignwide has-x-small-font-size">' . esc_html( $copy ) . '</p>
<!-- /wp:paragraph --></footer>
<!-- /wp:group -->';
}

/**
 * Home page author section: photo, name, role, bio, blog stats, social links and badges.
 *
 * @param array $d Data.
 * @return string
 */
function googlish_personal_author( $d ) {
	$a      = isset( $d['author'] ) ? $d['author'] : array();
	$photo  = googlish_personal_url( isset( $a['photo'] ) ? $a['photo'] : '' );
	$small  = googlish_personal_url( isset( $a['photo_small'] ) ? $a['photo_small'] : '' );
	$alt    = isset( $a['photo_alt'] ) ? $a['photo_alt'] : ( isset( $a['name'] ) ? $a['name'] : '' );
	$srcset = $small ? ' srcset="' . esc_url( $small ) . ' 400w, ' . esc_url( $photo ) . ' 800w" sizes="(max-width: 760px) 220px, 280px"' : '';

	$socials = '';
	foreach ( ( isset( $a['socials'] ) ? $a['socials'] : array() ) as $s ) {
		$socials .= '<li><a href="' . esc_url( $s['url'] ) . '" rel="me noopener" target="_blank">' . googlish_personal_icon( $s['key'] ) . esc_html( $s['label'] ) . '</a></li>';
	}
	$badges = '';
	foreach ( ( isset( $a['badges'] ) ? $a['badges'] : array() ) as $b ) {
		$badges .= '<a class="cb-badge" href="' . esc_url( $b['url'] ) . '" target="_blank" rel="noopener"><img src="' . googlish_personal_src( googlish_personal_url( $b['image'] ) ) . '" alt="" loading="lazy" width="88" height="88"><span class="cb-badge__name">' . esc_html( $b['name'] ) . '</span><span class="cb-badge__by">' . esc_html( $b['issuer'] ) . '</span></a>';
	}
	$badges_block = '' === $badges ? '' : '

<!-- wp:html -->
<div class="cb-badges-row"><p class="cb-badges-row__label">' . esc_html( isset( $a['badges_label'] ) ? $a['badges_label'] : 'Certifications and honors' ) . '</p><div class="cb-badges-grid">' . $badges . '</div></div>
<!-- /wp:html -->';

	return '<!-- wp:group {"align":"wide","className":"cb-author-band","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide cb-author-band"><!-- wp:group {"className":"cb-author-band__grid","layout":{"type":"default"}} -->
<div class="wp-block-group cb-author-band__grid"><!-- wp:html -->
<figure class="cb-portrait"><img src="' . googlish_personal_src( $photo ) . '"' . $srcset . ' alt="' . esc_attr( $alt ) . '" width="800" height="1000" decoding="async"></figure>
<!-- /wp:html -->

<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"cb-author-band__kicker"} -->
<p class="cb-author-band__kicker">' . esc_html( isset( $a['kicker'] ) ? $a['kicker'] : '' ) . '</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">' . esc_html( isset( $a['name'] ) ? $a['name'] : '' ) . '</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"cb-author-band__role"} -->
<p class="cb-author-band__role">' . esc_html( isset( $a['role'] ) ? $a['role'] : '' ) . '</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"cb-author-band__bio"} -->
<p class="cb-author-band__bio">' . esc_html( isset( $a['bio'] ) ? $a['bio'] : '' ) . '</p>
<!-- /wp:paragraph -->' . ( ! empty( $a['show_site_stats'] ) ? '

<!-- wp:cloudblogger/site-stats /-->' : '' ) . '

<!-- wp:html -->
<ul class="cb-social-row">' . $socials . '</ul>
<!-- /wp:html --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->' . $badges_block . '</div>
<!-- /wp:group -->';
}

/**
 * Home page tools section.
 *
 * @param array $d Data.
 * @return string
 */
function googlish_personal_tools( $d ) {
	$t     = isset( $d['tools'] ) ? $d['tools'] : array();
	$cards = array();
	foreach ( ( isset( $t['items'] ) ? $t['items'] : array() ) as $i ) {
		$c       = preg_replace( '/[^a-z]/', '', isset( $i['color'] ) ? $i['color'] : 'blue' );
		$cards[] = '<!-- wp:group {"className":"cb-tool cb-tool--' . $c . '","layout":{"type":"default"}} -->
<div class="wp-block-group cb-tool cb-tool--' . $c . '"><!-- wp:paragraph {"className":"cb-tool__name"} -->
<p class="cb-tool__name"><a href="' . esc_url( $i['url'] ) . '">' . esc_html( $i['name'] ) . '</a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"cb-tool__desc"} -->
<p class="cb-tool__desc">' . esc_html( $i['description'] ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->';
	}
	$button = empty( $t['button_url'] ) ? '' : '

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $t['button_url'] ) . '">' . esc_html( isset( $t['button_label'] ) ? $t['button_label'] : '' ) . '</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->';

	return '<!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"className":"cb-rail-head","layout":{"type":"default"}} -->
<div class="wp-block-group cb-rail-head"><!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"className":"cb-section-title"} -->
<h2 class="wp-block-heading cb-section-title">' . esc_html( isset( $t['title'] ) ? $t['title'] : '' ) . '</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"cb-section-lede"} -->
<p class="cb-section-lede">' . esc_html( isset( $t['lede'] ) ? $t['lede'] : '' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->' . $button . '</div>
<!-- /wp:group -->

<!-- wp:group {"className":"cb-tools","layout":{"type":"grid","minimumColumnWidth":"17rem"}} -->
<div class="wp-block-group cb-tools">' . implode( "\n\n", $cards ) . '</div>
<!-- /wp:group --></div>
<!-- /wp:group -->';
}
