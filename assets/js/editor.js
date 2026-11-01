/* Editor previews for CloudBlogger's server-rendered blocks. No build step: plain wp.* globals. */
(function (wp) {
	if (!wp || !wp.blocks) { return; }
	var el = wp.element.createElement;
	var SSR = wp.serverSideRender;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var CheckboxControl = wp.components.CheckboxControl;
	var __ = wp.i18n.__;

	var ITEMS = [
		['author', __('Author', 'cloudblogger')],
		['date', __('Date', 'cloudblogger')],
		['updated', __('Updated date', 'cloudblogger')],
		['readtime', __('Read time', 'cloudblogger')],
		['views', __('Views', 'cloudblogger')],
		['comments', __('Comments', 'cloudblogger')],
		['likes', __('Likes', 'cloudblogger')]
	];

	function preview(name) {
		return function (props) {
			return el('div', useBlockProps(), el(SSR, { block: name, attributes: props.attributes, skipBlockSupportAttributes: true }));
		};
	}

	wp.blocks.registerBlockType('cloudblogger/post-stats', {
		apiVersion: 3,
		title: __('Post stats', 'cloudblogger'),
		icon: 'chart-bar',
		category: 'theme',
		usesContext: ['postId', 'postType'],
		attributes: { items: { type: 'array', default: ['author', 'date', 'readtime', 'views', 'comments', 'likes'] } },
		edit: function (props) {
			var items = props.attributes.items || [];
			return el(wp.element.Fragment, null,
				el(InspectorControls, null,
					el(PanelBody, { title: __('Show', 'cloudblogger') },
						ITEMS.map(function (it) {
							return el(CheckboxControl, {
								key: it[0],
								label: it[1],
								checked: items.indexOf(it[0]) !== -1,
								onChange: function (on) {
									var next = ITEMS.map(function (x) { return x[0]; }).filter(function (k) {
										return k === it[0] ? on : items.indexOf(k) !== -1;
									});
									props.setAttributes({ items: next });
								}
							});
						})
					)
				),
				preview('cloudblogger/post-stats')(props)
			);
		},
		save: function () { return null; }
	});

	[
		['cloudblogger/site-stats', __('Blog stats', 'cloudblogger'), 'chart-area'],
		['cloudblogger/must-read', __('Must read', 'cloudblogger'), 'star-filled'],
		['cloudblogger/random-posts', __('Random picks', 'cloudblogger'), 'randomize'],
		['cloudblogger/recent-comments', __('Recent conversations', 'cloudblogger'), 'admin-comments'],
		['cloudblogger/share', __('Share post', 'cloudblogger'), 'share'],
		['cloudblogger/like', __('Like button', 'cloudblogger'), 'heart'],
		['cloudblogger/feedback', __('Was this helpful?', 'cloudblogger'), 'thumbs-up']
	].forEach(function (b) {
		wp.blocks.registerBlockType(b[0], {
			apiVersion: 3,
			title: b[1],
			icon: b[2],
			category: 'theme',
			usesContext: ['postId', 'postType'],
			edit: preview(b[0]),
			save: function () { return null; }
		});
	});
})(window.wp);
