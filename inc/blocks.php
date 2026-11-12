<?php
/**
 * Dynamic theme blocks: post stats, site stats, share, and "Was this helpful?" feedback.
 *
 * Each block renders on the server, so counts stay current without editing templates.
 *
 * @package CloudBlogger
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------------------------------------------------------------------
 * Small icon set (stroke icons, 24px grid).
 * ------------------------------------------------------------------------- */

/**
 * Inline SVG icon.
 *
 * @param string $name Icon name.
 * @return string
 */
function cb_icon( $name ) {
	$paths = array(
		'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
		'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'eye'      => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
		'comment'  => '<path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12z"/>',
		'heart'    => '<path d="M12 20s-7-4.4-9.2-8.6C1.2 8.2 3.1 5 6.4 5c2 0 3.3 1.1 4.1 2.3h3c.8-1.2 2.1-2.3 4.1-2.3 3.3 0 5.2 3.2 3.6 6.4C19 15.6 12 20 12 20z"/>',
		'share'    => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/>',
		'link'     => '<path d="M10 14a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1"/><path d="M14 10a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/>',
		'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
		'thumb-up' => '<path d="M7 11v9H4v-9zM7 11l4-7a2 2 0 0 1 2 2v4h5.5a2 2 0 0 1 2 2.3l-1.1 6A2 2 0 0 1 17.4 20H7"/>',
		'thumb-dn' => '<path d="M17 13V4h3v9zM17 13l-4 7a2 2 0 0 1-2-2v-4H5.5a2 2 0 0 1-2-2.3l1.1-6A2 2 0 0 1 6.6 4H17"/>',
		'feedback' => '<path d="M4 4h16v12H8l-4 4z"/><path d="M8 9h8M8 12h5"/>',
		'linkedin' => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10v7M8 7v.01M12 17v-4a2 2 0 0 1 4 0v4M12 10v7"/>',
		'x'        => '<path d="M4 4l16 16M20 4L4 20"/>',
		'whatsapp' => '<path d="M3.5 20.5l1.3-4.4A8.5 8.5 0 1 1 8 19.3z"/><path d="M9 8.5c0 3.5 3 6.5 6.5 6.5l1-1.6-2-1-1 .8a5 5 0 0 1-2.6-2.6l.8-1-1-2z"/>',
		'telegram' => '<path d="M21 4L3 11l6 2 2 6 3-4 5 4z"/><path d="M9 13l8-6"/>',
		'reddit'   => '<circle cx="12" cy="13.5" r="6.5"/><circle cx="18.5" cy="5.5" r="1.5"/><path d="M12 7l1-4 5.5 2.5M9.5 15.5c1.5 1 3.5 1 5 0"/><circle cx="9.5" cy="12.5" r=".6"/><circle cx="14.5" cy="12.5" r=".6"/>',
	);
	$p = isset( $paths[ $name ] ) ? $paths[ $name ] : '';
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $p . '</svg>';
}

/* ---------------------------------------------------------------------------
 * Data helpers.
 * ------------------------------------------------------------------------- */

/**
 * Minutes to read a post (225 words per minute, code counts at half speed).
 *
 * @param WP_Post $post Post.
 * @return int
 */
function cb_read_minutes( $post ) {
	$content = (string) $post->post_content;
	$code    = 0;
	if ( preg_match_all( '#<pre[^>]*>(.*?)</pre>#is', $content, $m ) ) {
		foreach ( $m[1] as $block ) {
			$code += str_word_count( wp_strip_all_tags( $block ) );
		}
		$content = preg_replace( '#<pre[^>]*>.*?</pre>#is', ' ', $content );
	}
	$words = str_word_count( wp_strip_all_tags( strip_shortcodes( $content ) ) );
	return max( 1, (int) round( ( $words + $code * 0.5 ) / 225 ) );
}

/**
 * All-time views for a post from Jetpack Stats, cached for 6 hours. Null when unavailable.
 *
 * @param int $post_id Post ID.
 * @return int|null
 */
