<?php
/**
 * Search engine hygiene: keep Google on the real posts and off the endless copies of them.
 *
 * WordPress (and Jetpack) produce many URLs that show the same content again: share links,
 * comment-reply links, comment pages, feeds, image attachment pages, date archives and
 * query-loop pagination parameters. Google crawls them, files them under "not indexed", and
 * spends less time on the posts. This file:
 *
 * - adds a canonical link to every archive (home, categories, tags, author, dates), without query strings;
 * - points comment pages (/comment-page-2/) at the post itself;
 * - marks thin pages "noindex, follow": date archives, the author archive, categories and tags
 *   with fewer than 2 posts, and pages opened with query-loop pagination parameters (?query-3-page=2);
 * - sends "X-Robots-Tag: noindex" with feeds (feed readers still work);
 * - sends attachment pages (/image-42/) to the post the image belongs to, or to the file itself;
 * - removes ?replytocom from Reply links, and sends old ?replytocom links to the comment;
 * - turns off Jetpack's own sharing buttons, which the theme replaces, so their ?share= links disappear.
 *
 * Nothing here runs when an SEO plugin (Yoast, Rank Math, SEOPress, AIOSEO) is active.
 * Turn it all off with add_filter( 'googlish_seo', '__return_false' ), or one part at a time
 * with the filters named below.
 *
 * @package CloudBlogger
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the theme should handle search hygiene at all.
 */
function cb_seo_enabled() {
	static $on = null;
	if ( null === $on ) {
		$plugin = defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
		$on     = (bool) apply_filters( 'googlish_seo', ! $plugin );
	}
	return $on;
}

/**
 * True when the request carries query-loop pagination (?query-3-page=2), from this theme's
 * "Load more" fallback links or from an earlier theme.
 */
