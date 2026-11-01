/* CloudBlogger theme front-end behaviour: theme toggle, code blocks, workflow TOC, heading anchors, reading progress. */
(function () {
	'use strict';

	var doc = document;
	var root = doc.documentElement;
	var i18n = (window.cbTheme && window.cbTheme.i18n) || {};
	function t(key, fallback) { return i18n[key] || fallback; }

	/* ---------------- Theme toggle ---------------- */
	function currentTheme() {
		var set = root.getAttribute('data-theme');
		if (set) { return set; }
		return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
	}
	function bindToggles() {
		var buttons = doc.querySelectorAll('.cb-theme-toggle');
		Array.prototype.forEach.call(buttons, function (btn) {
			btn.setAttribute('aria-pressed', currentTheme() === 'dark' ? 'true' : 'false');
			btn.addEventListener('click', function () {
				var next = currentTheme() === 'dark' ? 'light' : 'dark';
				root.setAttribute('data-theme', next);
				try { window.localStorage.setItem('cb-theme', next); } catch (e) { /* storage unavailable */ }
				Array.prototype.forEach.call(buttons, function (b) { b.setAttribute('aria-pressed', next === 'dark' ? 'true' : 'false'); });
			});
		});
	}

	/* ---------------- Code blocks ---------------- */
	var LANG_NAMES = {
		javascript: 'JavaScript', js: 'JavaScript', typescript: 'TypeScript', ts: 'TypeScript', python: 'Python', py: 'Python',
		bash: 'Bash', shell: 'Shell', sh: 'Shell', powershell: 'PowerShell', ps1: 'PowerShell', json: 'JSON', yaml: 'YAML', yml: 'YAML',
		xml: 'XML', html: 'HTML', sql: 'SQL', ini: 'INI', css: 'CSS', plaintext: 'Text', text: 'Text', log: 'Output', output: 'Output', http: 'HTTP'
	};
	var ALIAS = { js: 'javascript', ts: 'typescript', py: 'python', sh: 'bash', ps1: 'powershell', yml: 'yaml', html: 'xml', vro: 'javascript', text: 'plaintext', log: 'plaintext', output: 'plaintext' };
	var AUTO_SUBSET = ['javascript', 'python', 'bash', 'powershell', 'json', 'yaml', 'xml', 'sql', 'ini', 'typescript'];

	var ICON_COPY = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1"/></svg>';
	var ICON_CHECK = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>';
	var ICON_SPARK = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z"/></svg>';

	function classValue(el, prefixes) {
		if (!el) { return ''; }
		var list = (el.getAttribute('class') || '').split(/\s+/);
		for (var i = 0; i < list.length; i++) {
			for (var p = 0; p < prefixes.length; p++) {
				if (list[i].indexOf(prefixes[p]) === 0 && list[i].length > prefixes[p].length) {
					return list[i].slice(prefixes[p].length);
				}
			}
		}
		return '';
	}

	function looksLikeLog(text) {
		var lines = text.split('\n');
		var hits = 0;
		for (var i = 0; i < lines.length; i++) {
			if (/^\s*(INFO|ERROR|WARN|WARNING|DEBUG|TRACE)\b/.test(lines[i]) || /^\d+\/\d+\s/.test(lines[i]) || /^\s*[\w.]*(Error|Exception)\b.*:/.test(lines[i]) || /^\s*\[?\d{4}-\d{2}-\d{2}[T ]\d{2}:/.test(lines[i])) { hits++; }
		}
		return hits > 0 && hits >= lines.length / 2;
	}

	/* Cheap, blog-specific hints before falling back to highlight.js auto-detection. */
	function guessLang(text) {
		var s = text.trim();
		if (/^[\[{]/.test(s)) { try { JSON.parse(s); return 'json'; } catch (e) { /* not JSON */ } }
		if (/^<\?xml|^<[a-zA-Z][\w:-]*[\s>]/.test(s)) { return 'xml'; }
		if (/^\s*(def |import |from \S+ import |class \w+.*:\s*$)/m.test(s) && /:\s*$/m.test(s)) { return 'python'; }
		if (/\$\w+\s*=|\b(Get|Set|New|Remove|Connect|Invoke|Import|Write)-[A-Z]\w+/.test(s)) { return 'powershell'; }
		if (/^\s*(\$ |sudo |curl |wget |npm |npx |pip |bash |sh |cd |ls |cat |kubectl |vracli |systemctl |export |git |docker |ssh |scp |chmod |echo |openssl )/m.test(s)) { return 'bash'; }
		if (/\b(var|function)\s+\w+|System\.(log|warn|error|getModule)|Server\.\w+\(|\bnew\s+[A-Z]\w*\(|===|!==/.test(s)) { return 'javascript'; }
		if (/^(formatVersion|inputs|resources|name|version|apiVersion|kind):/m.test(s) && !/;\s*$/m.test(s)) { return 'yaml'; }
		return '';
	}

	function copyText(text) {
		if (navigator.clipboard && window.isSecureContext) {
			return navigator.clipboard.writeText(text);
		}
		return new Promise(function (resolve, reject) {
			var ta = doc.createElement('textarea');
			ta.value = text;
			ta.setAttribute('readonly', '');
			ta.style.position = 'fixed';
			ta.style.opacity = '0';
			doc.body.appendChild(ta);
			ta.select();
			try { doc.execCommand('copy') ? resolve() : reject(); } catch (e) { reject(e); }
			doc.body.removeChild(ta);
		});
	}

	function enhanceCode(scope) {
		var blocks = scope.querySelectorAll('pre.wp-block-code');
		var aiEnabled = !!(window.cbAI && window.cbAI.explain);
		Array.prototype.forEach.call(blocks, function (pre, index) {
			if (pre.closest('.cb-code')) { return; }
			var code = pre.querySelector('code') || pre;
			var text = code.textContent.replace(/\n$/, '');
			var langRaw = (classValue(pre, ['lang-', 'language-']) || classValue(code, ['lang-', 'language-'])).toLowerCase();
			var file = classValue(pre, ['file-', 'filename-']);
			var lang = ALIAS[langRaw] || langRaw;
			var label = '';

			var trimmed = text.trim();
			var oneLine = trimmed.indexOf('\n') === -1;
			if (!lang && /^(https?:\/\/|www\.)\S+$/i.test(trimmed)) { lang = 'plaintext'; label = t('link', 'Link'); }
			if (!lang && looksLikeLog(text)) { lang = 'plaintext'; label = t('output', 'Output'); }
			if (!lang) { lang = guessLang(text); }
			if (!lang && oneLine) { lang = 'plaintext'; }

			if (window.hljs) {
				try {
					var result;
					if (lang && lang !== 'plaintext' && window.hljs.getLanguage(lang)) {
						result = window.hljs.highlight(text, { language: lang, ignoreIllegals: true });
					} else if (!lang) {
						result = window.hljs.highlightAuto(text, AUTO_SUBSET);
						if (result.relevance >= 8 && text.split('\n').length >= 3) { lang = result.language; } else { result = null; lang = 'plaintext'; }
					}
					if (result) { code.innerHTML = result.value; code.classList.add('hljs'); }
				} catch (e) { /* leave plain */ }
			}
			if (!label) { label = LANG_NAMES[langRaw] || LANG_NAMES[lang] || (lang ? lang.charAt(0).toUpperCase() + lang.slice(1) : t('code', 'Code')); }

			var lineCount = text.split('\n').length;
			var showLines = lineCount >= 4 && !pre.classList.contains('no-lines') && label !== t('output', 'Output') && label !== t('link', 'Link');

			var wrap = doc.createElement('div');
			wrap.className = 'cb-code';
			wrap.setAttribute('data-cb-index', String(index));
			wrap.setAttribute('data-lang', label === t('output', 'Output') ? 'output' : (label === t('link', 'Link') ? 'link' : (lang || 'plaintext')));

			var bar = doc.createElement('div');
			bar.className = 'cb-code__bar';
			var langEl = doc.createElement('span');
			langEl.className = 'cb-code__lang';
			langEl.textContent = label;
			bar.appendChild(langEl);
			if (file) {
				var fileEl = doc.createElement('span');
				fileEl.className = 'cb-code__file';
				fileEl.textContent = file;
				bar.appendChild(fileEl);
			}
			var actions = doc.createElement('div');
			actions.className = 'cb-code__actions';

			if (aiEnabled && lineCount >= 2 && label !== t('output', 'Output')) {
				var explain = doc.createElement('button');
				explain.type = 'button';
				explain.className = 'cb-code__btn cb-code__btn--explain';
				explain.innerHTML = ICON_SPARK + '<span>' + t('explain', 'Explain') + '</span>';
				explain.setAttribute('data-cb-index', String(index));
				actions.appendChild(explain);
			}

			var copy = doc.createElement('button');
			copy.type = 'button';
			copy.className = 'cb-code__btn cb-code__btn--copy';
			copy.innerHTML = ICON_COPY + '<span>' + t('copy', 'Copy') + '</span>';
			copy.setAttribute('aria-label', t('copyLabel', 'Copy code to clipboard'));
			copy.addEventListener('click', function () {
				copyText(text).then(function () {
					copy.classList.add('is-copied');
					copy.innerHTML = ICON_CHECK + '<span>' + t('copied', 'Copied') + '</span>';
					setTimeout(function () {
						copy.classList.remove('is-copied');
						copy.innerHTML = ICON_COPY + '<span>' + t('copy', 'Copy') + '</span>';
					}, 1800);
				});
			});
			actions.appendChild(copy);
			bar.appendChild(actions);

			var scroll = doc.createElement('div');
			scroll.className = 'cb-code__scroll';
			if (showLines) {
				var gutter = doc.createElement('div');
				gutter.className = 'cb-code__gutter';
				gutter.setAttribute('aria-hidden', 'true');
				var nums = [];
				for (var n = 1; n <= lineCount; n++) { nums.push(n); }
				gutter.textContent = nums.join('\n');
				scroll.appendChild(gutter);
			}

			pre.parentNode.insertBefore(wrap, pre);
			wrap.appendChild(bar);
			scroll.appendChild(pre);
			wrap.appendChild(scroll);

			if (lineCount > 30) {
				wrap.classList.add('is-long', 'is-collapsed');
				var more = doc.createElement('button');
				more.type = 'button';
				more.className = 'cb-code__more';
				more.textContent = t('showAll', 'Show all %d lines').replace('%d', lineCount);
				more.setAttribute('aria-expanded', 'false');
				more.addEventListener('click', function () {
					var collapsed = wrap.classList.toggle('is-collapsed');
					more.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
					more.textContent = collapsed ? t('showAll', 'Show all %d lines').replace('%d', lineCount) : t('showLess', 'Show less');
				});
				wrap.appendChild(more);
			}

			var explainOut = doc.createElement('div');
			explainOut.className = 'cb-code__explain';
			explainOut.hidden = true;
			explainOut.setAttribute('aria-live', 'polite');
			wrap.appendChild(explainOut);
		});
	}

	/* ---------------- Headings, TOC ---------------- */
	function slugify(s) {
		return s.toLowerCase().replace(/<[^>]+>/g, '').replace(/[^\w\s-]/g, '').trim().replace(/[\s_]+/g, '-').replace(/-+/g, '-').slice(0, 60) || 'section';
	}

	function collectHeadings(prose) {
		var all = prose.querySelectorAll('h1, h2, h3');
		var used = {};
		var list = [];
		Array.prototype.forEach.call(all, function (h) {
			if (h.closest('.cb-tabs, .cb-feedback, .cb-download, .cb-compare, [class*="is-style-cb-"], .wp-block-kevinbatdorf-code-block-pro, .sharedaddy, .jp-relatedposts, .sd-block, [class*="wp-block-jetpack-"], .jetpack-likes-widget-wrapper')) { return; }
			var text = h.textContent.trim();
			if (!text) { return; }
			if (!h.id) {
				var base = slugify(text);
				var id = base;
				var k = 2;
				while (used[id] || doc.getElementById(id)) { id = base + '-' + k; k++; }
				h.id = id;
			}
			used[h.id] = true;
			list.push(h);

			var a = doc.createElement('a');
			a.className = 'cb-anchor';
			a.href = '#' + h.id;
			a.textContent = '#';
			a.setAttribute('aria-label', t('linkTo', 'Link to this section') + ': ' + text);
			h.appendChild(a);
		});
		return list;
	}

	function buildToc(headings) {
		var ol = doc.createElement('ol');
		var start = doc.createElement('li');
		start.className = 'cb-toc__start';
		start.textContent = t('start', 'Start');
		ol.appendChild(start);
		headings.forEach(function (h) {
			var li = doc.createElement('li');
			li.className = (h.tagName === 'H2' || h.tagName === 'H1') ? 'cb-toc__h2' : 'cb-toc__h3';
			var a = doc.createElement('a');
			a.href = '#' + h.id;
			var clone = h.cloneNode(true);
			var anchor = clone.querySelector('.cb-anchor');
			if (anchor) { anchor.parentNode.removeChild(anchor); }
			a.textContent = clone.textContent.trim();
			a.setAttribute('data-target', h.id);
			li.appendChild(a);
			ol.appendChild(li);
		});
		var end = doc.createElement('li');
		end.className = 'cb-toc__end';
		end.textContent = t('end', 'End');
		ol.appendChild(end);
		return ol;
	}

	function initToc() {
		var prose = doc.querySelector('.cb-prose');
		if (!prose) { return; }
		var headings = collectHeadings(prose);
		var navs = doc.querySelectorAll('[data-cb-toc]');
		if (!navs.length) { return; }
		var h2count = headings.filter(function (h) { return h.tagName === 'H2' || h.tagName === 'H1'; }).length;
		if (h2count < 2) {
			Array.prototype.forEach.call(navs, function (n) { n.hidden = true; });
			var article = doc.querySelector('.cb-article');
			if (article) { article.classList.add('cb-toc-empty'); }
			return;
		}

		var tocs = [];
		Array.prototype.forEach.call(navs, function (nav) {
			nav.hidden = false;
			var title = doc.createElement('p');
			title.className = 'cb-toc__title';
			title.textContent = t('onThisPage', 'In this article');
			if (!nav.closest('.cb-toc-mobile')) { nav.appendChild(title); }
			nav.appendChild(buildToc(headings));
			tocs.push(nav);
			var details = nav.closest('.cb-toc-mobile');
			if (details) {
				details.classList.add('has-items');
				nav.addEventListener('click', function (e) { if (e.target.tagName === 'A') { details.open = false; } });
			}
		});

		var headerH = 80;
		var ticking = false;
		function update() {
			ticking = false;
			var activeIndex = -1;
			for (var i = 0; i < headings.length; i++) {
				if (headings[i].getBoundingClientRect().top - headerH <= 0) { activeIndex = i; } else { break; }
			}
			var proseRect = prose.getBoundingClientRect();
			var finished = proseRect.bottom < window.innerHeight * 0.6;
			tocs.forEach(function (nav) {
				var items = nav.querySelectorAll('li.cb-toc__h2, li.cb-toc__h3');
				var ol = nav.querySelector('ol');
				Array.prototype.forEach.call(items, function (li, idx) {
					li.classList.toggle('is-active', idx === activeIndex && !finished);
					li.classList.toggle('is-done', idx < activeIndex || (finished && idx <= activeIndex));
				});
				nav.classList.toggle('is-finished', finished);
				var target = finished ? nav.querySelector('.cb-toc__end') : (activeIndex >= 0 ? items[activeIndex] : null);
				var px = target ? target.offsetTop + 8 : 0;
				ol.style.setProperty('--cb-toc-progress', px + 'px');
				if (target && nav.closest('.cb-article__rail') && nav.scrollHeight > nav.clientHeight) {
					var tTop = target.offsetTop;
					if (tTop < nav.scrollTop || tTop > nav.scrollTop + nav.clientHeight - 40) { nav.scrollTop = tTop - 60; }
				}
			});
		}
		window.addEventListener('scroll', function () { if (!ticking) { ticking = true; window.requestAnimationFrame(update); } }, { passive: true });
		window.addEventListener('resize', update);
		update();
	}

	/* ---------------- Reading progress ---------------- */
	function initProgress() {
		var prose = doc.querySelector('.cb-prose');
		if (!prose) { return; }
		var bar = doc.createElement('div');
		bar.className = 'cb-progress';
		bar.setAttribute('aria-hidden', 'true');
		doc.body.appendChild(bar);
		var ticking = false;
		function update() {
			ticking = false;
			var r = prose.getBoundingClientRect();
			var total = r.height - window.innerHeight * 0.5;
			var p = total > 0 ? Math.min(1, Math.max(0, -r.top / total)) : 0;
			bar.style.setProperty('--cb-read', p.toFixed(4));
		}
		window.addEventListener('scroll', function () { if (!ticking) { ticking = true; window.requestAnimationFrame(update); } }, { passive: true });
		update();
	}

	/* ---------------- Header: shadow on scroll, post title on articles ---------------- */
	function initHeader() {
		var header = doc.querySelector('.cb-header');
		if (!header) { return; }
		var title = doc.querySelector('.cb-article-hero .wp-block-post-title, .cb-article-hero h1');
		var label = null;
		if (title) {
			label = doc.createElement('div');
			label.className = 'cb-header__title';
			label.setAttribute('aria-hidden', 'true');
			label.textContent = title.textContent.trim();
			(header.firstElementChild || header).appendChild(label);
		}
		var ticking = false;
		function update() {
			ticking = false;
			header.classList.toggle('is-scrolled', window.scrollY > 4);
			if (title) { header.classList.toggle('show-title', title.getBoundingClientRect().bottom < 0); }
		}
		window.addEventListener('scroll', function () { if (!ticking) { ticking = true; window.requestAnimationFrame(update); } }, { passive: true });
		update();
	}

	/* ---------------- "Must read" carousel ---------------- */
	function initCarousels() {
		Array.prototype.forEach.call(doc.querySelectorAll('.cb-carousel'), function (car) {
			var track = car.querySelector('.wp-block-post-template');
			var nav = car.querySelector('.cb-carousel__nav') || (car.parentNode && car.parentNode.querySelector('.cb-carousel__nav'));
			if (!track) { return; }
			if (!track.children.length) { var sec = car.closest('.cb-mustread'); if (sec) { sec.hidden = true; } return; }
			if (!nav) { return; }
			var prev = nav.querySelector('[data-dir="-1"]');
			var next = nav.querySelector('[data-dir="1"]');
			var bar = nav.querySelector('.cb-carousel__bar');
			function step() { var card = track.children[0]; return card ? card.getBoundingClientRect().width + 20 : 300; }
			function update() {
				var max = track.scrollWidth - track.clientWidth;
				var ratio = track.scrollWidth ? track.clientWidth / track.scrollWidth : 1;
				if (prev) { prev.disabled = track.scrollLeft <= 2; }
				if (next) { next.disabled = track.scrollLeft >= max - 2; }
				if (bar) {
					bar.style.setProperty('--cb-w', Math.max(12, ratio * 100) + '%');
					var pos = max > 0 ? track.scrollLeft / max : 0;
					bar.style.setProperty('--cb-x', (pos * (100 / Math.max(ratio, .12) - 100)) + '%');
				}
				nav.hidden = max <= 2;
			}
			[prev, next].forEach(function (b) {
				if (!b) { return; }
				b.addEventListener('click', function () { track.scrollBy({ left: step() * parseInt(b.getAttribute('data-dir'), 10), behavior: 'smooth' }); });
			});
			track.addEventListener('scroll', function () { window.requestAnimationFrame(update); }, { passive: true });
			window.addEventListener('resize', update);
			update();
		});
	}

	/* ---------------- Count-up on stats (rest state already shows final value) ---------------- */
	function initCounters() {
		var nums = doc.querySelectorAll('[data-cb-count]');
		if (!nums.length || !('IntersectionObserver' in window)) { return; }
		if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }
		function fmt(n) { return n >= 10000 ? Math.round(n / 1000) + 'K' : n >= 1000 ? (Math.round(n / 100) / 10) + 'K' : String(n); }
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (!en.isIntersecting) { return; }
				io.unobserve(en.target);
				var el = en.target;
				var end = parseInt(el.getAttribute('data-cb-count'), 10) || 0;
				var suffix = end >= 1000 ? '+' : '';
				var t0 = null;
				function tick(ts) {
					if (!t0) { t0 = ts; }
					var p = Math.min(1, (ts - t0) / 1100);
					var eased = 1 - Math.pow(1 - p, 3);
					el.textContent = fmt(Math.round(end * eased)) + (p === 1 ? suffix : '');
					if (p < 1) { window.requestAnimationFrame(tick); }
				}
				window.requestAnimationFrame(tick);
			});
		}, { threshold: .6 });
		Array.prototype.forEach.call(nums, function (n) { io.observe(n); });
	}

	/* ---------------- Tabs: .cb-tabs > .cb-tab (first child .cb-tab__label names it) ---------------- */
	function initTabs() {
		Array.prototype.forEach.call(doc.querySelectorAll('.cb-tabs'), function (box, bi) {
			var panels = Array.prototype.filter.call(box.children, function (c) { return c.classList.contains('cb-tab'); });
			if (panels.length < 2) { return; }
			var list = doc.createElement('div');
			list.className = 'cb-tabs__list';
			list.setAttribute('role', 'tablist');
			var tabs = panels.map(function (p, i) {
				var lab = p.querySelector('.cb-tab__label');
				var b = doc.createElement('button');
				b.type = 'button';
				b.className = 'cb-tabs__tab';
				b.id = 'cb-tab-' + bi + '-' + i;
				b.setAttribute('role', 'tab');
				b.textContent = lab ? lab.textContent.trim() : 'Tab ' + (i + 1);
				p.id = p.id || 'cb-panel-' + bi + '-' + i;
				p.setAttribute('role', 'tabpanel');
				p.setAttribute('aria-labelledby', b.id);
				b.setAttribute('aria-controls', p.id);
				list.appendChild(b);
				return b;
			});
			function select(i, focus) {
				tabs.forEach(function (b, j) {
					b.setAttribute('aria-selected', i === j ? 'true' : 'false');
					b.tabIndex = i === j ? 0 : -1;
					panels[j].hidden = i !== j;
				});
				if (focus) { tabs[i].focus(); }
				try { window.localStorage.setItem('cb-tab:' + tabs[i].textContent, '1'); } catch (e) { /* ignore */ }
			}
			tabs.forEach(function (b, i) {
				b.addEventListener('click', function () { select(i); });
				b.addEventListener('keydown', function (e) {
					if (e.key === 'ArrowRight') { select((i + 1) % tabs.length, true); }
					if (e.key === 'ArrowLeft') { select((i - 1 + tabs.length) % tabs.length, true); }
				});
			});
			box.insertBefore(list, box.firstChild);
			box.classList.add('is-ready');
			select(0);
		});
	}

	/* ---------------- Share menu ---------------- */
	function initShare() {
		Array.prototype.forEach.call(doc.querySelectorAll('[data-cb-share]'), function (box) {
			var btn = box.querySelector('.cb-share__btn');
			var menu = box.querySelector('.cb-share__menu');
			var url = box.getAttribute('data-url') || window.location.href;
			if (!btn || !menu) { return; }
			function close() { menu.hidden = true; btn.setAttribute('aria-expanded', 'false'); }
			btn.addEventListener('click', function (e) {
				e.stopPropagation();
				if (navigator.share && window.matchMedia('(pointer: coarse)').matches) {
					navigator.share({ title: doc.title, url: url }).catch(function () {});
					return;
				}
				menu.hidden = !menu.hidden;
				btn.setAttribute('aria-expanded', menu.hidden ? 'false' : 'true');
			});
			doc.addEventListener('click', function (e) { if (!box.contains(e.target)) { close(); } });
			doc.addEventListener('keydown', function (e) { if (e.key === 'Escape') { close(); } });
			var copy = menu.querySelector('[data-cb-copy-link]');
			if (copy) {
				copy.addEventListener('click', function () {
					var label = copy.querySelector('span');
					copyText(url).then(function () {
						label.textContent = t('copiedLink', 'Link copied');
						setTimeout(function () { label.textContent = t('copyLink', 'Copy link'); close(); }, 1400);
					});
				});
			}
		});
	}

	/* ---------------- Feedback ---------------- */
	function initFeedback() {
		var root = (window.cbTheme && window.cbTheme.rest) || '';
		Array.prototype.forEach.call(doc.querySelectorAll('[data-cb-feedback]'), function (box) {
			var postId = parseInt(box.getAttribute('data-cb-feedback'), 10) || 0;
			var msg = box.querySelector('.cb-feedback__msg');
			var form = box.querySelector('.cb-feedback__form');
			var send = box.querySelector('.cb-feedback__send');
			var vote = '';
			var key = 'cb-fb-' + postId;
			function post(body) {
				if (!root) { return Promise.resolve(); }
				return fetch(root + 'feedback', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) })
					.then(function (r) { return r.json().then(function (d) { if (!r.ok) { throw new Error(d && d.message ? d.message : 'Could not send. Try again later.'); } return d; }); });
			}
			try { vote = window.localStorage.getItem(key) || ''; } catch (e) { vote = ''; }
			Array.prototype.forEach.call(box.querySelectorAll('.cb-feedback__btn'), function (b) {
				if (vote && b.getAttribute('data-vote') === vote) { b.setAttribute('aria-pressed', 'true'); }
				b.addEventListener('click', function () {
					if (vote) { return; }
					vote = b.getAttribute('data-vote');
					b.setAttribute('aria-pressed', 'true');
					try { window.localStorage.setItem(key, vote); } catch (e) { /* ignore */ }
					msg.textContent = vote === 'yes' ? t('fbYes', 'Thanks for letting me know.') : t('fbNo', 'Thanks. Tell me what was missing so I can fix it.');
					if (vote === 'no' && form.hidden) { send.click(); }
					post({ post_id: postId, vote: vote }).catch(function () { /* counted locally only */ });
				});
			});
			if (send) {
				send.addEventListener('click', function () {
					form.hidden = !form.hidden;
					send.setAttribute('aria-expanded', form.hidden ? 'false' : 'true');
					if (!form.hidden) { form.querySelector('textarea').focus(); }
				});
			}
			if (form) {
				form.addEventListener('submit', function (e) {
					e.preventDefault();
					var text = form.querySelector('textarea').value.trim();
					var hp = form.querySelector('.cb-hp');
					var btn = form.querySelector('.cb-feedback__submit');
					if (!text) { return; }
					btn.disabled = true;
					post({ post_id: postId, vote: vote, message: text, website: hp ? hp.value : '' }).then(function () {
						form.hidden = true;
						send.hidden = true;
						msg.textContent = t('fbSent', 'Feedback sent. Thank you, I read every one.');
					}).catch(function (err) {
						msg.textContent = err.message;
					}).then(function () { btn.disabled = false; });
				});
			}
		});
	}

	/* ---------------- Likes: theme Like button + counts (local likes + WordPress.com likes) ---------------- */
	function initLikes() {
		var host = window.cbTheme && window.cbTheme.siteHost;
		var root = (window.cbTheme && window.cbTheme.rest) || '';
		var ids = {};
		Array.prototype.forEach.call(doc.querySelectorAll('[data-cb-likes], [data-cb-like]'), function (el) {
			var id = el.getAttribute('data-cb-likes') || el.getAttribute('data-cb-like');
			(ids[id] = ids[id] || []).push(el);
		});
		function fmt(n) { return n === 1 ? '1 like' : n + ' likes'; }
		function paint(id, wpcom) {
			ids[id].forEach(function (el) {
				var local = parseInt(el.getAttribute('data-local'), 10) || 0;
				var total = local + (wpcom || 0);
				el.setAttribute('data-total', total);
				if (el.hasAttribute('data-cb-like')) {
					var c = el.querySelector('.cb-like__count');
					c.textContent = total;
					c.hidden = total === 0;
				} else if (total > 0) {
					el.querySelector('.cb-likes-n').textContent = fmt(total);
					el.hidden = false;
				}
			});
		}
		Object.keys(ids).forEach(function (id) {
			paint(id, 0);
			if (!host || !window.fetch) { return; }
			fetch('https://public-api.wordpress.com/rest/v1.1/sites/' + encodeURIComponent(host) + '/posts/' + encodeURIComponent(id) + '/likes')
				.then(function (r) { return r.ok ? r.json() : null; })
				.then(function (d) { ids[id].wpcom = d && d.found ? d.found : 0; paint(id, ids[id].wpcom); })
				.catch(function () {});
		});
		Array.prototype.forEach.call(doc.querySelectorAll('[data-cb-like]'), function (btn) {
			var id = btn.getAttribute('data-cb-like');
			var key = 'cb-liked-' + id;
			var liked = false;
			try { liked = window.localStorage.getItem(key) === '1'; } catch (e) { liked = false; }
			function setState(on) {
				btn.setAttribute('aria-pressed', on ? 'true' : 'false');
				btn.classList.toggle('is-liked', on);
				btn.querySelector('.cb-like__label').textContent = on ? t('liked', 'Liked') : t('like', 'Like');
			}
			setState(liked);
			btn.addEventListener('click', function () {
				if (btn.classList.contains('is-liked')) { return; }
				setState(true);
				btn.classList.add('is-bursting');
				setTimeout(function () { btn.classList.remove('is-bursting'); }, 700);
				try { window.localStorage.setItem(key, '1'); } catch (e) { /* ignore */ }
				ids[id].forEach(function (el) { el.setAttribute('data-local', (parseInt(el.getAttribute('data-local'), 10) || 0) + 1); });
				paint(id, ids[id].wpcom || 0);
				if (root) {
					fetch(root + 'like', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ post_id: parseInt(id, 10) }) }).catch(function () {});
				}
			});
		});
	}

	/* ---------------- "More from the blog": hide the post you're reading ---------------- */
	/* Random picks: the page (cached) carries a random pool; show a fresh random few on every view */
	function initRandom() {
		Array.prototype.forEach.call(doc.querySelectorAll('[data-cb-random]'), function (list) {
			var want = parseInt(list.getAttribute('data-cb-random'), 10) || 3;
			var items = Array.prototype.slice.call(list.children);
			for (var i = items.length - 1; i > 0; i--) {
				var j = Math.floor(Math.random() * (i + 1));
				var tmp = items[i]; items[i] = items[j]; items[j] = tmp;
			}
			items.forEach(function (li, k) {
				li.hidden = k >= want;
				list.appendChild(li);
			});
		});
	}

	/* ---------------- Image viewer: click any post image (unless it links elsewhere) ---------------- */
	function fullSrc(img) {
		var best = img.getAttribute('data-orig-file') || '';
		if (!best && img.srcset) {
			var maxW = 0;
			img.srcset.split(',').forEach(function (part) {
				var bits = part.trim().split(/\s+/);
				var w = parseInt(bits[1], 10) || 0;
				if (w > maxW) { maxW = w; best = bits[0]; }
			});
		}
		if (!best) { best = img.currentSrc || img.src; }
		return best.replace(/-\d+x\d+(\.(?:png|jpe?g|webp|gif))(\?.*)?$/i, '$1').replace(/[?&](w|h|resize|fit)=[^&]*/g, '');
	}
	function linksElsewhere(img) {
		var a = img.closest('a');
		if (!a) { return false; }
		var href = (a.getAttribute('href') || '').split('?')[0];
		return !/\.(png|jpe?g|webp|gif|avif)$/i.test(href) && href.indexOf('attachment') === -1;
	}
	function initLightbox() {
		var prose = doc.querySelector('.cb-prose');
		if (!prose) { return; }
		var imgs = Array.prototype.filter.call(prose.querySelectorAll('figure img, .wp-block-gallery img'), function (img) {
			return !linksElsewhere(img) && !img.closest('.cb-code, .wp-block-embed') && (img.naturalWidth || img.width || 200) >= 120;
		});
		if (!imgs.length) { return; }
		var ICON_X = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>';
		var ICON_L = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg>';
		var ICON_R = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg>';
		var box = doc.createElement('div');
		box.className = 'cb-lightbox';
		box.hidden = true;
		box.setAttribute('role', 'dialog');
		box.setAttribute('aria-modal', 'true');
		box.setAttribute('aria-label', t('imageViewer', 'Image viewer'));
		box.innerHTML = '<div class="cb-lightbox__top"><span class="cb-lightbox__count"></span><button type="button" class="cb-lightbox__btn cb-lightbox__close" aria-label="' + t('close', 'Close') + '">' + ICON_X + '</button></div>'
			+ '<div class="cb-lightbox__stage"><button type="button" class="cb-lightbox__btn cb-lightbox__prev" aria-label="' + t('prevImage', 'Previous image') + '">' + ICON_L + '</button>'
			+ '<img class="cb-lightbox__img" alt=""><button type="button" class="cb-lightbox__btn cb-lightbox__next" aria-label="' + t('nextImage', 'Next image') + '">' + ICON_R + '</button></div>'
			+ '<p class="cb-lightbox__cap"></p>';
		doc.body.appendChild(box);
		var big = box.querySelector('.cb-lightbox__img');
		var cap = box.querySelector('.cb-lightbox__cap');
		var count = box.querySelector('.cb-lightbox__count');
		var prev = box.querySelector('.cb-lightbox__prev');
		var next = box.querySelector('.cb-lightbox__next');
		var closeBtn = box.querySelector('.cb-lightbox__close');
		var current = 0;
		var opener = null;

		function show(i) {
			current = (i + imgs.length) % imgs.length;
			var img = imgs[current];
			var fig = img.closest('figure');
			var c = fig ? fig.querySelector('figcaption') : null;
			big.classList.add('is-loading');
			big.onload = function () { big.classList.remove('is-loading'); };
			big.onerror = function () { big.onerror = null; big.src = img.currentSrc || img.src; };
			big.src = fullSrc(img);
			big.alt = img.alt || '';
			cap.textContent = c ? c.textContent.trim() : (img.alt || '');
			count.textContent = imgs.length > 1 ? (current + 1) + ' / ' + imgs.length : '';
			prev.hidden = next.hidden = imgs.length < 2;
			var ahead = imgs[(current + 1) % imgs.length];
			if (ahead) { (new Image()).src = fullSrc(ahead); }
		}
		function open(i) {
			opener = doc.activeElement;
			box.hidden = false;
			doc.body.classList.add('cb-no-scroll');
			show(i);
			window.requestAnimationFrame(function () { box.classList.add('is-open'); });
			closeBtn.focus();
		}
		function close() {
			box.classList.remove('is-open');
			doc.body.classList.remove('cb-no-scroll');
			setTimeout(function () { box.hidden = true; big.removeAttribute('src'); }, 180);
			if (opener && opener.focus) { opener.focus(); }
		}
		imgs.forEach(function (img, i) {
			img.classList.add('cb-zoomable');
			img.setAttribute('tabindex', '0');
			img.setAttribute('role', 'button');
			img.setAttribute('aria-label', (img.alt ? img.alt + '. ' : '') + t('enlarge', 'Open larger image'));
			img.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(i); } });
		});
		// Capture phase, so this runs before WordPress's own single-image lightbox.
		doc.addEventListener('click', function (e) {
			var img = e.target.closest ? e.target.closest('img.cb-zoomable') : null;
			var btn = !img && e.target.closest ? e.target.closest('.wp-lightbox-container button') : null;
			if (btn) { img = btn.parentNode.querySelector('img.cb-zoomable'); }
			if (!img) { return; }
			var i = imgs.indexOf(img);
			if (i === -1) { return; }
			e.preventDefault();
			e.stopImmediatePropagation();
			open(i);
		}, true);
		prev.addEventListener('click', function () { show(current - 1); });
		next.addEventListener('click', function () { show(current + 1); });
		closeBtn.addEventListener('click', close);
		box.addEventListener('click', function (e) { if (e.target === box || e.target.classList.contains('cb-lightbox__stage')) { close(); } });
		doc.addEventListener('keydown', function (e) {
			if (box.hidden) { return; }
			if (e.key === 'Escape') { close(); }
			if (e.key === 'ArrowRight') { show(current + 1); }
			if (e.key === 'ArrowLeft') { show(current - 1); }
			if (e.key === 'Tab') {
				var f = [closeBtn, prev, next].filter(function (b) { return !b.hidden; });
				var idx = f.indexOf(doc.activeElement);
				e.preventDefault();
				f[(idx + (e.shiftKey ? -1 : 1) + f.length) % f.length].focus();
			}
		});
		var x0 = null;
		box.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
		box.addEventListener('touchend', function (e) {
			if (x0 === null) { return; }
			var dx = e.changedTouches[0].clientX - x0;
			if (Math.abs(dx) > 50) { show(current + (dx < 0 ? 1 : -1)); }
			x0 = null;
		});
	}

	/* ---------------- End of post: Like into the feedback bar, subscribe form as a card ---------------- */
	function tidyPostEnd() {
		var body = doc.querySelector('.cb-article__body');
		if (!body) { return; }
		var sub = body.querySelector('.wp-block-jetpack-subscriptions');
		if (sub) {
			var card = sub;
			while (card.parentElement && card.parentElement !== body) { card = card.parentElement; }
			card.classList.add('cb-subscribe');
		}
	}

	/* ---------------- Boot ---------------- */
	function boot() {
		bindToggles();
		var prose = doc.querySelector('.cb-prose') || doc.body;
		enhanceCode(prose);
		initToc();
		initProgress();
		initHeader();
		initCarousels();
		initCounters();
		initTabs();
		initShare();
		initFeedback();
		initLikes();
		initRandom();
		initLightbox();
		tidyPostEnd();
		doc.dispatchEvent(new CustomEvent('cb:ready'));
	}
	if (doc.readyState === 'loading') { doc.addEventListener('DOMContentLoaded', boot); } else { boot(); }
})();