function cb_post_views( $post_id ) {
	$key    = 'cb_views2_' . $post_id;
	$cached = get_transient( $key );
	if ( false !== $cached ) {
		return '' === $cached ? null : (int) $cached;
	}
	$views = null;
	try {
		if ( class_exists( '\Automattic\Jetpack\Stats\WPCOM_Stats' ) ) {
			$stats = new \Automattic\Jetpack\Stats\WPCOM_Stats();
			if ( method_exists( $stats, 'get_post_views' ) ) {
				$res = $stats->get_post_views( (int) $post_id );
				if ( is_array( $res ) && isset( $res['views'] ) ) {
					$views = (int) $res['views'];
				}
			}
		}
	} catch ( \Throwable $e ) {
		$views = null;
	}
	// Keep counts fresh: no answer or zero views is rechecked in 15 minutes, posts from the last week every 30 minutes,
	// older posts every 6 hours.
	if ( null === $views || 0 === $views ) {
		$ttl = 15 * MINUTE_IN_SECONDS;
	} elseif ( (int) get_post_time( 'U', true, $post_id ) > time() - WEEK_IN_SECONDS ) {
		$ttl = 30 * MINUTE_IN_SECONDS;
	} else {
		$ttl = 6 * HOUR_IN_SECONDS;
	}
	set_transient( $key, null === $views ? '' : $views, $ttl );
	return $views;
}

/**
 * Site-wide numbers for the author spotlight, cached for 12 hours.
 *
 * @return array
 */
function cb_site_stats() {
	$cached = get_transient( 'cb_site_stats' );
	if ( is_array( $cached ) ) {
		return $cached;
	}
	$posts    = (int) wp_count_posts( 'post' )->publish;
	$comments = (int) wp_count_comments()->approved;
	$first    = get_posts( array( 'numberposts' => 1, 'order' => 'ASC', 'orderby' => 'date', 'post_status' => 'publish', 'fields' => 'ids' ) );
	$since    = $first ? (int) get_the_date( 'Y', $first[0] ) : (int) gmdate( 'Y' );
	$views    = null;
	try {
		if ( class_exists( '\Automattic\Jetpack\Stats\WPCOM_Stats' ) ) {
			$stats = new \Automattic\Jetpack\Stats\WPCOM_Stats();
			$res   = $stats->get_stats();
			if ( is_array( $res ) && isset( $res['stats']['views'] ) ) {
				$views = (int) $res['stats']['views'];
			}
		}
	} catch ( \Throwable $e ) {
		$views = null;
	}
	$out = array( 'posts' => $posts, 'comments' => $comments, 'since' => $since, 'views' => $views );
	set_transient( 'cb_site_stats', $out, 12 * HOUR_IN_SECONDS );
	return $out;
}

/**
 * Short number: 118622 → 118K.
 *
 * @param int $n Number.
 * @return string
 */
function cb_short_number( $n ) {
	if ( $n >= 1000000 ) {
		return round( $n / 1000000, 1 ) . 'M';
	}
	if ( $n >= 10000 ) {
		return round( $n / 1000 ) . 'K';
	}
	if ( $n >= 1000 ) {
		return round( $n / 1000, 1 ) . 'K';
	}
	return (string) $n;
}

/* ---------------------------------------------------------------------------
 * Block renderers.
 * ------------------------------------------------------------------------- */

/**
 * The post the block is showing, from block context or the loop.
 *
 * @param WP_Block|int|null $block Block instance, or a post ID.
 * @return WP_Post|null
 */
function cb_block_post( $block ) {
	if ( is_int( $block ) ) {
		return get_post( $block );
	}
	$id = ( $block instanceof WP_Block && ! empty( $block->context['postId'] ) ) ? (int) $block->context['postId'] : get_the_ID();
	return $id ? get_post( $id ) : null;
}

/**
 * cloudblogger/post-stats
 *
 * @param array             $attrs   Attributes.
 * @param string            $content Inner content.
 * @param WP_Block|int|null $block   Block, or a post ID.
 * @return string
 */
