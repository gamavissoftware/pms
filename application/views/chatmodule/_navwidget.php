<?php
/**
 * Chat - global notification widget.
 * -----------------------------------------------------------------------------
 * Included once from common/nav-menu.php, so every PMS page carries the chat
 * icon, its unread badge and a dropdown of recent notifications.
 *
 * Deliberately a light POLL (12s) rather than the long-poll used inside the
 * messenger: a parked request per open tab would tie up a PHP worker for each
 * one. On the Chat page itself the messenger's own stream takes over and keeps
 * this badge in step through window.ChatNav.setCount().
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.cc-chatbtn{position:relative;}
.cc-chatbtn .cc-chat-badge{
	position:absolute;top:2px;right:1px;min-width:17px;height:17px;padding:0 4px;
	background:#dc2626;color:#fff;border-radius:9px;font-size:10px;font-weight:700;
	line-height:17px;text-align:center;display:none;box-shadow:0 0 0 2px #fff;
}
.cc-chatpop{
	position:absolute;top:46px;right:0;width:330px;max-height:430px;background:#fff;
	border:1px solid #e6ecf4;border-radius:12px;box-shadow:0 14px 40px rgba(15,32,60,.18);
	z-index:1500;display:none;overflow:hidden;
	/* PMS sets text-align:center on li.dropdown.user-box, which this panel
	   sits inside. Without resetting it every notification renders centred.
	   Stated here rather than on the host element so the panel is correct
	   wherever it is mounted. */
	text-align:left;
}
.cc-chatpop h6{margin:0;padding:12px 15px;border-bottom:1px solid #eef2f7;font-size:13px;font-weight:700;color:#1e293b;display:flex;justify-content:space-between;align-items:center;}
.cc-chatpop h6 a{font-size:11px;font-weight:600;color:#2563eb;text-decoration:none;}
.cc-chatpop .items{max-height:330px;overflow-y:auto;}
.cc-chatnote{display:block;padding:10px 15px;border-bottom:1px solid #f4f7fb;text-decoration:none!important;text-align:left;}
.cc-chatnote:hover{background:#f4f8ff;}
.cc-chatnote.unread{background:#eef4ff;}
.cc-chatnote .t{font-size:12.5px;font-weight:600;color:#1e293b;}
.cc-chatnote .b{font-size:11.5px;color:#7c8ba1;margin-top:1px;}
.cc-chatnote .a{font-size:10px;color:#a7b3c5;margin-top:2px;}
.cc-chatnote.mention{border-left:3px solid #f59e0b;}
.cc-chatempty{padding:26px 15px;text-align:center;color:#8b99ad;font-size:12px;}
.cc-chattoasts{position:fixed;top:74px;right:18px;z-index:1600;display:flex;flex-direction:column;gap:9px;}
.cc-chattoast{
	background:#fff;border:1px solid #e6ecf4;border-left:4px solid #2563eb;border-radius:10px;
	box-shadow:0 8px 26px rgba(15,32,60,.17);padding:11px 15px;min-width:250px;max-width:330px;cursor:pointer;
	font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
}
.cc-chattoast.mention{border-left-color:#f59e0b;}
.cc-chattoast b{display:block;font-size:12.5px;color:#1e293b;}
.cc-chattoast span{font-size:11.5px;color:#7c8ba1;}
</style>

<div class="cc-chattoasts" id="ccChatToasts"></div>

<script>
/* Chat nav badge + notification dropdown (all PMS pages) */
(function () {
	'use strict';
	if (window.ChatNav) return;

	var BASE  = '<?php echo page_url; ?>Chat/';
	var POLL  = 12000;
	var last  = null;        // previous unread total; null until the first read
	var timer = null;

	function $id(x) { return document.getElementById(x); }
	function esc(s) {
		return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;')
			.replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}

	function setCount(n) {
		var label = n > 99 ? '99+' : String(n);
		[['ccChatBadge', 'block'], ['ccChatMenuBadge', 'inline-block']].forEach(function (p) {
			var b = $id(p[0]);
			if (!b) return;
			if (n > 0) { b.textContent = label; b.style.display = p[1]; }
			else b.style.display = 'none';
		});
		// the floating dock owns the primary badge - keep it in step
		if (window.ChatDock && window.ChatDock.setCount) window.ChatDock.setCount(n);
	}

	/* ------------------------------------------------------------ sound ---
	 * The host page is the right place to make the noise: it is the top
	 * document, so it is the one that reliably collects the user gesture the
	 * autoplay policy demands, and it is present on every PMS page - including
	 * the ones where chat is closed, which is when a ping matters most.
	 * The dock's iframe hands its sounds here (see the 'sound' branch in
	 * _dock.php) so a page never pings twice for the same message.
	 * Preference is shared with the messenger through localStorage.
	 * ------------------------------------------------------------------- */
	var actx = null, lastBlip = 0, lastSent = 0;

	function soundOn() {
		try { return localStorage.getItem('ctChatSound') !== '0'; } catch (e) { return true; }
	}

	function unlockAudio() {
		try {
			var Ctx = window.AudioContext || window.webkitAudioContext;
			if (!Ctx) return;
			if (!actx) actx = new Ctx();
			if (actx.state === 'suspended') actx.resume();
		} catch (e) {}
	}

	// ONE context, reused - browsers cap them at about six per page, after
	// which every further `new AudioContext()` throws and the page goes silent.
	function tone(notes, peak, tail) {
		try {
			var Ctx = window.AudioContext || window.webkitAudioContext;
			if (!Ctx) return;
			if (!actx) actx = new Ctx();
			if (actx.state === 'suspended') actx.resume();

			var c = actx, t = c.currentTime, g = c.createGain();
			g.connect(c.destination);
			g.gain.setValueAtTime(0.0001, t);
			g.gain.exponentialRampToValueAtTime(peak, t + 0.01);
			g.gain.exponentialRampToValueAtTime(0.0001, t + tail);
			notes.forEach(function (n) {
				var o = c.createOscillator();
				o.type = 'sine';
				o.frequency.setValueAtTime(n[0], t + n[1]);
				o.connect(g);
				o.start(t + n[1]);
				o.stop(t + n[1] + tail);
			});
		} catch (e) {}
	}

	/**
	 * @param kind 'out' = you sent something; anything else = one arrived.
	 *
	 * The host page owns the speaker even when the sound is asked for from
	 * inside the dock's iframe (see the 'sound' branch in _dock.php) — the top
	 * document is the one holding the user gesture the autoplay policy wants.
	 * So both voices have to exist out here as well as in the messenger, and
	 * they are kept deliberately identical to the pair in chatmodule/index.php.
	 */
	function blip(kind) {
		if (!soundOn()) return;

		if (kind === 'out') {
			// Outgoing: lower, quieter, shorter, one note. Feedback that a
			// keypress landed — and barely rate-limited, so four quick
			// messages make four sounds.
			var n = Date.now();
			if (n - lastSent < 120) return;
			lastSent = n;
			return tone([[660, 0]], 0.05, 0.14);
		}

		var now = Date.now();
		if (now - lastBlip < 1500) return;      // one ping per burst
		lastBlip = now;
		tone([[880, 0], [1174.7, 0.11]], 0.09, 0.3);
	}

	function toast(title, body, kind) {
		var box = $id('ccChatToasts');
		if (!box) return;
		var el = document.createElement('div');
		el.className = 'cc-chattoast' + (kind === 'mention' ? ' mention' : '');
		el.innerHTML = '<b>' + esc(title) + '</b><span>' + esc(body) + '</span>';
		// open the dock in place rather than yanking the user off the page
		// they are working on; fall back to the full page if it is unavailable
		el.onclick = function () {
			if (window.ChatDock) { window.ChatDock.open(); if (el.parentNode) el.parentNode.removeChild(el); }
			else window.location.href = BASE;
		};
		box.appendChild(el);
		setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 6000);
	}

	function poll() {
		var x = new XMLHttpRequest();
		x.open('GET', BASE + 'unread', true);
		x.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		x.onload = function () {
			if (x.status !== 200) return;
			var r;
			try { r = JSON.parse(x.responseText); } catch (e) { return; }
			if (!r.ok) return;

			var total = r.unread.total || 0;
			setCount(total);

			// only announce a genuine increase, and never on the very first read
			// - and stay quiet while the dock is open, because the panel raises
			//   its own alert for the very same messages
			var dockOpen = !!(window.ChatDock && window.ChatDock.isOpen && window.ChatDock.isOpen());
			if (last !== null && total > last && !dockOpen) {
				blip();
				toast('New chat message',
					total + ' unread message' + (total > 1 ? 's' : '') +
					(r.unread.mentions ? ' · ' + r.unread.mentions + ' mention(s)' : ''));
			}
			last = total;
		};
		x.send();
	}

	function loadNotifications() {
		var box = $id('ccChatItems');
		if (!box) return;
		box.innerHTML = '<div class="cc-chatempty">Loading…</div>';

		var x = new XMLHttpRequest();
		x.open('GET', BASE + 'notifications', true);
		x.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		x.onload = function () {
			var r;
			try { r = JSON.parse(x.responseText); } catch (e) { return; }
			if (!r.ok || !r.notifications.length) {
				box.innerHTML = '<div class="cc-chatempty">No chat notifications yet</div>';
				return;
			}
			var h = '';
			r.notifications.forEach(function (n) {
				h += '<a class="cc-chatnote' + (n.is_read ? '' : ' unread') +
					 (n.type === 'mention' ? ' mention' : '') + '" data-conv="' + n.conv +
					 '" href="' + BASE + 'index/' + n.conv + '">' +
					 '<div class="t">' + esc(n.title) + '</div>' +
					 '<div class="b">' + esc(n.body) + '</div>' +
					 '<div class="a">' + esc(n.ago) + '</div></a>';
			});
			box.innerHTML = h;

			// prefer opening the dock on that conversation; the href stays as a
			// working fallback (and keeps middle-click / open-in-new-tab usable)
			Array.prototype.forEach.call(box.querySelectorAll('.cc-chatnote'), function (a) {
				a.addEventListener('click', function (ev) {
					if (!window.ChatDock) return;
					ev.preventDefault();
					window.ChatDock.open(parseInt(a.getAttribute('data-conv'), 10) || 0);
					var pop = $id('ccChatPop');
					if (pop) pop.style.display = 'none';
				});
			});
		};
		x.send();
	}

	// `toast` is exposed so the dock's iframe can surface its notifications
	// through the host page - see the 'toast' branch in _dock.php
	window.ChatNav = { setCount: setCount, refresh: poll, toast: toast, blip: blip };

	document.addEventListener('DOMContentLoaded', function () {
		var btn = $id('ccChatBtn'), pop = $id('ccChatPop');
		if (btn && pop) {
			btn.addEventListener('click', function (e) {
				e.preventDefault(); e.stopPropagation();
				var open = pop.style.display === 'block';
				pop.style.display = open ? 'none' : 'block';
				if (!open) loadNotifications();
			});
			document.addEventListener('click', function (e) {
				if (!pop.contains(e.target) && e.target !== btn) pop.style.display = 'none';
			});
		}

		// audio stays blocked until the user has interacted with the page once
		document.addEventListener('click', unlockAudio, { once: true });
		document.addEventListener('keydown', unlockAudio, { once: true });

		poll();
		timer = setInterval(poll, POLL);

		// stop polling while the tab is hidden; catch up the moment it returns
		document.addEventListener('visibilitychange', function () {
			if (document.hidden) { clearInterval(timer); timer = null; }
			else if (!timer) { poll(); timer = setInterval(poll, POLL); }
		});
	});
}());
</script>
