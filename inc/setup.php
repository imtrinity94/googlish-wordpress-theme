<?php
/**
 * Theme setup: supports, assets, block styles, pattern categories.
 *
 * @package CloudBlogger
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme supports and editor styles.
 */
function cb_setup() {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'post-thumbnails' );
	add_editor_style( 'assets/css/theme.css' );
}
add_action( 'after_setup_theme', 'cb_setup' );

/**
 * Apply the saved light/dark choice before first paint to avoid a flash.
 */
function cb_theme_mode_script() {
	$js = "(function(){try{var t=localStorage.getItem('cb-theme');if(t==='dark'||t==='light'){document.documentElement.setAttribute('data-theme',t);}}catch(e){}})();";
	wp_print_inline_script_tag( $js, array( 'id' => 'cb-theme-mode' ) );
}
add_action( 'wp_head', 'cb_theme_mode_script', 0 );

/**
 * Front-end assets.
 */
function cb_enqueue_assets() {
	$uri = get_template_directory_uri();

	wp_enqueue_style( 'cloudblogger', $uri . '/assets/css/theme.css', array(), CB_THEME_VERSION );

	wp_register_script( 'cb-highlight', $uri . '/assets/vendor/highlight.min.js', array(), '11.12.0', array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_enqueue_script( 'cloudblogger', $uri . '/assets/js/theme.js', array( 'cb-highlight' ), CB_THEME_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );

	wp_localize_script(
		'cloudblogger',
		'cbTheme',
		array(
			'rest'     => esc_url_raw( rest_url( 'cloudblogger/v1/' ) ),
			'siteHost' => wp_parse_url( home_url(), PHP_URL_HOST ),
			'i18n'     => array(
				'copy'       => __( 'Copy', 'cloudblogger' ),
				'copied'     => __( 'Copied', 'cloudblogger' ),
				'copyLabel'  => __( 'Copy code to clipboard', 'cloudblogger' ),
				'explain'    => __( 'Explain', 'cloudblogger' ),
				'output'     => __( 'Output', 'cloudblogger' ),
				'link'       => __( 'Link', 'cloudblogger' ),
				'like'       => __( 'Like', 'cloudblogger' ),
				'liked'      => __( 'Liked', 'cloudblogger' ),
				'code'       => __( 'Code', 'cloudblogger' ),
				/* translators: %d: number of lines */
				'showAll'    => __( 'Show all %d lines', 'cloudblogger' ),
				'showLess'   => __( 'Show less', 'cloudblogger' ),
				'onThisPage' => __( 'In this article', 'cloudblogger' ),
				'start'      => __( 'Start', 'cloudblogger' ),
				'end'        => __( 'End', 'cloudblogger' ),
				'linkTo'     => __( 'Link to this section', 'cloudblogger' ),
				'copyLink'   => __( 'Copy link', 'cloudblogger' ),
				'imageViewer' => __( 'Image viewer', 'cloudblogger' ),
				'close'      => __( 'Close', 'cloudblogger' ),
				'prevImage'  => __( 'Previous image', 'cloudblogger' ),
				'nextImage'  => __( 'Next image', 'cloudblogger' ),
				'enlarge'    => __( 'Open larger image', 'cloudblogger' ),
				'copiedLink' => __( 'Link copied', 'cloudblogger' ),
				'fbYes'      => __( 'Thanks for letting me know.', 'cloudblogger' ),
				'fbNo'       => __( 'Thanks. Tell me what was missing so I can fix it.', 'cloudblogger' ),
				'fbSent'     => __( 'Feedback sent. Thank you, I read every one.', 'cloudblogger' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'cb_enqueue_assets' );

/**
 * Block styles for callouts, steps and the "Tested on" line.
 */
function cb_register_block_styles() {
	$callouts = array(
		'cb-note'      => __( 'Note', 'cloudblogger' ),
		'cb-tip'       => __( 'Pro tip', 'cloudblogger' ),
		'cb-important' => __( 'Important', 'cloudblogger' ),
		'cb-caution'   => __( 'Caution', 'cloudblogger' ),
		'cb-critical'  => __( 'Warning', 'cloudblogger' ),
	);
	foreach ( array( 'core/group', 'core/paragraph' ) as $block ) {
		foreach ( $callouts as $name => $label ) {
			register_block_style( $block, array( 'name' => $name, 'label' => $label ) );
		}
	}
}
add_action( 'init', 'cb_register_block_styles' );

/**
 * Pattern categories.
 */
function cb_register_pattern_categories() {
	register_block_pattern_category( 'cloudblogger-callouts', array( 'label' => __( 'Googlish: callouts', 'cloudblogger' ) ) );
	register_block_pattern_category( 'cloudblogger-demo', array( 'label' => __( 'Googlish: demos', 'cloudblogger' ) ) );
	register_block_pattern_category( 'cloudblogger-home', array( 'label' => __( 'Googlish: home page', 'cloudblogger' ) ) );
	register_block_pattern_category( 'cloudblogger-docs', array( 'label' => __( 'Googlish: docs', 'cloudblogger' ) ) );
}
add_action( 'init', 'cb_register_pattern_categories' );

/**
 * Code Block Pro blocks: render them as the theme's own code blocks, so every snippet on the
 * site shares one look, follows light/dark mode and gets the language colour, copy button and
 * line numbers. The language you pick in Code Block Pro is kept; a "header" text becomes the
 * file name. Add the CSS class "cb-keep-cbp" to a Code Block Pro block to keep the plugin's own look.
 *
 * @param string $content Rendered block.
 * @param array  $block   Parsed block.
 * @return string
 */
function cb_render_code_block_pro( $content, $block ) {
	if ( 'kevinbatdorf/code-block-pro' !== $block['blockName'] ) {
		return $content;
	}
	$a = isset( $block['attrs'] ) ? $block['attrs'] : array();
	if ( ! empty( $a['className'] ) && false !== strpos( $a['className'], 'cb-keep-cbp' ) ) {
		return $content;
	}
	if ( ! isset( $a['code'] ) ) {
		return $content;
	}
	$code    = html_entity_decode( (string) $a['code'], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$classes = array( 'wp-block-code' );
	$map     = array( 'js' => 'javascript', 'ts' => 'typescript', 'sh' => 'bash', 'zsh' => 'bash', 'ps1' => 'powershell', 'yml' => 'yaml', 'txt' => 'plaintext', 'html' => 'xml' );
	if ( ! empty( $a['language'] ) && 'plaintext' !== $a['language'] && 'ansi' !== $a['language'] ) {
		$lang      = strtolower( preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $a['language'] ) );
		$classes[] = 'lang-' . ( isset( $map[ $lang ] ) ? $map[ $lang ] : $lang );
	}
	if ( ! empty( $a['headerString'] ) ) {
		$file = preg_replace( '/[^A-Za-z0-9._-]/', '', (string) $a['headerString'] );
		if ( '' !== $file ) {
			$classes[] = 'file-' . $file;
		}
	}
	if ( isset( $a['lineNumbers'] ) && false === $a['lineNumbers'] && substr_count( $code, "\n" ) < 3 ) {
		$classes[] = 'no-lines';
	}
	return '<pre class="' . esc_attr( implode( ' ', $classes ) ) . '"><code>' . esc_html( $code ) . '</code></pre>';
}
add_filter( 'render_block', 'cb_render_code_block_pro', 10, 2 );

/**
 * Sticky posts belong in "Must read" only. Every other post list on the site (featured
 * story, Just in, archives, More from the blog) shows posts in plain date order, so
 * sticky posts no longer get pinned to the top of every page of a paginated list.
 *
 * @param array $query Query vars built from a Query Loop block.
 * @return array
 */
function cb_query_loop_ignore_sticky( $query ) {
	if ( empty( $query['post__in'] ) ) {
		$query['ignore_sticky_posts'] = true;
	}
	return $query;
}
add_filter( 'query_loop_block_query_vars', 'cb_query_loop_ignore_sticky' );

/**
 * Same for the main query on the blog home, in case a template uses it.
 *
 * @param WP_Query $q Query.
 */
function cb_main_query_ignore_sticky( $q ) {
	if ( ! is_admin() && $q->is_main_query() && $q->is_home() ) {
		$q->set( 'ignore_sticky_posts', true );
	}
}
add_action( 'pre_get_posts', 'cb_main_query_ignore_sticky' );