function cb_render_post_stats( $attrs, $content, $block ) {
	$post  = cb_block_post( $block );
	$items = isset( $attrs['items'] ) && is_array( $attrs['items'] ) ? $attrs['items'] : array( 'author', 'date', 'readtime', 'views', 'comments', 'likes' );
	$wrap  = is_int( $block ) ? 'class="cb-postmeta"' : get_block_wrapper_attributes( array( 'class' => 'cb-postmeta' ) );
	if ( ! $post ) {
		return '<div ' . $wrap . '><span class="cb-postmeta__item">' . cb_icon( 'calendar' ) . esc_html__( 'Post date', 'cloudblogger' ) . '</span><span class="cb-postmeta__item">' . cb_icon( 'clock' ) . esc_html__( '5 min read', 'cloudblogger' ) . '</span></div>';
	}
	$out = '';
	foreach ( $items as $item ) {
		switch ( $item ) {
			case 'author':
				$out .= '<span class="cb-postmeta__item cb-postmeta__by">' . esc_html__( 'Posted by', 'cloudblogger' ) . '&nbsp;<strong>' . esc_html( get_the_author_meta( 'display_name', $post->post_author ) ) . '</strong></span>';
				break;
			case 'date':
				$out .= '<span class="cb-postmeta__item">' . cb_icon( 'calendar' ) . '<time datetime="' . esc_attr( get_the_date( 'c', $post ) ) . '">' . esc_html( get_the_date( 'j M Y', $post ) ) . '</time></span>';
				break;
			case 'updated':
				if ( get_the_modified_date( 'Ymd', $post ) > get_the_date( 'Ymd', $post ) ) {
					/* translators: %s: date */
					$out .= '<span class="cb-postmeta__item">' . esc_html( sprintf( __( 'Updated %s', 'cloudblogger' ), get_the_modified_date( 'j M Y', $post ) ) ) . '</span>';
				}
				break;
			case 'readtime':
				/* translators: %d: minutes */
				$out .= '<span class="cb-postmeta__item">' . cb_icon( 'clock' ) . esc_html( sprintf( _n( '%d min read', '%d min read', cb_read_minutes( $post ), 'cloudblogger' ), cb_read_minutes( $post ) ) ) . '</span>';
				break;
			case 'views':
				$views = cb_post_views( $post->ID );
				if ( null !== $views && $views > 0 ) {
					/* translators: %s: number of views */
					$out .= '<span class="cb-postmeta__item" title="' . esc_attr( number_format_i18n( $views ) ) . '">' . cb_icon( 'eye' ) . esc_html( sprintf( __( '%s views', 'cloudblogger' ), cb_short_number( $views ) ) ) . '</span>';
				}
				break;
			case 'comments':
				$n = (int) get_comments_number( $post );
				/* translators: %d: number of comments */
				$out .= '<a class="cb-postmeta__item" href="' . esc_url( get_permalink( $post ) . '#comments' ) . '">' . cb_icon( 'comment' ) . esc_html( sprintf( _n( '%d comment', '%d comments', $n, 'cloudblogger' ), $n ) ) . '</a>';
				break;
			case 'likes':
				$out .= '<span class="cb-postmeta__item" data-cb-likes="' . esc_attr( $post->ID ) . '" data-local="' . esc_attr( (int) get_post_meta( $post->ID, '_cb_likes', true ) ) . '" hidden>' . cb_icon( 'heart' ) . '<span class="cb-likes-n"></span></span>';
				break;
		}
	}
	return '<div ' . $wrap . '>' . $out . '</div>';
}

/**
 * cloudblogger/site-stats — posts, views, comments, years blogging.
 *
 * @return string
 */
function cb_render_site_stats() {
	$s     = cb_site_stats();
	$years = max( 1, (int) gmdate( 'Y' ) - $s['since'] );
	$cells = array(
		array( $s['posts'], __( 'Posts published', 'cloudblogger' ) ),
		array( null !== $s['views'] ? $s['views'] : 0, __( 'Reader views', 'cloudblogger' ) ),
		array( $s['comments'], __( 'Comments', 'cloudblogger' ) ),
		/* translators: %d: year */
		array( $years, sprintf( __( 'Years writing, since %d', 'cloudblogger' ), $s['since'] ) ),
	);
	$out = '<ul ' . get_block_wrapper_attributes( array( 'class' => 'cb-stats' ) ) . '>';
	foreach ( $cells as $c ) {
		if ( ! $c[0] ) {
			continue;
		}
		$out .= '<li class="cb-stat"><span class="cb-stat__num" data-cb-count="' . esc_attr( $c[0] ) . '">' . esc_html( cb_short_number( (int) $c[0] ) . ( $c[0] >= 1000 ? '+' : '' ) ) . '</span><span class="cb-stat__label">' . esc_html( $c[1] ) . '</span></li>';
	}
	return $out . '</ul>';
}

/**
 * cloudblogger/share
 *
 * @param array         $attrs   Attributes.
 * @param string        $content Inner content.
 * @param WP_Block|null $block   Block.
 * @return string
 */
