<?php
/**
 * Plugin Name:       Googlish Personal
 * Plugin URI:        https://github.com/imtrinity94/googlish-wordpress-theme
 * Description:       Makes the Googlish theme yours: your name, photo, bio, social links, badges, tools, header and footer, kept outside the theme so theme updates never touch them. All content lives in data/profile.json.
 * Version:           1.0.1
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            Mayank Goyal
 * Author URI:        https://github.com/imtrinity94
 * License:           GPLv2 or later
 * Text Domain:       googlish-personal
 *
 * @package GooglishPersonal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GOOGLISH_PERSONAL_VERSION', '1.0.1' );
define( 'GOOGLISH_PERSONAL_DIR', plugin_dir_path( __FILE__ ) );

require_once GOOGLISH_PERSONAL_DIR . 'inc/markup.php';

/**
 * The personal data. Reads data/profile.json; other code can change it with the
 * 'googlish_personal_data' filter (a future settings screen will use this).
 *
 * @return array
 */
function googlish_personal_data() {
	static $data = null;
	if ( null === $data ) {
		$raw  = file_get_contents( GOOGLISH_PERSONAL_DIR . 'data/profile.json' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$data = json_decode( (string) $raw, true );
		if ( ! is_array( $data ) ) {
			$data = array();
		}
		$data = apply_filters( 'googlish_personal_data', $data );
	}
	return $data;
}

/**
 * Turns a path inside the plugin (assets/…) into a URL; full URLs pass through.
 *
 * @param string $path Path or URL.
 * @return string
 */
function googlish_personal_url( $path ) {
	if ( '' === (string) $path || preg_match( '#^(https?:)?//|^data:#', $path ) ) {
		return (string) $path;
	}
	return plugins_url( ltrim( $path, '/' ), __FILE__ );
}

/**
 * The theme patterns this plugin replaces, and the plugin pattern that replaces each.
 *
 * @return array
 */
function googlish_personal_map() {
	return array(
		'cloudblogger/header'      => 'googlish-personal/header',
		'cloudblogger/footer'      => 'googlish-personal/footer',
		'cloudblogger/home-author' => 'googlish-personal/home-author',
		'cloudblogger/home-tools'  => 'googlish-personal/home-tools',
	);
}

/**
 * Register the personal patterns (hidden from the inserter).
 */
function googlish_personal_register_patterns() {
	$d     = googlish_personal_data();
	$parts = array(
		'header'      => array( __( 'Header (personal)', 'googlish-personal' ), 'googlish_personal_header' ),
		'footer'      => array( __( 'Footer (personal)', 'googlish-personal' ), 'googlish_personal_footer' ),
		'home-author' => array( __( 'Meet the author (personal)', 'googlish-personal' ), 'googlish_personal_author' ),
		'home-tools'  => array( __( 'Tools (personal)', 'googlish-personal' ), 'googlish_personal_tools' ),
	);
	foreach ( $parts as $slug => $p ) {
		register_block_pattern(
			'googlish-personal/' . $slug,
			array(
				'title'    => $p[0],
				'content'  => call_user_func( $p[1], $d ),
				'inserter' => false,
			)
		);
	}
}
add_action( 'init', 'googlish_personal_register_patterns', 20 );

/**
 * When the theme asks for one of its generic patterns, hand it the personal one instead.
 *
 * @param array $block Parsed block.
 * @return array
 */
function googlish_personal_swap_patterns( $block ) {
	if ( isset( $block['blockName'], $block['attrs']['slug'] ) && 'core/pattern' === $block['blockName'] ) {
		$map = googlish_personal_map();
		if ( isset( $map[ $block['attrs']['slug'] ] ) ) {
			$block['attrs']['slug'] = $map[ $block['attrs']['slug'] ];
		}
	}
	return $block;
}
add_filter( 'render_block_data', 'googlish_personal_swap_patterns' );

/**
 * Your X handle on "Share to X" links.
 *
 * @param string $via Current handle.
 * @return string
 */
function googlish_personal_x_handle( $via ) {
	$d = googlish_personal_data();
	return ! empty( $d['site']['x_handle'] ) ? $d['site']['x_handle'] : $via;
}
add_filter( 'cb_share_x_via', 'googlish_personal_x_handle' );

/**
 * Your search placeholder (the theme defaults to "Search <site name>").
 *
 * @param string $text Placeholder.
 * @return string
 */
function googlish_personal_search_placeholder( $text ) {
	$d = googlish_personal_data();
	return ! empty( $d['site']['search_placeholder'] ) ? $d['site']['search_placeholder'] : $text;
}
add_filter( 'googlish_search_placeholder', 'googlish_personal_search_placeholder' );
