<?php
/**
 * Chat module - messenger.
 *
 * ONE view, TWO presentations - so there is a single implementation to
 * maintain and the two can never drift apart:
 *
 *   $dock = FALSE (default)  full page at /Chat: app shell + three panes.
 *   $dock = TRUE             the floating dock at /Chat/dock, rendered inside
 *                            an iframe by views/chatmodule/_dock.php. No app
 *                            chrome, full-height, and narrow enough that the
 *                            existing <=820px responsive rules collapse it to
 *                            a list-then-thread flow on their own.
 *
 * The iframe is deliberate: every CRM page loads its own (differing) jQuery
 * and a lot of global CSS, so an iframe is what guarantees the dock behaves
 * identically everywhere and cannot collide with the host page either way.
 */
defined('BASEPATH') OR exit('No direct script access allowed');
$flash = $this->session->flashdata('msg');
$dock  = isset($dock) ? (bool) $dock : FALSE;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
	<title><?php echo sitetitle; ?> | Chat</title>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
	<link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

	<style>
	/* ===================== CHAT MODULE ===================== */
	:root{
		--ch-bg:#f4f7fb; --ch-panel:#ffffff; --ch-line:#e6ecf4; --ch-ink:#1e293b;
		--ch-muted:#7c8ba1; --ch-primary:#2563eb; --ch-primary-dark:#1d4ed8;
		--ch-navy:#0b1f3a; --ch-bubble:#eef2f8; --ch-danger:#dc2626;
	}
	.chat-wrap *{box-sizing:border-box;}
	.chat-wrap{
		font-family:'Inter','Manrope',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
		display:flex; height:calc(100vh - 130px); min-height:520px;
		background:var(--ch-panel); border:1px solid var(--ch-line);
		border-radius:12px; overflow:hidden; box-shadow:0 4px 18px rgba(15,32,60,.06);
	}

	/* ---------- SIDEBAR ---------- */
	/* The list pane scales with the window instead of being pinned at 320px:
	   a 1080p screen gets ~420px (all five tabs on one row, longer names
	   readable) while a laptop still keeps a sensible 300px minimum and leaves
	   the thread the rest. clamp() does this without a single media query. */
	.ch-side{width:clamp(300px,24vw,430px);flex:0 0 auto;border-right:1px solid var(--ch-line);display:flex;flex-direction:column;background:#fbfcfe;}
	.ch-side-head{padding:14px 16px 10px;border-bottom:1px solid var(--ch-line);background:#fff;}
	.ch-side-title{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;}
	.ch-side-title h3{margin:0;font-size:18px;font-weight:700;color:var(--ch-ink);letter-spacing:-.2px;}
	.ch-newbtn{background:var(--ch-primary);color:#fff;border:0;border-radius:8px;padding:7px 12px;font-size:12px;font-weight:600;cursor:pointer;}
	.ch-newbtn:hover{background:var(--ch-primary-dark);}
	.ch-newbtn+.ch-newbtn{margin-left:6px;}
	.ch-search{width:100%;border:1px solid var(--ch-line);border-radius:8px;padding:8px 11px;font-size:13px;outline:none;background:#f6f8fc;}
	.ch-search:focus{border-color:var(--ch-primary);background:#fff;}
	/* Tabs WRAP rather than scroll: with five of them a 320px sidebar cannot fit
	   one line, and a horizontal scroller just hides the last tab off the edge.
	   `flex:1 1 auto` lets them share the row and drop to a second line only
	   when they genuinely have to, so every tab is always visible. */
	.ch-tabs{display:flex;flex-wrap:wrap;gap:1px 3px;padding:9px 8px 0;background:#fff;
		border-bottom:1px solid var(--ch-line);}
	.ch-tab{flex:1 1 auto;text-align:center;padding:6px 7px;font-size:11.5px;font-weight:600;
		color:var(--ch-muted);cursor:pointer;border-bottom:2px solid transparent;white-space:nowrap;
		border-radius:6px 6px 0 0;transition:background .12s ease,color .12s ease;}
	.ch-tab:hover{background:#f4f7fb;color:var(--ch-ink);}
	/* last resort before wrapping: tighten the tabs rather than lose a row */
	.ch-tabs.compact .ch-tab{padding:6px 4px;font-size:11px;}
	.ch-tabs.compact .ch-tab .cnt{min-width:13px;padding:0 3px;margin-left:2px;font-size:9px;line-height:13px;}
	.ch-tab.active{color:var(--ch-primary);border-bottom-color:var(--ch-primary);}
	/* compact, so a count never pushes its own tab off the row */
	.ch-tab .cnt{display:inline-block;min-width:15px;padding:0 4px;margin-left:3px;background:var(--ch-danger);color:#fff;border-radius:8px;font-size:9.5px;font-weight:700;line-height:15px;vertical-align:1px;}
	.ch-list{flex:1;min-height:0;overflow-y:auto;overscroll-behavior:contain;-webkit-overflow-scrolling:touch;}
	.ch-item{display:flex;gap:10px;padding:11px 14px;cursor:pointer;border-bottom:1px solid #f1f5fa;position:relative;}
	.ch-item:hover{background:#f2f6fd;}
	.ch-item.active{background:#e8f0fe;box-shadow:inset 3px 0 0 var(--ch-primary);}
	.ch-avatar{width:40px;height:40px;flex:0 0 40px;border-radius:50%;object-fit:cover;background:var(--ch-primary);color:#fff;font-size:14px;font-weight:700;display:flex;align-items:center;justify-content:center;text-transform:uppercase;}
	.ch-avatar.sq{border-radius:10px;}
	.ch-ava-wrap{position:relative;flex:0 0 40px;}
	/* presence: green = online, red = offline. Shown for people (1:1 chats and
	   member lists); a group has no presence of its own so it gets no dot. */
	.ch-dot{position:absolute;right:0;bottom:1px;width:11px;height:11px;border-radius:50%;background:#22c55e;border:2px solid #fff;}
	.ch-dot.off{background:#ef4444;}
	.ch-status{display:inline-block;width:8px;height:8px;border-radius:50%;background:#22c55e;margin-right:5px;vertical-align:middle;flex:0 0 8px;}
	.ch-status.off{background:#ef4444;}
	.ch-meta{flex:1;min-width:0;}
	.ch-row1{display:flex;justify-content:space-between;align-items:baseline;gap:8px;}
	.ch-name{font-size:13.5px;font-weight:600;color:var(--ch-ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
	.ch-time{font-size:10.5px;color:var(--ch-muted);white-space:nowrap;}
	.ch-row2{display:flex;justify-content:space-between;align-items:center;gap:8px;margin-top:2px;}
	.ch-prev{font-size:12px;color:var(--ch-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
	.ch-badge{background:var(--ch-danger);color:#fff;border-radius:10px;min-width:19px;height:19px;padding:0 6px;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;}
	.ch-item.unread .ch-name,.ch-item.unread .ch-prev{font-weight:700;color:var(--ch-ink);}
	.ch-role{font-size:10.5px;color:#93a3b8;}
	.ch-aud{display:inline-block;font-size:9px;font-weight:800;letter-spacing:.4px;border-radius:7px;
		padding:1px 5px;margin-left:5px;vertical-align:middle;text-transform:uppercase;}
	.ch-aud.tech{background:#e0f2fe;color:#075985;}
	.ch-aud.vendor{background:#fef3c7;color:#92400e;}

	/* PENALTY DF.
	   Same visual language as the penalty pill on /Chat/df-groups and the
	   penalty report: squarer than the soft status pills, ringed, upper-case,
	   with a warning glyph — so it reads as a flag rather than as one more
	   state, and stays legible to anyone who cannot separate red from amber.
	   Sits OUTSIDE .ch-name in the row, because .ch-name truncates with an
	   ellipsis and a long DF group name would otherwise eat the flag. */
	.ch-pen{display:inline-flex;align-items:center;justify-content:center;flex:0 0 auto;
		padding:2px 4px;border-radius:5px;vertical-align:middle;
		background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;
		box-shadow:0 0 0 2px rgba(239,68,68,.10);}
	.ch-pen svg{flex:0 0 auto;display:block;}
	/* slightly bigger where there is room — the thread header and the
	   linked-DF card. The mark stays the same mark, only larger. */
	.ch-pen.lg{padding:3px 5px;border-radius:6px;}
	/* the whole row is tinted, so a penalty is scannable down a long list
	   without reading every line */
	.ch-item.pen{background:#fffafa;box-shadow:inset 3px 0 0 var(--ch-danger);}
	.ch-item.pen:hover{background:#fff5f5;}
	/* the blue edge still means "you are here" — the glyph and the tint carry
	   the penalty, so an open penalty group does not lose its active cue */
	.ch-item.pen.active{background:#ffeef0;box-shadow:inset 3px 0 0 var(--ch-primary);}

	/* ---------- LIST SECTIONS ----------
	   Groups are filed under DF Groups / Department Groups / Other Groups, so
	   a DF room and a department room are never mixed together in one run of
	   near-identical rows. Sticky, because the point of the heading is to say
	   what you are looking at — scrolled off the top it would say it only
	   while you did not need telling. */
	.ch-sec{position:sticky;top:0;z-index:2;display:flex;align-items:center;gap:7px;
		padding:7px 14px 6px;background:#f7f9fd;border-bottom:1px solid #e8eef7;
		font-size:10.5px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;
		color:#64748b;cursor:pointer;user-select:none;}
	.ch-sec:hover{background:#eef3fb;color:var(--ch-ink);}
	.ch-sec .cap{flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
	.ch-sec .n{font-weight:700;color:#94a3b8;letter-spacing:0;}
	/* unread inside a COLLAPSED section — otherwise a folded-away section can
	   hide a new message with nothing on screen to say so */
	.ch-sec .un{min-width:15px;padding:0 4px;background:var(--ch-danger);color:#fff;
		border-radius:8px;font-size:9.5px;line-height:15px;text-align:center;letter-spacing:0;}
	.ch-sec .caret{font-size:10px;color:#94a3b8;transition:transform .15s;}
	.ch-sec.closed .caret{transform:rotate(-90deg);}
	/* the DF section keeps the spanner's colour so the eye ties the heading to
	   the rows under it */
	.ch-sec[data-sec="df"]{color:#3f5d8a;}
	.ch-sec[data-sec="df"] .caret{color:#7e93b5;}
	/* Unread sits above everything and is the reason most people opened the
	   list at all, so it is the one heading with real colour. */
	.ch-sec[data-sec="unread"]{background:#eef4ff;border-bottom-color:#d7e5fb;color:var(--ch-primary);}
	.ch-sec[data-sec="unread"]:hover{background:#e4eeff;}
	.ch-sec[data-sec="unread"] .caret{color:#93b4e8;}

	/* A muted room now sits in Unread with everything else, so the row has to
	   say why it went quiet — otherwise a group that notified nobody appearing
	   at the top of the list reads as a bug. Sits beside the name, in the same
	   grey as the timestamp, so it informs without competing with it. */
	.ch-mute{flex:0 0 auto;font-size:10.5px;color:#9aa8bd;}

	/* a section that exists but whose rows the search box has hidden. Says so
	   in place rather than vanishing, so the list does not appear to lose a
	   whole category mid-keystroke. */
	.ch-secempty{padding:11px 14px;color:var(--ch-muted);font-size:11.5px;font-style:italic;
		border-bottom:1px solid #f1f5fa;}

	/* "Where this group lives" in the Details panel — the same three names the
	   sidebar headings use, so the two read as one idea. */
	.ch-where{display:flex;align-items:center;gap:7px;font-size:12.5px;font-weight:700;
		color:var(--ch-ink);}
	.ch-where i{color:var(--ch-primary);}
	.ch-wherewhy{margin-top:4px;font-size:11.5px;line-height:1.5;color:var(--ch-muted);}

	.ch-empty{padding:34px 20px;text-align:center;color:var(--ch-muted);font-size:13px;}

	/* ---------- THREAD ---------- */
	.ch-main{flex:1;display:flex;flex-direction:column;min-width:0;position:relative;}
	.ch-head{display:flex;align-items:center;gap:11px;padding:12px 16px;border-bottom:1px solid var(--ch-line);background:#fff;}
	.ch-head h4{margin:0;font-size:15px;font-weight:700;color:var(--ch-ink);
		white-space:nowrap;overflow:hidden;text-overflow:ellipsis;background:none;}
	/* Renamed off the very generic `.sub` - this markup sits inside the host
	   app's stylesheets and a two-letter class name is asking for a collision.
	   `user-select:none` also stops a stray drag painting the status line in
	   the OS selection colour, which is what it was doing. */
	.ch-head .ch-sub{
		font-size:11.5px;color:var(--ch-muted);margin-top:1px;background:none;
		white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
		-webkit-user-select:none;-ms-user-select:none;user-select:none;
	}
	.ch-head .ch-avatar,.ch-head h4,.ch-head .ch-sub{-webkit-user-select:none;user-select:none;}
	/* keep any deliberate selection inside chat on-brand rather than the OS accent */
	.chat-wrap ::selection{background:#cfe0fb;color:var(--ch-ink);}
	.chat-wrap ::-moz-selection{background:#cfe0fb;color:var(--ch-ink);}
	.ch-head-actions{margin-left:auto;display:flex;gap:6px;}
	.ch-ibtn{border:1px solid var(--ch-line);background:#fff;border-radius:8px;width:34px;height:34px;color:#64748b;cursor:pointer;font-size:14px;}
	.ch-ibtn:hover{background:#f1f5fb;color:var(--ch-primary);border-color:#c7d7f0;}
	.ch-ibtn.on{background:#e8f0fe;color:var(--ch-primary);border-color:#bcd3f7;}

	/* ---------- ARCHIVED ---------- */
	/* Slate rather than a warning colour: an archived group is a finished
	   piece of work, not a problem. It still has to be unmissable, because
	   every control below it has gone away. */
	.ch-archbar{display:none;padding:11px 16px;background:#f1f5f9;
		border-bottom:1px solid #dde5ee;font-size:12.5px;color:#334155;}
	.ch-archbar .t{display:flex;align-items:center;gap:8px;font-weight:700;}
	.ch-archbar .t i{color:#64748b;}
	.ch-archbar .w{margin:3px 0 0 22px;font-size:11.5px;color:var(--ch-muted);line-height:1.5;}
	.ch-archbar .b{display:flex;flex-wrap:wrap;gap:7px;margin:9px 0 0 22px;}
	.ch-archbar .b a,.ch-archbar .b button{display:inline-flex;align-items:center;gap:5px;
		padding:6px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;
		font-size:11.5px;font-weight:600;color:var(--ch-ink);cursor:pointer;text-decoration:none;}
	.ch-archbar .b a:hover,.ch-archbar .b button:hover{border-color:var(--ch-primary);
		background:#f5f9ff;color:var(--ch-primary);}
	/* the row in the sidebar, so an archived group reads as closed at a glance */
	.ch-item.arch .ch-name{color:#64748b;}
	.ch-item.arch .ch-ava-wrap{opacity:.72;}
	.ch-arch-tag{flex:0 0 auto;font-size:9px;font-weight:700;letter-spacing:.04em;
		text-transform:uppercase;color:#64748b;background:#e7edf5;border-radius:5px;padding:1px 5px;}

	.ch-pinbar{padding:8px 16px;background:#fffbeb;border-bottom:1px solid #fde68a;font-size:12px;color:#92400e;display:none;}
	/* Each pinned message is its own row with its own unpin button, because
	   "how do I get rid of this" has to be answerable from the thing you can
	   see. Hunting the original message back up the thread to toggle its
	   tack again is not an answer. */
	.ch-pinbar .pin-item{display:flex;align-items:center;gap:8px;padding:3px 0;}
	.ch-pinbar .pin-item .pt{flex:1;min-width:0;cursor:pointer;overflow:hidden;
		text-overflow:ellipsis;white-space:nowrap;}
	.ch-pinbar .pin-item .pt:hover{text-decoration:underline;}
	/* when it goes, in the same amber as the bar so it reads as information
	   rather than a warning */
	.ch-pinbar .pin-until{flex:0 0 auto;font-size:10.5px;color:#b4801f;white-space:nowrap;}
	.ch-pinbar .pin-off{flex:0 0 auto;border:0;background:none;padding:0 2px;cursor:pointer;
		color:#b4801f;font-size:12px;line-height:1;opacity:.75;}
	.ch-pinbar .pin-off:hover{opacity:1;color:var(--ch-danger);}
	.ch-pinbar .pin-more{padding-top:3px;font-size:11px;color:#b4801f;}

	/* the duration choices when pinning — one tap each, WhatsApp's set */
	.ch-pinpick{display:flex;flex-direction:column;gap:8px;}
	.ch-pinpick button{display:flex;align-items:center;gap:10px;width:100%;text-align:left;
		padding:11px 14px;border:1px solid var(--ch-line);border-radius:9px;background:#fff;
		font-size:13px;font-weight:600;color:var(--ch-ink);cursor:pointer;}
	.ch-pinpick button:hover{border-color:var(--ch-primary);background:#f5f9ff;}
	.ch-pinpick button i{color:#b4801f;width:14px;text-align:center;}
	.ch-pinpick button small{margin-left:auto;font-weight:500;color:var(--ch-muted);}

	.ch-body{flex:1;min-height:0;overflow-y:auto;overscroll-behavior:contain;-webkit-overflow-scrolling:touch;padding:16px 18px;background:linear-gradient(180deg,#f8fafd 0%,#f4f7fb 100%);}
	.ch-older{text-align:center;margin:2px 0 12px;}
	.ch-older button{border:1px solid var(--ch-line);background:#fff;border-radius:20px;
		padding:5px 14px;font-size:11.5px;font-weight:600;color:#64748b;cursor:pointer;}
	.ch-older button:hover{border-color:var(--ch-primary);color:var(--ch-primary);background:#f4f8ff;}
	.ch-day{text-align:center;margin:14px 0 10px;}
	.ch-day span{background:#e2e9f4;color:#5a6b83;font-size:11px;font-weight:600;padding:3px 12px;border-radius:20px;}

	.ch-msg{display:flex;gap:9px;margin-bottom:11px;position:relative;}
	/* Own messages deliberately look the SAME as incoming ones - white bubble,
	   normal text colours. Only the right alignment (and the absent sender name)
	   marks them as yours. */
	.ch-msg.mine{flex-direction:row-reverse;}
	.ch-msg .ch-avatar{width:32px;height:32px;flex:0 0 32px;font-size:11px;}
	.ch-bub-wrap{max-width:70%;min-width:0;}
	.ch-msg.mine .ch-bub-wrap{align-items:flex-end;display:flex;flex-direction:column;}
	.ch-sender{font-size:11.5px;font-weight:700;color:var(--ch-ink);margin-bottom:3px;}
	.ch-sender .r{font-weight:500;color:#a0aec0;margin-left:5px;font-size:10.5px;}
	.ch-bub{background:#fff;border:1px solid var(--ch-line);border-radius:12px;padding:8px 12px;font-size:13.5px;line-height:1.5;color:var(--ch-ink);word-wrap:break-word;overflow-wrap:anywhere;box-shadow:0 1px 2px rgba(15,32,60,.04);}
	.ch-bub a{color:var(--ch-primary);text-decoration:underline;}
	.ch-bub .men{background:#dbeafe;color:#1d4ed8;border-radius:4px;padding:0 3px;font-weight:600;}
	.ch-stamp{font-size:10px;color:#9aa8bd;margin-top:3px;}
	/* read receipts on your own messages */
	.ch-seen{cursor:pointer;color:#9aa8bd;}
	.ch-seen:hover{color:var(--ch-primary);text-decoration:underline;}
	.ch-seen.all{color:#2563eb;}
	.ch-seenlist .who{display:flex;align-items:center;gap:9px;padding:7px 2px;}
	.ch-seenlist .who .ch-avatar{width:30px;height:30px;flex:0 0 30px;font-size:10px;}
	.ch-seenlist .who .n{font-size:12.5px;font-weight:600;color:var(--ch-ink);}
	.ch-seenlist .who .t{font-size:10.5px;color:var(--ch-muted);}
	.ch-seenlist h6{margin:14px 0 4px;font-size:11px;font-weight:700;color:var(--ch-muted);
		text-transform:uppercase;letter-spacing:.5px;}
	.ch-seenlist h6:first-child{margin-top:0;}
	.ch-msg.mine .ch-stamp{text-align:right;}
/* the optimistic bubble: clearly the same message, visibly not landed yet */
.ch-pend{opacity:.62;}
.ch-pend .ch-bub{box-shadow:none;}
	.ch-deleted{font-style:italic;color:#9aa8bd;}
	/* a message that @mentions me, so it is findable when scrolling a busy group */
	.ch-msg.atme .ch-bub{border-left:3px solid #f59e0b;background:#fffdf5;}

	/* forwarded from / privately replying to */
	.ch-origin{border-left:3px solid #94a3b8;background:#f6f8fb;border-radius:6px;
		padding:5px 9px;margin-bottom:6px;}
	.ch-origin.priv{border-left-color:#7c3aed;background:#f8f5ff;}
	.ch-origin .oh{font-size:10.5px;font-weight:700;color:#64748b;text-transform:none;}
	.ch-origin.priv .oh{color:#6d28d9;}
	.ch-origin .ob{font-size:11.5px;color:#5b6b82;margin-top:2px;}
	.ch-msg.mine .ch-origin{background:#f2f5fa;}

	.ch-quote{border-left:3px solid var(--ch-primary);background:#f1f5fb;border-radius:6px;padding:4px 9px;margin-bottom:5px;font-size:11.5px;cursor:pointer;}
	.ch-quote b{display:block;color:var(--ch-primary);font-size:11px;}
	.ch-quote span{color:#64748b;}

	.ch-sys{text-align:center;margin:10px 0;}
	.ch-sys span{background:#e8edf5;color:#64748b;font-size:11px;padding:4px 12px;border-radius:20px;}

	/* attachments */
	.ch-file{display:flex;align-items:center;gap:9px;background:#f6f9fd;border:1px solid var(--ch-line);border-radius:9px;padding:8px 11px;margin-top:5px;}
	.ch-file .ico{width:32px;height:32px;border-radius:7px;background:var(--ch-primary);color:#fff;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;text-transform:uppercase;flex:0 0 32px;}
	.ch-file .fn{font-size:12px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
	.ch-file .fs{font-size:10.5px;opacity:.75;}
	.ch-file .acts a{font-size:11px;margin-left:8px;white-space:nowrap;}
	/* who shared it and when — the two things the list never said. Sits under
	   the size in the same muted grey, so it informs without turning a file
	   row into three competing lines. */
	/* WRAPS, where the filename above it truncates. The name is long and
	   replaceable by a hover title; "who and when" is the answer somebody
	   came here for, and half a date is no answer at all. Two short lines in
	   a 320px panel cost less than that. */
	.ch-file .fby{font-size:10.5px;color:var(--ch-muted);margin-top:2px;line-height:1.35;}
	.ch-file .fby i{font-size:9px;opacity:.7;margin-right:2px;}
	.ch-file .fby .dot{opacity:.55;}
	/* A row in "Shared files" is a link INTO the conversation: clicking it
	   lands on the message the file arrived in. Says so on hover rather than
	   leaving it to be discovered. */
	.ch-sfile{cursor:pointer;}
	.ch-sfile:hover{background:#eef4ff;border-color:#c9ddfb;}
	/* the download button is the one thing that must NOT jump, so it is
	   visibly its own control rather than more link text */
	.ch-file .acts .ch-dl{display:inline-flex;align-items:center;gap:4px;
		padding:4px 9px;border:1px solid var(--ch-line);border-radius:7px;
		background:#fff;color:var(--ch-primary);font-weight:600;text-decoration:none;}
	.ch-file .acts .ch-dl:hover{border-color:var(--ch-primary);background:#f5f9ff;}
	.ch-img{max-width:260px;max-height:220px;border-radius:9px;margin-top:5px;display:block;cursor:pointer;border:1px solid var(--ch-line);}

	/* linked-record tag card */
	.ch-tag{display:block;margin-top:6px;background:#fff;border:1px solid #cfe0fb;border-left:3px solid var(--ch-primary);border-radius:8px;padding:7px 10px;text-decoration:none!important;}
	.ch-tag:hover{background:#f4f8ff;}
	.ch-tag .code{font-size:10.5px;font-weight:700;color:var(--ch-primary);letter-spacing:.3px;}
	.ch-tag .ttl{font-size:12.5px;font-weight:600;color:var(--ch-ink);}
	.ch-tag .st{font-size:10px;color:#fff;background:#64748b;border-radius:10px;padding:1px 8px;display:inline-block;margin-top:3px;}

	/* reactions */
	.ch-reacts{display:flex;flex-wrap:wrap;gap:4px;margin-top:4px;}
	.ch-msg.mine .ch-reacts{justify-content:flex-end;}
	.ch-react{background:#fff;border:1px solid var(--ch-line);border-radius:20px;padding:1px 8px;font-size:12px;cursor:pointer;line-height:19px;}
	.ch-react:hover{border-color:var(--ch-primary);}
	.ch-react.byme{background:#e8f0fe;border-color:#8fb6f5;}

	/* hover toolbar */
	.ch-tools{position:absolute;top:-11px;display:none;gap:1px;background:#fff;border:1px solid var(--ch-line);border-radius:8px;box-shadow:0 3px 10px rgba(15,32,60,.13);padding:2px;z-index:6;}
	.ch-msg:not(.mine) .ch-tools{left:44px;}
	.ch-msg.mine .ch-tools{right:44px;}
	.ch-msg:hover .ch-tools{display:flex;}
	.ch-tools button{border:0;background:transparent;padding:4px 6px;font-size:13px;cursor:pointer;border-radius:5px;color:#64748b;line-height:1;}
	.ch-tools button:hover{background:#f1f5fb;color:var(--ch-primary);}

	/* screenshot annotator */
	.ch-annot-mask{position:fixed;inset:0;background:rgba(11,31,58,.82);z-index:1500;display:none;
		align-items:center;justify-content:center;padding:14px;}
	.ch-annot-mask.open{display:flex;}
	.ch-annot{background:#fff;border-radius:12px;width:100%;max-width:1100px;max-height:94vh;
		display:flex;flex-direction:column;overflow:hidden;box-shadow:0 24px 60px rgba(11,31,58,.45);}
	.ch-annot-tools{display:flex;align-items:center;gap:5px;padding:9px 12px;border-bottom:1px solid var(--ch-line);
		background:#fafcff;flex-wrap:wrap;}
	.ch-annot-tools .grp{display:flex;gap:3px;align-items:center;padding-right:9px;margin-right:5px;
		border-right:1px solid var(--ch-line);}
	.ch-annot-tools .grp:last-of-type{border-right:0;}
	.ch-tool{border:1px solid transparent;background:transparent;border-radius:7px;width:34px;height:32px;
		cursor:pointer;color:#475569;font-size:14px;}
	.ch-tool:hover{background:#eef3fb;color:var(--ch-primary);}
	.ch-tool.on{background:#e8f0fe;border-color:#bcd3f7;color:var(--ch-primary);}
	.ch-swatch{width:22px;height:22px;border-radius:50%;border:2px solid #fff;cursor:pointer;
		box-shadow:0 0 0 1px #cbd5e1;}
	.ch-swatch.on{box-shadow:0 0 0 2px var(--ch-ink);transform:scale(1.12);}
	.ch-annot-stage{flex:1;overflow:auto;background:#eef2f7;display:flex;align-items:center;
		justify-content:center;padding:16px;position:relative;}
	.ch-annot-wrap{position:relative;line-height:0;box-shadow:0 4px 20px rgba(11,31,58,.22);}
	.ch-annot-canvas{max-width:100%;max-height:70vh;cursor:crosshair;display:block;background:#fff;}
	.ch-annot-text{position:absolute;border:2px dashed var(--ch-primary);background:rgba(255,255,255,.94);
		font-family:inherit;padding:2px 5px;outline:none;border-radius:4px;display:none;z-index:5;min-width:90px;}
	.ch-annot-ft{padding:11px 14px;border-top:1px solid var(--ch-line);display:flex;align-items:center;gap:9px;background:#fafcff;}
	.ch-annot-ft .hint{font-size:11.5px;color:var(--ch-muted);margin-right:auto;}
	.ch-chip-annot{background:#fef3c7;color:#92400e;border:1px solid #fde68a;border-radius:12px;
		padding:1px 8px;font-size:10.5px;font-weight:700;cursor:pointer;margin-left:5px;}
	.ch-chip-annot:hover{background:#fde68a;}
	@media(max-width:820px){
		.ch-annot-tools .lbl{display:none;}
		.ch-annot-canvas{max-height:52vh;}
	}

	/* call cards */
	.ch-callbtn{color:#059669;}
	.ch-callbtn:hover{background:#e7f7f0;color:#047857;border-color:#a7e3cb;}
	.ch-call{border:1px solid #cfe0fb;border-left:4px solid var(--ch-primary);background:#f7faff;
		border-radius:10px;padding:11px 13px;margin-top:4px;max-width:340px;}
	.ch-call.meet{border-left-color:#00897b;background:#f4fbf9;border-color:#bfe6df;}
	.ch-call.teams{border-left-color:#5b5fc7;background:#f7f7fd;border-color:#d3d4f2;}
	.ch-call.ended{border-left-color:#94a3b8;background:#f6f8fa;border-color:#e2e8f0;opacity:.85;}
	.ch-call .hd{display:flex;align-items:center;gap:8px;margin-bottom:6px;}
	.ch-call .ic{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;
		background:var(--ch-primary);color:#fff;font-size:14px;flex:0 0 30px;}
	.ch-call.meet .ic{background:#00897b;}
	.ch-call.teams .ic{background:#5b5fc7;}
	.ch-call.ended .ic{background:#94a3b8;}
	.ch-call .ttl{font-size:13px;font-weight:700;color:var(--ch-ink);}
	.ch-call .cs{font-size:11px;color:var(--ch-muted);}
	.ch-call .topic{font-size:12px;color:#475569;margin:2px 0 7px;}
	.ch-call .btns{display:flex;gap:7px;flex-wrap:wrap;}
	.ch-call a.join,.ch-call button.endc,.ch-call button.addlink{
		border:0;border-radius:7px;padding:6px 13px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none!important;}
	.ch-call a.join{background:var(--ch-primary);color:#fff!important;}
	.ch-call a.join:hover{background:var(--ch-primary-dark);}
	.ch-call.meet a.join{background:#00897b;}
	.ch-call.meet a.join:hover{background:#00695c;}
	.ch-call.teams a.join{background:#5b5fc7;}
	.ch-call.teams a.join:hover{background:#4a4eb0;}
	.ch-call button.endc{background:#fee2e2;color:#b91c1c;}
	.ch-call button.endc:hover{background:#fecaca;}
	.ch-call button.addlink{background:#fef3c7;color:#92400e;}
	.ch-call .waiting{font-size:11.5px;color:#92400e;background:#fffbeb;border:1px solid #fde68a;
		border-radius:6px;padding:5px 9px;margin-bottom:7px;}

	/* message search */
	.ch-search-bar{display:none;background:#fff;border-bottom:1px solid var(--ch-line);}
	.ch-search-bar.open{display:block;}
	.ch-search-row{display:flex;align-items:center;gap:8px;padding:9px 14px;}
	.ch-search-row .fa-search{color:var(--ch-muted);font-size:12px;}
	.ch-search-row input[type=text]{flex:1;border:1px solid var(--ch-line);border-radius:8px;padding:7px 10px;font-size:13px;outline:none;}
	.ch-search-row input[type=text]:focus{border-color:var(--ch-primary);}
	.ch-search-row label{font-size:11.5px;color:var(--ch-muted);display:flex;align-items:center;gap:4px;margin:0;white-space:nowrap;cursor:pointer;font-weight:600;}
	.ch-search-row .fa-times{cursor:pointer;color:#94a3b8;}
	.ch-search-row .fa-times:hover{color:var(--ch-danger);}
	.ch-search-results{max-height:46vh;overflow-y:auto;overscroll-behavior:contain;border-top:1px solid #f1f5fa;}
	.ch-sres{display:flex;gap:9px;padding:9px 14px;border-bottom:1px solid #f4f7fb;cursor:pointer;}
	.ch-sres:hover{background:#f2f6fd;}
	.ch-sres .ch-avatar{width:30px;height:30px;flex:0 0 30px;font-size:10px;}
	.ch-sres .who{font-size:12.5px;font-weight:600;color:var(--ch-ink);}
	.ch-sres .where{font-size:10.5px;color:var(--ch-primary);font-weight:600;}
	.ch-sres .txt{font-size:12px;color:#5b6b82;margin-top:2px;}
	.ch-sres .when{font-size:10px;color:#a7b3c5;margin-top:2px;}
	.ch-sres mark{background:#fde68a;color:inherit;padding:0 1px;border-radius:2px;}
	.ch-sempty{padding:20px 14px;text-align:center;color:var(--ch-muted);font-size:12.5px;}

	/* jump-to-latest pill, shown only while the user is scrolled up */
	.ch-jump{position:absolute;left:50%;transform:translateX(-50%);bottom:12px;z-index:9;
		background:var(--ch-primary);color:#fff;border:0;border-radius:20px;padding:6px 14px;
		font-size:12px;font-weight:600;cursor:pointer;box-shadow:0 4px 14px rgba(37,99,235,.4);display:none;}
	.ch-jump:hover{background:var(--ch-primary-dark);}

	/* typing */
	.ch-typing{padding:0 18px 6px;font-size:11.5px;color:var(--ch-muted);font-style:italic;height:19px;}

	/* ---------- COMPOSER ---------- */
	.ch-comp{border-top:1px solid var(--ch-line);background:#fff;padding:9px 14px 11px;}
	.ch-reply-bar,.ch-tag-bar{display:none;align-items:center;gap:8px;background:#f1f5fb;border-left:3px solid var(--ch-primary);border-radius:6px;padding:6px 10px;margin-bottom:7px;font-size:12px;}
	.ch-reply-bar b{color:var(--ch-primary);}
	.ch-priv-bar{border-left-color:#7c3aed;background:#f8f5ff;}
	.ch-priv-bar b{color:#6d28d9;}
	.ch-tag-bar{border-left-color:#059669;flex-wrap:wrap;}
	.ch-chip{background:#fff;border:1px solid #cfe0fb;border-radius:14px;padding:2px 9px;font-size:11px;display:inline-flex;align-items:center;gap:6px;}
	.ch-chip i{cursor:pointer;color:#94a3b8;}
	.ch-comp-row{display:flex;align-items:flex-end;gap:8px;}
	.ch-input{flex:1;border:1px solid var(--ch-line);border-radius:10px;padding:9px 12px;font-size:13.5px;resize:none;max-height:130px;min-height:40px;outline:none;font-family:inherit;line-height:1.45;}
	.ch-input:focus{border-color:var(--ch-primary);box-shadow:0 0 0 3px rgba(37,99,235,.09);}
	.ch-send{background:var(--ch-primary);border:0;color:#fff;width:40px;height:40px;border-radius:10px;font-size:15px;cursor:pointer;flex:0 0 40px;}
	.ch-send:hover{background:var(--ch-primary-dark);}
	.ch-send:disabled{opacity:.5;cursor:not-allowed;}
	.ch-files-pre{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:7px;}
	/* formatting toolbar */
	.ch-format{display:flex;align-items:center;gap:1px;margin-bottom:6px;flex-wrap:wrap;}
	.ch-fmt{border:1px solid transparent;background:transparent;border-radius:6px;min-width:28px;height:26px;
		padding:0 6px;cursor:pointer;color:#64748b;font-size:12.5px;line-height:1;}
	.ch-fmt:hover{background:#eef3fb;color:var(--ch-primary);border-color:#d9e5f7;}
	.ch-fmt:active{background:#e2ebf9;}
	.ch-fmt b,.ch-fmt i,.ch-fmt s{font-size:13px;}
	.ch-fmt-sep{width:1px;height:16px;background:var(--ch-line);margin:0 5px;}

	/* rendered formatting inside a bubble */
	.ch-bub strong{font-weight:700;}
	.ch-bub em{font-style:italic;}
	.ch-bub del{opacity:.75;}
	.ch-bub code{background:#eef2f8;border:1px solid #e2e8f0;border-radius:4px;padding:1px 4px;
		font-family:'IBM Plex Mono',Menlo,Consolas,monospace;font-size:12px;}
	.ch-bub ul,.ch-bub ol{margin:4px 0 4px 0;padding-left:20px;}
	.ch-bub li{margin:1px 0;}
	.ch-bub ul{list-style:disc;}
	.ch-bub ol{list-style:decimal;}

	.ch-enterhint{margin-top:5px;font-size:10.5px;color:#a0aec0;cursor:pointer;user-select:none;
		display:inline-block;padding:1px 5px;border-radius:5px;}
	.ch-enterhint:hover{background:#f1f5fb;color:var(--ch-primary);}
	.ch-enterhint b{font-weight:700;}

	/* emoji picker + mention list
	   These are anchored to the composer. In the narrow dock they were taller
	   than the space above it, so the top of the list was clipped outside the
	   panel and could not be reached. Cap them against the VIEWPORT rather than
	   a fixed pixel height, and keep the wheel inside the list. */
	.ch-pop{position:absolute;bottom:56px;background:#fff;border:1px solid var(--ch-line);border-radius:10px;box-shadow:0 8px 26px rgba(15,32,60,.16);z-index:40;display:none;
		max-height:calc(100vh - 120px);overflow-y:auto;overscroll-behavior:contain;-webkit-overflow-scrolling:touch;}
	.ch-emoji-pop{width:290px;padding:9px;}
	.ch-emoji-grid{display:grid;grid-template-columns:repeat(8,1fr);gap:2px;max-height:38vh;overflow-y:auto;overscroll-behavior:contain;}
	.ch-emoji-grid button{border:0;background:transparent;font-size:19px;padding:3px;cursor:pointer;border-radius:6px;line-height:1;}
	.ch-emoji-grid button:hover{background:#f1f5fb;}
	.ch-mention-pop{width:280px;max-height:46vh;overflow-y:auto;padding:5px;overscroll-behavior:contain;-webkit-overflow-scrolling:touch;}
	.ch-mention-item{display:flex;gap:8px;align-items:center;padding:6px 8px;border-radius:7px;cursor:pointer;}
	.ch-mention-item:hover,.ch-mention-item.sel{background:#e8f0fe;}
	.ch-mention-item .ch-avatar{width:28px;height:28px;flex:0 0 28px;font-size:10px;}
	.ch-mention-item .n{font-size:12.5px;font-weight:600;color:var(--ch-ink);}
	.ch-mention-item .r{font-size:10.5px;color:var(--ch-muted);}

	/* ---------- RIGHT PANEL ---------- */
	.ch-info{width:290px;flex:0 0 290px;border-left:1px solid var(--ch-line);background:#fbfcfe;display:none;flex-direction:column;overflow-y:auto;}
	.ch-info.open{display:flex;}
	.ch-info-sec{padding:14px 16px;border-bottom:1px solid var(--ch-line);}
	.ch-info-sec h5{margin:0 0 10px;font-size:11px;font-weight:700;color:var(--ch-muted);text-transform:uppercase;letter-spacing:.6px;}
	.ch-member{display:flex;align-items:center;gap:9px;padding:6px 0;}
	.ch-member .ch-avatar{width:32px;height:32px;flex:0 0 32px;font-size:11px;}
	.ch-member .ch-ava-wrap{flex:0 0 32px;width:32px;}
	.ch-member .ch-dot{width:10px;height:10px;bottom:0;}
	.ch-member .n{font-size:12.5px;font-weight:600;color:var(--ch-ink);}
	.ch-member .r{font-size:10.5px;color:var(--ch-muted);}
	.ch-member .x{margin-left:auto;color:#cbd5e1;cursor:pointer;font-size:13px;}
	.ch-member .x:hover{color:var(--ch-danger);}
	.ch-owner-tag{font-size:9.5px;background:#e8f0fe;color:var(--ch-primary);border-radius:8px;padding:1px 6px;margin-left:5px;font-weight:700;}
	.ch-admin-tag{font-size:9.5px;background:#fef3c7;color:#92400e;border-radius:8px;padding:1px 6px;margin-left:5px;font-weight:700;}
	.ch-member .x.adm{margin-left:auto;color:#94a3b8;}
	.ch-member .x.adm:hover{color:var(--ch-primary);}
	.ch-member .x.adm + .x{margin-left:8px;}

	/* ---------- MODALS / TOASTS ---------- */
	.ch-mask{position:fixed;inset:0;background:rgba(11,31,58,.5);z-index:1200;display:none;align-items:center;justify-content:center;padding:20px;}
	.ch-mask.open{display:flex;}
	.ch-modal{background:#fff;border-radius:13px;width:100%;max-width:520px;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 50px rgba(11,31,58,.3);}
	.ch-modal h4{margin:0;padding:16px 20px;border-bottom:1px solid var(--ch-line);font-size:16px;font-weight:700;color:var(--ch-ink);}
	.ch-modal .bd{padding:16px 20px;overflow-y:auto;overscroll-behavior:contain;-webkit-overflow-scrolling:touch;flex:1;min-height:0;}
	.ch-modal .ft{padding:12px 20px;border-top:1px solid var(--ch-line);text-align:right;background:#fafcff;}
	.ch-modal label{display:block;font-size:12px;font-weight:600;color:#475569;margin:0 0 5px;}
	.ch-modal input[type=text],.ch-modal textarea{width:100%;border:1px solid var(--ch-line);border-radius:8px;padding:9px 11px;font-size:13px;outline:none;margin-bottom:13px;}
	.ch-modal input[type=text]:focus,.ch-modal textarea:focus{border-color:var(--ch-primary);}
	/* audience tabs inside the people pickers (new DM / add members) */
	.ch-fwd-src{background:#f6f8fb;border:1px solid var(--ch-line);border-left:3px solid #94a3b8;
		border-radius:8px;padding:9px 11px;margin-bottom:14px;}
	.ch-fwd-src .t{font-size:11.5px;font-weight:700;color:#64748b;}
	.ch-fwd-src .b{font-size:12.5px;color:#475569;margin-top:3px;}

	.ch-ibtn.rec{color:#dc2626;animation:chPulse 1.15s ease-in-out infinite;}
	@keyframes chPulse{0%,100%{opacity:1;}50%{opacity:.35;}}
	.ch-ibtn.busy{opacity:.5;pointer-events:none;}
	.ch-dictating{box-shadow:0 0 0 2px rgba(220,38,38,.25) inset;}
	.ch-picktabs{display:flex;gap:2px;margin:0 0 12px;border-bottom:1px solid var(--ch-line);
		overflow-x:auto;overflow-y:hidden;scrollbar-width:none;}
	.ch-picktabs::-webkit-scrollbar{display:none;}
	.ch-picktab{
		flex:0 0 auto;padding:8px 14px;font-size:12.5px;font-weight:600;color:var(--ch-muted);
		cursor:pointer;border:0;background:transparent;border-bottom:2px solid transparent;
		white-space:nowrap;line-height:1.2;user-select:none;
	}
	.ch-picktab:hover{color:var(--ch-ink);background:#f4f7fb;}
	.ch-picktab.active{color:var(--ch-primary);border-bottom-color:var(--ch-primary);background:transparent;}
	.ch-picklist{border:1px solid var(--ch-line);border-radius:8px;max-height:min(250px,44vh);overflow-y:auto;overscroll-behavior:contain;-webkit-overflow-scrolling:touch;}
	/* `.ch-modal label` above is (0,1,1) and would beat a bare `.ch-pick`
	   (0,1,0), which made the add-members rows stack vertically because the
	   row IS a <label>. The second selector raises specificity to (0,2,1). */
	.ch-pick,
	.ch-modal label.ch-pick{display:flex;align-items:center;gap:9px;padding:7px 11px;cursor:pointer;
		border-bottom:1px solid #f1f5fa;margin:0;font-weight:400;color:inherit;font-size:inherit;}
	.ch-pick:hover{background:#f4f8ff;}
	.ch-pick .ch-avatar{width:30px;height:30px;flex:0 0 30px;font-size:10px;}
	.ch-pick .n{font-size:12.5px;font-weight:600;color:var(--ch-ink);}
	.ch-pick .r{font-size:10.5px;color:var(--ch-muted);}
	.ch-pick input{margin-left:auto;width:16px;height:16px;}
	/* ---------- GROUP KIND CHOOSER ----------
	   Two radios side by side in the create-a-group modal. NOT `.ch-pick`,
	   whose `input{margin-left:auto}` throws the control to the right edge —
	   correct for a tick against a person's name, wrong for a radio you are
	   meant to read after. */
	.ch-kind{display:flex;gap:8px;margin:0 0 13px;}
	.ch-kind label{flex:1;display:flex;align-items:center;gap:7px;margin:0;padding:9px 11px;
		border:1px solid var(--ch-line);border-radius:8px;cursor:pointer;
		font-size:12.5px;font-weight:600;color:#475569;background:#fff;}
	.ch-kind label:hover{border-color:#c7d7ee;background:#f8fbff;}
	.ch-kind label.on{border-color:var(--ch-primary);background:#f2f7ff;color:var(--ch-primary);}
	.ch-kind input{margin:0;width:15px;height:15px;flex:0 0 15px;}
	/* the "this department already has a group" line under the picker */
	.ch-note{margin:-6px 0 13px;font-size:11.5px;line-height:1.45;color:#92400e;
		background:#fffbeb;border:1px solid #fde68a;border-radius:7px;padding:8px 10px;}
	.ch-note a{color:#1d4ed8;font-weight:600;cursor:pointer;text-decoration:underline;}

	.ch-btn{border:0;border-radius:8px;padding:9px 17px;font-size:13px;font-weight:600;cursor:pointer;}
	.ch-btn.primary{background:var(--ch-primary);color:#fff;}
	.ch-btn.primary:hover{background:var(--ch-primary-dark);}
	.ch-btn.ghost{background:#eef2f7;color:#475569;margin-right:7px;}

	.ch-toasts{position:fixed;top:74px;right:18px;z-index:1400;display:flex;flex-direction:column;gap:9px;}
	.ch-toast{background:#fff;border:1px solid var(--ch-line);border-left:4px solid var(--ch-primary);border-radius:10px;box-shadow:0 8px 26px rgba(15,32,60,.17);padding:11px 15px;min-width:260px;max-width:340px;cursor:pointer;animation:chIn .25s ease;}
	.ch-toast.mention{border-left-color:#f59e0b;}
	.ch-toast b{display:block;font-size:12.5px;color:var(--ch-ink);}
	.ch-toast span{font-size:11.5px;color:var(--ch-muted);}
	@keyframes chIn{from{transform:translateX(24px);opacity:0}to{transform:none;opacity:1}}

	.ch-conn{position:fixed;bottom:14px;left:50%;transform:translateX(-50%);background:#92400e;color:#fff;font-size:11.5px;padding:5px 14px;border-radius:20px;z-index:1300;display:none;}

	@media(max-width:1200px){.ch-info{display:none!important;}}
	@media(max-width:820px){
		.chat-wrap{height:calc(100vh - 110px);}
		.ch-side{width:100%;flex:1 1 auto;}
		.ch-side.hide-sm{display:none;}
		.ch-main{display:none;}
		.ch-main.show-sm{display:flex;}
	}

	<?php if ($dock): ?>
	/* ---------- DOCK MODE ----------
	   Fills the iframe edge to edge. The narrow width means the <=820px rules
	   above are already active, so the list/thread toggle comes for free. */
	html,body{height:100%;margin:0;padding:0;background:#fff;overflow:hidden;}
	.chat-wrap{
		height:100vh; min-height:0; border:0; border-radius:0;
		box-shadow:none; margin:0;
	}
	.ch-side{background:#fff;}
	.ch-side-head{padding:10px 12px 8px;}
	.ch-side-title{margin-bottom:8px;}
	.ch-side-title h3{font-size:15px;}
	.ch-newbtn{padding:5px 9px;font-size:11px;}
	.ch-body{padding:12px 12px 6px;}
	.ch-head{padding:9px 11px;}
	.ch-comp{padding:8px 10px 10px;}
	.ch-msg .ch-bub-wrap{max-width:88%;}
	.ch-toasts{display:none;}          /* the host page owns notifications */
	.ch-conn{bottom:8px;font-size:10.5px;padding:3px 10px;}
	/* the dock is always narrow: force the single-column flow regardless of
	   how the browser happens to compute the iframe's media queries */
	.ch-info{display:none!important;}
	.ch-side{width:100%;flex:1 1 auto;}
	.ch-side.hide-sm{display:none;}
	.ch-main{display:none;}
	.ch-main.show-sm{display:flex;}
	.ch-back-dock{display:inline-block!important;}
	<?php endif; ?>
	</style>
</head>
<body>
	<?php if (!$dock): ?>
	<header id="topnav">
		<?php
		/* One shell, because PMS has one kind of account. The CoreTech original
		   switched between three nav menus here — its vendor and engineer
		   portals could not be shown common/nav-menu, which reads session keys
		   only a staff account has. */
		$this->load->view('common/nav-menu');
		?>
	</header>

	<div class="wrapper">
		<div class="container-fluid">
			<?php if ($flash): ?>
				<div style="background:#fef3c7;color:#92400e;padding:10px 15px;border-radius:6px;margin-bottom:12px;"><?php echo $flash; ?></div>
			<?php endif; ?>
	<?php endif; ?>

			<div class="chat-wrap">

				<!-- ============ SIDEBAR ============ -->
				<aside class="ch-side" id="chSide">
					<div class="ch-side-head">
						<div class="ch-side-title">
							<h3>Chat</h3>
							<div>
								<button class="ch-newbtn" id="chSoundBtn" title="Notification sound"><i class="fa fa-bell"></i></button>
								<?php if (!empty($can['create_channel'])): ?>
									<button class="ch-newbtn" id="chNewChannel"><i class="fa fa-users"></i> Group</button>
								<?php endif; ?>
								<?php if (!empty($can['start_dm'])): ?>
									<button class="ch-newbtn" id="chNewDirect"><i class="fa fa-pencil"></i> New</button>
								<?php endif; ?>
							</div>
						</div>
						<input type="text" class="ch-search" id="chFilter" placeholder="Search conversations...">
					</div>
					<!-- The CoreTech original split these by audience (Team /
					     Technicians / Vendors) because it had three kinds of
					     account. PMS has one, so those tabs would each show
					     either everything or nothing. What is still worth
					     separating is one-to-one from many-to-many.

					     Each label carries a short alternative. fitTabs()
					     measures the row and swaps to the short form only if
					     the full one would wrap. -->
					<div class="ch-tabs" id="chTabs">
						<div class="ch-tab active" data-tab="all"><span class="lb" data-full="All" data-short="All">All</span></div>
						<div class="ch-tab" data-tab="direct"><span class="lb" data-full="Direct" data-short="Direct">Direct</span><span class="cnt" id="chCntDirect" style="display:none">0</span></div>
						<div class="ch-tab" data-tab="channel"><span class="lb" data-full="Groups" data-short="Groups">Groups</span><span class="cnt" id="chCntChannel" style="display:none">0</span></div>
					</div>
					<div class="ch-list" id="chList"></div>
				</aside>

				<!-- ============ THREAD ============ -->
				<section class="ch-main" id="chMain">
					<div class="ch-head" id="chHead" style="display:none;">
						<button class="ch-ibtn visible-xs<?php echo $dock ? ' ch-back-dock' : ''; ?>" id="chBack"><i class="fa fa-arrow-left"></i></button>
						<div class="ch-ava-wrap"><div class="ch-avatar" id="chHeadAva">?</div></div>
						<div style="min-width:0;">
							<h4 id="chHeadName">—</h4>
							<div class="ch-sub" id="chHeadSub"></div>
						</div>
						<!-- penalty flag for a DF group. Its own slot rather than a span
						     inside the <h4>, which truncates with an ellipsis and would
						     swallow the flag on a long group name. -->
						<span id="chHeadPen"></span>
						<div class="ch-head-actions">
							<?php if (!empty($can['start_call'])): ?>
							<button class="ch-ibtn ch-callbtn" id="chCallBtn" title="Start a call"><i class="fa fa-video-camera"></i></button>
							<?php endif; ?>
							<button class="ch-ibtn" id="chSearchBtn" title="Search messages"><i class="fa fa-search"></i></button>
							<button class="ch-ibtn" id="chTogglePins" title="Pinned messages"><i class="fa fa-thumb-tack"></i></button>
							<button class="ch-ibtn" id="chToggleInfo" title="Details"><i class="fa fa-info"></i></button>
							<?php if ($dock): ?>
								<button class="ch-ibtn" id="chExpand" title="Open full screen"><i class="fa fa-expand"></i></button>
							<?php endif; ?>
						</div>
					</div>

					<!-- Archived groups: the room is read-only and this bar is the
					     whole of what you can still do with it — take the files,
					     or bring it back. Above the pin bar because it changes
					     what the whole screen means. -->
					<div class="ch-archbar" id="chArchBar"></div>
					<div class="ch-pinbar" id="chPinBar"></div>

					<div class="ch-search-bar" id="chSearchBar">
						<div class="ch-search-row">
							<i class="fa fa-search"></i>
							<input type="text" id="chSearchInput" placeholder="Search messages...">
							<label title="Search every conversation, not just this one">
								<input type="checkbox" id="chSearchAll"> All chats
							</label>
							<i class="fa fa-times" id="chSearchClose" title="Close search"></i>
						</div>
						<div class="ch-search-results" id="chSearchResults"></div>
					</div>

					<div class="ch-body" id="chBody">
						<div class="ch-empty" style="margin-top:120px;">
							<i class="fa fa-comments-o" style="font-size:44px;color:#cbd8ea;display:block;margin-bottom:12px;"></i>
							Select a conversation to start chatting
						</div>
					</div>

					<button type="button" class="ch-jump" id="chJump">&darr; New messages</button>
					<div class="ch-typing" id="chTyping"></div>

					<div class="ch-comp" id="chComp" style="display:none;position:relative;">
						<div class="ch-reply-bar ch-priv-bar" id="chPrivBar">
							<i class="fa fa-user-secret"></i>
							<div style="flex:1;min-width:0;">Replying privately to <b id="chPrivName"></b>
								<div id="chPrivText" style="color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></div>
							</div>
							<i class="fa fa-times" id="chPrivCancel" style="cursor:pointer;color:#94a3b8;"></i>
						</div>

						<div class="ch-reply-bar" id="chReplyBar">
							<i class="fa fa-reply"></i>
							<div style="flex:1;min-width:0;">Replying to <b id="chReplyName"></b>
								<div id="chReplyText" style="color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></div>
							</div>
							<i class="fa fa-times" id="chReplyCancel" style="cursor:pointer;color:#94a3b8;"></i>
						</div>

						<div class="ch-tag-bar" id="chTagBar">
							<i class="fa fa-link" style="color:#059669;"></i>
							<span style="color:#065f46;font-weight:600;">Linked:</span>
							<span id="chTagChips"></span>
						</div>

						<div class="ch-files-pre" id="chFilesPre"></div>

						<div class="ch-pop ch-emoji-pop" id="chEmojiPop"><div class="ch-emoji-grid" id="chEmojiGrid"></div></div>
						<div class="ch-pop ch-mention-pop" id="chMentionPop"></div>

						<!-- Formatting. The message is stored as PLAIN TEXT with
						     markdown-style markers, never HTML - so search, previews
						     and notifications keep working, and there is no HTML
						     sanitising to get wrong. -->
						<div class="ch-format" id="chFormat">
							<button type="button" class="ch-fmt" data-fmt="bold" title="Bold  (Ctrl+B)"><b>B</b></button>
							<button type="button" class="ch-fmt" data-fmt="italic" title="Italic  (Ctrl+I)"><i>I</i></button>
							<button type="button" class="ch-fmt" data-fmt="strike" title="Strikethrough"><s>S</s></button>
							<button type="button" class="ch-fmt" data-fmt="code" title="Code">&lt;/&gt;</button>
							<span class="ch-fmt-sep"></span>
							<button type="button" class="ch-fmt" data-fmt="ul" title="Bulleted list"><i class="fa fa-list-ul"></i></button>
							<button type="button" class="ch-fmt" data-fmt="ol" title="Numbered list"><i class="fa fa-list-ol"></i></button>
						</div>

						<div class="ch-comp-row">
							<?php if (!empty($can['file_share'])): ?>
								<button class="ch-ibtn" id="chAttach" title="Attach file"><i class="fa fa-paperclip"></i></button>
								<input type="file" id="chFileInput" multiple style="display:none;">
							<?php endif; ?>
							<button class="ch-ibtn" id="chEmojiBtn" title="Emoji"><i class="fa fa-smile-o"></i></button>
							<!-- Dictation. Runs on the browser's own SpeechRecognition
							     engine, so it needs no API key and the audio never
							     leaves the machine. Hidden by JS where unsupported. -->
							<button class="ch-ibtn" id="chMicBtn" title="Speak to type" style="display:none;"><i class="fa fa-microphone"></i></button>
							<?php if (!empty($can['ai_assist'])): ?>
								<button class="ch-ibtn" id="chAiBtn" title="Tidy up this message with AI">&#10024;</button>
							<?php endif; ?>
							<?php if (!empty($can['lead_tag'])): ?>
								<button class="ch-ibtn" id="chLeadBtn" title="Link a DF, task or lead"><i class="fa fa-link"></i></button>
							<?php endif; ?>
							<textarea class="ch-input" id="chInput" rows="1"
								spellcheck="true" autocorrect="on" autocapitalize="sentences" lang="en"
								placeholder="Write a message...  (@ to mention, Enter to send, Ctrl+V to paste an image)"></textarea>
							<button class="ch-send" id="chSend"><i class="fa fa-paper-plane"></i></button>
						</div>
						<div class="ch-enterhint" id="chEnterHint" title="Click to change how Enter behaves"></div>
					</div>
				</section>

				<!-- ============ RIGHT PANEL ============ -->
				<aside class="ch-info" id="chInfo">
					<div class="ch-info-sec" id="chInfoRef" style="display:none;"></div>
					<!-- Which sidebar section this room lives under, and (for a
					     group that may be moved) the control that changes it. -->
					<div class="ch-info-sec" id="chInfoSection" style="display:none;"></div>
					<div class="ch-info-sec">
						<h5>Members <span id="chMemberCount"></span></h5>
						<div id="chMembers"></div>
						<div style="margin-top:11px;" id="chMemberActions"></div>
					</div>
					<div class="ch-info-sec">
						<h5>Shared files</h5>
						<div id="chSharedFiles" style="font-size:12px;color:var(--ch-muted);">No files yet</div>
					</div>
				</aside>

			</div>
	<?php if (!$dock): ?>
		</div>
	</div>
	<?php endif; ?>

	<!-- ============ MODALS ============ -->
	<div class="ch-mask" id="chModalMask">
		<div class="ch-modal" id="chModal">
			<h4 id="chModalTitle">Title</h4>
			<div class="bd" id="chModalBody"></div>
			<div class="ft">
				<button class="ch-btn ghost" id="chModalCancel">Cancel</button>
				<button class="ch-btn primary" id="chModalOk">Save</button>
			</div>
		</div>
	</div>

	<!-- ============ SCREENSHOT ANNOTATOR ============ -->
	<div class="ch-annot-mask" id="chAnnotMask">
		<div class="ch-annot">
			<div class="ch-annot-tools">
				<div class="grp">
					<button class="ch-tool on" data-tool="arrow" title="Arrow"><i class="fa fa-long-arrow-right"></i></button>
					<button class="ch-tool" data-tool="rect" title="Box"><i class="fa fa-square-o"></i></button>
					<button class="ch-tool" data-tool="pen" title="Draw"><i class="fa fa-pencil"></i></button>
					<button class="ch-tool" data-tool="text" title="Text"><i class="fa fa-font"></i></button>
					<button class="ch-tool" data-tool="hide" title="Hide / blur out sensitive details"><i class="fa fa-eye-slash"></i></button>
				</div>
				<div class="grp" id="chAnnotColors"></div>
				<div class="grp">
					<span class="lbl" style="font-size:11px;color:#64748b;font-weight:600;">Size</span>
					<input type="range" id="chAnnotSize" min="2" max="14" value="4" style="width:80px;">
				</div>
				<div class="grp">
					<button class="ch-tool" id="chAnnotUndo" title="Undo"><i class="fa fa-undo"></i></button>
					<button class="ch-tool" id="chAnnotClear" title="Remove all annotations"><i class="fa fa-trash"></i></button>
				</div>
			</div>

			<div class="ch-annot-stage">
				<div class="ch-annot-wrap" id="chAnnotWrap">
					<canvas class="ch-annot-canvas" id="chAnnotCanvas"></canvas>
					<input type="text" class="ch-annot-text" id="chAnnotText" placeholder="Type, then Enter">
				</div>
			</div>

			<div class="ch-annot-ft">
				<span class="hint">Drag to draw · click for text · Esc to cancel</span>
				<button class="ch-btn ghost" id="chAnnotCancel">Cancel</button>
				<button class="ch-btn primary" id="chAnnotSave">Attach image</button>
			</div>
		</div>
	</div>

	<div class="ch-toasts" id="chToasts"></div>
	<div class="ch-conn" id="chConn">Reconnecting…</div>

	<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>

<script>
/* =============================================================================
 * CHAT CLIENT
 * -----------------------------------------------------------------------------
 * Real-time delivery is a long-poll against Chat/stream: the request parks on
 * the server until an event lands for this user, then we render and immediately
 * reconnect. Failures back off (1s -> 30s) instead of hammering the server.
 * =========================================================================== */
(function ($) {
	'use strict';

	var BASE = '<?php echo page_url; ?>Chat/';
	var ME   = <?php echo json_encode($me); ?>;
	var CAN  = <?php echo json_encode($can); ?>;
	var DOCK = <?php echo $dock ? 'true' : 'false'; ?>;
	/* The records a conversation or message can be linked to, straight from
	   chat_ref_types() — so the picker, the chips and the server all agree on
	   the list and adding a fourth type never means editing this view. */
	var REFS = <?php
		$__refs = array();
		foreach (chat_ref_types() as $__k => $__v) $__refs[$__k] = $__v['label'];
		echo json_encode($__refs);
	?>;
	/* How long a pin can be made to last, straight from
	   Chat_model::pin_durations() — so the menu offers exactly what the server
	   accepts. An ARRAY, not the model's days=>label map: JSON objects with
	   numeric keys come back in ascending order, which would put "Always"
	   (0 days) at the top of a menu that should read 24 hours first. */
	var PIN_FOR = <?php
		$__pins = array();
		foreach ($this->chat->pin_durations() as $__d => $__l) {
			$__pins[] = array('days' => (int) $__d, 'label' => $__l);
		}
		echo json_encode($__pins);
	?>;

	/**
	 * In dock mode we live in an iframe. Keep the host page in step so it can
	 * persist which conversation is open (and restore it after a navigation),
	 * and paint the unread bubble without waiting for its own slower poll.
	 * Same-origin only - the parent validates the origin on its side too.
	 */
	function toHost(type, data) {
		if (!DOCK || window.parent === window) return;
		try {
			window.parent.postMessage(
				$.extend({ source: 'ctchat', type: type }, data || {}),
				window.location.origin
			);
		} catch (e) {}
	}

	var S = {
		conversations: <?php echo json_encode($conversations); ?>,
		cursor:        <?php echo (int) $cursor; ?>,
		activeId:      <?php echo (int) $open_id; ?>,
		messages:      [],
		members:       [],
		myTeam:        [],   // user ids I lead; filled by loadDirectory()
		myRole:        'member',
		header:        null,
		tab:           'all',
		filter:        '',
		replyTo:       null,
		pendingTags:   [],
		pendingFiles:  [],
		mentions:      [],
		mentionAll:    false,
		privateOrigin: null,
		// Enter SENDS; Shift+Enter and Alt+Enter start a new line. Anyone who
		// prefers the opposite can flip it from the composer hint and the
		// choice sticks per browser.
		//
		// Read as "not explicitly turned off" rather than "== 1" on purpose:
		// the default used to be the other way round, so an absent key means
		// "never expressed a preference" and should follow the new default,
		// while a stored '0' is a real choice somebody made and is kept.
		enterSends:    (function () {
			try { return localStorage.getItem('ctChatEnter') !== '0'; } catch (e) { return true; }
		}()),
		directory:     [],
		departments:   null,   // picker list; loaded on demand by loadDepartments()
		// which sidebar sections are folded away, by section key. Per browser,
		// like the sound and Enter preferences — somebody who never touches DF
		// groups should not have to fold that section on every visit.
		secClosed:     (function () {
			// Archived starts FOLDED for somebody who has never touched these
			// controls — finished rooms should be one click away, not a wall
			// of them above the groups you are actually working in. The moment
			// they open or close anything the stored choice wins, archived
			// included, so this is a default and not a rule.
			try {
				var raw = localStorage.getItem('ctChatSections');
				if (raw === null) return { archived: 1 };
				return JSON.parse(raw || '{}') || {};
			}
			catch (e) { return { archived: 1 }; }
		}()),
		oldestId:      0,
		lastMessageId: 0,
		loadingOlder:  false,
		pinned:        true,     // following the newest message?
		streamFails:   0,
		streamIdle:    false,
		streamStopped: false,
		docTitle:      document.title
	};

	/** must stay in step with chat_providers() in chat_access_helper.php */
	var PROVIDERS = {
		zoom:  { label: 'Zoom',            colour: '#2563eb', dark: '#1d4ed8',
		         eg: 'https://zoom.us/j/1234567890' },
		meet:  { label: 'Google Meet',     colour: '#00897b', dark: '#00695c',
		         eg: 'https://meet.google.com/abc-defg-hij' },
		teams: { label: 'Microsoft Teams', colour: '#5b5fc7', dark: '#4a4eb0',
		         eg: 'https://teams.microsoft.com/l/meetup-join/...' }
	};
	function provider(p) { return PROVIDERS[p] || PROVIDERS.zoom; }

	var EMOJIS = ['👍','❤️','😂','🎉','👀','✅','🙏','🔥','💯','😊','😍','🤔','😅','😉','😎','🥳',
	              '👏','🙌','🤝','💪','✍️','📌','📎','📅','⏰','⚠️','❌','❓','❗','💡','📈','📉',
	              '😀','😃','😄','😁','😆','😇','🙂','😌','😴','😢','😡','😱','🤯','🤗','🤞','👌'];
	var QUICK  = ['👍','❤️','😂','🎉','✅','👀'];

	/* ---------------------------------------------------------------- utils */
	function esc(s) {
		return String(s == null ? '' : s)
			.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
	}
	function escRe(s) { return String(s).replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }

	/**
	 * Turn a stored message into display HTML.
	 *
	 * ORDER MATTERS, and escaping comes FIRST. The message is plain text in the
	 * database - never HTML - so every tag below is one we generated ourselves
	 * from markers we recognise. Nothing a user types can become markup.
	 *
	 *   1. escape          - anything user-typed is now inert
	 *   2. code spans      - pulled out behind placeholders so their contents
	 *                        are not treated as bold/italic/links
	 *   3. block lists     - consecutive "- " / "1. " lines become <ul>/<ol>
	 *   4. inline marks    - **bold**, *bold*, _italic_, ~~strike~~
	 *                        (single *x* is BOLD since 2026-10-01, WhatsApp-style,
	 *                        same as the app; the Italic button writes _x_)
	 *   5. links, mentions
	 *   6. restore code spans
	 */
	function renderBody(text, mentions) {
		var html = esc(text);

		// --- 2. protect `code` -------------------------------------------
		var codes = [];
		html = html.replace(/`([^`\n]+)`/g, function (_, inner) {
			codes.push(inner);
			return ' CODE' + (codes.length - 1) + ' ';
		});

		// --- inline marks, applied per line so lists stay clean ----------
		var inline = function (line) {
			line = line.replace(/\*\*([^\*\n]+)\*\*/g, '<strong>$1</strong>');
			line = line.replace(/(^|[\s(])\*([^\*\n]+)\*(?=[\s).,!?:;]|$)/g, '$1<strong>$2</strong>');
			line = line.replace(/(^|[\s(])_([^_\n]+)_(?=[\s).,!?:;]|$)/g, '$1<em>$2</em>');
			line = line.replace(/~~([^~\n]+)~~/g, '<del>$1</del>');

			line = line.replace(/(https?:\/\/[^\s<]+)/g, function (u) {
				return '<a href="' + u + '" target="_blank" rel="noopener noreferrer">' + u + '</a>';
			});
			line = line.replace(/@(everyone|all)\b/gi, '<span class="men">@$1</span>');
			(mentions || []).forEach(function (m) {
				if (!m.name) return;
				line = line.replace(new RegExp('@' + escRe(esc(m.name)), 'g'),
					'<span class="men">@' + esc(m.name) + '</span>');
			});
			return line;
		};

		// --- 3. group consecutive list items into a single list ----------
		var lines = html.split('\n');
		var out = [], buf = null, bufType = null;

		var flush = function () {
			if (!buf) return;
			out.push('<' + bufType + '>' + buf.map(function (li) {
				return '<li>' + inline(li) + '</li>';
			}).join('') + '</' + bufType + '>');
			buf = null; bufType = null;
		};

		lines.forEach(function (line) {
			var ul = line.match(/^\s*[-*]\s+(.*)$/);
			var ol = line.match(/^\s*\d+[.)]\s+(.*)$/);
			var type = ul ? 'ul' : (ol ? 'ol' : null);

			if (type) {
				if (bufType && bufType !== type) flush();
				bufType = type;
				(buf = buf || []).push(ul ? ul[1] : ol[1]);
			} else {
				flush();
				out.push(inline(line));
			}
		});
		flush();

		// join plain lines with <br>, but never add one around a list block
		var htmlOut = out.reduce(function (acc, part, i) {
			var isBlock = /^<(ul|ol)>/.test(part);
			var prevBlock = i > 0 && /^<(ul|ol)>/.test(out[i - 1]);
			if (i === 0) return part;
			return acc + (isBlock || prevBlock ? '' : '<br>') + part;
		}, '');

		// --- 6. put the code spans back ----------------------------------
		return htmlOut.replace(/ CODE(\d+) /g, function (_, i) {
			return '<code>' + codes[Number(i)] + '</code>';
		});
	}
	function avatar(o, cls) {
		var extra = cls || '';
		if (o && o.avatar) {
			return '<img src="' + esc(o.avatar) + '" class="ch-avatar ' + extra + '" alt="" ' +
			       'onerror="this.outerHTML=\'<div class=&quot;ch-avatar ' + extra + '&quot;>' +
			       esc(o.initials || '?') + '</div>\'">';
		}
		var bg = o && o.color ? ' style="background:' + esc(o.color) + '"' : '';
		return '<div class="ch-avatar ' + extra + '"' + bg + '>' + esc((o && o.initials) || '?') + '</div>';
	}
	function isMine(m) { return m.sender_id == ME.id && m.sender_type === ME.type; }

	/**
	 * Seconds of edit window left on a message.
	 * The server sends `edit_left` as of the moment it built the response, so
	 * we subtract however long we have been holding it. That keeps the button
	 * honest without trusting the browser clock or its timezone.
	 */
	function editLeft(m) {
		if (!m || m.edit_left === undefined) return 0;
		if (!m._rx) m._rx = Date.now();
		return m.edit_left - Math.floor((Date.now() - m._rx) / 1000);
	}
	function timeAgo(ts) {
		if (!ts) return '';
		var d = (Date.now() - new Date(ts.replace(/-/g, '/')).getTime()) / 1000;
		if (d < 60) return 'now';
		if (d < 3600) return Math.floor(d / 60) + 'm';
		if (d < 86400) return Math.floor(d / 3600) + 'h';
		if (d < 604800) return Math.floor(d / 86400) + 'd';
		return ts.substring(8, 10) + '/' + ts.substring(5, 7);
	}
	// No chat request may hang indefinitely. The long-poll overrides this with
	// its own longer timeout; everything else fails fast and says so.
	$.ajaxSetup({ timeout: 20000 });

	function post(url, data) { return $.post(BASE + url, data); }

	/** Turn a failed XHR into something a human can act on. */
	function xhrReason(x) {
		if (!x) return 'Unknown error';
		if (x.statusText === 'timeout') return 'The server did not respond in time.';
		if (x.status === 0)   return 'Connection lost - check your network.';
		if (x.status === 401) return 'Your session expired. Please sign in again.';
		if (x.status === 403) return 'You do not have access to this conversation.';
		if (x.status === 404) return 'That conversation no longer exists.';
		if (x.status >= 500)  return 'The server returned an error (' + x.status + ').';
		try { var j = JSON.parse(x.responseText); if (j.message_text) return j.message_text; } catch (e) {}
		return 'Request failed (' + x.status + ').';
	}

	/** escape, then wrap the search term so hits stand out */
	function highlight(text, term) {
		var h = esc(text);
		if (!term) return h;
		try {
			return h.replace(new RegExp('(' + escRe(esc(term)) + ')', 'gi'), '<mark>$1</mark>');
		} catch (e) { return h; }
	}

	/**
	 * Scroll a message into view and flash it. If it is older than the loaded
	 * page, pull older pages until it turns up (bounded, so a very old hit
	 * cannot spin forever).
	 */
	function jumpToMessage(mid, tries) {
		tries = tries || 0;
		var $t = $('#chBody .ch-msg[data-mid="' + mid + '"]');
		if ($t.length) {
			$t[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
			$t.css('background', '#fff8dd');
			setTimeout(function () { $t.css('background', ''); }, 1600);
			S.pinned = false;
			return;
		}
		if (tries >= 8 || !S.oldestId) { toast('Message is further back', 'Scroll up to load more history'); return; }

		$.getJSON(BASE + 'messages/' + S.activeId, { before: S.oldestId }, function (r) {
			if (!r.ok || !r.messages.length) { S.oldestId = 0; return; }
			S.messages = r.messages.concat(S.messages);
			S.oldestId = r.messages[0].id;
			renderMessages(false);
			jumpToMessage(mid, tries + 1);
		});
	}

	/** yyyymmdd-hhmmss, for naming pasted screenshots */
	function stamp() {
		var d = new Date(), p = function (n) { return (n < 10 ? '0' : '') + n; };
		return '' + d.getFullYear() + p(d.getMonth() + 1) + p(d.getDate()) + '-' +
		       p(d.getHours()) + p(d.getMinutes()) + p(d.getSeconds());
	}

	/* ------------------------------------------------------------- SIDEBAR */

	/** does this conversation belong under the given tab? */
	function matchesTab(c, tab) {
		switch (tab) {
			case 'channel': return c.type !== 'direct';
			case 'direct':  return c.type === 'direct';
			default:        return true;                     // "All"
		}
	}

	/**
	 * Keep every tab on one row.
	 *
	 * Try the full labels; if the last tab has dropped to a second line
	 * (its offsetTop no longer matches the first tab's), fall back to the
	 * short labels. Measuring beats guessing at breakpoints, because the width
	 * also depends on which unread counts happen to be showing.
	 */
	function fitTabs() {
		var el = document.getElementById('chTabs');
		if (!el) return;

		var setMode = function (short) {
			$(el).find('.lb').each(function () {
				var $l = $(this);
				var want = short ? $l.attr('data-short') : $l.attr('data-full');
				if ($l.text() !== want) $l.text(want);
			});
		};

		var wrapped = function () {
			var t = el.querySelectorAll('.ch-tab');
			return t.length > 1 && t[t.length - 1].offsetTop > t[0].offsetTop;
		};

		// widest first, then step down only as far as needed
		el.classList.remove('compact');
		setMode(false);
		if (!wrapped()) return;

		setMode(true);                       // short labels
		if (!wrapped()) return;

		el.classList.add('compact');         // and tighter padding
	}

	function emptyLabel(tab) {
		switch (tab) {
			case 'direct':  return 'No direct messages yet.';
			case 'channel': return 'No groups yet.';
			default:        return 'No conversations here yet.';
		}
	}

	/**
	 * Chip marking who a conversation is with.
	 *
	 * In the CoreTech CRM this said TECH or VENDOR, so you could tell at a
	 * glance that a row reached outside the company. Everyone in PMS is a
	 * colleague, so there is nothing to warn about and the chip is empty.
	 * Kept (rather than deleted at its ~3 call sites) as the hook to fill in
	 * if outside parties are ever given accounts.
	 */
	function audienceTag(c) {
		return '';
	}

	/**
	 * The penalty mark for a DF group.
	 *
	 * Takes anything the server stamps with `penalty` — a conversation row
	 * from the list, or a DF card (a group header, or a DF tagged onto a
	 * message) — so one mark covers every place a DF is named.
	 *
	 * THE MARK ONLY: no wording, and deliberately no figure. What a DF's
	 * penalty comes to is the penalty report's business
	 * (views/master/penalitydf.php); the job here is to say THAT this DF is
	 * flagged, next to its name, without money on a chat screen.
	 *
	 * The title/aria-label is what makes a bare glyph mean something — to a
	 * screen reader it is the only text there is.
	 */
	function penaltyFlag(o, big) {
		if (!o || !o.penalty) return '';
		var px = big ? 12 : 10;
		return '<span class="ch-pen' + (big ? ' lg' : '') + '" role="img" ' +
				'title="Marked as a penalty DF" aria-label="Marked as a penalty DF">' +
				'<svg width="' + px + '" height="' + px + '" viewBox="0 0 24 24" ' +
					'fill="currentColor" aria-hidden="true">' +
					'<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>' +
			'</span>';
	}

	/**
	 * The sidebar's sections, in the order they are stacked.
	 *
	 * DF groups above department groups above the rest, with direct messages
	 * first in the "All" tab.
	 */
	var SECTIONS = [
		// `badge` = show the unread COUNT instead of the row count, always
		// rather than only while folded. It is the one thing anybody wants
		// from this heading, and "Unread 3" (rooms) beside a 9-message total
		// would be two numbers meaning different things in one line.
		{ key: 'unread',     label: 'Unread', badge: true },
		{ key: 'direct',     label: 'Direct Messages' },
		{ key: 'df',         label: 'DF Groups' },
		{ key: 'department', label: 'Department Groups' },
		{ key: 'other',      label: 'Other Groups' },
		// LAST, and collapsed by default (see S.secClosed below): an archived
		// room is finished work. It has to be findable, not in the way.
		{ key: 'archived',   label: 'Archived' }
	];

	/**
	 * A conversation's HOME section — where it lives once it has been read.
	 *
	 * The SERVER decides this and sends it as `group_kind`
	 * (Chat_model::group_kind()), so this page and the dock can never file the
	 * same room under different headings. The fallback re-derives it from the
	 * same fields and exists for one case only: a tab left open across the
	 * deploy of this feature, still holding rows the previous version fetched.
	 */
	/**
	 * "Manager · Sector - 59" — a person's role and which plant they are at.
	 *
	 * ONE function, called from every place a person is named: the sidebar's
	 * DM rows, the thread header, each message byline, the member list, the
	 * mention picker, the people picker, the forward list and the read
	 * receipts. Written once so the separator, the order and the behaviour
	 * when a field is missing are the same in all of them — which they were
	 * not going to stay if nine call sites each concatenated their own.
	 *
	 * Either half can be empty (a user with no role, a plant_unit that is 0 or
	 * a column that has not been added yet) and the dot goes with it.
	 */
	function roleLine(o) {
		if (!o) return '';
		var bits = [];
		if (o.role)  bits.push(o.role);
		if (o.plant) bits.push(o.plant);
		return bits.join(' · ');
	}

	function sectionOf(c) {
		// Archived first, matching Chat_model::group_kind(). A dispatched DF
		// group is not a DF group you are working in any more, and leaving it
		// under "DF Groups" means the heading people scan every day slowly
		// fills with rooms that are finished.
		if (c.archived) return 'archived';
		if (c.type === 'direct') return 'direct';
		if (c.group_kind) return c.group_kind;
		if (c.ref_type === 'df' && c.ref_id > 0) return 'df';
		return (c.department_id > 0) ? 'department' : 'other';
	}

	/**
	 * Which section a conversation is drawn under RIGHT NOW.
	 *
	 * Anything unread is lifted out of its home section into "Unread" at the
	 * very top — a direct message and a DF group with new messages sit side by
	 * side there, because when you open chat the question is "what is waiting
	 * for me", not "what kind of room is it". Reading it drops it straight back
	 * under its own heading, so the kinds stay separated for everything you are
	 * actually finished with.
	 *
	 * MUTED ROOMS FLOAT TOO. Unread is unread: a muted group is still a group
	 * somebody is waiting on, and burying it under every read conversation is
	 * how it gets missed for a week. Muting keeps doing what it is actually
	 * for — no sound, no toast, no desktop notification (see the alert loop in
	 * handleStream(), which still skips a muted room) — it simply stops
	 * deciding where the row sits. The row carries a struck-through bell so it
	 * is obvious why a noisy group arrived at the top in silence.
	 *
	 * Matches Chat_model::unread_first(), which applies the same rule to the
	 * list every surface draws — this is the fallback for a tab left open
	 * across the deploy that installed it.
	 */
	function bucketOf(c) {
		// An archived room never floats into Unread. It is closed: whatever is
		// unread in it was said before it was archived, and hoisting a
		// finished DF to the top of the list every morning is noise.
		if (c.archived) return 'archived';
		return (c.unread > 0) ? 'unread' : sectionOf(c);
	}

	/** One conversation row. */
	function listItemHtml(c) {
		// job groups keep a spanner so they stand out; ordinary groups get no
		// prefix at all (the "#" was Slack-style and read as noise)
		var icon = c.type === 'job' ? '<i class="fa fa-wrench"></i> ' : '';
		return '<div class="ch-item' + (c.id == S.activeId ? ' active' : '') + (c.unread > 0 ? ' unread' : '') +
				(c.penalty ? ' pen' : '') + (c.archived ? ' arch' : '') + '" data-id="' + c.id + '">' +
				'<div class="ch-ava-wrap">' + avatar(c, c.type === 'direct' ? '' : 'sq') +
					// 1:1 chats always carry a presence dot - green online, red offline
					(c.type === 'direct'
						? '<span class="ch-dot' + (c.is_online ? '' : ' off') + '" title="' +
						  (c.is_online ? 'Online' : 'Offline') + '"></span>'
						: '') +
				'</div>' +
				'<div class="ch-meta">' +
					'<div class="ch-row1">' +
						'<div class="ch-name">' + icon + esc(c.name) + audienceTag(c) + '</div>' +
						// a marked DF flies its flag beside the group name
						penaltyFlag(c) +
						// muted rooms float on unread like everything else, so the
						// row carries the reason it arrived without a sound
						(c.is_muted
							? '<i class="fa fa-bell-slash ch-mute" title="Muted \u2014 no sound or notification"></i>'
							: '') +
						// A search can pull an archived room up next to live
						// ones, so the row says what it is rather than relying
						// on the heading it usually sits under.
						(c.archived ? '<span class="ch-arch-tag" title="Archived \u2014 read only">archived</span>' : '') +
						'<div class="ch-time">' + timeAgo(c.last_at) + '</div>' +
					'</div>' +
					'<div class="ch-row2">' +
						'<div class="ch-prev">' +
							(c.last_sender ? esc(c.last_sender) + ': ' : '') + esc(c.last_text || 'No messages yet') +
						'</div>' +
						(c.unread > 0 ? '<div class="ch-badge">' + (c.unread > 99 ? '99+' : c.unread) + '</div>' : '') +
					'</div>' +
					(c.type === 'direct' && roleLine(c) ? '<div class="ch-role">' + esc(roleLine(c)) + '</div>' : '') +
				'</div>' +
			'</div>';
	}

	/** One section heading: its name, how many rows it holds, and — only while
	 *  it is folded away — the unread it would otherwise be hiding. */
	function sectionHeadHtml(sec, count, unread) {
		var closed = !!S.secClosed[sec.key];
		// The unread heading carries its message total and nothing else; every
		// other heading carries its row count, and its unread total only while
		// it is folded — a collapsed section must never swallow a new message.
		var tally = sec.badge
			? (unread > 0
				? '<span class="un" title="' + unread + ' unread">' +
				  (unread > 99 ? '99+' : unread) + '</span>'
				: '')
			: '<span class="n">' + count + '</span>' +
			  (closed && unread > 0
				? '<span class="un" title="' + unread + ' unread in this section">' +
				  (unread > 99 ? '99+' : unread) + '</span>'
				: '');

		return '<div class="ch-sec' + (closed ? ' closed' : '') + '" data-sec="' + sec.key + '" ' +
				'role="button" tabindex="0" aria-expanded="' + (closed ? 'false' : 'true') + '" ' +
				'title="' + (closed ? 'Show ' : 'Hide ') + esc(sec.label) + '">' +
				'<i class="fa fa-caret-down caret" aria-hidden="true"></i>' +
				'<span class="cap">' + esc(sec.label) + '</span>' +
				tally +
			'</div>';
	}

	function renderList() {
		var q = S.filter.toLowerCase();

		// Bucket first, so a heading knows its own size before it is drawn.
		// `total` counts everything in the section and `rows` only what the
		// filter kept, which is why a search can empty a section without
		// making the section itself disappear.
		var buckets = {}, order = [], kinds = 0, shown = 0, html = '';
		SECTIONS.forEach(function (s) { buckets[s.key] = { rows: [], unread: 0, total: 0 }; });

		S.conversations.forEach(function (c) {
			if (!matchesTab(c, S.tab)) return;
			var b = buckets[bucketOf(c)];
			if (!b) return;
			b.total++;
			if (c.unread > 0) b.unread += c.unread;

			// "penalty" is searchable text, so the filter box pulls the
			// flagged DF groups together — the same trick /Chat/df-groups uses
			if (q && (c.name + ' ' + (c.last_text || '') + (c.penalty ? ' penalty' : ''))
			         .toLowerCase().indexOf(q) === -1) return;
			b.rows.push(c);
			shown++;
		});

		SECTIONS.forEach(function (s) { if (buckets[s.key].total > 0) { order.push(s); kinds++; } });

		// Headings earn their space only when there is something to separate.
		// Somebody whose entire list is four direct messages gets the plain
		// list they had before, not a caption sitting over it.
		var heads = kinds > 1;

		// A search that matches NOTHING gets one answer, not one per section.
		// Holding the structure open is worth it while some of it still has
		// rows; five "no match" lines in a row is just the same word five times.
		if (q && shown === 0) {
			return finishList('<div class="ch-empty">Nothing matches “' + esc(S.filter) + '”.' +
				'<br><small>Try a different word, or clear the search.</small></div>');
		}

		order.forEach(function (s) {
			var b = buckets[s.key];
			if (heads) html += sectionHeadHtml(s, b.total, b.unread);
			if (heads && S.secClosed[s.key]) return;

			if (!b.rows.length) {
				// the section has rows, but this filter hid every one of them
				html += '<div class="ch-secempty">No match in ' + esc(s.label.toLowerCase()) + '.</div>';
				return;
			}
			b.rows.forEach(function (c) { html += listItemHtml(c); });
		});

		return finishList(html ? html :
			(CAN.is_external
				? '<div class="ch-empty">No conversations yet.<br><small>Your team will start one with you ' +
				  'when they need to reach you \u2014 you will see it here.</small></div>'
				: '<div class="ch-empty">' + esc(emptyLabel(S.tab)) +
				  '<br><small>Use <b>New</b> to start one.</small></div>'));
	}

	/** Paint the list and keep the badges in step. Every exit from renderList()
	 *  goes through here, so no early return can leave them stale. */
	function finishList(html) {
		$('#chList').html(html);
		updateBadges();
	}

	/** Fold a section away, or open it again, and remember the choice. */
	function toggleSection(key) {
		if (!key) return;
		if (S.secClosed[key]) delete S.secClosed[key];
		else S.secClosed[key] = 1;
		try { localStorage.setItem('ctChatSections', JSON.stringify(S.secClosed)); } catch (e) {}
		renderList();
	}

	function updateBadges() {
		var d = 0, g = 0;
		S.conversations.forEach(function (c) {
			if (!(c.unread > 0)) return;
			if (c.type === 'direct') d += c.unread; else g += c.unread;
		});
		$('#chCntDirect').toggle(d > 0).text(d);
		$('#chCntChannel').toggle(g > 0).text(g);
		fitTabs();          // counts change the row width, so re-measure

		var total = d + g;
		document.title = total > 0 ? '(' + total + ') ' + S.docTitle : S.docTitle;
		// keep the global nav badge in step without waiting for its own poll
		if (window.ChatNav && window.ChatNav.setCount) window.ChatNav.setCount(total);
		toHost('unread', { total: total });
	}

	/* -------------------------------------------------------------- THREAD */

	/**
	 * Forget the conversation we were trying to open and fall back to the list.
	 *
	 * The dock remembers the last open conversation in localStorage so it can
	 * reopen on the same thread after the user navigates. That memory is per
	 * BROWSER, not per user - so a conversation the current user cannot see
	 * (someone else used this browser, they left the group, they were removed,
	 * the row is gone) would be restored on every single page load and 403
	 * forever, with "Try again" retrying the same forbidden id. Clearing the
	 * id on the host is what actually breaks that loop.
	 */
	function dropConversation(reason) {
		S.activeId = 0;
		toHost('conv', { id: 0 });          // stop the dock restoring it
		$('#chHead').hide(); $('#chComp').hide();
		$('#chPinBar').hide().empty();
		$('#chSide').removeClass('hide-sm'); $('#chMain').removeClass('show-sm');

		$('#chBody').html(
			'<div class="ch-empty" style="margin-top:70px;">' +
				'<i class="fa fa-comments-o" style="font-size:34px;color:#cbd8ea;display:block;margin-bottom:10px;"></i>' +
				'<div style="font-weight:600;color:#475569;">' + esc(reason) + '</div>' +
				'<div style="margin-top:5px;">Pick a conversation from the list to carry on.</div>' +
				'<button class="ch-btn primary" id="chToList" style="margin-top:14px;">Back to chats</button>' +
			'</div>');

		// the list itself may be stale for the same reason - refresh it
		refreshConversations();
	}

	function openConversation(id, push) {
		if (!id) return;
		S.activeId = id;
		S.replyTo = null; S.pendingTags = []; S.pendingFiles = []; S.mentions = []; S.mentionAll = false;
		S.privateOrigin = null;
		S.pinned = true; $('#chJump').hide();
		syncComposerBars();
		renderList();
		$('#chSide').addClass('hide-sm'); $('#chMain').addClass('show-sm');

		$('#chBody').html('<div class="ch-empty" style="margin-top:80px;"><i class="fa fa-spinner fa-spin"></i> Loading…</div>');

		$.getJSON(BASE + 'conversation/' + id)
		.fail(function (x) {
			// Not-allowed / gone is not a transient failure: retrying can only
			// fail again. Forget the id and send the user back to their list.
			if (x && (x.status === 403 || x.status === 404)) {
				return dropConversation(x.status === 404
					? 'That conversation no longer exists.'
					: 'You are no longer a member of that conversation.');
			}

			// never leave a spinner sitting there - say what went wrong
			$('#chBody').html(
				'<div class="ch-empty" style="margin-top:70px;">' +
					'<i class="fa fa-exclamation-triangle" style="font-size:30px;color:#f59e0b;display:block;margin-bottom:10px;"></i>' +
					'<div style="font-weight:600;color:#475569;">Could not load this conversation</div>' +
					'<div style="margin-top:5px;">' + esc(xhrReason(x)) + '</div>' +
					'<button class="ch-btn primary" id="chRetry" style="margin-top:14px;">Try again</button>' +
					'<div id="chDiag" style="margin-top:12px;font-size:11.5px;color:#94a3b8;"></div>' +
				'</div>');
			$('#chComp').hide();

			// a 500 here is almost always a half-finished deploy - check and say so
			if (x && x.status >= 500) {
				$.getJSON(BASE + 'health', function (h) {
					if (h && !h.ok) {
						$('#chDiag').html('<b>Setup incomplete:</b> ' + esc(h.advice));
					}
				});
			}
		})
		.done(function (r) {
			if (!r.ok) return dropConversation('You no longer have access to that conversation.');
			S.header  = r.header;
			S.members = r.members;
			S.myRole  = r.my_role;
			S.messages = r.messages;
			S.oldestId = r.messages.length ? r.messages[0].id : 0;
			S.lastMessageId = r.messages.length ? r.messages[r.messages.length - 1].id : 0;

			renderHeader(); renderMessages(true); renderPins(r.pinned); renderInfo();
			// renderHeader() -> renderArchiveBar() already settled whether the
			// composer belongs on screen. An unconditional show() here would
			// hand it straight back on an archived group.
			renderArchiveBar();

			var c = findConv(id);
			if (c) { c.unread = 0; renderList(); }

			// the host remembers this so the dock reopens on the same
			// conversation after the user navigates to another module
			toHost('conv', { id: id });

			if (push && !DOCK && window.history && history.replaceState) {
				history.replaceState(null, '', BASE + 'index/' + id);
			}
		});
	}

	function findConv(id) {
		for (var i = 0; i < S.conversations.length; i++) if (S.conversations[i].id == id) return S.conversations[i];
		return null;
	}

	function renderHeader() {
		var h = S.header, c = findConv(h.id) || {};
		$('#chHead').css('display', 'flex');
		$('#chHeadAva').replaceWith(avatar({
			avatar: h.avatar, initials: (h.name || '?').substring(0, 2).toUpperCase(), color: c.color
		}, h.type === 'direct' ? '' : 'sq').replace('class="ch-avatar', 'id="chHeadAva" class="ch-avatar'));

		$('#chHeadName').text(h.name || 'Conversation');
		// a DF group whose DF is marked flies the flag next to the group name
		$('#chHeadPen').html(penaltyFlag(h.ref, true));

		if (h.type === 'direct') {
			// coloured dot + label, so presence reads at a glance
			$('#chHeadSub').html(
				'<span class="ch-status' + (h.online ? '' : ' off') + '"></span>' +
				esc(h.online ? 'Online' : 'Offline') +
				(roleLine(h) ? ' · ' + esc(roleLine(h)) : '')
			);
		} else {
			$('#chHeadSub').text(
				S.members.length + ' members' + (h.description ? ' · ' + h.description : '')
			);
		}

		renderArchiveBar();
	}

	/**
	 * The bar an archived group wears, and the closing of everything below it.
	 *
	 * An archived room is READ-ONLY. Letting people keep posting into a group
	 * that was closed because its machine shipped is how you end up with half
	 * a conversation in a room nobody is watching — and the bundle, built at
	 * the moment of archiving, would silently stop matching what the thread
	 * shows. Reopening it is one click for anyone who can manage the group.
	 */
	function renderArchiveBar() {
		var h = S.header;
		if (!h || !h.archived) {
			$('#chArchBar').hide().empty();
			$('#chComp').show();
			return;
		}

		var when = h.archived_at ? (String(h.archived_at).split(' ')[0] || '') : '';
		var bits = [];
		if (h.archive_files) {
			bits.push(h.archive_files + (h.archive_files === 1 ? ' file' : ' files') +
			          (h.archive_size ? ' · ' + esc(h.archive_size) : ''));
		}

		var html = '<div class="t"><i class="fa fa-archive"></i> This group is archived' +
					(when ? ' <span style="font-weight:400;color:#64748b;">· ' + esc(when) + '</span>' : '') +
				'</div>' +
				'<div class="w">' +
					esc(h.archive_reason || 'The work in this group is finished.') +
					' Nobody can post here any more, and the history stays exactly as it is.' +
					(h.archive_files
						? ' Every file shared here is in one download' +
						  (bits.length ? ' (' + bits.join('') + ')' : '') + '.'
						: ' No files were shared in this group.') +
				'</div><div class="b">';

		if (h.archive_files) {
			html += '<a href="' + BASE + 'archive_zip/' + h.id + '" ' +
					'title="Download every file shared in this group, as one .zip">' +
					'<i class="fa fa-file-archive-o"></i> Download all files</a>';
		}
		if (h.can_unarchive) {
			// Says what it will DO, not just what it is called: reopening
			// restores the loose files from the bundle, which is the part
			// somebody needs to know before they press it.
			html += '<button id="chUnarchive" title="Reopen this group and restore its files">' +
					'<i class="fa fa-undo"></i> Reopen group</button>';
		}
		html += '</div>';

		$('#chArchBar').html(html).show();
		$('#chComp').hide();
	}

	function renderMessages(scroll) {
		if (!S.messages.length) {
			$('#chBody').html('<div class="ch-empty" style="margin-top:90px;">No messages yet. Say hello 👋</div>');
			return;
		}
		var html = '';
		if (S.oldestId) {
			html += '<div class="ch-older"><button type="button" id="chOlder">' +
			        '<i class="fa fa-history"></i> Load earlier messages</button></div>';
		}
		var lastDay = '';
		S.messages.forEach(function (m) {
			if (m.day !== lastDay) { html += '<div class="ch-day"><span>' + esc(m.day) + '</span></div>'; lastDay = m.day; }
			html += messageHtml(m);
		});
		$('#chBody').html(html);
		if (scroll !== false) scrollBottom();
	}

	function messageHtml(m) {
		if (m.type === 'system') {
			return '<div class="ch-sys" data-mid="' + m.id + '"><span>' + esc(m.body) + '</span></div>';
		}
		var mine = isMine(m), h = '';

		var atMe = !mine && (m.mentions || []).some(function (x) {
			return x.id == ME.id && x.type === ME.type;
		});

		h += '<div class="ch-msg' + (mine ? ' mine' : '') + (atMe ? ' atme' : '') + '" data-mid="' + m.id + '">';
		h += avatar(m);
		h += '<div class="ch-bub-wrap">';

		if (!mine) {
			h += '<div class="ch-sender">' + esc(m.sender_name) +
			     (roleLine({ role: m.sender_role, plant: m.sender_plant })
			        ? '<span class="r">' + esc(roleLine({ role: m.sender_role, plant: m.sender_plant })) + '</span>'
			        : '') + '</div>';
		}

		h += '<div class="ch-bub">';
		if (m.origin) {
			var fwd = m.origin_kind === 'forward';
			h += '<div class="ch-origin ' + (fwd ? 'fwd' : 'priv') + '">' +
					'<div class="oh"><i class="fa ' + (fwd ? 'fa-share' : 'fa-user-secret') + '"></i> ' +
						(fwd ? 'Forwarded from ' : 'Privately replying to ') + esc(m.origin.from) +
						(m.origin.where ? ' in ' + esc(m.origin.where) : '') +
					'</div>' +
					(fwd ? '' : '<div class="ob">' + esc(m.origin.body) + '</div>') +
				'</div>';
		}
		if (m.reply_to) {
			h += '<div class="ch-quote" data-jump="' + m.reply_to.id + '"><b>' + esc(m.reply_to.sender) + '</b>' +
			     '<span>' + esc(m.reply_to.body) + '</span></div>';
		}
		if (m.is_deleted) {
			h += '<span class="ch-deleted"><i class="fa fa-ban"></i> This message was deleted</span>';
		} else if (m.call) {
			h += callCardHtml(m.call, mine);
		} else {
			if (m.body) h += '<div>' + renderBody(m.body, m.mentions) + '</div>';

			(m.attachments || []).forEach(function (a) {
				if (a.is_image) {
					h += '<a href="' + esc(a.url) + '" target="_blank" rel="noopener noreferrer">' +
					     '<img class="ch-img" src="' + esc(a.url) + '" alt="' + esc(a.name) + '"></a>';
				} else {
					h += '<div class="ch-file">' +
							'<div class="ico">' + esc(a.ext || 'file') + '</div>' +
							'<div style="min-width:0;flex:1;">' +
								'<div class="fn">' + esc(a.name) + '</div>' +
								'<div class="fs">' + esc(a.size_h) + '</div>' +
							'</div>' +
							'<div class="acts">' +
								'<a href="' + esc(a.url) + '" target="_blank" rel="noopener noreferrer">Open</a>' +
								'<a href="' + esc(a.download) + '">Download</a>' +
							'</div>' +
						'</div>';
				}
			});

			(m.tags || []).forEach(function (t) {
				h += '<a class="ch-tag" href="' + esc(t.url) + '" target="_blank" rel="noopener noreferrer">' +
						'<div class="code">' + esc(refLabel(t.ref_type)) + ' · ' + esc(t.code) + '</div>' +
						'<div class="ttl">' + esc(t.title) + '</div>' +
						'<span class="st">' + esc(t.status) + '</span> ' +
						penaltyFlag(t, true) +
					'</a>';
			});
		}
		h += '</div>'; /* bubble */

		if (m.reactions && m.reactions.length) {
			h += '<div class="ch-reacts">';
			m.reactions.forEach(function (r) {
				var byMe = r.keys.indexOf(ME.type + ':' + ME.id) !== -1;
				h += '<span class="ch-react' + (byMe ? ' byme' : '') + '" data-emoji="' + esc(r.emoji) +
				     '" title="' + esc(r.users.join(', ')) + '">' + esc(r.emoji) + ' ' + r.count + '</span>';
			});
			h += '</div>';
		}

		h += '<div class="ch-stamp">' + esc(m.time) +
		     (m.is_edited ? ' · edited' : '') + (m.is_pinned ? ' · <i class="fa fa-thumb-tack"></i> pinned' : '') +
		     (mine && m.type !== 'system' ? seenHtml(m) : '') + '</div>';
		h += '</div>'; /* wrap */

		/* hover toolbar */
		if (!m.is_deleted) {
			h += '<div class="ch-tools">';
			QUICK.forEach(function (e) { h += '<button data-act="react" data-emoji="' + e + '">' + e + '</button>'; });
			h += '<button data-act="reply" title="Reply"><i class="fa fa-reply"></i></button>';
			h += '<button data-act="fwd" title="Forward"><i class="fa fa-share"></i></button>';
			if (!mine && CAN.start_dm && S.header && S.header.type !== 'direct') {
				h += '<button data-act="priv" title="Reply privately to ' + esc(m.sender_name) + '">' +
				     '<i class="fa fa-user-secret"></i></button>';
			}
			// One control, two jobs — and it has to SAY which one it is doing.
			// Labelled "Pin" on an already-pinned message, the only way to
			// discover it also unpins was to press it and see.
			if (CAN.pin) {
				h += '<button data-act="pin" title="' + (m.is_pinned ? 'Unpin' : 'Pin') + '">' +
				     '<i class="fa fa-thumb-tack"></i>' + (m.is_pinned ? '<i class="fa fa-times"></i>' : '') +
				     '</button>';
			}
			if (CAN.lead_tag) h += '<button data-act="tag" title="Link a record"><i class="fa fa-link"></i></button>';
			// edit: author only, and only inside the 15-minute window
			if (mine && editLeft(m) > 0) {
				h += '<button data-act="edit" title="Edit (' + Math.ceil(editLeft(m) / 60) + ' min left)">' +
				     '<i class="fa fa-pencil"></i></button>';
			}
			// unsend: author only, same 15-minute window as editing
			if (mine && editLeft(m) > 0) {
				h += '<button data-act="del" title="Unsend (' + Math.ceil(editLeft(m) / 60) + ' min left)">' +
				     '<i class="fa fa-trash"></i></button>';
			}
			h += '</div>';
		}
		return h + '</div>';
	}

	/**
	 * The join card for a Zoom / Google Meet call.
	 * Three states: live with a link (everyone can join), live but still
	 * waiting for the starter to paste the link, and ended.
	 */
	function callCardHtml(c, mine) {
		var ended = c.status === 'ended';
		var cls   = 'ch-call ' + (c.provider || 'zoom') + (ended ? ' ended' : '');
		var icon  = c.provider === 'meet' ? 'fa-video-camera' : 'fa-video-camera';

		var h = '<div class="' + cls + '" data-call="' + c.id + '">';
		h += '<div class="hd">' +
				'<div class="ic"><i class="fa ' + icon + '"></i></div>' +
				'<div style="min-width:0;">' +
					'<div class="ttl">' + esc(c.label) + (ended ? ' call ended' : ' call') + '</div>' +
					'<div class="cs">Started ' + esc(c.started) + '</div>' +
				'</div>' +
			'</div>';

		if (c.topic) h += '<div class="topic">' + esc(c.topic) + '</div>';

		if (!ended && !c.join_url) {
			h += '<div class="waiting"><i class="fa fa-clock-o"></i> Waiting for the meeting link…</div>';
		}

		h += '<div class="btns">';
		if (!ended && c.join_url) {
			h += '<a class="join" href="' + esc(c.join_url) + '" target="_blank" rel="noopener noreferrer">' +
			     '<i class="fa fa-sign-in"></i> Join ' + esc(c.label) + '</a>';
		}
		if (!ended && mine && !c.join_url) {
			h += '<button type="button" class="addlink" data-addlink="' + c.id + '">Add the link</button>';
		}
		if (!ended && mine) {
			h += '<button type="button" class="endc" data-endcall="' + c.id + '">End call</button>';
		}
		h += '</div></div>';
		return h;
	}

	/**
	 * Read receipt shown under your own messages.
	 * A 1:1 chat just needs "Seen"; a group needs a count you can click to see
	 * exactly who has and has not caught up.
	 */
	function seenHtml(m) {
		var total = m.seen_total || 0;
		var seen  = m.seen_count || 0;
		if (!total) return '';                       // nobody else in the room yet

		var isGroup = S.header && S.header.type !== 'direct';
		if (!isGroup) {
			return seen > 0
				? ' · <span class="ch-seen all"><i class="fa fa-check"></i> Seen</span>'
				: ' · <span class="ch-seen"><i class="fa fa-check"></i> Sent</span>';
		}
		if (seen === 0) return ' · <span class="ch-seen" data-seen="' + m.id + '">Sent</span>';
		return ' · <span class="ch-seen' + (seen >= total ? ' all' : '') + '" data-seen="' + m.id + '">' +
		       '<i class="fa fa-check"></i> Seen by ' + seen + ' of ' + total + '</span>';
	}

	/**
	 * Pull the previous page of history.
	 *
	 * History size never affects load time: a conversation opens with the most
	 * recent page only (Chat_model::PAGE_SIZE), and each older page is one
	 * indexed lookup on (conversation_id, id) — the same cost whether the room
	 * holds 50 messages or 500,000. Scroll position is preserved so the thread
	 * does not jump under the reader.
	 */
	function loadOlder() {
		if (S.loadingOlder || !S.oldestId || !S.activeId) return;
		S.loadingOlder = true;

		var body = document.getElementById('chBody');
		var keepH = body ? body.scrollHeight : 0;

		$('#chOlder').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading…');

		$.getJSON(BASE + 'messages/' + S.activeId, { before: S.oldestId })
			.done(function (r) {
				S.loadingOlder = false;
				if (!r.ok || !r.messages.length) {
					S.oldestId = 0;              // nothing older; the button disappears
					renderMessages(false);
					return;
				}
				S.messages = r.messages.concat(S.messages);
				S.oldestId = r.messages[0].id;
				renderMessages(false);
				if (body) body.scrollTop = body.scrollHeight - keepH;   // stay where you were
			})
			.fail(function (x) {
				S.loadingOlder = false;
				$('#chOlder').prop('disabled', false).html('<i class="fa fa-history"></i> Load earlier messages');
				toast('Could not load older messages', xhrReason(x), 'err');
			});
	}

	function scrollBottom() {
		var b = document.getElementById('chBody');
		if (!b) return;
		b.scrollTop = b.scrollHeight;
		S.pinned = true;
		$('#chJump').hide();
	}
	function nearBottom() {
		var b = document.getElementById('chBody');
		return b && (b.scrollHeight - b.scrollTop - b.clientHeight) < 140;
	}
	/**
	 * Follow the conversation unless the user has deliberately scrolled up.
	 * A plain nearBottom() check at append time was not enough: an image or a
	 * file card finishes loading after the append and grows the list, which
	 * silently left the newest message below the fold.
	 */
	function scrollIfFollowing(force) {
		if (!force && !S.pinned) { $('#chJump').show(); return; }
		scrollBottom();
		// re-pin once late-loading media has changed the height
		clearTimeout(S._scrollT);
		S._scrollT = setTimeout(function () { if (S.pinned) scrollBottom(); }, 120);
	}

	/**
	 * How much longer a pin has, as a phrase rather than a timestamp.
	 *
	 * Deliberately coarse: "2 days left" is what somebody wants to know, and
	 * an exact expiry time would imply a precision the sweep does not have —
	 * a pin is taken down the next time its conversation's pin bar is read,
	 * not on the second. Anything under an hour says "expiring soon" for the
	 * same reason.
	 *
	 * '' for a pin with no expiry, which is every pin made before chat_004 and
	 * anything pinned with "Always".
	 */
	function pinLeft(m) {
		if (!m.pinned_until) return '';
		// 'YYYY-MM-DD HH:MM:SS' is not parseable by every browser we support;
		// hand the parts over explicitly rather than trusting Date(string).
		var p = String(m.pinned_until).split(/[- :]/);
		if (p.length < 6) return '';
		var ms = new Date(p[0], p[1] - 1, p[2], p[3], p[4], p[5]).getTime() - Date.now();
		if (ms <= 0)          return 'expiring now';
		var mins = Math.floor(ms / 60000);
		if (mins < 60)        return 'expiring soon';
		// ceil, not floor: a pin made for 24 hours is read a heartbeat later,
		// and "23h left" on something you just set reads as broken.
		var hrs = Math.ceil(mins / 60);
		if (hrs < 24)         return hrs + 'h left';
		var days = Math.round(hrs / 24);
		return days + (days === 1 ? ' day left' : ' days left');
	}

	function renderPins(list) {
		if (!list || !list.length) { $('#chPinBar').hide().empty(); return; }

		var h = '<b><i class="fa fa-thumb-tack"></i> Pinned (' + list.length + ')</b>';
		list.slice(0, 3).forEach(function (m) {
			var left = pinLeft(m);
			h += '<div class="pin-item">' +
					'<span class="pt" data-jump="' + m.id + '" title="Go to this message">· ' +
						esc(m.sender_name) + ': ' + esc((m.body || 'Attachment').substring(0, 90)) +
					'</span>' +
					(left ? '<span class="pin-until">' + esc(left) + '</span>' : '') +
					// Unpinning belongs HERE, on the row you are looking at.
					// The tack on the message still toggles, but nobody should
					// have to scroll back through the thread to find it.
					(CAN.pin
						? '<button class="pin-off" data-unpin="' + m.id + '" ' +
						  'title="Unpin this message"><i class="fa fa-times"></i></button>'
						: '') +
				'</div>';
		});
		if (list.length > 3) {
			h += '<div class="pin-more">and ' + (list.length - 3) + ' more pinned</div>';
		}
		$('#chPinBar').html(h).show();
	}

	/**
	 * Pin a message, having first asked how long for.
	 *
	 * The ask is the whole point: a pin nobody put a limit on is a pin nobody
	 * ever takes down, and the pin bar fills with things that stopped
	 * mattering weeks ago. "Always" is still there for the standing notice
	 * that genuinely should outlive everything.
	 */
	function pinModal(mid) {
		var h = '<div class="ch-pinpick">';
		PIN_FOR.forEach(function (d) {
			h += '<button data-days="' + d.days + '">' +
					'<i class="fa fa-' + (d.days ? 'clock-o' : 'thumb-tack') + '"></i>' +
					esc(d.label) +
					(d.days ? '' : '<small>until someone unpins it</small>') +
				'</button>';
		});
		h += '</div>';

		modal('Pin this message for…', h, 'Cancel', closeModal);
		// The choices ARE the action, so there is nothing for a confirm button
		// to do — and the footer already has its own Cancel. Same shape as the
		// call-provider picker.
		$('#chModalOk').hide();

		$('#chModalBody').off('click', '[data-days]').on('click', '[data-days]', function () {
			closeModal();
			sendPin(mid, $(this).data('days'));
		});
	}

	/** The one place that posts a pin or an unpin, so both refresh alike. */
	function sendPin(mid, days) {
		post('pin', { message_id: mid, days: days || 0 }).done(function (r) {
			if (!r.ok) return toast('Not permitted', '', 'err');
			renderPins(r.pinned_list);
			openConversation(S.activeId);
		});
	}

	function renderInfo() {
		var h = S.header;
		if (h.ref) {
			$('#chInfoRef').show().html(
				'<h5>Linked ' + esc(refLabel(h.ref.ref_type).toLowerCase()) + '</h5>' +
				'<a class="ch-tag" href="' + esc(h.ref.url) + '" target="_blank" rel="noopener noreferrer">' +
					'<div class="code">' + esc(refLabel(h.ref.ref_type)) + ' · ' + esc(h.ref.code) + '</div>' +
					'<div class="ttl">' + esc(h.ref.title) + '</div>' +
					// the penalty rides alongside the status, never replacing
					// it - a DF can be running and penalised at the same time
					'<span class="st">' + esc(h.ref.status) + '</span> ' +
					penaltyFlag(h.ref, true) + '</a>');
		} else $('#chInfoRef').hide();

		renderInfoSection();

		// running a group you own does not need the CRM-wide capability -
		// mirrors Chat::can_manage_members() on the server
		var canManage = CAN.is_admin || S.myRole === 'owner' || S.myRole === 'admin' || CAN.manage_members;
		// appointing admins is the OWNER's call alone, so an admin cannot
		// quietly demote the person who created the group
		var isOwner   = CAN.is_admin || S.myRole === 'owner';
		var mh = '';
		S.members.forEach(function (m) {
			mh += '<div class="ch-member">' +
					'<div class="ch-ava-wrap">' + avatar(m) +
						'<span class="ch-dot' + (m.is_online ? '' : ' off') + '" title="' +
						(m.is_online ? 'Online' : 'Offline') + '"></span>' +
					'</div>' +
					'<div style="min-width:0;">' +
						'<div class="n">' + esc(m.name) +
							(m.member_role === 'owner' ? '<span class="ch-owner-tag">OWNER</span>' : '') +
							(m.member_role === 'admin' ? '<span class="ch-admin-tag">ADMIN</span>' : '') +
						'</div>' +
						'<div class="r">' +
							'<span class="ch-status' + (m.is_online ? '' : ' off') + '"></span>' +
							esc(m.is_online ? 'Online' : 'Offline') +
							(roleLine(m) ? ' · ' + esc(roleLine(m)) : '') +
						'</div>' +
					'</div>' +
					(isOwner && h.type !== 'direct' && m.member_role !== 'owner'
						? '<i class="fa ' + (m.member_role === 'admin' ? 'fa-user-times' : 'fa-user-plus') + ' x adm" ' +
						  'data-role="' + m.id + '" data-rtype="' + m.type + '" ' +
						  'data-to="' + (m.member_role === 'admin' ? 'member' : 'admin') + '" title="' +
						  (m.member_role === 'admin' ? 'Remove admin rights' : 'Make group admin') + '"></i>' : '') +
					(canManage && h.type !== 'direct' && m.member_role !== 'owner'
						? '<i class="fa fa-times x" data-remove="' + m.id + '" data-rtype="' + m.type + '" title="Remove from group"></i>' : '') +
				'</div>';
		});
		$('#chMembers').html(mh);
		$('#chMemberCount').text('(' + S.members.length + ')');

		var acts = '';
		if (h.type !== 'direct') {
			// h.can_add is true for someone who may run the room OR who leads a
			// team — a team leader may add their own people only, which
			// add_members() re-checks on the server.
			if (canManage || h.can_add) {
				acts += '<button class="ch-btn primary" id="chAddMembers" style="width:100%;margin-bottom:7px;">' +
				        '<i class="fa fa-user-plus"></i> Add members</button>';
			}
			if (canManage) {
				acts += '<button class="ch-btn ghost" id="chGroupPhoto" style="width:100%;margin:0 0 7px;">' +
				        '<i class="fa fa-camera"></i> ' + (h.avatar ? 'Change' : 'Add') + ' group photo</button>';
				acts += '<input type="file" id="chGroupPhotoInput" accept="image/*" style="display:none;">';
				acts += '<button class="ch-btn ghost" id="chRenameChannel" style="width:100%;margin:0 0 7px;">' +
				        '<i class="fa fa-pencil"></i> Rename group</button>';
			}
			// Archiving is the gentle end of a group's life and sits above
			// Leave and Delete for that reason: it is almost always what
			// somebody reaching for "Delete group" actually wanted.
			if (h.can_archive) {
				acts += '<button class="ch-btn ghost" id="chArchiveGroup" style="width:100%;margin:0 0 7px;">' +
				        '<i class="fa fa-archive"></i> Archive group</button>';
			}
			acts += '<button class="ch-btn ghost" id="chLeave" style="width:100%;margin:0 0 7px;color:#dc2626;">' +
			        '<i class="fa fa-sign-out"></i> Leave group</button>';
			// Deleting the room is the creator's call, not every admin's —
			// see Chat::delete_channel().
			if (h.can_delete) {
				acts += '<button class="ch-btn ghost" id="chDeleteGroup" style="width:100%;margin:0;' +
				        'color:#b91c1c;border-color:#fecaca;background:#fef2f2;">' +
				        '<i class="fa fa-trash"></i> Delete group</button>';
			}
		}
		$('#chMemberActions').html(acts);

		/* ---------------------------------------------------------- FILES
		 * A file in this list used to be a name and a size with a "Get" link,
		 * which answers none of the questions people actually bring to it:
		 * who sent this, when, and what was being discussed around it. The
		 * attachment was pushed on its own and the message it arrived in —
		 * which knows all three — was thrown away.
		 *
		 * So the message is carried alongside, and each row now:
		 *   - says WHO shared it and WHEN ("Anil Panchal · Today, 12:08 PM")
		 *   - JUMPS to that message in the thread when the row is clicked,
		 *     landing you in the conversation the file belongs to
		 *   - has a plain "Download" button instead of "Get", which nobody
		 *     should have to guess at
		 *
		 * Jump and download have to be separate targets: the row is the jump,
		 * the button is the download, and the button stops the click so one
		 * never triggers the other.
		 *
		 * Still only the messages currently loaded in the thread — this list
		 * has always been "recent files", not an archive of the whole
		 * conversation, and the jump can only land on a message that is
		 * actually on screen, so the two limits are the same limit.
		 */
		var files = [];
		S.messages.forEach(function (m) {
			(m.attachments || []).forEach(function (a) { files.push({ a: a, m: m }); });
		});

		if (files.length) {
			var fh = '';
			files.slice(-12).reverse().forEach(function (f) {
				var a = f.a, m = f.m;
				// "Today, 12:08 PM" — day_label() already says Today /
				// Yesterday / 04 Sep 2026, so the date reads the same here as
				// it does on the separators in the thread itself.
				var when = (m.day ? m.day + ', ' : '') + (m.time || '');
				fh += '<div class="ch-file ch-sfile" data-jump="' + m.id + '" ' +
						'title="Go to this message">' +
						'<div class="ico">' + esc(a.ext || 'f') + '</div>' +
						'<div style="min-width:0;flex:1;">' +
							// the full name on hover: these truncate, and
							// "WhatsApp Image 2026…" identifies nothing
							'<div class="fn" title="' + esc(a.name) + '">' + esc(a.name) + '</div>' +
							'<div class="fs">' + esc(a.size_h) + '</div>' +
							'<div class="fby"><i class="fa fa-user"></i> ' + esc(m.sender_name || 'Unknown') +
							(when ? ' <span class="dot">·</span> ' + esc(when) : '') + '</div>' +
						'</div>' +
						'<div class="acts">' +
							'<a class="ch-dl" href="' + esc(a.download) + '" download ' +
							'title="Download ' + esc(a.name) + '">' +
							'<i class="fa fa-download"></i> Download</a>' +
						'</div>' +
					'</div>';
			});
			$('#chSharedFiles').html(fh);
		} else $('#chSharedFiles').text('No files yet');
	}

	/**
	 * "Where this group lives" in the Details panel.
	 *
	 * Worth stating even when it cannot be changed: a room's section is how
	 * everyone else finds it, and the only other place it is visible is the
	 * sidebar heading — which is exactly what someone reading Details has
	 * scrolled away from.
	 */
	function renderInfoSection() {
		var h = S.header, $box = $('#chInfoSection');
		if (!h || h.type === 'direct') return $box.hide().empty();

		var kind = h.group_kind ||
			((h.ref_type === 'df' && h.ref_id > 0) ? 'df' : (h.department_id > 0 ? 'department' : 'other'));

		var where, why;
		if (kind === 'df') {
			where = 'DF Groups';
			why   = 'This room belongs to a DF, so it is filed with the other DF groups.';
		} else if (kind === 'department') {
			where = 'Department Groups';
			why   = 'Filed under <b>' + esc(h.department || 'a department') + '</b>.';
		} else {
			where = 'Other Groups';
			why   = 'Not tied to a DF or a department.';
		}

		$box.show().html(
			'<h5>Where this group lives</h5>' +
			'<div class="ch-where"><i class="fa ' +
				(kind === 'df' ? 'fa-wrench' : kind === 'department' ? 'fa-sitemap' : 'fa-comments-o') +
				'"></i> ' + esc(where) + '</div>' +
			'<div class="ch-wherewhy">' + why + '</div>' +
			(h.can_set_department
				? '<button class="ch-btn ghost" id="chSetDept" style="width:100%;margin-top:9px;">' +
				  '<i class="fa fa-sitemap"></i> ' +
				  (h.department_id > 0 ? 'Change department' : 'Move to a department') + '</button>'
				: ''));
	}

	/**
	 * Move this group into a department, or out of one.
	 *
	 * Refused on the server for a DF group, and the button is not drawn for
	 * one either (`can_set_department`) — a DF group's section follows its DF.
	 */
	function setDepartmentModal() {
		loadDepartments(function (depts) {
			if (!depts.length) return toast('No departments to choose from', '', 'err');

			var cur = S.header.department_id || 0;
			var h = '<label>Department</label>' +
				'<select class="ch-search" id="chDeptSel" style="margin-bottom:13px;">' +
					deptOptionsHtml(depts, cur) +
				'</select>' +
				'<div style="font-size:11.5px;color:var(--ch-muted);line-height:1.5;">' +
					'Everyone in this group sees it move to the chosen heading in their sidebar, ' +
					'and the change is recorded in the conversation. ' +
					'Pick <b>No department</b> to file it under Other Groups instead.' +
				'</div>';

			modal('Move group to a department', h, 'Save', function () {
				var want = parseInt($('#chDeptSel').val(), 10) || 0;
				if (want === cur) return closeModal();
				modalBusy(true, 'Saving\u2026');
				post('set_department', { conversation_id: S.activeId, department_id: want })
					.done(function (r) {
						modalBusy(false);
						if (!r || !r.ok) {
							return toast((r && r.message_text) || 'Could not move the group', '', 'err');
						}
						closeModal();
						// re-read the room so the header, this panel and the
						// sidebar all come from the one source of truth
						openConversation(S.activeId, false);
						refreshConversations();
					})
					.fail(function (x) {
						modalBusy(false);
						// the server explains the two refusals worth explaining
						// (a DF group, and the migration not being run yet)
						var why = '';
						try { why = (JSON.parse(x.responseText) || {}).message_text || ''; } catch (e) {}
						toast('Could not move the group', why || xhrReason(x), 'err');
					});
			});
		});
	}

	/* ----------------------------------------------------------- COMPOSER */
	function syncComposerBars() {
		if (S.privateOrigin) {
			$('#chPrivName').text(S.privateOrigin.from || '');
			$('#chPrivText').text((S.privateOrigin.body || '').substring(0, 120));
			$('#chPrivBar').css('display', 'flex');
		} else $('#chPrivBar').hide();

		if (S.replyTo) {
			$('#chReplyName').text(S.replyTo.sender_name);
			$('#chReplyText').text((S.replyTo.body || 'Attachment').substring(0, 110));
			$('#chReplyBar').css('display', 'flex');
		} else $('#chReplyBar').hide();

		if (S.pendingTags.length) {
			var h = '';
			S.pendingTags.forEach(function (t, i) {
				h += '<span class="ch-chip">' + esc(t.code) + ' — ' + esc(t.title) +
				     ' <i class="fa fa-times" data-untag="' + i + '"></i></span> ';
			});
			$('#chTagChips').html(h);
			$('#chTagBar').css('display', 'flex');
		} else $('#chTagBar').hide();

		if (S.pendingFiles.length) {
			var f = '';
			S.pendingFiles.forEach(function (file, i) {
				var isImg = file.type && file.type.indexOf('image/') === 0;
				f += '<span class="ch-chip">' +
						'<i class="fa ' + (isImg ? 'fa-picture-o' : 'fa-file-o') + '"></i> ' + esc(file.name) +
						// images can be marked up before they are sent
						(isImg ? '<span class="ch-chip-annot" data-annot="' + i + '" ' +
						         'title="Add arrows, boxes, text or blur out details">' +
						         '<i class="fa fa-pencil"></i> Annotate</span>' : '') +
						' <i class="fa fa-times" data-unfile="' + i + '"></i>' +
					'</span>';
			});
			$('#chFilesPre').html(f);
		} else $('#chFilesPre').empty();
	}

	/**
	 * Show the message the instant it is sent, before the server answers.
	 *
	 * The round trip is where chat feels slow: on shared hosting, writing the
	 * message plus its notification and event fan-out is a few hundred
	 * milliseconds, and staring at your own unchanged composer for that long
	 * reads as "did that even work?". The bubble goes up immediately and is
	 * swapped for the real one when the reply lands - or taken away again,
	 * with the text handed back, if the send fails.
	 *
	 * Deliberately kept OUT of S.messages: it has no real id, so letting it
	 * into the array would break dedupe, editing and read receipts. It is a
	 * picture of a message, nothing more.
	 */
	var pendSeq = 0;

	/**
	 * @param replyTo the message being answered, or null. The quote is drawn
	 *        on the pending bubble too — replying is the one case where the
	 *        bubble alone is ambiguous, because a reply that shows no quote
	 *        until the server answers looks exactly like a reply that failed
	 *        to attach. Same markup as the real bubble, so nothing shifts when
	 *        the two swap over.
	 */
	function showPending(pid, body, replyTo) {
		var h = '<div class="ch-msg mine ch-pend" data-pend="' + pid + '">' +
			avatar({ initials: ME.initials }) +      // initials, never a URL that could 404
			'<div class="ch-bub-wrap"><div class="ch-bub">' +
				(replyTo
					? '<div class="ch-quote"><b>' + esc(replyTo.sender_name || '') + '</b>' +
					  '<span>' + esc((replyTo.body || 'Attachment').substring(0, 110)) + '</span></div>'
					: '') +
				(body ? '<div>' + renderBody(body, []) + '</div>' : '') +
			'</div>' +
			'<div class="ch-stamp"><i class="fa fa-clock-o"></i> Sending…</div>' +
			'</div></div>';

		if ($('#chBody .ch-empty').length) $('#chBody').html(h);
		else $('#chBody').append(h);
		scrollIfFollowing(true);
	}
	function clearPending(pid) { $('#chBody .ch-pend[data-pend="' + pid + '"]').remove(); }

	function sendMessage() {
		if (!S.activeId) return;
		var body = $('#chInput').val().trim();
		if (!body && !S.pendingFiles.length) return;

		// The button is only frozen for an UPLOAD, which genuinely occupies the
		// composer until it finishes. A text message must never block the next
		// one: the bubble is already on screen and the box is already empty, so
		// locking the button for the round trip only stops somebody firing off
		// four quick lines the way they would in WhatsApp. That wait was the
		// whole of "sending is slow" — the send itself was never the hold-up.
		if (S.pendingFiles.length) $('#chSend').prop('disabled', true);

		/* EVERYTHING THIS MESSAGE CARRIES IS CAPTURED HERE, FIRST.
		 *
		 * The composer is emptied further down so the next message can be
		 * typed straight away, and the request is built after that — so
		 * anything read from S.* at the bottom would already be gone. That is
		 * exactly what happened when the early clear was added: `reply_to` was
		 * read after `S.replyTo = null`, so every reply was sent as a plain
		 * message and stopped appearing under the one it answered. Tags and
		 * the private-reply origin went the same way.
		 *
		 * Locals, captured before anything is released, and the payload built
		 * only from these. Nothing below this point reads S.* for the message
		 * being sent.
		 */
		var replyTo = S.replyTo;
		var tags    = S.pendingTags.slice();
		var origin  = S.privateOrigin;
		// only keep mentions whose @Name still appears in the final text
		var mentions = S.mentions.filter(function (m) { return body.indexOf('@' + m.name) !== -1; });
		var mentionAll = S.mentionAll && /@(everyone|all)\b/i.test(body);

		// Files are not shown ahead of time - they can genuinely take a while
		// and a bubble that sits there "Sending…" for ten seconds is worse
		// than no bubble at all.
		var pid = 0;
		if (!S.pendingFiles.length) {
			pid = ++pendSeq;
			showPending(pid, body, replyTo);
			$('#chInput').val('').css('height', 'auto');   // free the composer at once

			// Everything the composer owns is released HERE, not in done() —
			// the reply bar, the tags, the mention list. Left until the
			// response came back, a fast second message inherited the first
			// one's reply-to and its tags, which is the other half of what
			// made this feel sticky.
			S.replyTo = null; S.pendingTags = []; S.mentions = []; S.mentionAll = false;
			S.privateOrigin = null;
			syncComposerBars();

			blipSent();
		}

		var failed = function (msg) {
			$('#chSend').prop('disabled', false);
			if (pid) {
				clearPending(pid);
				// Hand back everything the message was carrying, not just the
				// words. The composer was cleared before the request went out,
				// so without this a failed reply loses what it was replying to
				// and the person retypes it as a loose message.
				if (!$('#chInput').val()) {
					$('#chInput').val(body).trigger('input');
					S.replyTo       = replyTo;
					S.pendingTags   = tags.slice();
					S.privateOrigin = origin;
					syncComposerBars();
				}
			}
			toast(msg || 'Message could not be sent', '', 'err');
		};

		var done = function (r) {
			$('#chSend').prop('disabled', false);
			if (pid) clearPending(pid);
			if (!r || !r.ok) { return failed(); }

			// An upload still clears the composer here: unlike a text message
			// it was never cleared up front, because the files it is carrying
			// can take a while and handing the box back before they land would
			// be a lie.
			if (!pid) {
				$('#chInput').val('').css('height', 'auto');
				S.replyTo = null; S.pendingTags = []; S.mentions = []; S.mentionAll = false;
				S.privateOrigin = null;
				syncComposerBars();
				blipSent();
			}
			S.pendingFiles = [];
			appendMessage(r.message);
			// the sidebar only needs the new preview and ordering - it is not
			// worth a second round trip racing every keystroke-fast sender
			refreshConversationsSoon();
		};

		if (S.pendingFiles.length) {
			var fd = new FormData();
			fd.append('conversation_id', S.activeId);
			fd.append('body', body);
			fd.append('reply_to', replyTo ? replyTo.id : 0);
			S.pendingFiles.forEach(function (f) { fd.append('files[]', f); });

			$.ajax({ url: BASE + 'upload', type: 'POST', data: fd, processData: false, contentType: false })
				.done(done)
				.fail(function (x) {
					var msg = 'Upload failed';
					try { var j = JSON.parse(x.responseText); if (j.details && j.details.length) msg += ': ' + j.details.join(', '); } catch (e) {}
					failed(msg);
				});
			return;
		}

		post('send', {
			conversation_id: S.activeId,
			body: body,
			reply_to: replyTo ? replyTo.id : 0,
			mentions: JSON.stringify(mentions),
			mention_all: mentionAll ? 1 : 0,
			origin_id: origin ? origin.id : 0,
			tags: JSON.stringify(tags)
		}).done(done).fail(function (x) {
			failed(x.status === 403 ? 'You are not a member of this conversation'
			                        : 'Message could not be sent');
		});
	}

	function appendMessage(m) {
		if (!m || m.conversation != S.activeId) return;
		for (var i = 0; i < S.messages.length; i++) if (S.messages[i].id === m.id) return;

		S.messages.push(m);
		S.lastMessageId = Math.max(S.lastMessageId, m.id);

		var prev = S.messages.length > 1 ? S.messages[S.messages.length - 2] : null;
		var html = (!prev || prev.day !== m.day ? '<div class="ch-day"><span>' + esc(m.day) + '</span></div>' : '') + messageHtml(m);

		if ($('#chBody .ch-empty').length) $('#chBody').html(html);
		else $('#chBody').append(html);

		// your own message always jumps the view down; someone else's only when
		// you were already following the bottom
		scrollIfFollowing(isMine(m));
	}

	function replaceMessage(m) {
		for (var i = 0; i < S.messages.length; i++) {
			if (S.messages[i].id === m.id) {
				S.messages[i] = m;
				var $el = $('#chBody .ch-msg[data-mid="' + m.id + '"]');
				if ($el.length) $el.replaceWith(messageHtml(m));
				return true;
			}
		}
		return false;
	}

	/* -------------------------------------------------------- @ MENTIONS */
	var mentionState = { open: false, start: -1, items: [], sel: 0 };

	function checkMention() {
		var $i = $('#chInput'), val = $i.val(), pos = $i[0].selectionStart;
		var upto = val.substring(0, pos), at = upto.lastIndexOf('@');

		if (at === -1 || (at > 0 && !/[\s\n]/.test(upto.charAt(at - 1)))) return closeMention();
		var term = upto.substring(at + 1);
		if (term.length > 25 || /\n/.test(term)) return closeMention();

		var pool = (S.header && S.header.type !== 'direct') ? S.members : [];
		if (!pool.length) return closeMention();

		var t = term.toLowerCase();
		var hits = pool.filter(function (m) {
			return (m.id != ME.id || m.type !== ME.type) && m.name.toLowerCase().indexOf(t) !== -1;
		}).slice(0, 20);

		// "@everyone" notifies the whole group in one go
		if ('everyone'.indexOf(t) === 0 || 'all'.indexOf(t) === 0) {
			hits.unshift({
				id: 0, type: 'all', name: 'everyone', initials: '@',
				role: 'Notify all ' + pool.length + ' members', color: '#d97706'
			});
		}
		if (!hits.length) return closeMention();

		mentionState = { open: true, start: at, items: hits, sel: 0 };
		renderMentionPop();
	}

	function renderMentionPop() {
		var h = '';
		mentionState.items.forEach(function (m, i) {
			h += '<div class="ch-mention-item' + (i === mentionState.sel ? ' sel' : '') + '" data-mi="' + i + '">' +
					avatar(m) + '<div><div class="n">' + esc(m.name) + '</div>' +
					'<div class="r">' + esc(roleLine(m)) + '</div></div></div>';
		});
		$('#chMentionPop').html(h).show();
	}
	function closeMention() { mentionState.open = false; $('#chMentionPop').hide(); }

	function pickMention(i) {
		var m = mentionState.items[i];
		if (!m) return;
		var $i = $('#chInput'), val = $i.val(), pos = $i[0].selectionStart;
		var before = val.substring(0, mentionState.start);
		var after  = val.substring(pos);

		$i.val(before + '@' + m.name + ' ' + after).focus();
		var np = (before + '@' + m.name + ' ').length;
		$i[0].setSelectionRange(np, np);

		if (m.type === 'all') {
			S.mentionAll = true;
		} else if (!S.mentions.some(function (x) { return x.id === m.id && x.type === m.type; })) {
			S.mentions.push({ id: m.id, type: m.type, name: m.name });
		}
		closeMention();
	}

	/* ------------------------------------------------------------- MODALS */
	function modal(title, bodyHtml, okLabel, onOk) {
		$('#chModalTitle').text(title);
		$('#chModalBody').html(bodyHtml);

		// A double-click is ONE intent, not two. Every modal confirm here does
		// something with a side effect - forward, add members, create a group -
		// so the second bounce of an impatient double-click must not go
		// through. Applied in the shared helper so no modal can forget it.
		var lastOk = 0;
		// .show() matters: the call picker hides this button, and without
		// restoring it here every later modal would open with no Save button
		$('#chModalOk').show().prop('disabled', false)
			.data('lbl', okLabel || 'Save').text(okLabel || 'Save')
			.off('click').on('click', function (e) {
				var now = Date.now();
				if (now - lastOk < 900) return;
				lastOk = now;
				return onOk.call(this, e);
			});
		$('#chModalMask').addClass('open');
	}
	function closeModal() { $('#chModalMask').removeClass('open'); }

	/** Freeze the modal's confirm button while its request is in the air. */
	function modalBusy(on, label) {
		$('#chModalOk').prop('disabled', !!on).text(label || ($('#chModalOk').data('lbl') || 'Save'));
	}

	function loadDirectory(cb) {
		if (S.directory.length) return cb(S.directory);
		$.getJSON(BASE + 'directory', function (r) {
			S.directory = (r && r.ok) ? r.people : [];
			// ids this user leads; used to narrow the picker when adding their
			// own team is the only thing they are allowed to do
			S.myTeam = (r && r.ok && r.my_team) ? r.my_team : [];
			cb(S.directory);
		});
	}

	/**
	 * The departments a group may be filed under.
	 *
	 * Its own request rather than a field on the directory: tagging a group
	 * needs no address book, and the address book is the expensive half.
	 * Cached for the life of the page — the department list does not move.
	 * `null` means "never asked", which is why the guard is not `.length`: an
	 * account with no departments to offer must not re-ask on every modal.
	 */
	function loadDepartments(cb) {
		if (S.departments !== null) return cb(S.departments);
		$.getJSON(BASE + 'departments', function (r) {
			S.departments = (r && r.ok && r.departments) ? r.departments : [];
			cb(S.departments);
		}).fail(function () { S.departments = []; cb(S.departments); });
	}

	/** <option> list for a department picker, marking the ones already taken. */
	function deptOptionsHtml(depts, selected) {
		var h = '<option value="0">\u2014 No department \u2014</option>';
		depts.forEach(function (d) {
			// A department that already has a group is still offered, because
			// a second one is occasionally what somebody genuinely wants. It
			// just says so, instead of letting two "DESIGN" rooms appear with
			// no hint that the first exists.
			h += '<option value="' + d.id + '"' + (Number(selected) === Number(d.id) ? ' selected' : '') + '>' +
				esc(d.name) + (d.conv_id ? ' \u00b7 already has a group' : '') + '</option>';
		});
		return h;
	}

	/** Sorted, de-duplicated department list out of a set of people. */
	function deptsOf(people) {
		var seen = {}, out = [];
		people.forEach(function (p) {
			var d = (p.dept || '').trim();
			if (d === '' || seen[d]) return;
			seen[d] = true; out.push(d);
		});
		return out.sort(function (a, b) { return a.localeCompare(b); });
	}

	/**
	 * The department <select> that sits above every people picker.
	 *
	 * Built from the people actually in the list rather than from a master
	 * department table, so it can never offer a department with nobody in it.
	 */
	function deptSelectHtml(people) {
		var h = '<select class="ch-search" id="chPickDept" style="margin-bottom:8px;">' +
				'<option value="">All departments</option>';
		deptsOf(people).forEach(function (d) {
			h += '<option value="' + esc(d) + '">' + esc(d) + '</option>';
		});
		return h + '</select>';
	}

	function peopleListHtml(people, exclude) {
		// Pick a department first, then search within it. Ticks survive both,
		// so you can take three people from DESIGN and two from PURCHASE in
		// one go.
		var h = deptSelectHtml(people) +
				'<input type="text" class="ch-search" id="chPickSearch" placeholder="Search people..." style="margin-bottom:8px;">' +
				'<div id="chPickAllWrap" style="display:none;margin-bottom:8px;">' +
					'<a href="javascript:void(0);" id="chPickAll" style="font-size:12px;font-weight:600;">Select everyone shown</a>' +
					'<span style="color:#cbd5e1;margin:0 7px;">|</span>' +
					'<a href="javascript:void(0);" id="chPickNone" style="font-size:12px;font-weight:600;color:#64748b;">Clear</a>' +
				'</div>' +
				'<div class="ch-picklist" id="chPickList">';

		people.forEach(function (p) {
			if (exclude && exclude.some(function (e) { return e.id == p.id && e.type === p.type; })) return;
			h += '<label class="ch-pick" data-aud="' + esc(p.type) + '" data-name="' + esc(p.name.toLowerCase()) +
					'" data-dept="' + esc((p.dept || '').trim()) + '">' +
					avatar(p) +
					'<div style="min-width:0;"><div class="n">' + esc(p.name) + '</div>' +
					'<div class="r">' + esc(roleLine(p)) + (p.dept ? ' · ' + esc(p.dept) : '') + '</div></div>' +
					'<input type="checkbox" data-pid="' + p.id + '" data-ptype="' + p.type + '">' +
				'</label>';
		});
		return h + '</div><div class="ch-sempty" id="chPickEmpty" style="display:none;">Nobody here</div>';
	}
	/**
	 * Show only the people in the selected audience tab that also match the
	 * search box. Used by the new-DM picker and the add-members picker; both
	 * degrade to "search only" when there are no tabs.
	 */
	function filterPicker() {
		var $tab = $('.ch-picktab.active');
		var aud  = $tab.length ? $tab.data('aud') : null;
		var q    = ($('#chPickSearch').val() || '').toLowerCase();
		var dept = $('#chPickDept').val() || '';
		var shown = 0;

		$('#chPickList .ch-pick').each(function () {
			var $p = $(this);
			var okAud  = !aud || $p.data('aud') === aud;
			var okQ    = !q || String($p.data('name')).indexOf(q) !== -1;
			var okDept = !dept || String($p.data('dept')) === dept;
			var show   = okAud && okQ && okDept;
			$p.toggle(show);
			if (show) shown++;
		});

		// "Select everyone shown" only makes sense on a multi-select picker
		// (add members), and only once the list has been narrowed — offering
		// it against the whole company invites a 200-person group by accident.
		$('#chPickAllWrap').toggle(
			$('#chPickList input[type=checkbox]').length > 0 && (dept !== '' || q !== '') && shown > 0
		);

		$('#chPickEmpty').toggle(shown === 0).text(
			q ? 'Nobody matches “' + q + '” here' : 'Nobody in this list'
		);
	}

	function pickedPeople() {
		var out = [];
		$('#chPickList input:checked').each(function () {
			out.push({ id: parseInt($(this).data('pid'), 10), type: $(this).data('ptype') });
		});
		return out;
	}

	function newDirectModal() {
		loadDirectory(function (people) {
			// One staff directory, listed alphabetically. The CoreTech original
			// split this three ways to stop people messaging the wrong Ravi
			// across companies; within one company the search box is enough.
			var h = deptSelectHtml(people) +
					'<input type="text" class="ch-search" id="chPickSearch" placeholder="Search people..." style="margin-bottom:8px;">' +
					'<div class="ch-picklist" id="chPickList">';

			people.forEach(function (p) {
				h += '<div class="ch-pick" data-aud="' + esc(p.type) + '" data-name="' + esc(p.name.toLowerCase()) +
						'" data-dept="' + esc((p.dept || '').trim()) + '" data-pid="' + p.id + '" data-ptype="' + p.type + '">' +
						avatar(p) + '<div style="min-width:0;"><div class="n">' + esc(p.name) + '</div>' +
						'<div class="r">' + esc(roleLine(p)) + (p.dept ? ' · ' + esc(p.dept) : '') + '</div></div></div>';
			});
			h += '</div><div class="ch-sempty" id="chPickEmpty" style="display:none;">Nobody here</div>';

			modal('New direct message', h, 'Close', closeModal);
			filterPicker();
			$('#chPickList').on('click', '.ch-pick', function () {
				post('open_direct', { peer_id: $(this).data('pid'), peer_type: $(this).data('ptype') })
					.done(function (r) {
						if (!r.ok) return toast('Could not open that chat', '', 'err');
						closeModal();
						refreshConversations(function () { openConversation(r.conversation_id, true); });
					});
			});
		});
	}

	/**
	 * Create a group, optionally filed under a department.
	 *
	 * The department is asked for HERE rather than left to be set afterwards
	 * because it decides which heading everyone finds the room under, and a
	 * room that spends its first week in "Other Groups" is a room people
	 * learn to look for in the wrong place. It stays editable from Details.
	 *
	 * DF groups are not created from this dialog at all — they are made when a
	 * DF is released (Task::dfrelease()) and backfilled at /chat/df-groups —
	 * so there is no third option here.
	 */
	function newChannelModal() {
		loadDirectory(function (people) {
			loadDepartments(function (depts) {
				var canDept = depts.length > 0;
				var h = '<label>Group name</label>' +
					'<input type="text" id="chChName" placeholder="e.g. Delhi Rollout Team">';

				if (canDept) {
					h += '<label>What kind of group is this?</label>' +
						'<div class="ch-kind">' +
							'<label class="on"><input type="radio" name="chChKind" value="general" checked> General</label>' +
							'<label><input type="radio" name="chChKind" value="dept"> Department</label>' +
						'</div>' +
						'<div id="chChDeptWrap" style="display:none;">' +
							'<label>Department</label>' +
							'<select class="ch-search" id="chChDept" style="margin-bottom:13px;">' +
								deptOptionsHtml(depts, 0) +
							'</select>' +
							'<div id="chChDeptNote"></div>' +
						'</div>';
				}

				h += '<label>Purpose (optional)</label>' +
					'<input type="text" id="chChDesc" placeholder="What is this group for?">' +
					'<label>Add members</label>' + peopleListHtml(people, []);

				modal('Create a group', h, 'Create group', function () {
					var name = $('#chChName').val().trim();
					if (!name) { $('#chChName').focus(); return; }
					post('create_channel', {
						name: name,
						description: $('#chChDesc').val().trim(),
						department_id: chosenDept(),
						members: JSON.stringify(pickedPeople())
					}).done(function (r) {
						if (!r.ok) return toast('Group could not be created', '', 'err');
						closeModal();
						refreshConversations(function () { openConversation(r.conversation_id, true); });
					});
				});

				if (canDept) wireDeptChooser(depts);
				filterPicker();
			});
		});
	}

	/** The department id the create dialog is currently set to, or 0. */
	function chosenDept() {
		if ($('input[name=chChKind]:checked').val() !== 'dept') return 0;
		return parseInt($('#chChDept').val(), 10) || 0;
	}

	/**
	 * Live behaviour of the department half of the create dialog: show the
	 * picker only when it applies, offer the department's name as the group
	 * name, and say when that department already has a room.
	 */
	function wireDeptChooser(depts) {
		var byId = {};
		depts.forEach(function (d) { byId[d.id] = d; });

		// Tracks the name WE put in the box. The prefill only ever overwrites
		// its own previous suggestion, so a name somebody typed is never lost
		// to a change of department.
		var suggested = '';

		var note = function () {
			var d = byId[chosenDept()];
			if (!d || !d.conv_id) return $('#chChDeptNote').empty();
			$('#chChDeptNote').html(
				'<div class="ch-note">' + esc(d.name) + ' already has a group, ' +
				'<b>' + esc(d.conv_name) + '</b>. Creating another is fine \u2014 ' +
				'both will sit under Department Groups. ' +
				'<a data-open="' + d.conv_id + '">Open the existing one instead</a></div>');
		};

		var sync = function () {
			var on = $('input[name=chChKind]:checked').val() === 'dept';
			$('#chChDeptWrap').toggle(on);
			$('.ch-kind label').removeClass('on')
				.has('input:checked').addClass('on');
			if (!on) { $('#chChDeptNote').empty(); return; }

			var d = byId[chosenDept()];
			var $n = $('#chChName');
			if (d && ($n.val().trim() === '' || $n.val() === suggested)) {
				suggested = d.name;
				$n.val(suggested);
			}
			note();
		};

		// Default to the first department so picking "Department" is one
		// click, not two — an empty select would just be a second decision.
		if (depts.length) $('#chChDept').val(String(depts[0].id));

		$('input[name=chChKind]').on('change', sync);
		$('#chChDept').on('change', sync);
		$('#chChDeptNote').on('click', 'a[data-open]', function () {
			var id = parseInt($(this).data('open'), 10);
			closeModal();
			openConversation(id, true);
		});
	}

	function addMembersModal() {
		loadDirectory(function (people) {
			// Someone who may not run this room but leads a team sees only
			// their own people — anything else would be refused on the server
			// anyway, so showing it would just be a trap.
			var canManage = CAN.is_admin || S.myRole === 'owner' || S.myRole === 'admin' || CAN.manage_members;
			if (!canManage && S.myTeam && S.myTeam.length) {
				people = people.filter(function (p) {
					return p.type === 'user' && S.myTeam.indexOf(parseInt(p.id, 10)) !== -1;
				});
			}

			var onAdd = function () {
				var picked = pickedPeople();
				if (!picked.length) return closeModal();
				post('add_members', { conversation_id: S.activeId, members: JSON.stringify(picked) })
					.done(function (r) {
						if (!r.ok) return toast('Could not add members', '', 'err');
						S.members = r.members; renderInfo(); renderHeader(); closeModal();
					});
			};
			modal('Add members', peopleListHtml(people, S.members), 'Add', onAdd);
			filterPicker();
		});
	}

	function renameModal() {
		var h = '<label>Group name</label><input type="text" id="chRnName" value="' + esc(S.header.name) + '">' +
		        '<label>Purpose</label><input type="text" id="chRnDesc" value="' + esc(S.header.description || '') + '">';
		modal('Rename group', h, 'Save', function () {
			var name = $('#chRnName').val().trim();
			if (!name) return;
			post('rename_channel', {
				conversation_id: S.activeId, name: name, description: $('#chRnDesc').val().trim()
			}).done(function (r) {
				if (!r.ok) return toast('Rename failed', '', 'err');
				closeModal(); openConversation(S.activeId); refreshConversations();
			});
		});
	}

	/**
	 * Forward a message to one or more of my conversations.
	 * The list is my own sidebar, so there is nothing to pick that I am not
	 * already a member of - and the server re-checks every target anyway.
	 */
	function forwardModal(msg) {
		var preview = msg.body
			? msg.body.substring(0, 180)
			: ((msg.attachments && msg.attachments.length)
				? msg.attachments.length + ' attachment(s)' : 'this message');

		var h = '<div class="ch-fwd-src">' +
					'<div class="t"><i class="fa fa-share"></i> Forwarding ' +
						esc(msg.sender_name) + '’s message</div>' +
					'<div class="b">' + esc(preview) + '</div>' +
				'</div>' +
				'<label>Add a note (optional)</label>' +
				'<input type="text" id="chFwdNote" placeholder="Say why you are sharing this...">' +
				'<label>Send to</label>' +
				'<input type="text" class="ch-search" id="chFwdSearch" placeholder="Search your conversations..." style="margin-bottom:10px;">' +
				'<div class="ch-picklist" id="chFwdList">';

		var n = 0;
		S.conversations.forEach(function (c) {
			if (c.id === S.activeId) return;          // it is already here
			n++;
			h += '<label class="ch-pick" data-name="' + esc(c.name.toLowerCase()) + '">' +
					avatar(c, c.type === 'direct' ? '' : 'sq') +
					'<div style="min-width:0;"><div class="n">' + esc(c.name) + audienceTag(c) + '</div>' +
					'<div class="r">' + esc(c.type === 'direct' ? (roleLine(c) || 'Direct message') : 'Group') + '</div></div>' +
					'<input type="checkbox" data-cid="' + c.id + '">' +
				'</label>';
		});
		h += '</div>';
		if (!n) h = '<div class="ch-sempty">You have no other conversations to forward this to.</div>';

		modal('Forward message', h, 'Forward', function () {
			var targets = [];
			$('#chFwdList input:checked').each(function () { targets.push(parseInt($(this).data('cid'), 10)); });
			if (!targets.length) return toast('Pick at least one conversation', '', 'err');

			// Hold the button down for the whole round trip, not just the
			// double-click window - forwarding to several rooms takes a moment
			// and that pause is exactly when people click again.
			modalBusy(true, 'Forwarding…');

			post('forward', {
				message_id: msg.id,
				targets: JSON.stringify(targets),
				note: $('#chFwdNote').val() || ''
			}).done(function (r) {
				if (!r || !r.ok) { modalBusy(false); return toast('Could not forward', '', 'err'); }
				closeModal();
				refreshConversations();
				toast('Forwarded', 'Sent to ' + r.sent + ' conversation' + (r.sent > 1 ? 's' : ''));
			}).fail(function (x) {
				modalBusy(false);
				toast('Could not forward', xhrReason(x), 'err');
			});
		});

		$(document).off('keyup.fwd').on('keyup.fwd', '#chFwdSearch', function () {
			var q = $(this).val().toLowerCase();
			$('#chFwdList .ch-pick').each(function () {
				$(this).toggle(String($(this).data('name')).indexOf(q) !== -1);
			});
		});
	}

	/** 'df' -> 'DF'. Unknown types still render, as their own bare key. */
	function refLabel(rt) {
		return (REFS && REFS[rt]) ? REFS[rt].toUpperCase() : String(rt || '').toUpperCase();
	}

	/** two-letter square badge for the picker rows */
	function refBadge(rt) {
		return refLabel(rt).substring(0, 2);
	}

	/** Record picker - used both for the composer and for an existing message. */
	function recordModal(messageId) {
		var radios = '', first = true;
		for (var k in REFS) {
			if (!REFS.hasOwnProperty(k)) continue;
			radios += '<label style="display:inline;margin-right:14px;">' +
						'<input type="radio" name="chRk" value="' + esc(k) + '"' +
						(first ? ' checked' : '') + '> ' + esc(REFS[k]) + '</label>';
			first = false;
		}
		var h = '<div style="margin-bottom:10px;">' + radios + '</div>' +
				'<input type="text" class="ch-search" id="chRecSearch" placeholder="Search by name, company or number..." style="margin-bottom:10px;">' +
				'<div class="ch-picklist" id="chRecList"><div style="padding:14px;color:#7c8ba1;font-size:12px;">Type to search…</div></div>';

		modal(messageId ? 'Link a record to this message' : 'Link a record', h, 'Close', closeModal);

		var search = function () {
			var kind = $('input[name=chRk]:checked').val();
			$.getJSON(BASE + 'search_records', { q: $('#chRecSearch').val(), kind: kind }, function (r) {
				if (!r || !r.ok) return $('#chRecList').html('<div style="padding:14px;color:#dc2626;font-size:12px;">Not permitted</div>');
				if (!r.records.length) return $('#chRecList').html('<div style="padding:14px;color:#7c8ba1;font-size:12px;">No matches</div>');
				var hh = '';
				r.records.forEach(function (x) {
					hh += '<div class="ch-pick" data-rt="' + esc(x.ref_type) + '" data-ri="' + x.ref_id +
					      '" data-code="' + esc(x.code) + '" data-title="' + esc(x.title) + '">' +
							'<div class="ch-avatar sq" style="background:#059669;">' + esc(refBadge(x.ref_type)) + '</div>' +
							'<div style="min-width:0;"><div class="n">' + esc(x.title) + '</div>' +
							'<div class="r">' + esc(x.code) + ' · ' + esc(x.status) + '</div></div></div>';
				});
				$('#chRecList').html(hh);
			});
		};
		var t = null;
		$('#chRecSearch').on('keyup', function () { clearTimeout(t); t = setTimeout(search, 260); });
		$('input[name=chRk]').on('change', search);
		search();

		$('#chRecList').on('click', '.ch-pick', function () {
			var d = $(this).data();
			if (messageId) {
				post('tag_record', { message_id: messageId, ref_type: d.rt, ref_id: d.ri })
					.done(function (r) { if (r.ok) replaceMessage(r.message); closeModal(); });
			} else {
				S.pendingTags.push({ ref_type: d.rt, ref_id: d.ri, code: d.code, title: d.title });
				syncComposerBars(); closeModal(); $('#chInput').focus();
			}
		});
	}

	/* ================================================================
	 * SCREENSHOT ANNOTATOR
	 * ----------------------------------------------------------------
	 * Draw arrows, boxes, freehand, text and blur-outs over a pasted or
	 * attached image before sending it.
	 *
	 * Shapes are kept as objects and the canvas is redrawn from the original
	 * image every time, so Undo is exact and nothing is ever baked in until
	 * the user presses Attach. The canvas works in the image's own pixel
	 * space; pointer coordinates are mapped through the element's rect, so it
	 * behaves the same whether the image is displayed full size or scaled
	 * down to fit the narrow dock.
	 * ================================================================ */
	var AN = {
		idx: -1, img: null, shapes: [], tool: 'arrow',
		color: '#e11d48', size: 4, drawing: false, start: null, current: null
	};
	var AN_COLORS = ['#e11d48', '#f59e0b', '#16a34a', '#2563eb', '#111827', '#ffffff'];
	/** biggest edge we keep - stops a 6000px screenshot eating memory and upload */
	var AN_MAX = 2400;

	function annotOpen(index) {
		var file = S.pendingFiles[index];
		if (!file || file.type.indexOf('image/') !== 0) return;

		var url = URL.createObjectURL(file);
		var img = new Image();
		img.onload = function () {
			URL.revokeObjectURL(url);

			var c = document.getElementById('chAnnotCanvas');
			var scale = Math.min(1, AN_MAX / Math.max(img.width, img.height));
			c.width  = Math.round(img.width * scale);
			c.height = Math.round(img.height * scale);

			AN.idx = index; AN.img = img; AN.shapes = [];
			AN.tool = 'arrow'; AN.color = AN_COLORS[0]; AN.size = 4;

			$('.ch-tool[data-tool]').removeClass('on').filter('[data-tool="arrow"]').addClass('on');
			$('#chAnnotSize').val(4);
			renderColors();
			annotRender();
			$('#chAnnotMask').addClass('open');
		};
		img.onerror = function () { URL.revokeObjectURL(url); toast('Could not open that image', '', 'err'); };
		img.src = url;
	}

	function annotClose() {
		$('#chAnnotMask').removeClass('open');
		$('#chAnnotText').hide().val('');
		AN.img = null; AN.shapes = []; AN.idx = -1;
	}

	function renderColors() {
		var h = '';
		AN_COLORS.forEach(function (col) {
			h += '<span class="ch-swatch' + (col === AN.color ? ' on' : '') +
			     '" data-col="' + col + '" style="background:' + col + '"></span>';
		});
		$('#chAnnotColors').html(h);
	}

	/** Redraw everything: the original image, then each shape in order. */
	function annotRender() {
		var c = document.getElementById('chAnnotCanvas');
		if (!c || !AN.img) return;
		var x = c.getContext('2d');

		x.clearRect(0, 0, c.width, c.height);
		x.drawImage(AN.img, 0, 0, c.width, c.height);

		var all = AN.shapes.slice();
		if (AN.current) all.push(AN.current);
		all.forEach(function (s) { drawShape(x, s, c); });
	}

	function drawShape(x, s, c) {
		x.save();
		x.strokeStyle = s.color;
		x.fillStyle   = s.color;
		x.lineWidth   = s.size;
		x.lineCap = 'round';
		x.lineJoin = 'round';

		if (s.type === 'rect') {
			x.strokeRect(s.x1, s.y1, s.x2 - s.x1, s.y2 - s.y1);

		} else if (s.type === 'arrow') {
			var dx = s.x2 - s.x1, dy = s.y2 - s.y1;
			var len = Math.sqrt(dx * dx + dy * dy);
			if (len > 1) {
				var head = Math.max(10, s.size * 3.6);
				var a = Math.atan2(dy, dx);
				// shorten the shaft so it does not poke through the head
				var sx = s.x2 - Math.cos(a) * head * 0.85;
				var sy = s.y2 - Math.sin(a) * head * 0.85;
				x.beginPath(); x.moveTo(s.x1, s.y1); x.lineTo(sx, sy); x.stroke();
				x.beginPath();
				x.moveTo(s.x2, s.y2);
				x.lineTo(s.x2 - Math.cos(a - 0.4) * head, s.y2 - Math.sin(a - 0.4) * head);
				x.lineTo(s.x2 - Math.cos(a + 0.4) * head, s.y2 - Math.sin(a + 0.4) * head);
				x.closePath(); x.fill();
			}

		} else if (s.type === 'pen') {
			if (s.pts && s.pts.length > 1) {
				x.beginPath();
				x.moveTo(s.pts[0].x, s.pts[0].y);
				for (var i = 1; i < s.pts.length; i++) x.lineTo(s.pts[i].x, s.pts[i].y);
				x.stroke();
			}

		} else if (s.type === 'text') {
			var fs = Math.max(14, s.size * 5);
			x.font = '700 ' + fs + 'px Inter, Arial, sans-serif';
			x.textBaseline = 'top';
			// outline first so the text stays readable on any background
			x.lineWidth = Math.max(3, fs / 7);
			x.strokeStyle = (s.color === '#ffffff') ? 'rgba(0,0,0,.75)' : 'rgba(255,255,255,.9)';
			x.strokeText(s.text, s.x1, s.y1);
			x.fillText(s.text, s.x1, s.y1);

		} else if (s.type === 'hide') {
			// pixelate the region by sampling the ORIGINAL image, so redaction
			// survives undo/redraw and cannot be recovered from the export
			var rx = Math.min(s.x1, s.x2), ry = Math.min(s.y1, s.y2);
			var rw = Math.abs(s.x2 - s.x1), rh = Math.abs(s.y2 - s.y1);
			if (rw > 2 && rh > 2) {
				var blocks = Math.max(3, Math.round(Math.min(rw, rh) / 9));
				var tmp = document.createElement('canvas');
				tmp.width = blocks; tmp.height = Math.max(1, Math.round(blocks * rh / rw));
				var t = tmp.getContext('2d');
				t.drawImage(c, rx, ry, rw, rh, 0, 0, tmp.width, tmp.height);
				x.imageSmoothingEnabled = false;
				x.drawImage(tmp, 0, 0, tmp.width, tmp.height, rx, ry, rw, rh);
			}
		}
		x.restore();
	}

	/** pointer position -> canvas pixel coordinates (handles the display scale) */
	function annotPos(e) {
		var c = document.getElementById('chAnnotCanvas');
		var r = c.getBoundingClientRect();
		var src = (e.touches && e.touches[0]) ? e.touches[0] : e;
		return {
			x: (src.clientX - r.left) * (c.width / r.width),
			y: (src.clientY - r.top) * (c.height / r.height)
		};
	}

	function annotExport() {
		var c = document.getElementById('chAnnotCanvas');
		var orig = S.pendingFiles[AN.idx];
		var idx = AN.idx;
		if (!c || !orig) return annotClose();

		// PNG keeps text and arrows crisp; screenshots are usually PNG anyway
		var name = (orig.name || ('screenshot-' + stamp() + '.png')).replace(/\.[^.]+$/, '') + '-annotated.png';

		c.toBlob(function (blob) {
			if (!blob) { toast('Could not save the annotation', '', 'err'); return annotClose(); }
			S.pendingFiles[idx] = new File([blob], name, { type: 'image/png' });
			annotClose();
			syncComposerBars();
			$('#chInput').focus();
		}, 'image/png');
	}

	/* ------------------------------------------------------------- TOASTS */
	function toast(title, body, kind) {
		// In the dock our own toasts are hidden (the host page owns that corner
		// of the screen), so hand them to the host instead of dropping them -
		// otherwise a message in another conversation would wait for the host's
		// slower poll before it announced itself.
		if (DOCK && kind !== 'err') toHost('toast', { title: title, body: body, kind: kind || '' });


		var $t = $('<div class="ch-toast' + (kind === 'mention' ? ' mention' : '') + '">' +
			'<b>' + esc(title) + '</b>' + (body ? '<span>' + esc(body) + '</span>' : '') + '</div>');
		if (kind === 'err') $t.css('border-left-color', '#dc2626');
		$('#chToasts').append($t);
		setTimeout(function () { $t.fadeOut(280, function () { $t.remove(); }); }, 5200);
		return $t;
	}

	/**
	 * Notification sound - synthesised, so there is no audio file to upload.
	 *
	 * Two things this has to get right, both of which silently produce no
	 * sound at all when they are wrong:
	 *
	 *  1. ONE AudioContext, reused. Browsers cap them at around six per page;
	 *     building a fresh one per blip means the seventh notification throws
	 *     and every one after it is silent, and the dead contexts leak.
	 *  2. Autoplay policy. A context created before the user has interacted
	 *     with the document starts suspended, so it must be resumed off a real
	 *     gesture - hence unlockAudio() below, wired to the first click/key.
	 *
	 * Inside the dock the sound is handed to the host page instead: the top
	 * document is the one that reliably has gestures, and it also stops the
	 * iframe and the page both pinging for the same message.
	 */
	var audioCtx = null, lastBlip = 0, lastSent = 0;

	function soundOn() {
		try { return localStorage.getItem('ctChatSound') !== '0'; } catch (e) { return true; }
	}

	function unlockAudio() {
		try {
			var Ctx = window.AudioContext || window.webkitAudioContext;
			if (!Ctx) return;
			if (!audioCtx) audioCtx = new Ctx();
			if (audioCtx.state === 'suspended') audioCtx.resume();
		} catch (e) {}
	}

	/**
	 * Play a short tone sequence. The two voices below are the only callers.
	 *
	 * Synthesised rather than an audio file on purpose: no asset to upload, no
	 * request to wait on, and the first one is never late — which for a sound
	 * that is meant to confirm a keypress is the entire point.
	 */
	function tone(notes, peak, tail) {
		try {
			var Ctx = window.AudioContext || window.webkitAudioContext;
			if (!Ctx) return;
			if (!audioCtx) audioCtx = new Ctx();
			if (audioCtx.state === 'suspended') audioCtx.resume();

			var c = audioCtx, t = c.currentTime, g = c.createGain();
			g.connect(c.destination);
			g.gain.setValueAtTime(0.0001, t);
			g.gain.exponentialRampToValueAtTime(peak, t + 0.01);   // ramp, not a click
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

	/** INCOMING: a message arrived. Two notes rising a fourth — reads as
	 *  "message", not as an error. */
	function blip() {
		if (!soundOn()) return;
		if (DOCK) return toHost('sound', { kind: 'in' });

		// several messages can land in one poll; one ping is enough
		var now = Date.now();
		if (now - lastBlip < 1500) return;
		lastBlip = now;

		tone([[880, 0], [1174.7, 0.11]], 0.09, 0.3);
	}

	/**
	 * OUTGOING: you sent something.
	 *
	 * Lower, quieter and half the length of the incoming ping, and a single
	 * note rather than two — the same distinction WhatsApp draws. It is
	 * feedback that a keypress landed, not an announcement; if it sounded like
	 * the incoming one you would look at the screen every time you typed.
	 *
	 * Barely rate-limited (120ms), because firing off four short messages in a
	 * row should make four sounds. That is what it feels like to be typing
	 * quickly, and swallowing three of them makes the app feel like it missed
	 * them.
	 */
	function blipSent() {
		if (!soundOn()) return;
		if (DOCK) return toHost('sound', { kind: 'out' });

		var now = Date.now();
		if (now - lastSent < 120) return;
		lastSent = now;

		tone([[660, 0]], 0.05, 0.14);
	}

	function desktopNotify(title, body, convId) {
		if (!('Notification' in window) || Notification.permission !== 'granted') return;
		try {
			var n = new Notification(title, { body: body, tag: 'chat-' + convId, icon: '<?php echo assets_url; ?>images/favicon.ico' });
			n.onclick = function () { window.focus(); openConversation(convId, true); n.close(); };
		} catch (e) {}
	}

	/* ------------------------------------------------------- REAL-TIME */

	/**
	 * Coalesce sidebar refreshes.
	 *
	 * Several things all want the conversation list re-read - sending, an
	 * incoming event, a membership change - and when they land together the
	 * list would be fetched several times over for one visible change. One
	 * request shortly after the flurry gives the same result for a fraction
	 * of the traffic, which matters on a small shared-hosting worker pool.
	 */
	var convRefreshT = null;
	function refreshConversationsSoon(cb) {
		clearTimeout(convRefreshT);
		convRefreshT = setTimeout(function () { refreshConversations(cb); }, 500);
	}

	function refreshConversations(cb) {
		$.getJSON(BASE + 'conversations')
			.done(function (r) {
				if (r && r.ok) { S.conversations = r.conversations; renderList(); }
				if (cb) cb();
			})
			.fail(function (x) {
				// leave whatever is on screen; just say the refresh failed
				toast('Could not refresh conversations', xhrReason(x), 'err');
				if (cb) cb();
			});
	}

	var streamXhr = null;

	/**
	 * The long-poll.
	 *
	 * IMPORTANT: it parks a PHP worker on the server for up to 25 seconds. That
	 * is cheap for one active user and ruinous when every background tab holds
	 * one too - on shared hosting the FPM pool is small, and a few forgotten
	 * tabs will starve it until ordinary page loads start timing out.
	 *
	 * So the stream runs ONLY while the tab is actually being looked at. A
	 * hidden tab releases its worker immediately and catches up the moment it
	 * comes back, which costs the user nothing: they were not reading it.
	 */
	function stream() {
		if (S.streamStopped || document.hidden) { S.streamIdle = true; return; }
		S.streamIdle = false;

		var params = { cursor: S.cursor, conv: S.activeId || 0, last_message: S.lastMessageId || 0 };

		streamXhr = $.ajax({ url: BASE + 'stream', data: params, dataType: 'json', timeout: 45000 })
			.done(function (r) {
				$('#chConn').hide();
				S.streamFails = 0;
				if (r && r.ok) handleStream(r);
				setTimeout(stream, 120);
			})
			.fail(function (x, status) {
				if (status === 'abort') return;
				S.streamFails++;
				if (S.streamFails > 2) $('#chConn').show();
				// exponential backoff, capped at 30s, so a server hiccup never storms
				var wait = Math.min(30000, 1000 * Math.pow(2, Math.min(S.streamFails, 5)));
				setTimeout(stream, wait);
			});
	}

	/** Release the worker when the tab goes away; catch up when it returns. */
	function watchVisibility() {
		document.addEventListener('visibilitychange', function () {
			if (document.hidden) {
				S.streamIdle = true;
				if (streamXhr && streamXhr.abort) streamXhr.abort();   // frees the worker now
				return;
			}
			// back on screen: pick up anything missed, then resume streaming
			refreshConversations();
			if (S.activeId) { fetchNewMessages(); markRead(); }
			if (S.streamIdle) { S.streamFails = 0; stream(); }
		});
	}

	function handleStream(r) {
		if (r.cursor) S.cursor = r.cursor;

		if (r.typing !== undefined) {
			$('#chTyping').text(r.typing.length
				? (r.typing.length === 1 ? r.typing[0] + ' is typing…' : r.typing.length + ' people are typing…')
				: '');
		}
		if (!r.changed) return;

		var prevUnread = {};
		S.conversations.forEach(function (c) { prevUnread[c.id] = c.unread; });

		if (r.conversations) { S.conversations = r.conversations; renderList(); }

		// new messages in the open conversation
		if (r.messages && r.messages.length) {
			r.messages.forEach(appendMessage);
			markRead();
		} else {
			// Safety net: an event says something was posted here but the stream
			// carried no payload for it. Without this the thread sat stale until
			// the user refreshed the page.
			var missed = (r.events || []).some(function (e) {
				return e.type === 'message' && e.conv == S.activeId && e.msg > S.lastMessageId;
			});
			if (missed && S.activeId) fetchNewMessages();
		}
		// content that changed in place (edit / delete / reaction / pin)
		if (r.refresh && r.refresh.length) r.refresh.forEach(replaceMessage);
		if (r.pinned) renderPins(r.pinned);

		// someone caught up: refresh the "Seen by" counts in place, no reload
		if ((r.events || []).some(function (e) { return e.type === 'read' && e.conv == S.activeId; })) {
			refreshSeenCounts();
		}

		// membership / channel changes: reload the open conversation wholesale
		var structural = (r.events || []).some(function (e) {
			return (e.type === 'member' || e.type === 'channel') && e.conv == S.activeId;
		});
		if (structural && S.activeId) openConversation(S.activeId);

		// Being mentioned always alerts, even if that conversation is open and
		// on screen - that is the whole point of an @mention.
		(r.mention_alerts || []).forEach(function (a) {
			var title = a.from + ' mentioned you' + (a.conv_name ? ' in ' + a.conv_name : '');
			toast(title, a.preview, 'mention').on('click', function () {
				openConversation(a.conversation, true);
				setTimeout(function () { jumpToMessage(a.message_id); }, 500);
			});
			desktopNotify(title, a.preview, a.conversation);
			blip();
		});

		// alert for anything that arrived in a conversation we are NOT looking at
		var alerted = {};
		(r.events || []).forEach(function (e) {
			if (e.type !== 'message') return;
			if (e.conv == S.activeId && !document.hidden) return;
			if (alerted[e.conv]) return;                       // one alert per conversation per batch
			var c = findConv(e.conv);
			if (!c || c.is_muted) return;
			if ((prevUnread[c.id] || 0) >= c.unread) return;   // already counted
			alerted[e.conv] = true;

			var title = c.type === 'direct' ? c.name : '#' + c.name;
			var body  = (c.last_sender ? c.last_sender + ': ' : '') + (c.last_text || '');
			toast(title, body).on('click', function () { openConversation(c.id, true); });
			desktopNotify(title, body, c.id);
			blip();
		});
	}

	/** Pull anything newer than what we already hold for the open conversation. */
	function fetchNewMessages() {
		if (!S.activeId || S._fetching) return;
		S._fetching = true;
		$.getJSON(BASE + 'messages/' + S.activeId, { after: S.lastMessageId }, function (r) {
			S._fetching = false;
			if (r && r.ok && r.messages.length) { r.messages.forEach(appendMessage); markRead(); }
		}).fail(function () { S._fetching = false; });
	}

	/**
	 * Re-derive the "Seen by" numbers from everyone's read pointers and repaint
	 * just the stamp line. Cheaper and far less jarring than reloading the
	 * thread every time somebody opens the conversation.
	 */
	function refreshSeenCounts() {
		if (!S.activeId) return;
		$.getJSON(BASE + 'read_state/' + S.activeId, function (r) {
			if (!r || !r.ok) return;

			S.messages.forEach(function (m) {
				if (!isMine(m) || m.type === 'system') return;
				var seen = 0, total = 0;
				r.readers.forEach(function (p) {
					if (p.id == m.sender_id && p.type === m.sender_type) return;   // the sender
					total++;
					if (p.read >= m.id) seen++;
				});
				m.seen_count = seen;
				m.seen_total = total;

				var $st = $('#chBody .ch-msg[data-mid="' + m.id + '"] .ch-stamp');
				if ($st.length) {
					$st.html(esc(m.time) +
						(m.is_edited ? ' · edited' : '') +
						(m.is_pinned ? ' · <i class="fa fa-thumb-tack"></i> pinned' : '') +
						seenHtml(m));
				}
			});
		});
	}

	function markRead() {
		if (!S.activeId) return;
		post('mark_read', { conversation_id: S.activeId, up_to: S.lastMessageId });
		var c = findConv(S.activeId);
		if (c && c.unread) { c.unread = 0; renderList(); }
	}

	/* ------------------------------------------------------------- EVENTS */
	$(function () {

		/* sidebar */
		$('#chList').on('click', '.ch-item', function () { openConversation($(this).data('id'), true); });
		// Section headings fold their run of rows away. Delegated, because
		// renderList() rebuilds the whole list on every refresh — and bound
		// for the keyboard too, since the heading is a <div role="button">
		// and gets none of a real button's key handling for free.
		$('#chList').on('click', '.ch-sec', function () { toggleSection($(this).data('sec')); });
		$('#chList').on('keydown', '.ch-sec', function (e) {
			if (e.which !== 13 && e.which !== 32) return;
			e.preventDefault();
			toggleSection($(this).data('sec'));
		});
		$('.ch-tab').on('click', function () {
			$('.ch-tab').removeClass('active'); $(this).addClass('active');
			S.tab = $(this).data('tab'); renderList();
		});
		$('#chFilter').on('keyup', function () { S.filter = $(this).val(); renderList(); });
		$('#chNewDirect').on('click', newDirectModal);
		$('#chNewChannel').on('click', newChannelModal);
		$('#chBack').on('click', function () {
			$('#chSide').removeClass('hide-sm'); $('#chMain').removeClass('show-sm');
			S.activeId = 0; toHost('conv', { id: 0 });   // reopen on the list next time
		});
		$('#chExpand').on('click', function () { toHost('expand', { id: S.activeId || 0 }); });
		$('#chToggleInfo').on('click', function () { $('#chInfo').toggleClass('open'); $(this).toggleClass('on'); });
		$('#chTogglePins').on('click', function () { $('#chPinBar').slideToggle(120); $(this).toggleClass('on'); });

		/* modal chrome */
		$('#chModalCancel').on('click', closeModal);
		$('#chModalMask').on('click', function (e) { if (e.target === this) closeModal(); });
		/* ---------------------------------------------------------------
		   SPEAK TO TYPE

		   Uses the browser's built-in SpeechRecognition (Chrome, Edge,
		   Safari). Deliberately NOT a server round-trip: the audio never
		   leaves the machine, there is nothing to pay for, and there is no
		   key to leak. Anthropic's API has no speech-to-text endpoint, so
		   it could not do this job even if we wanted it to.

		   Interim results are shown as they are spoken and replaced when the
		   engine settles on the final wording, so it reads like dictation
		   rather than a pause followed by a paragraph.
		   --------------------------------------------------------------- */
		(function () {
			var SR = window.SpeechRecognition || window.webkitSpeechRecognition;
			var $btn = $('#chMicBtn');
			if (!SR || !$btn.length) return;   // unsupported browser: button stays hidden

			$btn.show();

			var rec = null, listening = false, baseText = '', finalText = '';

			var stop = function () {
				listening = false;
				$btn.removeClass('rec').attr('title', 'Speak to type');
				$('#chInput').removeClass('ch-dictating');
				if (rec) { try { rec.stop(); } catch (e) {} }
			};

			$btn.on('click', function () {
				if (listening) return stop();

				rec = new SR();
				rec.continuous     = true;
				rec.interimResults = true;
				// en-IN gets Indian names, places and numbers noticeably more
				// right than en-US does.
				rec.lang           = 'en-IN';

				// Whatever is already typed stays put; dictation appends.
				baseText  = $('#chInput').val();
				if (baseText !== '' && !/\s$/.test(baseText)) baseText += ' ';
				finalText = '';

				rec.onresult = function (ev) {
					var interim = '';
					for (var i = ev.resultIndex; i < ev.results.length; i++) {
						var chunk = ev.results[i][0].transcript;
						if (ev.results[i].isFinal) finalText += chunk;
						else interim += chunk;
					}
					$('#chInput').val(baseText + finalText + interim).trigger('input');
				};

				rec.onerror = function (ev) {
					stop();
					if (ev.error === 'not-allowed' || ev.error === 'service-not-allowed') {
						toast('Microphone blocked', 'Allow microphone access for this site, then try again.', 'err');
					} else if (ev.error !== 'aborted' && ev.error !== 'no-speech') {
						toast('Dictation stopped', ev.error || '', 'err');
					}
				};

				// continuous recognition still ends itself on a long silence
				rec.onend = function () { if (listening) stop(); };

				try {
					rec.start();
					listening = true;
					$btn.addClass('rec').attr('title', 'Stop dictation');
					$('#chInput').addClass('ch-dictating').focus();
				} catch (e) {
					stop();
					toast('Could not start dictation', '', 'err');
				}
			});

			// Sending or leaving the page should not leave the mic live.
			$(document).on('click', '#chSend', function () { if (listening) stop(); });
			$(window).on('beforeunload', function () { if (listening) stop(); });
		})();

		/* ---------------------------------------------------------------
		   AI TIDY-UP  (✨)

		   Sends the draft to Chat/ai_polish, which calls Anthropic
		   server-side. Most useful straight after dictating, since speech
		   recognition returns one long unpunctuated run of words.

		   The original is kept so one more click puts it back — an AI that
		   "improves" your message with no way back is worse than none.
		   --------------------------------------------------------------- */
		(function () {
			var $btn = $('#chAiBtn');
			if (!$btn.length) return;

			var before = null;

			$btn.on('click', function () {
				var $in = $('#chInput');

				// second press = undo
				if (before !== null) {
					$in.val(before).trigger('input').focus();
					before = null;
					$btn.attr('title', 'Tidy up this message with AI').html('&#10024;');
					return;
				}

				var text = $in.val().trim();
				if (text === '') return;

				$btn.addClass('busy');
				post('ai_polish', { text: text })
					.done(function (r) {
						if (!r || !r.ok) {
							toast('AI assist', (r && r.message_text) ? r.message_text : 'Could not tidy that up.', 'err');
							return;
						}
						before = text;
						$in.val(r.text).trigger('input').focus();
						$btn.attr('title', 'Undo — put my original text back').html('&#8630;');
					})
					.fail(function () { toast('AI assist', 'Could not reach the server.', 'err'); })
					.always(function () { $btn.removeClass('busy'); });
			});

			// once the message is sent, there is nothing left to undo
			$(document).on('click', '#chSend', function () {
				before = null;
				$btn.attr('title', 'Tidy up this message with AI').html('&#10024;');
			});
		})();

		$(document).on('keyup', '#chPickSearch', filterPicker);
		$(document).on('change', '#chPickDept', filterPicker);
		$(document).on('click', '#chPickAll', function () {
			$('#chPickList .ch-pick:visible input[type=checkbox]').prop('checked', true);
		});
		$(document).on('click', '#chPickNone', function () {
			$('#chPickList input[type=checkbox]').prop('checked', false);
		});
		$(document).on('click', '.ch-picktab', function () {
			$('.ch-picktab').removeClass('active');
			$(this).addClass('active');
			filterPicker();
		});

		/* message actions */
		$('#chBody').on('click', '.ch-tools button', function (e) {
			e.stopPropagation();
			var act = $(this).data('act');
			var mid = parseInt($(this).closest('.ch-msg').data('mid'), 10);
			var msg = S.messages.filter(function (m) { return m.id === mid; })[0];
			if (!msg) return;

			if (act === 'react') {
				post('react', { message_id: mid, emoji: $(this).data('emoji') })
					.done(function (r) { if (r.ok) replaceMessage(r.message); });
			} else if (act === 'reply') {
				S.replyTo = msg; syncComposerBars(); $('#chInput').focus();
			} else if (act === 'pin') {
				// Unpinning is one press — asking "are you sure" about
				// something this reversible is just a second press. Pinning
				// asks how long for, because that is a real choice.
				if (msg && msg.is_pinned) sendPin(mid, 0);
				else                      pinModal(mid);
			} else if (act === 'fwd') {
				forwardModal(msg);

			} else if (act === 'priv') {
				post('private_reply_to', { message_id: mid }).done(function (r) {
					if (!r.ok) return toast('Could not open a private reply', '', 'err');
					// switch to the DM and carry the group message across as context
					var origin = r.origin;
					openConversation(r.conversation_id, true);
					setTimeout(function () {
						S.privateOrigin = origin;
						syncComposerBars();
						$('#chInput').focus();
					}, 450);
				}).fail(function (x) {
					var m = '';
					try { var j = JSON.parse(x.responseText); if (j.message_text) m = j.message_text; } catch (e) {}
					toast('Could not reply privately', m, 'err');
				});

			} else if (act === 'tag') {
				recordModal(mid);
			} else if (act === 'edit') {
				if (editLeft(msg) <= 0) {
					return toast('Too late to edit', 'Messages can only be edited within 15 minutes.', 'err');
				}
				var h = '<label>Edit message</label><textarea id="chEditBody" rows="4" spellcheck="true" autocorrect="on" lang="en">' + esc(msg.body) + '</textarea>' +
				        '<div style="font-size:11.5px;color:#7c8ba1;margin-top:-8px;">' +
				        'You can edit this for another ' + Math.ceil(editLeft(msg) / 60) + ' minute(s).</div>';
				modal('Edit message', h, 'Save changes', function () {
					var b = $('#chEditBody').val().trim();
					if (!b) return;
					post('edit_message', { message_id: mid, body: b }).done(function (r) {
						if (!r.ok) return toast('Edit failed', '', 'err');
						replaceMessage(r.message); closeModal();
					}).fail(function (x) {
						closeModal();
						var m = 'Edit failed';
						try { var j = JSON.parse(x.responseText); if (j.message_text) m = j.message_text; } catch (e) {}
						toast('Too late to edit', m === 'Edit failed' ? '' : m, 'err');
					});
				});
			} else if (act === 'del') {
				if (editLeft(msg) <= 0) {
					return toast('Too late to unsend', 'Messages can only be unsent within 15 minutes.', 'err');
				}
				if (!confirm('Unsend this message? It will be removed for everyone.')) return;
				post('delete_message', { message_id: mid }).done(function (r) {
					if (!r.ok) return toast('Could not unsend', '', 'err');
					if (r.message) replaceMessage(r.message); else openConversation(S.activeId);
				}).fail(function (x) {
					var m = '';
					try { var j = JSON.parse(x.responseText); if (j.message_text) m = j.message_text; } catch (e) {}
					toast('Could not unsend', m, 'err');
				});
			}
		});

		/* who has seen this message */
		$('#chBody').on('click', '[data-seen]', function (e) {
			e.stopPropagation();
			var mid = $(this).data('seen');
			modal('Seen by', '<div class="ch-sempty"><i class="fa fa-spinner fa-spin"></i> Loading…</div>', 'Close', closeModal);
			$('#chModalOk').text('Close');

			$.getJSON(BASE + 'seen_by/' + mid, function (r) {
				if (!r || !r.ok) return $('#chModalBody').html('<div class="ch-sempty">Could not load</div>');

				var rowFor = function (p, when) {
					return '<div class="who">' + avatar(p) +
						'<div style="min-width:0;"><div class="n">' + esc(p.name) + '</div>' +
						'<div class="t">' + esc(when || roleLine(p)) + '</div></div></div>';
				};
				var h = '<div class="ch-seenlist">';
				if (r.seen.length) {
					h += '<h6><i class="fa fa-check"></i> Seen · ' + r.seen.length + '</h6>';
					r.seen.forEach(function (p) { h += rowFor(p, p.at); });
				}
				if (r.pending.length) {
					h += '<h6>Not seen yet · ' + r.pending.length + '</h6>';
					r.pending.forEach(function (p) { h += rowFor(p, roleLine(p)); });
				}
				if (!r.seen.length && !r.pending.length) h += '<div class="ch-sempty">Nobody else is in this conversation</div>';
				$('#chModalBody').html(h + '</div>');
			});
		});

		$('#chBody').on('click', '.ch-react', function () {
			var mid = parseInt($(this).closest('.ch-msg').data('mid'), 10);
			post('react', { message_id: mid, emoji: $(this).data('emoji') })
				.done(function (r) { if (r.ok) replaceMessage(r.message); });
		});

		/* jump to a quoted / pinned / shared-file message.
		   #chInfo is in the list because a row in "Shared files" is a link
		   into the conversation — see renderInfoSection(). */
		$('#chBody, #chPinBar, #chInfo').on('click', '[data-jump]', function () {
			var $t = $('#chBody .ch-msg[data-mid="' + $(this).data('jump') + '"]');
			// Never silently do nothing. The message can be off the loaded
			// page (the thread keeps a window, not the whole history), and a
			// click that appears to be ignored is the bug people report.
			if (!$t.length) {
				return toast('That message is not loaded',
				             'Use "Load older messages" to go further back.');
			}
			$t[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
			$t.css('background', '#fff8dd');
			setTimeout(function () { $t.css('background', ''); }, 1300);
		});

		/* Downloading must not also jump — the button sits inside the row,
		   and the row is the jump. Let the browser follow the link, just stop
		   the click getting to the handler above. */
		$('#chInfo').on('click', '.ch-dl', function (e) { e.stopPropagation(); });

		/* take a pin down from the bar itself — see renderPins() */
		$('#chPinBar').on('click', '[data-unpin]', function (e) {
			e.stopPropagation();          // the row behind this jumps; the × must not
			sendPin($(this).data('unpin'), 0);
		});

		/* member panel */
		$('#chInfo').on('click', '#chAddMembers', addMembersModal);
		$('#chInfo').on('click', '#chRenameChannel', renameModal);
		$('#chInfo').on('click', '#chSetDept', setDepartmentModal);
		$('#chInfo').on('click', '#chLeave', function () {
			if (!confirm('Leave this group? You will stop receiving its messages.')) return;
			post('leave_channel', { conversation_id: S.activeId }).done(function (r) {
				if (!r.ok) return toast('Could not leave', '', 'err');
				S.activeId = 0; $('#chHead').hide(); $('#chComp').hide();
				$('#chBody').html('<div class="ch-empty" style="margin-top:120px;">You left the group.</div>');
				refreshConversations();
			});
		});
		/* delete the group (creator or app admin only; server re-checks) */
		$('#chInfo').on('click', '#chDeleteGroup', function () {
			var nm = (S.header && S.header.name) ? S.header.name : 'this group';
			// Two steps on purpose: this removes the room for everyone, and
			// the second prompt makes the person type the deletion rather than
			// click past it.
			if (!confirm('Delete “' + nm + '”?\n\nIt disappears for every member.')) return;
			if (!confirm('Are you sure? This affects everyone in the group, not just you.')) return;

			post('delete_channel', { conversation_id: S.activeId }).done(function (r) {
				if (!r.ok) return toast('Could not delete', r.message_text || '', 'err');
				S.activeId = 0; $('#chHead').hide(); $('#chComp').hide();
				$('#chBody').html('<div class="ch-empty" style="margin-top:120px;">Group deleted.</div>');
				refreshConversations();
			});
		});

		/* Reopen an archived group. Restores the loose files from the bundle
		   first — the server refuses and leaves it archived if it cannot. */
		$('#chArchBar').on('click', '#chUnarchive', function () {
			var nm = (S.header && S.header.name) ? S.header.name : 'this group';
			if (!confirm('Reopen “' + nm + '”?\n\nIts files are restored from the archive and ' +
			             'everyone can post in it again.')) return;

			var $b = $(this).prop('disabled', true)
				.html('<i class="fa fa-spinner fa-spin"></i> Reopening…');

			post('unarchive_channel', { conversation_id: S.activeId }).done(function (r) {
				if (!r.ok) {
					$b.prop('disabled', false).html('<i class="fa fa-undo"></i> Reopen group');
					return toast('Could not reopen', r.error || '', 'err');
				}
				toast('Group reopened', r.restored
					? r.restored + ' file' + (r.restored === 1 ? '' : 's') + ' restored'
					: '');
				openConversation(S.activeId);
				refreshConversations();
			}).fail(function () {
				$b.prop('disabled', false).html('<i class="fa fa-undo"></i> Reopen group');
				toast('Could not reopen', '', 'err');
			});
		});

		/* Archive a group by hand. DF groups do this themselves once their DF
		   is dispatched; this is for everything else, and for closing one
		   early. */
		$('#chInfo').on('click', '#chArchiveGroup', function () {
			var nm = (S.header && S.header.name) ? S.header.name : 'this group';
			if (!confirm('Archive “' + nm + '”?\n\nIt becomes read-only for everyone, its files ' +
			             'are bundled into one download, and it moves to Archived. You can ' +
			             'reopen it at any time.')) return;

			var $b = $(this).prop('disabled', true)
				.html('<i class="fa fa-spinner fa-spin"></i> Archiving…');

			post('archive_channel', { conversation_id: S.activeId }).done(function (r) {
				if (!r.ok) {
					$b.prop('disabled', false).html('<i class="fa fa-archive"></i> Archive group');
					return toast('Could not archive', r.error || '', 'err');
				}
				toast('Group archived', r.files
					? r.files + ' file' + (r.files === 1 ? '' : 's') + ' bundled'
					: 'No files to bundle');
				openConversation(S.activeId);
				refreshConversations();
			}).fail(function () {
				$b.prop('disabled', false).html('<i class="fa fa-archive"></i> Archive group');
				toast('Could not archive', '', 'err');
			});
		});

		/* promote / demote a group admin (owner only) */
		$('#chInfo').on('click', '[data-role]', function () {
			var to = $(this).data('to');
			if (!confirm(to === 'admin'
				? 'Make this member a group admin? They will be able to add and remove people.'
				: 'Remove admin rights from this member?')) return;

			post('set_member_role', {
				conversation_id: S.activeId,
				user_id: $(this).data('role'),
				user_type: $(this).data('rtype'),
				member_role: to
			}).done(function (r) {
				if (!r.ok) return toast('Could not change admin rights', '', 'err');
				S.members = r.members; renderInfo();
			}).fail(function (x) {
				var m = '';
				try { var j = JSON.parse(x.responseText); if (j.message_text) m = j.message_text; } catch (e) {}
				toast('Not permitted', m, 'err');
			});
		});

		/* group photo */
		$('#chInfo').on('click', '#chGroupPhoto', function () { $('#chGroupPhotoInput').click(); });
		$('#chInfo').on('change', '#chGroupPhotoInput', function () {
			if (!this.files || !this.files[0]) return;
			var fd = new FormData();
			fd.append('conversation_id', S.activeId);
			fd.append('photo', this.files[0]);
			this.value = '';

			$.ajax({ url: BASE + 'group_photo', type: 'POST', data: fd, processData: false, contentType: false })
				.done(function (r) {
					if (!r || !r.ok) return toast('Could not set group photo', '', 'err');
					S.header.avatar = r.photo;
					renderHeader(); renderInfo(); refreshConversations();
					toast('Group photo updated', '');
				})
				.fail(function (x) {
					var m = '';
					try { var j = JSON.parse(x.responseText); if (j.message_text) m = j.message_text; } catch (e) {}
					toast('Could not set group photo', m, 'err');
				});
		});

		$('#chInfo').on('click', '[data-remove]', function () {
			var id = $(this).data('remove'), type = $(this).data('rtype');
			if (!confirm('Remove this member from the group?')) return;
			post('remove_member', { conversation_id: S.activeId, user_id: id, user_type: type })
				.done(function (r) {
					if (!r.ok) return toast('Could not remove member', '', 'err');
					S.members = r.members; renderInfo(); renderHeader();
				});
		});

		/* composer */
		$('#chSend').on('click', sendMessage);
		$('#chReplyCancel').on('click', function () { S.replyTo = null; syncComposerBars(); });
		$('#chPrivCancel').on('click', function () { S.privateOrigin = null; syncComposerBars(); });
		$('#chTagBar').on('click', '[data-untag]', function () {
			S.pendingTags.splice($(this).data('untag'), 1); syncComposerBars();
		});
		$('#chFilesPre').on('click', '[data-unfile]', function () {
			S.pendingFiles.splice($(this).data('unfile'), 1); syncComposerBars();
		});
		$('#chLeadBtn').on('click', function () { recordModal(null); });
		$('#chAttach').on('click', function () { $('#chFileInput').click(); });
		/* Size check the moment a file is picked or dropped, so a 300 MB video
		   is refused now rather than after minutes of uploading. Videos up to
		   200 MB, everything else 25 MB - the same caps Chat::upload() applies
		   (MAX_VIDEO_UPLOAD / MAX_UPLOAD), which still re-checks. */
		var VIDEO_EXT = ['mp4', 'mov', 'm4v', '3gp', 'webm', 'mkv', 'avi'];
		function admitFile(f) {
			var ext = (f.name.split('.').pop() || '').toLowerCase();
			var isVideo = VIDEO_EXT.indexOf(ext) !== -1;
			var cap = isVideo ? 200 : 25;
			if (f.size > cap * 1048576) {
				toast(isVideo ? 'Video too large' : 'File too large',
				      f.name + ' is ' + (f.size / 1048576).toFixed(1) + ' MB. ' +
				      (isVideo ? 'Videos' : 'Files') + ' up to ' + cap + ' MB can be sent.', 'err');
				return false;
			}
			S.pendingFiles.push(f);
			return true;
		}
		$('#chFileInput').on('change', function () {
			for (var i = 0; i < this.files.length; i++) admitFile(this.files[i]);
			this.value = ''; syncComposerBars();
		});

		/* Paste an image straight from the clipboard - screenshots never have to
		   be saved to disk first. Works for Cmd/Ctrl+V and for drag-and-drop. */
		$('#chInput').on('paste', function (e) {
			var cd = e.originalEvent && e.originalEvent.clipboardData;
			if (!cd || !cd.items) return;
			var added = 0;
			for (var i = 0; i < cd.items.length; i++) {
				var it = cd.items[i];
				if (it.kind !== 'file' || it.type.indexOf('image/') !== 0) continue;
				var blob = it.getAsFile();
				if (!blob) continue;
				// clipboard images arrive unnamed - give them a sane filename
				var ext = (it.type.split('/')[1] || 'png').replace('jpeg', 'jpg');
				var named = new File([blob], 'pasted-' + stamp() + '.' + ext, { type: it.type });
				S.pendingFiles.push(named);
				added++;
			}
			if (added) { e.preventDefault(); syncComposerBars(); }
		});

		var $drop = $('#chBody, #chComp');
		$drop.on('dragover dragenter', function (e) { e.preventDefault(); e.stopPropagation(); })
		     .on('drop', function (e) {
			e.preventDefault(); e.stopPropagation();
			var dt = e.originalEvent && e.originalEvent.dataTransfer;
			if (!dt || !dt.files || !dt.files.length || !CAN.file_share) return;
			for (var i = 0; i < dt.files.length; i++) admitFile(dt.files[i]);
			syncComposerBars(); $('#chInput').focus();
		});

		var typingSent = 0;

		/**
		 * Put a newline in at the caret by hand.
		 *
		 * Shift+Enter types a newline on its own, but Alt+Enter does not - the
		 * browser has no default action for it in a textarea, so without this
		 * the key would simply do nothing. Written through the value + caret
		 * so it replaces any selection and leaves undo/typing state sane.
		 */
		function insertNewline(el) {
			var v = el.value, a = el.selectionStart, b = el.selectionEnd;
			el.value = v.slice(0, a) + '\n' + v.slice(b);
			el.selectionStart = el.selectionEnd = a + 1;
			$(el).trigger('input');            // re-grow the box, keep typing alive
		}

		$('#chInput').on('keydown', function (e) {
			if (mentionState.open) {
				if (e.key === 'ArrowDown') { e.preventDefault(); mentionState.sel = (mentionState.sel + 1) % mentionState.items.length; return renderMentionPop(); }
				if (e.key === 'ArrowUp')   { e.preventDefault(); mentionState.sel = (mentionState.sel - 1 + mentionState.items.length) % mentionState.items.length; return renderMentionPop(); }
				// Alt+Enter means "new line" even here, so it must not be
				// swallowed as "pick the highlighted name"
				if ((e.key === 'Enter' && !e.altKey) || e.key === 'Tab') { e.preventDefault(); return pickMention(mentionState.sel); }
				if (e.key === 'Escape') return closeMention();
			}
			if (e.key !== 'Enter') return;

			// Shift+Enter and Alt+Enter are always a new line, whichever way
			// the preference is set. Alt has no default action in a textarea,
			// so that one has to be typed in by hand.
			if (e.altKey)   { e.preventDefault(); return insertNewline(this); }
			if (e.shiftKey) return;

			// Ctrl/Cmd+Enter always sends, whichever way the preference is set -
			// so there is one shortcut that is never ambiguous.
			if (e.ctrlKey || e.metaKey) { e.preventDefault(); return sendMessage(); }

			// A bare Enter: send, unless this browser asked for the opposite,
			// in which case it falls through and the textarea takes the newline.
			if (S.enterSends) { e.preventDefault(); sendMessage(); }
		}).on('keyup input', function () {
			this.style.height = 'auto';
			this.style.height = Math.min(this.scrollHeight, 130) + 'px';
			checkMention();

			var now = Date.now();
			if (S.activeId && now - typingSent > 2500) {
				typingSent = now;
				post('typing', { conversation_id: S.activeId });
			}
		});

		$('#chMentionPop').on('click', '.ch-mention-item', function () { pickMention($(this).data('mi')); });

		/* ---------------- formatting toolbar ---------------- */

		/**
		 * Apply a format to the textarea selection.
		 * Wrapping marks toggle: hitting Bold on already-bold text unwraps it.
		 * List marks work line by line over whatever is selected.
		 */
		function applyFormat(kind) {
			var el = document.getElementById('chInput');
			if (!el) return;

			var val = el.value;
			var a = el.selectionStart, b = el.selectionEnd;
			var sel = val.slice(a, b);

			// italic is _x_: a single *x* now means bold (WhatsApp, and the app)
			var WRAP = { bold: '**', italic: '_', strike: '~~', code: '`' };

			if (WRAP[kind]) {
				var mark = WRAP[kind], n = mark.length;
				var already = sel.length > n * 2 &&
					sel.slice(0, n) === mark && sel.slice(-n) === mark;

				var next, caretA, caretB;
				if (already) {                               // unwrap
					next = sel.slice(n, -n);
					caretA = a; caretB = a + next.length;
				} else if (sel) {                            // wrap the selection
					next = mark + sel + mark;
					caretA = a + n; caretB = a + n + sel.length;
				} else {                                     // nothing selected
					next = mark + mark;
					caretA = caretB = a + n;                 // park the caret inside
				}
				el.value = val.slice(0, a) + next + val.slice(b);
				el.focus();
				el.setSelectionRange(caretA, caretB);

			} else {
				// list: prefix every selected line (or the current one)
				var lineStart = val.lastIndexOf('\n', a - 1) + 1;
				var lineEnd   = val.indexOf('\n', b);
				if (lineEnd === -1) lineEnd = val.length;

				var block = val.slice(lineStart, lineEnd);
				var rows  = block.split('\n');
				var isOl  = (kind === 'ol');

				// already a list of this kind? then strip it (toggle off)
				var re = isOl ? /^\s*\d+[.)]\s+/ : /^\s*[-*]\s+/;
				var allMarked = rows.every(function (r) { return r.trim() === '' || re.test(r); });

				var n2 = 1;
				var next2 = rows.map(function (r) {
					if (r.trim() === '') return r;
					if (allMarked) return r.replace(re, '');
					var clean = r.replace(/^\s*(?:[-*]|\d+[.)])\s+/, '');   // swap list type cleanly
					return (isOl ? (n2++) + '. ' : '- ') + clean;
				}).join('\n');

				el.value = val.slice(0, lineStart) + next2 + val.slice(lineEnd);
				el.focus();
				el.setSelectionRange(lineStart, lineStart + next2.length);
			}

			$(el).trigger('input');        // resize + mention check
		}

		$('#chFormat').on('click', '.ch-fmt', function (e) {
			e.preventDefault();
			applyFormat($(this).data('fmt'));
		});

		// Ctrl/Cmd+B and Ctrl/Cmd+I, as everyone expects
		$('#chInput').on('keydown', function (e) {
			if (!(e.ctrlKey || e.metaKey) || e.altKey) return;
			var k = (e.key || '').toLowerCase();
			if (k === 'b') { e.preventDefault(); applyFormat('bold'); }
			else if (k === 'i') { e.preventDefault(); applyFormat('italic'); }
		});

		/* ---------------- notification sound ---------------- */

		// The very first real gesture is what lets audio play at all - after
		// this the context stays usable even when the tab is in the background,
		// which is exactly when a notification sound earns its keep.
		$(document).one('click keydown', unlockAudio);

		function renderSoundBtn() {
			var on = soundOn();
			$('#chSoundBtn')
				.html('<i class="fa fa-bell' + (on ? '' : '-slash') + '"></i>')
				.attr('title', on ? 'Notification sound is on - click to mute'
				                  : 'Notification sound is off - click to unmute')
				.css('opacity', on ? 1 : 0.55);
		}
		$('#chSoundBtn').on('click', function () {
			var next = !soundOn();
			try { localStorage.setItem('ctChatSound', next ? '1' : '0'); } catch (e) {}
			renderSoundBtn();
			// the dock iframe and the host page are the same origin, so they
			// already share this preference through localStorage
			if (next) { unlockAudio(); lastBlip = 0; blip(); }   // let them hear it
			else toast('Notification sound off', 'Click the bell to turn it back on');
		});
		renderSoundBtn();

		/* ---------------- Enter behaviour ---------------- */
		function renderEnterHint() {
			$('#chEnterHint').html(S.enterSends
				? '<b>Enter</b> sends · <b>Shift+Enter</b> or <b>Alt+Enter</b> new line — click to swap'
				: '<b>Enter</b> starts a new line · send with the button or <b>Ctrl+Enter</b> — click to swap');
			$('#chInput').attr('placeholder', S.enterSends
				? 'Write a message...  (@ to mention, Enter to send, Shift+Enter for a new line)'
				: 'Write a message...  (@ to mention, Enter for a new line, Ctrl+Enter to send)');
		}
		$('#chEnterHint').on('click', function () {
			S.enterSends = !S.enterSends;
			try { localStorage.setItem('ctChatEnter', S.enterSends ? '1' : '0'); } catch (e) {}
			renderEnterHint();
			toast(S.enterSends ? 'Enter now sends' : 'Enter now starts a new line',
			      S.enterSends ? 'Shift+Enter or Alt+Enter for a new line' : 'Ctrl+Enter to send');
			$('#chInput').focus();
		});
		renderEnterHint();

		/* emoji */
		var eg = '';
		EMOJIS.forEach(function (e) { eg += '<button type="button">' + e + '</button>'; });
		$('#chEmojiGrid').html(eg);
		$('#chEmojiBtn').on('click', function (e) { e.stopPropagation(); $('#chEmojiPop').toggle(); });
		$('#chEmojiGrid').on('click', 'button', function () {
			var $i = $('#chInput'), pos = $i[0].selectionStart, v = $i.val();
			$i.val(v.substring(0, pos) + $(this).text() + v.substring(pos)).focus();
			var np = pos + $(this).text().length;
			$i[0].setSelectionRange(np, np);
		});
		$(document).on('click', function (e) {
			if (!$(e.target).closest('#chEmojiPop, #chEmojiBtn').length) $('#chEmojiPop').hide();
			if (!$(e.target).closest('#chMentionPop, #chInput').length) closeMention();
		});

		$('#chJump').on('click', function () { scrollBottom(); });
		$('#chBody').on('click', '#chRetry', function () { if (S.activeId) openConversation(S.activeId); });
	$('#chBody').on('click', '#chToList', function () {
		$('#chSide').removeClass('hide-sm'); $('#chMain').removeClass('show-sm');
		refreshConversations();
	});
		$('#chBody').on('click', '#chOlder', function () { loadOlder(); });

		/* ---------------- calls: Zoom / Google Meet ---------------- */

		$('#chCallBtn').on('click', function () {
			if (!S.activeId) return;
			var who = S.header && S.header.type === 'direct'
				? esc(S.header.name)
				: (S.members.length + ' people in ' + esc(S.header ? S.header.name : 'this group'));

			modal('Start a call',
				'<p style="font-size:12.5px;color:#64748b;margin:0 0 14px;">' +
					'Everyone in this conversation gets a join link and a notification. ' +
					'<b>' + who + '</b> will be notified.</p>' +
				'<label>Add a topic (optional)</label>' +
				'<input type="text" id="chCallTopic" placeholder="e.g. DF-1826 design review" maxlength="180">' +
				'<label>Choose a provider</label>' +
				'<div style="display:flex;gap:8px;flex-wrap:wrap;">' +
					'<button type="button" class="ch-btn primary chprov" data-prov="zoom" style="flex:1 1 120px;">' +
						'<i class="fa fa-video-camera"></i> Zoom</button>' +
					'<button type="button" class="ch-btn primary chprov" data-prov="meet" style="flex:1 1 120px;background:#00897b;">' +
						'<i class="fa fa-video-camera"></i> Google Meet</button>' +
					'<button type="button" class="ch-btn primary chprov" data-prov="teams" style="flex:1 1 120px;background:#5b5fc7;">' +
						'<i class="fa fa-video-camera"></i> Microsoft Teams</button>' +
				'</div>' +
				'<div style="margin-top:14px;font-size:11.5px;color:#94a3b8;">' +
					'Tip: save your personal meeting room once and starting a call becomes a single click. ' +
					'<a href="javascript:void(0)" id="chMeetLinks">Set up my rooms</a></div>',
				'Cancel', closeModal);
			$('#chModalOk').hide();
		});

		// starting: open the provider, post the card, then collect the link
		$(document).on('click', '.chprov', function () {
			var prov  = $(this).data('prov');
			var topic = $('#chCallTopic').val() || '';
			closeModal();

			// the popup must be opened in the SAME user gesture or the browser
			// blocks it, so open it first and point it at the provider after
			var win = window.open('', '_blank');

			post('start_call', { conversation_id: S.activeId, provider: prov, topic: topic })
				.done(function (r) {
					if (!r || !r.ok) { if (win) win.close(); return toast('Could not start the call', '', 'err'); }

					appendMessage(r.message);
					refreshConversations();

					if (r.join_url) {
						// personal room saved -> straight into the meeting
						if (win) win.location = r.join_url; else window.open(r.join_url, '_blank');
					} else {
						if (win) win.location = r.start_url; else window.open(r.start_url, '_blank');
						askForCallLink(r.call_id, prov);
					}
				})
				.fail(function () { if (win) win.close(); toast('Could not start the call', '', 'err'); });
		});

		/** Prompt the starter to paste the link the provider just generated. */
		function askForCallLink(callId, prov) {
			var P     = provider(prov);
			var label = P.label;
			var eg    = P.eg;

			modal('Share the ' + label + ' link',
				'<p style="font-size:12.5px;color:#64748b;margin:0 0 12px;">' +
					'Your meeting opened in a new tab. Copy its address bar link and paste it here so the ' +
					'others can join.</p>' +
				'<label>' + label + ' link</label>' +
				'<input type="text" id="chCallUrl" placeholder="' + eg + '">' +
				'<label style="display:flex;align-items:center;gap:7px;font-weight:500;">' +
					'<input type="checkbox" id="chCallRemember" checked style="width:auto;margin:0;"> ' +
					'Remember this as my ' + label + ' room (one-click next time)</label>',
				'Share link', function () {
					var url = $('#chCallUrl').val().trim();
					if (!url) return;
					post('attach_call_url', {
						call_id: callId, join_url: url,
						remember: $('#chCallRemember').is(':checked') ? 1 : 0
					}).done(function (r) {
						if (!r.ok) return toast('Could not share the link', '', 'err');
						replaceMessage(r.message); closeModal();
					}).fail(function (x) {
						var m = '';
						try { var j = JSON.parse(x.responseText); if (j.message_text) m = j.message_text; } catch (e) {}
						toast('That link was not accepted', m, 'err');
					});
				});
			setTimeout(function () { $('#chCallUrl').focus(); }, 80);
		}

		$('#chBody').on('click', '[data-addlink]', function () {
			var id = $(this).data('addlink');
			var card = $(this).closest('.ch-call');
			var prov = card.hasClass('meet') ? 'meet' : (card.hasClass('teams') ? 'teams' : 'zoom');
			askForCallLink(id, prov);
		});

		$('#chBody').on('click', '[data-endcall]', function () {
			if (!confirm('End this call for everyone?')) return;
			post('end_call', { call_id: $(this).data('endcall') }).done(function (r) {
				if (!r.ok) return toast('Could not end the call', '', 'err');
				replaceMessage(r.message);
			});
		});

		/* personal meeting rooms */
		$(document).on('click', '#chMeetLinks', function () {
			$.getJSON(BASE + 'meeting_links', function (r) {
				var L = (r && r.ok) ? r.links : { zoom: '', meet: '', teams: '' };
				modal('My meeting rooms',
					'<p style="font-size:12.5px;color:#64748b;margin:0 0 14px;">' +
						'Save your personal room links and starting a call becomes one click - no pasting.</p>' +
					'<label>Zoom personal meeting room</label>' +
					'<input type="text" id="chZoomUrl" placeholder="https://zoom.us/j/1234567890" value="' + esc(L.zoom) + '">' +
					'<label>Google Meet room</label>' +
					'<input type="text" id="chMeetUrl" placeholder="https://meet.google.com/abc-defg-hij" value="' + esc(L.meet) + '">' +
					'<label>Microsoft Teams room</label>' +
					'<input type="text" id="chTeamsUrl" placeholder="https://teams.microsoft.com/l/meetup-join/..." value="' + esc(L.teams || '') + '">',
					'Save', function () {
						var z = $('#chZoomUrl').val().trim(),
						    g = $('#chMeetUrl').val().trim(),
						    t = $('#chTeamsUrl').val().trim();
						$.when(
							post('meeting_links', { provider: 'zoom',  join_url: z }),
							post('meeting_links', { provider: 'meet',  join_url: g }),
							post('meeting_links', { provider: 'teams', join_url: t })
						).always(function () { closeModal(); toast('Meeting rooms saved', ''); });
					});
			});
		});

		/* ---------------- screenshot annotator ---------------- */

		$('#chFilesPre').on('click', '[data-annot]', function () {
			annotOpen(parseInt($(this).data('annot'), 10));
		});

		$('.ch-annot-tools').on('click', '.ch-tool[data-tool]', function () {
			$('.ch-tool[data-tool]').removeClass('on');
			$(this).addClass('on');
			AN.tool = $(this).data('tool');
			$('#chAnnotText').hide();
		});
		$('#chAnnotColors').on('click', '.ch-swatch', function () {
			AN.color = $(this).data('col'); renderColors();
		});
		$('#chAnnotSize').on('input change', function () { AN.size = parseInt(this.value, 10) || 4; });
		$('#chAnnotUndo').on('click', function () { AN.shapes.pop(); annotRender(); });
		$('#chAnnotClear').on('click', function () {
			if (AN.shapes.length && !confirm('Remove all annotations from this image?')) return;
			AN.shapes = []; annotRender();
		});
		$('#chAnnotCancel').on('click', annotClose);
		$('#chAnnotSave').on('click', annotExport);
		$('#chAnnotMask').on('click', function (e) { if (e.target === this) annotClose(); });

		/* drawing: mouse and touch share one path */
		var $canvas = $('#chAnnotCanvas');

		function annotDown(e) {
			if (!AN.img) return;
			e.preventDefault();
			var p = annotPos(e);

			if (AN.tool === 'text') {
				// position an input over the click point and commit on Enter
				var c = document.getElementById('chAnnotCanvas');
				var r = c.getBoundingClientRect();
				var wrapR = document.getElementById('chAnnotWrap').getBoundingClientRect();
				$('#chAnnotText')
					.css({
						left: (r.left - wrapR.left + p.x * (r.width / c.width)) + 'px',
						top:  (r.top - wrapR.top + p.y * (r.height / c.height)) + 'px',
						color: AN.color,
						fontSize: Math.max(13, AN.size * 5 * (r.width / c.width)) + 'px'
					})
					.data('at', p).val('').show().focus();
				return;
			}

			AN.drawing = true;
			AN.start = p;
			AN.current = (AN.tool === 'pen')
				? { type: 'pen', color: AN.color, size: AN.size, pts: [p] }
				: { type: AN.tool, color: AN.color, size: AN.size, x1: p.x, y1: p.y, x2: p.x, y2: p.y };
		}

		function annotMove(e) {
			if (!AN.drawing || !AN.current) return;
			e.preventDefault();
			var p = annotPos(e);
			if (AN.current.type === 'pen') AN.current.pts.push(p);
			else { AN.current.x2 = p.x; AN.current.y2 = p.y; }
			annotRender();
		}

		function annotUp() {
			if (!AN.drawing) return;
			AN.drawing = false;
			var s = AN.current; AN.current = null;
			if (!s) return;

			// ignore accidental taps that produced nothing
			var big = (s.type === 'pen')
				? s.pts.length > 2
				: (Math.abs(s.x2 - s.x1) > 4 || Math.abs(s.y2 - s.y1) > 4);
			if (big) AN.shapes.push(s);
			annotRender();
		}

		$canvas.on('mousedown', annotDown).on('touchstart', annotDown);
		$(document).on('mousemove', annotMove).on('touchmove', annotMove);
		$(document).on('mouseup', annotUp).on('touchend', annotUp);

		$('#chAnnotText').on('keydown', function (e) {
			if (e.key === 'Enter') {
				var v = $(this).val().trim();
				var at = $(this).data('at');
				if (v && at) {
					AN.shapes.push({ type: 'text', color: AN.color, size: AN.size, x1: at.x, y1: at.y, text: v });
					annotRender();
				}
				$(this).hide().val('');
			} else if (e.key === 'Escape') {
				$(this).hide().val('');
			}
			e.stopPropagation();
		});

		$(document).on('keydown', function (e) {
			if (!$('#chAnnotMask').hasClass('open')) return;
			if (e.key === 'Escape') annotClose();
			if ((e.ctrlKey || e.metaKey) && e.key === 'z') { e.preventDefault(); AN.shapes.pop(); annotRender(); }
		});

		/* ---------------- message search ---------------- */
		$('#chSearchBtn').on('click', function () {
			var $bar = $('#chSearchBar').toggleClass('open');
			$(this).toggleClass('on', $bar.hasClass('open'));
			if ($bar.hasClass('open')) $('#chSearchInput').focus();
			else $('#chSearchResults').empty();
		});
		$('#chSearchClose').on('click', function () {
			$('#chSearchBar').removeClass('open');
			$('#chSearchBtn').removeClass('on');
			$('#chSearchResults').empty();
			$('#chSearchInput').val('');
		});

		var searchT = null;
		function runSearch() {
			var q = $('#chSearchInput').val().trim();
			if (q.length < 2) {
				$('#chSearchResults').html('<div class="ch-sempty">Type at least 2 characters</div>');
				return;
			}
			$('#chSearchResults').html('<div class="ch-sempty"><i class="fa fa-spinner fa-spin"></i> Searching…</div>');

			$.getJSON(BASE + 'search_messages', {
				q: q,
				conv: $('#chSearchAll').is(':checked') ? 0 : (S.activeId || 0)
			}, function (r) {
				if (!r || !r.ok) return $('#chSearchResults').html('<div class="ch-sempty">Search failed</div>');
				if (!r.results.length) {
					return $('#chSearchResults').html('<div class="ch-sempty">No messages found for “' + esc(q) + '”</div>');
				}
				var h = '';
				r.results.forEach(function (x) {
					h += '<div class="ch-sres" data-conv="' + x.conversation + '" data-mid="' + x.id + '">' +
							avatar(x) +
							'<div style="min-width:0;flex:1;">' +
								'<div class="who">' + esc(x.sender_name) +
									' <span class="where">' + esc(x.conv_name) + '</span></div>' +
								'<div class="txt">' + highlight(x.body, q) + '</div>' +
								'<div class="when">' + esc(x.when) + '</div>' +
							'</div>' +
						'</div>';
				});
				$('#chSearchResults').html(h);
			});
		}
		$('#chSearchInput').on('keyup', function (e) {
			if (e.key === 'Escape') return $('#chSearchClose').click();
			clearTimeout(searchT); searchT = setTimeout(runSearch, 280);
		});
		$('#chSearchAll').on('change', runSearch);

		// jump to the hit: switch conversation if needed, then highlight it
		$('#chSearchResults').on('click', '.ch-sres', function () {
			var conv = parseInt($(this).data('conv'), 10);
			var mid  = parseInt($(this).data('mid'), 10);
			if (conv !== S.activeId) {
				openConversation(conv, true);
				setTimeout(function () { jumpToMessage(mid); }, 700);
			} else {
				jumpToMessage(mid);
			}
		});

		/* older messages: same path whether you scroll up or press the button */
		$('#chBody').on('scroll', function () {
			// remember whether the user is following the conversation
			S.pinned = nearBottom();
			if (S.pinned) $('#chJump').hide();

			if (this.scrollTop > 40) return;
			loadOlder();
		});

		/* reading resumes the moment the tab comes back */
		document.addEventListener('visibilitychange', function () {
			if (!document.hidden && S.activeId) markRead();
		});

		if ('Notification' in window && Notification.permission === 'default') {
			$('#chBody').one('click', function () { Notification.requestPermission(); });
		}

		var fitT = null;
		$(window).on('resize', function () {
			clearTimeout(fitT);
			fitT = setTimeout(fitTabs, 120);
		});

		/* go */
		renderList();
		fitTabs();
		watchVisibility();
		// Open a conversation ONLY when one was asked for - a deep link
		// (/Chat/open/12, /Chat/job/45) or the conversation the dock was on
		// before the user navigated. Landing on Chat with nothing selected
		// shows the list and the "Select a conversation" panel: opening the
		// most recent thread by itself marks it read and buries the unread
		// ones the user actually came to look at.
		if (S.activeId) openConversation(S.activeId);
		stream();
	});

}(jQuery));
</script>

	<?php if (!$dock) $this->load->view('common/footer'); ?>
</body>
</html>