function cb_render_share( $attrs, $content, $block ) {
	$post  = cb_block_post( $block );
	$url   = $post ? get_permalink( $post ) : home_url( '/' );
	$title = $post ? html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ) : get_bloginfo( 'name' );
	$id    = 'cb-share-' . wp_unique_id();
	return '<div ' . get_block_wrapper_attributes( array( 'class' => 'cb-share' ) ) . ' data-cb-share data-url="' . esc_attr( $url ) . '">'
		. '<button type="button" class="cb-share__btn" aria-expanded="false" aria-controls="' . esc_attr( $id ) . '">' . cb_icon( 'share' ) . esc_html__( 'Share', 'cloudblogger' ) . '</button>'
		. '<div class="cb-share__menu" id="' . esc_attr( $id ) . '" hidden>'
		. '<button type="button" data-cb-copy-link>' . cb_icon( 'link' ) . '<span>' . esc_html__( 'Copy link', 'cloudblogger' ) . '</span></button>'
		. '<a href="' . esc_url( 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $url ) ) . '" target="_blank" rel="noopener">' . cb_icon( 'linkedin' ) . esc_html__( 'LinkedIn', 'cloudblogger' ) . '</a>'
		. '<a href="' . esc_url( 'https://x.com/intent/post?url=' . rawurlencode( $url ) . '&text=' . rawurlencode( $title ) . '' . ( apply_filters( 'cb_share_x_via', '' ) ? '&via=' . rawurlencode( apply_filters( 'cb_share_x_via', '' ) ) : '' ) ) . '" target="_blank" rel="noopener">' . cb_icon( 'x' ) . esc_html__( 'X', 'cloudblogger' ) . '</a>'
		. '<a href="' . esc_url( 'https://wa.me/?text=' . rawurlencode( $title . ' ' . $url ) ) . '" target="_blank" rel="noopener">' . cb_icon( 'whatsapp' ) . esc_html__( 'WhatsApp', 'cloudblogger' ) . '</a>'
		. '<a href="' . esc_url( 'https://t.me/share/url?url=' . rawurlencode( $url ) . '&text=' . rawurlencode( $title ) ) . '" target="_blank" rel="noopener">' . cb_icon( 'telegram' ) . esc_html__( 'Telegram', 'cloudblogger' ) . '</a>'
		. '<a href="' . esc_url( 'https://www.reddit.com/submit?url=' . rawurlencode( $url ) . '&title=' . rawurlencode( $title ) ) . '" target="_blank" rel="noopener">' . cb_icon( 'reddit' ) . esc_html__( 'Reddit', 'cloudblogger' ) . '</a>'
		. '<a href="' . esc_url( 'mailto:?subject=' . rawurlencode( $title ) . '&body=' . rawurlencode( $url ) ) . '">' . cb_icon( 'mail' ) . esc_html__( 'Email', 'cloudblogger' ) . '</a>'
		. '</div></div>';
}

/**
 * cloudblogger/feedback — "Was this helpful?" with an optional message.
 *
 * @param array         $attrs   Attributes.
 * @param string        $content Inner content.
 * @param WP_Block|null $block   Block.
 * @return string
 */
function cb_render_feedback( $attrs, $content, $block ) {
	$post = cb_block_post( $block );
	$pid  = $post ? $post->ID : 0;
	$uid  = wp_unique_id( 'cb-fb-' );
	return '<div ' . get_block_wrapper_attributes( array( 'class' => 'cb-feedback' ) ) . ' data-cb-feedback="' . esc_attr( $pid ) . '">'
		. '<p class="cb-feedback__q">' . esc_html__( 'Was this helpful?', 'cloudblogger' ) . '</p>'
		. '<div class="cb-feedback__btns">'
		. '<button type="button" class="cb-feedback__btn" data-vote="yes" aria-pressed="false">' . cb_icon( 'thumb-up' ) . esc_html__( 'Yes', 'cloudblogger' ) . '</button>'
		. '<button type="button" class="cb-feedback__btn" data-vote="no" aria-pressed="false">' . cb_icon( 'thumb-dn' ) . esc_html__( 'No', 'cloudblogger' ) . '</button>'
		. '</div>'
		. '<button type="button" class="cb-feedback__send" aria-expanded="false" aria-controls="' . esc_attr( $uid ) . '">' . cb_icon( 'feedback' ) . esc_html__( 'Send feedback', 'cloudblogger' ) . '</button>'
		. '<form class="cb-feedback__form" id="' . esc_attr( $uid ) . '" hidden>'
		. '<label for="' . esc_attr( $uid ) . '-msg">' . esc_html__( 'What should change? A wrong step, an outdated version, a broken link…', 'cloudblogger' ) . '</label>'
		. '<textarea id="' . esc_attr( $uid ) . '-msg" name="message" maxlength="2000" required></textarea>'
		. '<input class="cb-hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">'
		. '<div class="cb-feedback__row"><button type="submit" class="cb-feedback__submit">' . esc_html__( 'Send feedback', 'cloudblogger' ) . '</button>'
		. '<p class="cb-feedback__note">' . esc_html__( 'Only the author sees this. Don’t include passwords or customer details.', 'cloudblogger' ) . '</p></div>'
		. '</form>'
		. '<p class="cb-feedback__msg" role="status"></p>'
		. '</div>';
}

