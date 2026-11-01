<?php
/**
 * Friendly mascots for commenters: a small cloud character drawn from the commenter's
 * name, so each person keeps the same mascot everywhere. Replaces avatars on comments
 * (the post author keeps their real photo).
 *
 * @package CloudBlogger
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SVG markup for a mascot.
 *
 * @param string $seed Name or email.
 * @return string
 */
function cb_mascot_svg( $seed ) {
	$h       = is_int( $seed ) ? $seed : crc32( strtolower( trim( (string) $seed ) ) );
	$palette = array(
		array( '#4c8dff', '#dbe8ff' ), array( '#8b6cff', '#ebe4ff' ), array( '#16a38a', '#d6f5ee' ), array( '#f2853b', '#ffe8d6' ),
		array( '#e2659f', '#ffe1ee' ), array( '#2fa84f', '#dcf5e2' ), array( '#d4a106', '#fff3cc' ), array( '#0b8ac4', '#d4f0fc' ),
	);
	$c    = $palette[ $h % count( $palette ) ];
	$eyes = ( $h >> 3 ) % 4;
	$mth  = ( $h >> 5 ) % 3;
	$acc  = ( $h >> 7 ) % 5;

	$svg  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">';
	$svg .= '<rect width="64" height="64" rx="32" fill="' . $c[1] . '"/>';
	// Everything below is drawn 35% larger around the centre so the mascot fills its circle.
	$svg .= '<g transform="translate(32 33) scale(1.35) translate(-32 -33)">';
	// Cloud body.
	$svg .= '<path d="M16 45a9 9 0 0 1 1.5-17.9A13 13 0 0 1 42.6 23 10 10 0 0 1 48 45z" fill="' . $c[0] . '"/>';
	// Eyes.
	switch ( $eyes ) {
		case 0:
			$svg .= '<circle cx="27" cy="34" r="2.6" fill="#fff"/><circle cx="38" cy="34" r="2.6" fill="#fff"/>';
			break;
		case 1:
			$svg .= '<path d="M24.5 35c1.5-2.2 3.5-2.2 5 0M35.5 35c1.5-2.2 3.5-2.2 5 0" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round"/>';
			break;
		case 2:
			$svg .= '<circle cx="27" cy="34" r="2.6" fill="#fff"/><path d="M35.5 34.5h5" stroke="#fff" stroke-width="2" stroke-linecap="round"/>';
			break;
		default:
			$svg .= '<circle cx="27" cy="34" r="3.2" fill="#fff"/><circle cx="38" cy="34" r="3.2" fill="#fff"/><circle cx="27.8" cy="34.6" r="1.5" fill="#1b1d22"/><circle cx="38.8" cy="34.6" r="1.5" fill="#1b1d22"/>';
	}
	// Mouth.
	switch ( $mth ) {
		case 0:
			$svg .= '<path d="M28.5 39.5c2 2 5 2 7 0" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round"/>';
			break;
		case 1:
			$svg .= '<ellipse cx="32" cy="40.5" rx="2" ry="1.7" fill="#fff"/>';
			break;
		default:
			$svg .= '<path d="M28 39h8a4 4 0 0 1-8 0z" fill="#fff"/>';
	}
	// Cheeks.
	$svg .= '<circle cx="22.5" cy="38.5" r="1.8" fill="#fff" opacity=".35"/><circle cx="42.5" cy="38.5" r="1.8" fill="#fff" opacity=".35"/>';
	// Accessory.
	switch ( $acc ) {
		case 1: // Antenna.
			$svg .= '<path d="M32 18v-6" stroke="' . $c[0] . '" stroke-width="2" stroke-linecap="round"/><circle cx="32" cy="10.5" r="2.6" fill="' . $c[0] . '"/>';
			break;
		case 2: // Headset.
			$svg .= '<path d="M19 33a13 13 0 0 1 26 0" stroke="#1b1d22" stroke-width="2" fill="none" opacity=".75"/><rect x="16" y="31" width="5" height="8" rx="2" fill="#1b1d22" opacity=".75"/><rect x="43" y="31" width="5" height="8" rx="2" fill="#1b1d22" opacity=".75"/>';
			break;
		case 3: // Glasses.
			$svg .= '<circle cx="27" cy="34" r="5" stroke="#1b1d22" stroke-width="1.6" fill="none" opacity=".8"/><circle cx="38" cy="34" r="5" stroke="#1b1d22" stroke-width="1.6" fill="none" opacity=".8"/><path d="M32 34h1" stroke="#1b1d22" stroke-width="1.6" opacity=".8"/>';
			break;
		case 4: // Sparkle.
			$svg .= '<path d="M47 14l1.3 3.2 3.2 1.3-3.2 1.3L47 23l-1.3-3.2-3.2-1.3 3.2-1.3z" fill="' . $c[0] . '"/>';
			break;
	}
	return $svg . '</g></svg>';
}

/**
 * Data URI for a mascot.
 *
 * @param string $seed Name or email.
 * @return string
 */
function cb_mascot_url( $seed ) {
	// A real URL (WordPress strips data: URIs from avatars). The number only picks the look.
	$h = crc32( strtolower( trim( (string) $seed ) ) );
	return add_query_arg( 'cb-mascot', $h, home_url( '/' ) );
}

