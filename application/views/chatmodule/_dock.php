<?php
/**
 * Chat - floating dock launcher.
 * -----------------------------------------------------------------------------
 * Included once from common/nav-menu.php, so every PMS page carries a chat
 * bubble in the bottom-right corner. Clicking it opens the messenger in place;
 * the user keeps working in whatever module they are in.
 *
 * THE POINT OF THIS FILE is the "stays open" behaviour the customer asked for:
 * PMS is a classic multi-page app, so every menu click throws the whole
 * page (and the chat panel with it) away. We therefore persist the dock's
 * state in localStorage and restore it on the next page - the panel is painted
 * open *before* first paint, so it reads as "still open", not "reopened".
 * It stays that way until the user actually closes it with the X.
 *
 * The messenger itself runs inside an iframe pointing at /Chat/dock. That is
 * deliberate: PMS pages load differing jQuery versions and a lot of global
 * CSS, and the iframe is what makes the dock behave identically on all of
 * them, in both directions.
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.ctdock-bubble{
	position:fixed; right:26px; bottom:26px; width:66px; height:66px; border-radius:50%;
	background:linear-gradient(145deg,#2f6bf0,#1d4ed8); color:#fff; border:3px solid #fff;
	cursor:pointer; z-index:11000;
	box-shadow:0 10px 28px rgba(29,78,216,.55), 0 2px 8px rgba(11,31,58,.25);
	display:flex; align-items:center; justify-content:center;
	transition:transform .18s ease, box-shadow .18s ease, background .18s ease;
}
/* a slow idle pulse so it is noticed on a busy PMS page */
@keyframes ctdockIdle{
	0%,100%{box-shadow:0 10px 28px rgba(29,78,216,.55), 0 0 0 0 rgba(37,99,235,.42);}
	50%{box-shadow:0 10px 28px rgba(29,78,216,.55), 0 0 0 12px rgba(37,99,235,0);}
}
.ctdock-bubble:not(.is-open){animation:ctdockIdle 3.4s ease-out infinite;}
.ctdock-bubble::after{
	content:'Chat'; position:absolute; right:76px; white-space:nowrap;
	background:#0b1f3a; color:#fff; font-size:12px; font-weight:600;
	padding:5px 10px; border-radius:7px; opacity:0; pointer-events:none;
	transition:opacity .16s ease; font-family:'Inter',-apple-system,'Segoe UI',Roboto,sans-serif;
}
.ctdock-bubble:hover::after{opacity:1;}
.ctdock-bubble:hover{background:linear-gradient(145deg,#1d4ed8,#1740b8); transform:scale(1.07);}
.ctdock-bubble:active{transform:scale(.97);}
.ctdock-bubble svg{width:31px;height:31px;fill:#fff;}
.ctdock-bubble .ctdock-close-ico{display:none;}
.ctdock-bubble.is-open .ctdock-open-ico{display:none;}
.ctdock-bubble.is-open .ctdock-close-ico{display:block;}
.ctdock-bubble.is-open{background:#334155;}
.ctdock-bubble.is-open:hover{background:#1e293b;}

.ctdock-badge{
	position:absolute; top:-4px; right:-4px; min-width:24px; height:24px; padding:0 6px;
	background:#dc2626; color:#fff; border-radius:12px; font-size:11.5px; font-weight:800;
	line-height:24px; text-align:center; box-shadow:0 0 0 3px #fff; display:none;
	font-family:'Inter',-apple-system,'Segoe UI',Roboto,sans-serif;
}
/* one gentle pulse when something new lands while the panel is shut */
@keyframes ctdockPulse{
	0%{box-shadow:0 6px 20px rgba(37,99,235,.42), 0 0 0 0 rgba(37,99,235,.55);}
	70%{box-shadow:0 6px 20px rgba(37,99,235,.42), 0 0 0 16px rgba(37,99,235,0);}
	100%{box-shadow:0 6px 20px rgba(37,99,235,.42), 0 0 0 0 rgba(37,99,235,0);}
}
.ctdock-bubble.ctdock-ping{animation:ctdockPulse 1.5s ease-out 2;}

.ctdock-panel{
	position:fixed; right:26px; bottom:104px; width:396px; height:588px;
	background:#fff; border-radius:14px; overflow:hidden; z-index:10999;
	box-shadow:0 18px 52px rgba(11,31,58,.3), 0 0 0 1px rgba(15,32,60,.09);
	display:none; flex-direction:column;
}
.ctdock-panel.is-open{display:flex;}
/* only animate when the user opens it by hand; a restore after a page load
   must appear instantly or it looks like it re-opened rather than stayed open */
@keyframes ctdockIn{from{opacity:0;transform:translateY(14px) scale(.985);}to{opacity:1;transform:none;}}
.ctdock-panel.ctdock-animate{animation:ctdockIn .17s ease-out;}

.ctdock-bar{
	display:flex; align-items:center; gap:8px; padding:9px 12px;
	background:#0b1f3a; color:#fff; flex:0 0 auto;
}
.ctdock-bar b{font-size:13px;font-weight:600;font-family:'Inter',-apple-system,'Segoe UI',Roboto,sans-serif;letter-spacing:.2px;}
.ctdock-bar .ctdock-sp{margin-left:auto;display:flex;gap:2px;}
.ctdock-bar button{
	background:transparent;border:0;color:#c7d5ea;width:26px;height:26px;
	border-radius:6px;cursor:pointer;font-size:12px;line-height:1;
}
.ctdock-bar button:hover{background:rgba(255,255,255,.14);color:#fff;}
/* icons are inline SVG, not an icon font: this bar renders on every page in
   PMS and not all of them are guaranteed to have loaded Font Awesome */
.ctdock-bar button svg{width:13px;height:13px;fill:currentColor;display:block;margin:auto;}
.ctdock-bar button .ctdock-min-ico{display:none;}
.ctdock-bar button .ctdock-minup-ico{display:none;}
.ctdock-panel.is-min .ctdock-bar button .ctdock-minup-ico{display:block;}
.ctdock-panel.is-min .ctdock-bar button .ctdock-mindn-ico{display:none;}
.ctdock-panel.is-max .ctdock-bar button .ctdock-min-ico{display:block;}
.ctdock-panel.is-max .ctdock-bar button .ctdock-max-ico{display:none;}
.ctdock-frame{flex:1 1 auto;width:100%;border:0;display:block;background:#fff;}

/* The bubble is pinned bottom-right at z-index 11000, ABOVE the panel. In the
   normal docked position the panel starts at bottom:104px so they never meet,
   but a maximised panel reaches bottom:20px and the bubble then sits on top of
   the panel's own bottom-right corner - exactly where the Send button is.
   Hide it in that state; the title bar already has close / minimise. */
.ctdock-bubble.ctdock-away{display:none;}

.ctdock-panel.is-min{height:44px;min-height:44px;}
.ctdock-panel.is-min .ctdock-frame{display:none;}
.ctdock-panel.is-min.is-max{width:396px;height:44px;right:26px;bottom:104px;max-width:396px;}
.ctdock-panel.is-max{
	right:20px; bottom:20px; width:calc(100vw - 40px); height:calc(100vh - 40px);
	max-width:1180px; max-height:840px;
}

@media(max-width:600px){
	.ctdock-panel{right:0;left:0;bottom:0;width:100%;height:82vh;border-radius:14px 14px 0 0;}
	.ctdock-panel.is-max{right:0;left:0;bottom:0;width:100%;height:100vh;border-radius:0;max-height:none;}
	/* minimised stays a full-width strip pinned to the bottom on a phone */
	.ctdock-panel.is-min,
	.ctdock-panel.is-min.is-max{right:0;left:0;bottom:0;width:100%;max-width:none;height:44px;border-radius:10px 10px 0 0;}
	.ctdock-bubble{right:16px;bottom:16px;}
}
@media print{.ctdock-bubble,.ctdock-panel{display:none!important;}}
</style>

<button type="button" class="ctdock-bubble" id="ctdockBubble" title="Chat" aria-label="Open chat">
	<svg class="ctdock-open-ico" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM7 9h10v2H7V9zm7 5H7v-2h7v2zm3-6H7V6h10v2z"/></svg>
	<svg class="ctdock-close-ico" viewBox="0 0 24 24"><path d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
	<span class="ctdock-badge" id="ctdockBadge">0</span>
</button>

<div class="ctdock-panel" id="ctdockPanel" role="dialog" aria-label="Chat">
	<div class="ctdock-bar">
		<svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:#7fb0ff;"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
		<b>Chat</b>
		<div class="ctdock-sp">
			<button type="button" id="ctdockMin" title="Minimise / restore" aria-label="Minimise or restore chat">
				<svg class="ctdock-mindn-ico" viewBox="0 0 24 24"><path d="M6 19h12v2H6z"/></svg>
				<svg class="ctdock-minup-ico" viewBox="0 0 24 24"><path d="M12 8l6 6H6z"/></svg>
			</button>
			<button type="button" id="ctdockMax" title="Maximise / restore" aria-label="Maximise or restore chat">
				<svg class="ctdock-max-ico" viewBox="0 0 24 24"><path d="M4 4h7v2H6v5H4V4zm9 0h7v7h-2V6h-5V4zM4 13h2v5h5v2H4v-7zm14 0h2v7h-7v-2h5v-5z"/></svg>
				<svg class="ctdock-min-ico" viewBox="0 0 24 24"><path d="M11 4h2v5h5v2h-7V4zm-7 9h7v7h-2v-5H4v-2zm9 0h7v2h-5v5h-2v-7z"/></svg>
			</button>
			<button type="button" id="ctdockHide" title="Close" aria-label="Close chat">
				<svg viewBox="0 0 24 24"><path d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
			</button>
		</div>
	</div>
	<iframe class="ctdock-frame" id="ctdockFrame" title="Chat" src="about:blank"
	        allow="clipboard-write" referrerpolicy="same-origin"></iframe>
</div>

<script>
/* Chat dock — floating launcher, persistent across page navigation */
(function () {
	'use strict';
	if (window.ChatDock) return;

	var BASE  = '<?php echo page_url; ?>Chat/';
	var KEY   = 'ctChatDock';
	var ORIGIN = window.location.origin;

	var bubble, panel, frame, badge, loaded = false, unread = 0;

	/* ---------------------------------------------------------- state ---- */
	/** {open:bool, conv:int, max:bool} — survives navigation, per browser. */
	function readState() {
		try {
			var s = JSON.parse(localStorage.getItem(KEY) || '{}');
			return { open: !!s.open, conv: parseInt(s.conv, 10) || 0, max: !!s.max, min: !!s.min };
		} catch (e) { return { open: false, conv: 0, max: false, min: false }; }
	}
	function writeState(patch) {
		try {
			localStorage.setItem(KEY, JSON.stringify($extend(readState(), patch)));
		} catch (e) {}
	}
	function $extend(a, b) { for (var k in b) if (b.hasOwnProperty(k)) a[k] = b[k]; return a; }

	/* ---------------------------------------------------------- frame ---- */
	function frameUrl() {
		var st = readState();
		return BASE + 'dock' + (st.conv ? '/' + st.conv : '');
	}
	/** The iframe is only ever loaded on demand, so a user who never opens the
	 *  dock pays nothing for it. */
	function ensureFrame() {
		if (loaded) return;
		frame.src = frameUrl();
		loaded = true;
	}

	/**
	 * Show or hide the launcher bubble depending on whether the panel would be
	 * sitting underneath it.
	 *
	 * The bubble is fixed bottom-right above everything, so any panel state
	 * that reaches the bottom-right corner buries the composer's Send button
	 * beneath it. That is fine while docked (the panel starts at bottom:104px)
	 * but not when maximised, and not on a phone where the panel is pinned to
	 * the bottom edge at every size.
	 */
	function syncBubble() {
		if (!bubble || !panel) return;
		var open   = panel.classList.contains('is-open');
		var max    = panel.classList.contains('is-max');
		var mobile = window.innerWidth <= 600;

		// closed -> always show it, that is the only way back in
		bubble.classList.toggle('ctdock-away', open && (max || mobile));
	}

	/* ----------------------------------------------------------- open ---- */
	function open(animate) {
		ensureFrame();
		panel.classList.toggle('ctdock-animate', animate !== false);
		panel.classList.add('is-open');
		bubble.classList.add('is-open');
		bubble.classList.remove('ctdock-ping');
		bubble.setAttribute('aria-label', 'Close chat');
		writeState({ open: true });
		syncBubble();
		setTimeout(function () {
			try { frame.contentWindow.focus(); } catch (e) {}
		}, 60);
	}

	function close() {
		panel.classList.remove('is-open', 'ctdock-animate');
		bubble.classList.remove('is-open');
		bubble.setAttribute('aria-label', 'Open chat');
		writeState({ open: false });
		syncBubble();
	}

	function toggle() {
		if (panel.classList.contains('is-open')) close();
		else open(true);
	}

	/** CSS swaps the maximise/restore glyph off the panel's own class. */
	function setMax(on) {
		panel.classList.toggle('is-max', on);
		if (on) setMin(false);
		writeState({ max: on });
		syncBubble();
	}

	/**
	 * Minimise: collapse to the title bar only. Distinct from close - the panel
	 * stays "open", the iframe keeps running, so the conversation and its live
	 * connection survive and one click brings it straight back.
	 */
	function setMin(on) {
		panel.classList.toggle('is-min', on);
		writeState({ min: on });
		syncBubble();
	}

	/* ---------------------------------------------------------- badge ---- */
	function setCount(n) {
		n = parseInt(n, 10) || 0;
		var wasLower = n > unread;
		unread = n;
		if (!badge) return;
		if (n > 0) { badge.textContent = n > 99 ? '99+' : String(n); badge.style.display = 'block'; }
		else badge.style.display = 'none';

		// nudge only when they cannot already see the messages
		if (wasLower && n > 0 && !panel.classList.contains('is-open')) {
			bubble.classList.remove('ctdock-ping');
			void bubble.offsetWidth;                 // restart the animation
			bubble.classList.add('ctdock-ping');
		}
	}

	/* -------------------------------------------------- frame messages --- */
	window.addEventListener('message', function (e) {
		if (e.origin !== ORIGIN) return;                       // same-origin only
		var d = e.data;
		if (!d || d.source !== 'ctchat') return;

		if (d.type === 'conv')        writeState({ conv: parseInt(d.id, 10) || 0 });
		else if (d.type === 'unread') setCount(d.total);
		else if (d.type === 'close')  close();
		else if (d.type === 'sound') {
			// the panel asks, the host page plays - the top document is the one
			// that has the user gesture the autoplay policy needs. d.kind is
			// 'out' when the user sent something and 'in' when one arrived;
			// they are different sounds, so it has to be passed through.
			if (window.ChatNav && window.ChatNav.blip) window.ChatNav.blip(d.kind);
		}
		else if (d.type === 'toast') {
			// the panel hides its own toasts; show them out here instead
			if (window.ChatNav && window.ChatNav.toast) window.ChatNav.toast(d.title, d.body, d.kind);
		}
		else if (d.type === 'expand') {
			// "full screen" from inside the dock -> the real /Chat page
			window.location.href = BASE + 'index/' + (parseInt(d.id, 10) || 0);
		}
	}, false);

	/* ----------------------------------------------------------- init ---- */
	function init() {
		bubble = document.getElementById('ctdockBubble');
		panel  = document.getElementById('ctdockPanel');
		frame  = document.getElementById('ctdockFrame');
		badge  = document.getElementById('ctdockBadge');
		if (!bubble || !panel || !frame) return;

		// Bind defensively: a stale cached copy of this markup missing one of
		// these buttons must not throw and take the whole dock down with it.
		function on(el, ev, fn) { if (el && el.addEventListener) el.addEventListener(ev, fn); }

		on(bubble, 'click', toggle);
		on(document.getElementById('ctdockHide'), 'click', close);
		on(document.getElementById('ctdockMax'), 'click', function () { setMax(!panel.classList.contains('is-max')); });
		on(document.getElementById('ctdockMin'), 'click', function () { setMin(!panel.classList.contains('is-min')); });

		// clicking the collapsed title bar itself restores it
		on(document.querySelector('.ctdock-bar'), 'click', function (e) {
			if (!panel.classList.contains('is-min')) return;
			if (e.target && e.target.closest && e.target.closest('button')) return;
			setMin(false);
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && panel.classList.contains('is-open')) close();
		});

		// RESTORE — the behaviour the customer asked for. No animation: this
		// should look like the panel was never gone.
		var st = readState();
		if (st.max) setMax(true);
		if (st.min) setMin(true);
		if (st.open) open(false);
		syncBubble();

		var rt = null;
		window.addEventListener('resize', function () {
			clearTimeout(rt);
			rt = setTimeout(syncBubble, 120);
		});

		window.ChatDock = {
			open: function (convId) {
				if (convId) { writeState({ conv: convId }); if (loaded) frame.src = frameUrl(); }
				setMin(false);
				open(true);
			},
			close: close,
			toggle: toggle,
			minimise: setMin,
			setCount: setCount,
			isOpen: function () { return panel.classList.contains('is-open'); }
		};
	}

	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
	else init();
}());
</script>