/**
 * cloudblogger/random-posts — random posts as cards ("Random picks"). A different set on every page view.
 *
 * @param array $attrs Attributes.
 * @return string
 */
function cb_render_random_posts( $attrs ) {
	$count   = isset( $attrs['count'] ) ? max( 1, min( 6, (int) $attrs['count'] ) ) : 3;
	$pool    = isset( $attrs['pool'] ) ? max( $count, min( 30, (int) $attrs['pool'] ) ) : $count;
	$any_age = ! empty( $attrs['anyAge'] );
	$args    = array(
		'numberposts'      => $pool,
		'orderby'          => 'rand',
		'post_status'      => 'publish',
		'ignore_sticky_posts' => true,
		'suppress_filters' => false,
	);
	if ( ! $any_age ) {
		$args['date_query'] = array( array( 'before' => '3 months ago' ) );
	}
	if ( is_singular() ) {
		$args['exclude'] = array( get_queried_object_id() );
	}
	$posts = get_posts( $args );
	if ( ! $posts ) {
		return '';
	}
	// Pages are cached, so the server sends a random pool and the browser picks $count of them on every view.
	$out = '<ul ' . get_block_wrapper_attributes( array( 'class' => 'cb-random wp-block-post-template' ) ) . ' data-cb-random="' . esc_attr( $count ) . '">';
	foreach ( $posts as $i => $p ) {
		$cats  = get_the_category( $p->ID );
		$thumb = get_the_post_thumbnail( $p, 'medium_large', array( 'loading' => 'lazy', 'alt' => '' ) );
		$out  .= '<li class="wp-block-post"' . ( $i >= $count ? ' hidden' : '' ) . '><div class="cb-card-post">'
			. '<figure class="wp-block-post-featured-image">' . $thumb . '</figure>'
			. ( $cats ? '<div class="wp-block-post-terms"><a href="' . esc_url( get_category_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a></div>' : '' )
			. '<h4 class="wp-block-post-title"><a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( get_the_title( $p ) ) . '</a></h4>'
			. cb_render_post_stats( array( 'items' => array( 'date', 'readtime' ) ), '', $p->ID )
			. '</div></li>';
	}
	return $out . '</ul>';
}

/**
 * Most-viewed post IDs from Jetpack Stats over the last $days days, cached for 6 hours.
 * Falls back to the most-commented posts if Stats has nothing to return.
 *
 * @param int $days Window in days.
 * @param int $max  How many IDs.
 * @return int[]
 */
function cb_top_post_ids( $days = 90, $max = 12 ) {
	$key    = 'cb_top_posts_' . (int) $days;
	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		return array_slice( $cached, 0, $max );
	}
	$ids = array();
	try {
		if ( class_exists( '\Automattic\Jetpack\Stats\WPCOM_Stats' ) ) {
			$stats = new \Automattic\Jetpack\Stats\WPCOM_Stats();
			if ( method_exists( $stats, 'get_top_posts' ) ) {
				$res  = $stats->get_top_posts( array( 'period' => 'day', 'num' => (int) $days, 'summarize' => 1, 'max' => 40 ) );
				$rows = array();
				if ( is_array( $res ) && isset( $res['summary']['postviews'] ) ) {
					$rows = $res['summary']['postviews'];
				} elseif ( is_array( $res ) && isset( $res['days'] ) && is_array( $res['days'] ) ) {
					$first = reset( $res['days'] );
					$rows  = isset( $first['postviews'] ) ? $first['postviews'] : array();
				}
				foreach ( (array) $rows as $row ) {
					$id = isset( $row['id'] ) ? (int) $row['id'] : 0;
					if ( $id && 'post' === get_post_type( $id ) && 'publish' === get_post_status( $id ) ) {
						$ids[] = $id;
					}
				}
			}
		}
	} catch ( \Throwable $e ) {
		$ids = array();
	}
	if ( ! $ids ) {
		$ids = get_posts( array( 'numberposts' => 20, 'orderby' => 'comment_count', 'order' => 'DESC', 'post_status' => 'publish', 'fields' => 'ids' ) );
	}
	$ids = array_values( array_unique( array_map( 'intval', $ids ) ) );
	set_transient( $key, $ids, 6 * HOUR_IN_SECONDS );
	return array_slice( $ids, 0, $max );
}

/**
 * cloudblogger/must-read — your sticky posts first, then the most-viewed posts.
 *
 * @param array $attrs Attributes.
 * @return string
 */
function cb_render_must_read( $attrs ) {
	$total  = isset( $attrs['count'] ) ? max( 3, min( 16, (int) $attrs['count'] ) ) : 10;
	$days   = isset( $attrs['days'] ) ? max( 7, (int) $attrs['days'] ) : 90;
	$sticky = array_values( array_filter( array_map( 'intval', (array) get_option( 'sticky_posts', array() ) ), function ( $id ) {
		return 'publish' === get_post_status( $id ) && 'post' === get_post_type( $id );
	} ) );
	$sticky = array_slice( array_reverse( $sticky ), 0, $total );
	$ids    = $sticky;
	foreach ( cb_top_post_ids( $days, $total + count( $sticky ) ) as $id ) {
		if ( count( $ids ) >= $total ) {
			break;
		}
		if ( ! in_array( $id, $ids, true ) ) {
			$ids[] = $id;
		}
	}
	if ( ! $ids ) {
		return '';
	}
	$out = '<div ' . get_block_wrapper_attributes( array( 'class' => 'cb-carousel' ) ) . '><ul class="wp-block-post-template">';
	foreach ( $ids as $id ) {
		$p      = get_post( $id );
		$cats   = get_the_category( $id );
		$pinned = in_array( $id, $sticky, true );
		$items  = $pinned ? array( 'date', 'readtime' ) : array( 'date', 'views' );
		$out   .= '<li class="wp-block-post"><div class="cb-card-post' . ( $pinned ? ' is-pinned' : '' ) . '">'
			. '<figure class="wp-block-post-featured-image">' . get_the_post_thumbnail( $p, 'medium_large', array( 'loading' => 'lazy', 'alt' => '' ) )
			. '<span class="cb-card-flag">' . esc_html( $pinned ? __( 'Pick', 'cloudblogger' ) : __( 'Most read', 'cloudblogger' ) ) . '</span></figure>'
			. ( $cats ? '<div class="wp-block-post-terms"><a href="' . esc_url( get_category_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a></div>' : '' )
			. '<h3 class="wp-block-post-title"><a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( get_the_title( $p ) ) . '</a></h3>'
			. cb_render_post_stats( array( 'items' => $items ), '', $id )
			. '</div></li>';
	}
	return $out . '</ul></div>';
}

/**
 * cloudblogger/like — the theme's own Like button. Anyone can like once per post (per browser,
 * and once a day per network address). The count shown adds likes the post already has on
 * WordPress.com, so nothing is lost.
 *
 * @param array         $attrs   Attributes.
 * @param string        $content Inner content.
 * @param WP_Block|null $block   Block.
 * @return string
 */
function cb_render_like( $attrs, $content, $block ) {
	$post = cb_block_post( $block );
	if ( ! $post ) {
		return '';
	}
	$local = (int) get_post_meta( $post->ID, '_cb_likes', true );
	return '<div ' . get_block_wrapper_attributes( array( 'class' => 'cb-like' ) ) . '>'
		. '<p class="cb-like__q">' . esc_html__( 'Enjoyed this post?', 'cloudblogger' ) . '</p>'
		. '<button type="button" class="cb-like__btn" data-cb-like="' . esc_attr( $post->ID ) . '" data-local="' . esc_attr( $local ) . '" aria-pressed="false">'
		. '<span class="cb-like__icon" aria-hidden="true">' . cb_icon( 'heart' ) . '</span>'
		. '<span class="cb-like__label">' . esc_html__( 'Like', 'cloudblogger' ) . '</span>'
		. '<span class="cb-like__count" hidden></span></button></div>';
}

/**
 * Register the blocks and their editor script.
 */
function cb_register_blocks() {
	wp_register_script(
		'cb-blocks-editor',
		get_template_directory_uri() . '/assets/js/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-server-side-render', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		CB_THEME_VERSION,
		true
	);
	$common = array(
		'api_version'   => 3,
		'editor_script' => 'cb-blocks-editor',
		'uses_context'  => array( 'postId', 'postType' ),
		'supports'      => array( 'html' => false, 'spacing' => array( 'margin' => true ) ),
	);
	register_block_type(
		'cloudblogger/post-stats',
		array_merge(
			$common,
			array(
				'title'           => __( 'Post stats', 'cloudblogger' ),
				'category'        => 'theme',
				'attributes'      => array( 'items' => array( 'type' => 'array', 'default' => array( 'author', 'date', 'readtime', 'views', 'comments', 'likes' ) ) ),
				'render_callback' => 'cb_render_post_stats',
			)
		)
	);
	register_block_type( 'cloudblogger/site-stats', array_merge( $common, array( 'title' => __( 'Blog stats', 'cloudblogger' ), 'category' => 'theme', 'render_callback' => 'cb_render_site_stats' ) ) );
	register_block_type( 'cloudblogger/must-read', array_merge( $common, array( 'title' => __( 'Must read', 'cloudblogger' ), 'category' => 'theme', 'attributes' => array( 'count' => array( 'type' => 'integer', 'default' => 10 ), 'days' => array( 'type' => 'integer', 'default' => 90 ) ), 'render_callback' => 'cb_render_must_read' ) ) );
	register_block_type( 'cloudblogger/random-posts', array_merge( $common, array( 'title' => __( 'Random picks', 'cloudblogger' ), 'category' => 'theme', 'attributes' => array( 'count' => array( 'type' => 'integer', 'default' => 3 ), 'pool' => array( 'type' => 'integer', 'default' => 12 ), 'anyAge' => array( 'type' => 'boolean', 'default' => false ) ), 'render_callback' => 'cb_render_random_posts' ) ) );
	register_block_type( 'cloudblogger/share', array_merge( $common, array( 'title' => __( 'Share post', 'cloudblogger' ), 'category' => 'theme', 'render_callback' => 'cb_render_share' ) ) );
	register_block_type( 'cloudblogger/like', array_merge( $common, array( 'title' => __( 'Like button', 'cloudblogger' ), 'category' => 'theme', 'render_callback' => 'cb_render_like' ) ) );
	register_block_type( 'cloudblogger/feedback', array_merge( $common, array( 'title' => __( 'Was this helpful?', 'cloudblogger' ), 'category' => 'theme', 'render_callback' => 'cb_render_feedback' ) ) );
}
add_action( 'init', 'cb_register_blocks' );

/* ---------------------------------------------------------------------------
 * Feedback endpoint. Votes are counted per post; written feedback is saved as a
 * private, unapproved comment of type "cb_feedback" (Comments screen, pending).
 * ------------------------------------------------------------------------- */

/**
 * REST route.
 */
function cb_feedback_route() {
	register_rest_route(
		'cloudblogger/v1',
		'/feedback',
		array(
			'methods'             => 'POST',
			'permission_callback' => '__return_true',
			'args'                => array(
				'post_id' => array( 'required' => true, 'type' => 'integer' ),
				'vote'    => array( 'type' => 'string', 'enum' => array( 'yes', 'no', '' ), 'default' => '' ),
				'message' => array( 'type' => 'string', 'default' => '' ),
				'website' => array( 'type' => 'string', 'default' => '' ),
			),
			'callback'            => 'cb_feedback_handle',
		)
	);
}
add_action( 'rest_api_init', 'cb_feedback_route' );

/**
 * REST route for the Like button.
 */
function cb_like_route() {
	register_rest_route(
		'cloudblogger/v1',
		'/like',
		array(
			'methods'             => 'POST',
			'permission_callback' => '__return_true',
			'args'                => array( 'post_id' => array( 'required' => true, 'type' => 'integer' ) ),
			'callback'            => function ( WP_REST_Request $req ) {
				$post = get_post( (int) $req['post_id'] );
				if ( ! $post || 'publish' !== $post->post_status ) {
					return new WP_Error( 'cb_like_post', __( 'This post can’t be liked.', 'cloudblogger' ), array( 'status' => 404 ) );
				}
				$ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0';
				$lock = 'cb_like_' . md5( $ip . '|' . $post->ID );
				$n    = (int) get_post_meta( $post->ID, '_cb_likes', true );
				if ( ! get_transient( $lock ) ) {
					set_transient( $lock, 1, DAY_IN_SECONDS );
					$n++;
					update_post_meta( $post->ID, '_cb_likes', $n );
				}
				return rest_ensure_response( array( 'local' => $n ) );
			},
		)
	);
}
add_action( 'rest_api_init', 'cb_like_route' );

/**
 * Store a vote or message.
 *
 * @param WP_REST_Request $req Request.
 * @return WP_REST_Response|WP_Error
 */
function cb_feedback_handle( WP_REST_Request $req ) {
	$post = get_post( (int) $req['post_id'] );
	if ( ! $post || 'publish' !== $post->post_status ) {
		return new WP_Error( 'cb_fb_post', __( 'This post can’t take feedback.', 'cloudblogger' ), array( 'status' => 404 ) );
	}
	if ( '' !== (string) $req['website'] ) {
		return rest_ensure_response( array( 'ok' => true ) ); // Honeypot: pretend success.
	}
	$ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0';
	$kind = '' !== trim( (string) $req['message'] ) ? 'msg' : 'vote';
	$lock = 'cb_fb_' . $kind . '_' . md5( $ip . '|' . $post->ID );
	if ( get_transient( $lock ) ) {
		return new WP_Error( 'cb_fb_rate', __( 'Thanks, we already have your feedback for this post.', 'cloudblogger' ), array( 'status' => 429 ) );
	}
	set_transient( $lock, 1, 'msg' === $kind ? 10 * MINUTE_IN_SECONDS : DAY_IN_SECONDS );

	$vote = (string) $req['vote'];
	if ( 'vote' === $kind && in_array( $vote, array( 'yes', 'no' ), true ) ) {
		$key = 'yes' === $vote ? '_cb_fb_yes' : '_cb_fb_no';
		update_post_meta( $post->ID, $key, (int) get_post_meta( $post->ID, $key, true ) + 1 );
	}
	if ( 'msg' === $kind ) {
		$msg = mb_substr( sanitize_textarea_field( (string) $req['message'] ), 0, 2000 );
		$id  = wp_insert_comment(
			array(
				'comment_post_ID'  => $post->ID,
				'comment_author'   => __( 'Reader feedback', 'cloudblogger' ),
				'comment_content'  => ( $vote ? '[' . ( 'yes' === $vote ? 'Helpful' : 'Not helpful' ) . '] ' : '' ) . $msg,
				'comment_type'     => 'cb_feedback',
				'comment_approved' => 0,
				'comment_author_IP' => $ip,
			)
		);
		if ( $id ) {
			wp_notify_moderator( $id );
		}
	}
	return rest_ensure_response( array( 'ok' => true ) );
}

/**
 * Show helpful votes in the Posts list.
 *
 * @param array $cols Columns.
 * @return array
 */
function cb_feedback_column( $cols ) {
	$cols['cb_helpful'] = __( 'Helpful', 'cloudblogger' );
	return $cols;
}
add_filter( 'manage_post_posts_columns', 'cb_feedback_column' );

/**
 * Column value.
 *
 * @param string $col     Column.
 * @param int    $post_id Post ID.
 */
function cb_feedback_column_value( $col, $post_id ) {
	if ( 'cb_helpful' !== $col ) {
		return;
	}
	$y = (int) get_post_meta( $post_id, '_cb_fb_yes', true );
	$n = (int) get_post_meta( $post_id, '_cb_fb_no', true );
	echo $y || $n ? esc_html( sprintf( '%d yes / %d no', $y, $n ) ) : '—';
}
add_action( 'manage_post_posts_custom_column', 'cb_feedback_column_value', 10, 2 );

/**
 * Keep feedback out of public comment counts and lists.
 *
 * @param WP_Comment_Query $q Query.
 */
function cb_hide_feedback_comments( $q ) {
	if ( is_admin() ) {
		return;
	}
	$types = (array) $q->query_vars['type__not_in'];
	$types[] = 'cb_feedback';
	$q->query_vars['type__not_in'] = $types;
}
add_action( 'pre_get_comments', 'cb_hide_feedback_comments' );