function cb_seo_has_loop_pagination() {
	foreach ( array_keys( $_GET ) as $key ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( preg_match( '/^query-\d+-page$/', (string) $key ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Clean canonical URL for the current archive view, or '' when there isn't one.
 */
function cb_seo_archive_canonical() {
	$base = '';
	if ( is_front_page() && is_home() ) {
		$base = home_url( '/' );
	} elseif ( is_home() ) {
		$page = (int) get_option( 'page_for_posts' );
		$base = $page ? get_permalink( $page ) : home_url( '/' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$link = get_term_link( get_queried_object() );
		$base = is_wp_error( $link ) ? '' : $link;
	} elseif ( is_author() ) {
		$base = get_author_posts_url( (int) get_queried_object_id() );
	} elseif ( is_day() ) {
		$base = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
	} elseif ( is_month() ) {
		$base = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
	} elseif ( is_year() ) {
		$base = get_year_link( get_query_var( 'year' ) );
	} elseif ( is_post_type_archive() ) {
		$link = get_post_type_archive_link( get_query_var( 'post_type' ) );
		$base = $link ? $link : '';
	}
	if ( ! $base ) {
		return '';
	}
	$paged = (int) get_query_var( 'paged' );
	if ( $paged > 1 ) {
		global $wp_rewrite;
		if ( $wp_rewrite && $wp_rewrite->using_permalinks() ) {
			$base = user_trailingslashit( trailingslashit( $base ) . $wp_rewrite->pagination_base . '/' . $paged, 'paged' );
		} else {
			$base = add_query_arg( 'paged', $paged, $base );
		}
	}
	return (string) apply_filters( 'googlish_archive_canonical', $base );
}

/* Canonical link on archives. WordPress already prints one on posts and pages. */
add_action(
	'wp_head',
	function () {
		if ( ! cb_seo_enabled() || is_singular() || is_search() || is_404() || is_feed() ) {
			return;
		}
		$url = cb_seo_archive_canonical();
		if ( $url ) {
			echo '<link rel="canonical" href="' . esc_url( $url ) . '" />' . "\n";
		}
	},
	1
);

/* Comment pages (/comment-page-2/) and ?replytocom copies: canonical is the post itself. */
add_filter(
	'get_canonical_url',
	function ( $url, $post ) {
		if ( cb_seo_enabled() && ( get_query_var( 'cpage' ) || isset( $_GET['replytocom'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return get_permalink( $post );
		}
		return $url;
	},
	10,
	2
);

/* noindex, follow for thin pages. Links on them are still followed. */
add_filter(
	'wp_robots',
	function ( $robots ) {
		if ( ! cb_seo_enabled() ) {
			return $robots;
		}
		$noindex = false;
		if ( cb_seo_has_loop_pagination() && ! is_singular() ) {
			$noindex = true;
		} elseif ( is_date() ) {
			$noindex = (bool) apply_filters( 'googlish_noindex_dates', true );
		} elseif ( is_author() ) {
			$noindex = (bool) apply_filters( 'googlish_noindex_author', true );
		} elseif ( is_category() || is_tag() ) {
			$term = get_queried_object();
			$min  = (int) apply_filters( 'googlish_noindex_term_min_posts', 2 );
			$noindex = $term && isset( $term->count ) && (int) $term->count < $min;
		} elseif ( is_attachment() ) {
			$noindex = true;
		}
		$noindex = (bool) apply_filters( 'googlish_noindex', $noindex );
		if ( $noindex ) {
			unset( $robots['index'] );
			$robots['noindex'] = true;
			$robots['follow']  = true;
		}
		return $robots;
	}
);

/* Redirects: attachment pages, old ?replytocom links. Feeds get a noindex header. */
add_action(
	'template_redirect',
	function () {
		if ( ! cb_seo_enabled() ) {
			return;
		}
		// Old "Reply" links: /post/?replytocom=12 -> /post/#comment-12.
		if ( isset( $_GET['replytocom'] ) && is_singular() && ! is_user_logged_in() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$id = absint( $_GET['replytocom'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_safe_redirect( get_permalink( get_queried_object_id() ) . ( $id ? '#comment-' . $id : '' ), 301 );
			exit;
		}
		// Attachment pages (/image-42/, /feed/?attachment_id=42): to the post, or the file.
		if ( is_attachment() && apply_filters( 'googlish_redirect_attachments', true ) ) {
			$att    = get_queried_object();
			$parent = $att && $att->post_parent ? get_post( $att->post_parent ) : null;
			$target = ( $parent && 'publish' === $parent->post_status ) ? get_permalink( $parent ) : wp_get_attachment_url( $att ? $att->ID : 0 );
			if ( $target ) {
				wp_safe_redirect( $target, 301 );
				exit;
			}
		}
		if ( is_feed() && ! headers_sent() && apply_filters( 'googlish_noindex_feeds', true ) ) {
			header( 'X-Robots-Tag: noindex, follow', true );
		}
	},
	1
);

/* Reply links without ?replytocom. The comment-reply script still opens the reply form in place. */
add_filter(
	'comment_reply_link',
	function ( $link ) {
		if ( ! cb_seo_enabled() ) {
			return $link;
		}
		return preg_replace_callback(
			'/href=([\'"])([^\'"]*replytocom=[^\'"]*)\1/',
			function ( $m ) {
				$href = str_replace( '&amp;', '&', $m[2] );
				$hash = '';
				if ( false !== strpos( $href, '#' ) ) {
					list( $href, $hash ) = explode( '#', $href, 2 );
					$hash = '#' . $hash;
				}
				$href = remove_query_arg( 'replytocom', $href );
				return 'href=' . $m[1] . esc_url( $href . $hash ) . $m[1];
			},
			$link
		);
	}
);

/* The theme has its own Share menu; Jetpack's buttons (hidden by the theme's CSS) only add ?share= links. */
add_filter(
	'sharing_show',
	function ( $show ) {
		return ( cb_seo_enabled() && apply_filters( 'googlish_hide_jetpack_sharing', true ) ) ? false : $show;
	}
);