/**
 * Serve a mascot as an SVG image: /?cb-mascot=<number>. Cached by browsers and CDNs for a year.
 */
function cb_mascot_serve() {
	if ( ! isset( $_GET['cb-mascot'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	$h = absint( wp_unslash( $_GET['cb-mascot'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	header( 'Content-Type: image/svg+xml; charset=utf-8' );
	header( 'Cache-Control: public, max-age=31536000, immutable' );
	header( 'X-Content-Type-Options: nosniff' );
	echo cb_mascot_svg( (int) $h ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from fixed markup and a number.
	exit;
}
add_action( 'init', 'cb_mascot_serve', 1 );

/**
 * Use mascots for comment avatars. Registered authors (you) keep their real avatar.
 *
 * @param array $args        Avatar args.
 * @param mixed $id_or_email Avatar subject.
 * @return array
 */
function cb_mascot_avatar_data( $args, $id_or_email ) {
	if ( ! ( $id_or_email instanceof WP_Comment ) ) {
		return $args;
	}
	if ( (int) $id_or_email->user_id > 0 && user_can( (int) $id_or_email->user_id, 'edit_posts' ) ) {
		return $args;
	}
	$seed               = $id_or_email->comment_author_email ? $id_or_email->comment_author_email : $id_or_email->comment_author;
	$args['url']        = cb_mascot_url( $seed . '|' . $id_or_email->comment_author );
	$args['found_avatar'] = true;
	$args['class']      = array_merge( (array) ( isset( $args['class'] ) ? $args['class'] : array() ), array( 'cb-mascot' ) );
	return $args;
}
add_filter( 'pre_get_avatar_data', 'cb_mascot_avatar_data', 20, 2 );

/**
 * cloudblogger/recent-comments — recent comments as cards with mascots.
 *
 * @param array $attrs Attributes.
 * @return string
 */
function cb_render_recent_comments( $attrs ) {
	$count    = isset( $attrs['count'] ) ? max( 1, min( 8, (int) $attrs['count'] ) ) : 4;
	$comments = get_comments(
		array(
			'number'       => $count,
			'status'       => 'approve',
			'post_status'  => 'publish',
			'type'         => 'comment',
		)
	);
	if ( ! $comments ) {
		return '<p ' . get_block_wrapper_attributes( array( 'class' => 'cb-convo-empty' ) ) . '>' . esc_html__( 'No comments yet. Be the first on any post.', 'cloudblogger' ) . '</p>';
	}
	$out = '<ul ' . get_block_wrapper_attributes( array( 'class' => 'cb-convo' ) ) . '>';
	foreach ( $comments as $c ) {
		$is_author = (int) $c->user_id > 0 && user_can( (int) $c->user_id, 'edit_posts' );
		$avatar    = $is_author ? get_avatar_url( $c->user_id, array( 'size' => 96 ) ) : cb_mascot_url( ( $c->comment_author_email ? $c->comment_author_email : $c->comment_author ) . '|' . $c->comment_author );
		$text      = wp_trim_words( wp_strip_all_tags( $c->comment_content ), 28, '…' );
		$when      = human_time_diff( strtotime( $c->comment_date_gmt . ' UTC' ), time() );
		$out      .= '<li class="cb-convo__item' . ( $is_author ? ' is-author' : '' ) . '">'
			. '<img class="cb-convo__avatar" src="' . esc_attr( $avatar ) . '" alt="" width="44" height="44" loading="lazy">'
			. '<div class="cb-convo__body"><p class="cb-convo__meta"><strong>' . esc_html( $c->comment_author ) . '</strong>'
			. ( $is_author ? '<span class="cb-convo__badge">' . esc_html__( 'Author', 'cloudblogger' ) . '</span>' : '' )
			/* translators: %s: time ago */
			. '<span class="cb-convo__time">' . esc_html( sprintf( __( '%s ago', 'cloudblogger' ), $when ) ) . '</span></p>'
			. '<a class="cb-convo__text" href="' . esc_url( get_comment_link( $c ) ) . '">' . esc_html( $text ) . '</a>'
			. '<p class="cb-convo__on">' . esc_html__( 'on', 'cloudblogger' ) . ' <a href="' . esc_url( get_permalink( $c->comment_post_ID ) ) . '">' . esc_html( get_the_title( $c->comment_post_ID ) ) . '</a></p></div></li>';
	}
	return $out . '</ul>';
}

/**
 * Register the block.
 */
function cb_register_comments_block() {
	register_block_type(
		'cloudblogger/recent-comments',
		array(
			'api_version'     => 3,
			'title'           => __( 'Recent conversations', 'cloudblogger' ),
			'category'        => 'theme',
			'editor_script'   => 'cb-blocks-editor',
			'attributes'      => array( 'count' => array( 'type' => 'integer', 'default' => 4 ) ),
			'supports'        => array( 'html' => false ),
			'render_callback' => 'cb_render_recent_comments',
		)
	);
}
add_action( 'init', 'cb_register_comments_block', 20 );
