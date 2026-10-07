<?php
/**
 * Chat — DF group backfill.
 *
 * New DFs get their chat group automatically when they are released
 * (Task::dfrelease()). Every DF already in flight when that shipped does not,
 * so this lists them and creates one on a click.
 *
 * Read-only until a button is pressed, and every button is the same
 * find-or-create call the release hook uses — there is no second code path
 * that could create a differently-named or differently-populated group.
 *
 * Only marketing and administrators reach this page (chat_df_group_scope()).
 * Marketing sees only the DFs they released; administrators see every DF.
 *
 * SCRIPTS: this page loads the app's own JS bundle at the bottom
 * (jquery.core.js + jquery.app.js and their dependencies). Those are what
 * drive the sidebar — without them the collapsible menus in
 * common/nav-menu.php do nothing when clicked, which is exactly what happened
 * on the first cut of this page. jQuery itself goes in the HEAD, because
 * nav-menu.php runs inline jQuery while it renders, long before anything at
 * the bottom of the document has parsed.
 *
 * Expects: $rows, $show_all, $scope, $me
 */
defined('BASEPATH') OR exit('No direct script access allowed');
$flash = $this->session->flashdata('msg');

$with    = 0;
$without = 0;
$penalty = 0;
foreach ($rows as $r) {
	if ($r['conv_id']) $with++; else $without++;
	if (!empty($r['penalty'])) $penalty++;
}
$total = count($rows);

/* This screen FLAGS a penalty DF, it does not price one. What a penalty comes
   to belongs to the penalty report (views/master/penalitydf.php); chat carries
   no money figure anywhere, which is also why there is no currency formatter
   in the module to drift out of step with that report. */
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
	<title><?php echo sitetitle; ?> | DF chat groups</title>

	<!-- In the head deliberately: common/nav-menu.php executes inline jQuery
	     as it renders and cannot wait for the bottom of the page. -->
	<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>

	<link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

<style>
.dfg{font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#334155;padding-bottom:26px;}

/* ---- hero ---------------------------------------------------------- */
.dfg-hero{
	background:linear-gradient(120deg,#1e3a8a 0%,#2563eb 55%,#3b82f6 100%);
	border-radius:16px;padding:22px 26px;color:#fff;margin-bottom:18px;
	display:flex;justify-content:space-between;align-items:center;gap:26px;flex-wrap:wrap;
	box-shadow:0 12px 30px rgba(37,99,235,.22);
}
.dfg-hero h1{margin:0 0 6px;font-size:21px;font-weight:700;letter-spacing:-.01em;display:flex;align-items:center;gap:9px;color:#fff;}
.dfg-hero p{margin:0;font-size:12.5px;line-height:1.6;color:rgba(255,255,255,.88);max-width:660px;}
.dfg-hero b{color:#fff;}
.dfg-hero code{background:rgba(255,255,255,.18);border-radius:5px;padding:1px 7px;font-size:12px;color:#fff;}
.dfg-stats{display:flex;gap:10px;flex-wrap:wrap;}
.dfg-stat{background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.26);border-radius:12px;
	padding:11px 17px;min-width:104px;text-align:center;}
.dfg-stat b{display:block;font-size:23px;font-weight:700;line-height:1.15;color:#fff;}
.dfg-stat span{font-size:10.5px;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.85);}

/* ---- toolbar ------------------------------------------------------- */
.dfg-bar{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:12px;}
.dfg-scope{font-size:12.5px;color:#64748b;}
.dfg-scope b{color:#1e293b;}
.dfg-tools{display:flex;gap:9px;align-items:center;flex-wrap:wrap;}
.dfg-search{border:1px solid #dbe3ee;border-radius:9px;padding:8px 13px;font-size:13px;width:250px;
	outline:none;transition:border-color .15s,box-shadow .15s;background:#fff;color:#334155;}
.dfg-search:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.12);}
.dfg-link{font-size:12.5px;color:#2563eb;text-decoration:none;font-weight:600;padding:8px 12px;
	border:1px solid #dbe3ee;border-radius:9px;background:#fff;display:inline-block;}
.dfg-link:hover{background:#f0f6ff;color:#1d4ed8;text-decoration:none;}

/* ---- table --------------------------------------------------------- */
.dfg-card{background:#fff;border:1px solid #e6ecf4;border-radius:14px;
	box-shadow:0 1px 3px rgba(15,32,60,.05);
	/* MUST scroll rather than clip. The first cut of this page used
	   overflow:hidden and the right-hand column was cut off. */
	overflow-x:auto;}
.dfg-table{width:100%;border-collapse:collapse;min-width:780px;}
.dfg-table th{background:#f8fafc;text-align:left;font-size:10.5px;letter-spacing:.05em;text-transform:uppercase;
	color:#7c8ba1;padding:11px 16px;border-bottom:1px solid #e6ecf4;white-space:nowrap;font-weight:700;}
.dfg-table td{padding:12px 16px;border-bottom:1px solid #f4f7fb;font-size:13px;vertical-align:middle;}
.dfg-table tbody tr:last-child td{border-bottom:0;}
.dfg-table tbody tr{transition:background .12s;}
.dfg-table tbody tr:hover{background:#f8fbff;}

.dfg-df{display:flex;align-items:center;gap:11px;}
.dfg-badge{width:38px;height:38px;border-radius:10px;background:#eef4ff;color:#2563eb;
	display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;}
.dfg-badge.has{background:#dcfce7;color:#166534;}
.dfg-no{font-weight:700;color:#1e293b;font-size:13.5px;}
.dfg-sub{font-size:11.5px;color:#94a3b8;margin-top:1px;}

.dfg-st{display:inline-block;font-size:10.5px;font-weight:700;padding:3px 10px;border-radius:20px;white-space:nowrap;}
.dfg-st.run{background:#e0f2fe;color:#0369a1;}
.dfg-st.hold{background:#fef3c7;color:#92400e;}
.dfg-st.done{background:#dcfce7;color:#166534;}

/* PENALTY.
   The status pills above are soft, rounded and pastel because a status is
   just a fact. A penalty is an exception, so this is deliberately a different
   SHAPE as well as a different colour — squarer, ringed, upper-case, with a
   warning glyph — so it reads as a flag rather than as one more state. That
   also keeps it legible to anyone who cannot separate red from amber. */
.dfg-pen{
	display:inline-flex;align-items:center;gap:5px;
	font-size:10.5px;font-weight:800;letter-spacing:.04em;text-transform:uppercase;
	padding:3px 9px;border-radius:6px;white-space:nowrap;
	background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;
	box-shadow:0 0 0 2px rgba(239,68,68,.10);
}
/* the whole row is marked, so a penalty is scannable down a long list
   without reading every line */
tr.is-pen td{background:#fffafa;}
tr.is-pen:hover td{background:#fff5f5;}
tr.is-pen td:first-child{box-shadow:inset 3px 0 0 #ef4444;}
.dfg-stat.pen b{color:#fecaca;}

.dfg-btn{display:inline-flex;align-items:center;gap:6px;border:0;border-radius:9px;padding:7px 15px;
	font-size:12px;font-weight:600;cursor:pointer;text-decoration:none!important;white-space:nowrap;
	transition:background .15s;}
.dfg-btn.make{background:#2563eb;color:#fff;box-shadow:0 2px 8px rgba(37,99,235,.28);}
.dfg-btn.make:hover{background:#1d4ed8;color:#fff;}
.dfg-btn.open{background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;}
.dfg-btn.open:hover{background:#e8eef6;color:#1e293b;}

.dfg-empty{padding:52px 18px;text-align:center;color:#8b99ad;font-size:13px;}
.dfg-empty svg{opacity:.32;margin-bottom:10px;}
.dfg-flash{background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:11px 15px;border-radius:10px;
	margin-bottom:14px;font-size:13px;}

@media (max-width:860px){
	.dfg-hero{padding:18px;}
	.dfg-hero h1{font-size:18px;}
	.dfg-search{width:100%;}
	.dfg-tools{width:100%;}
}
</style>
</head>
<body>
	<header id="topnav">
		<?php $this->load->view('common/nav-menu'); ?>
	</header>

	<div class="wrapper">
		<div class="container-fluid">
			<div class="dfg">

				<?php if ($flash): ?>
					<div class="dfg-flash"><?php echo $flash; ?></div>
				<?php endif; ?>

				<div class="dfg-hero">
					<div>
						<h1>
							<svg width="21" height="21" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
							DF chat groups
						</h1>
						<p>
							A DF released from now on gets its group automatically, named
							<code>DF - &lt;df no&gt; - Group</code>, with every active department leader
							added and notified. DFs already running before this was switched on need
							theirs creating once — that is what this page is for.
							Pressing <b>Create group</b> twice is harmless: it adds only leaders who are
							genuinely new, and notifies only them.
						</p>
					</div>
					<div class="dfg-stats">
						<div class="dfg-stat"><b><?php echo $without; ?></b><span>To create</span></div>
						<div class="dfg-stat"><b><?php echo $with; ?></b><span>Have one</span></div>
						<?php if ($penalty > 0): ?>
							<div class="dfg-stat pen"><b><?php echo $penalty; ?></b><span>Penalty</span></div>
						<?php endif; ?>
					</div>
				</div>

				<div class="dfg-bar">
					<div class="dfg-scope">
						<?php if ($scope === 'own'): ?>
							Showing the <b>DFs you released</b>
						<?php else: ?>
							Showing <b>every DF</b> (administrator)
						<?php endif; ?>
						· <?php echo $show_all ? '<b>all</b> statuses' : '<b>running</b> only'; ?>
						· <span id="dfgCount"><?php echo $total; ?></span> shown
					</div>
					<div class="dfg-tools">
						<input type="text" class="dfg-search" id="dfgSearch"
						       placeholder="Search DF no or customer..." autocomplete="off">
						<?php if ($penalty > 0): ?>
							<label class="dfg-link" style="cursor:pointer;font-weight:600;margin:0;">
								<input type="checkbox" id="dfgPenOnly" style="vertical-align:-1px;margin-right:5px;">
								Penalty only
							</label>
						<?php endif; ?>
						<?php if ($show_all): ?>
							<a class="dfg-link" href="<?php echo page_url; ?>chat/df-groups">Running only</a>
						<?php else: ?>
							<a class="dfg-link" href="<?php echo page_url; ?>chat/df-groups?all=1">Include completed</a>
						<?php endif; ?>
					</div>
				</div>

				<div class="dfg-card">
					<?php if (empty($rows)): ?>
						<div class="dfg-empty">
							<svg width="42" height="42" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
							<div>No DFs to show<?php echo $scope === 'own' ? ' against your account' : ''; ?>.</div>
						</div>
					<?php else: ?>
					<table class="dfg-table">
						<thead>
							<tr>
								<th>DF</th>
								<th>Customer</th>
								<th>Released</th>
								<th>Status</th>
								<th style="text-align:right;">Chat group</th>
							</tr>
						</thead>
						<tbody id="dfgBody">
						<?php foreach ($rows as $r): ?>
							<?php
							$cls = $r['status'] === 'Completed' ? 'done'
								 : ($r['status'] === 'On hold' ? 'hold' : 'run');
							$is_pen = !empty($r['penalty']);
							// "penalty" is searchable text, so the box finds
							// these rows as well as the toggle filtering them
							$hay = strtolower($r['df_no'] . ' ' . $r['company'] . ' ' . $r['title']
								. ($is_pen ? ' penalty' : ''));

							?>
							<tr data-find="<?php echo html_escape($hay); ?>"
							    data-pen="<?php echo $is_pen ? '1' : '0'; ?>"
							    class="<?php echo $is_pen ? 'is-pen' : ''; ?>">
								<td>
									<div class="dfg-df">
										<div class="dfg-badge <?php echo $r['conv_id'] ? 'has' : ''; ?>">DF</div>
										<div style="min-width:0;">
											<div class="dfg-no"><?php echo html_escape($r['df_no']); ?></div>
											<?php if ($r['title'] !== ''): ?>
												<div class="dfg-sub"><?php echo html_escape($r['title']); ?></div>
											<?php endif; ?>
										</div>
									</div>
								</td>
								<td><?php echo html_escape($r['company'] !== '' ? $r['company'] : '—'); ?></td>
								<td style="white-space:nowrap;color:#64748b;"><?php echo html_escape($r['released'] !== '' ? $r['released'] : '—'); ?></td>
								<td>
									<span class="dfg-st <?php echo $cls; ?>"><?php echo html_escape($r['status']); ?></span>
									<?php if ($is_pen): ?>
										<span class="dfg-pen" title="Marked as a penalty DF">
											<svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
											Penalty
										</span>
									<?php endif; ?>
								</td>
								<td style="text-align:right;">
									<?php if ($r['conv_id']): ?>
										<!-- routed through chat/df/<df id>, not straight to the
										     conversation, so the same find-or-create-and-join path
										     runs. Linking to Chat/index directly would drop a
										     non-member into a room they cannot open. -->
										<a class="dfg-btn open" href="<?php echo page_url; ?>chat/df/<?php echo (int) $r['df_id']; ?>">
											<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
											Open group
										</a>
									<?php else: ?>
										<a class="dfg-btn make" href="<?php echo page_url; ?>chat/df/<?php echo (int) $r['df_id']; ?>">
											<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
											Create group
										</a>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
					<div class="dfg-empty" id="dfgNoMatch" style="display:none;">Nothing matches that search.</div>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</div>

	<?php $this->load->view('common/footer'); ?>

<!-- The app's own bundle. jquery.core.js + jquery.app.js are what make the
     sidebar's collapsible menus work; the four above them are their
     dependencies. jQuery itself is already loaded in the head. -->
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/detect.js"></script>
<script src="<?php echo assets_url; ?>js/fastclick.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
<script src="<?php echo assets_url; ?>js/waves.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

<script>
/* Client-side filter. The whole list is already on the page — with a few
   hundred DFs at most that is far cheaper than a round trip per keystroke. */
(function () {
	var box = document.getElementById('dfgSearch');
	if (!box) return;

	var rows  = [].slice.call(document.querySelectorAll('#dfgBody tr'));
	var count = document.getElementById('dfgCount');
	var none  = document.getElementById('dfgNoMatch');
	var pen   = document.getElementById('dfgPenOnly');

	function apply() {
		var q      = box.value.toLowerCase().trim();
		var penOnly = pen && pen.checked;
		var shown  = 0;
		rows.forEach(function (tr) {
			var okQ   = !q || (tr.getAttribute('data-find') || '').indexOf(q) !== -1;
			var okPen = !penOnly || tr.getAttribute('data-pen') === '1';
			var hit   = okQ && okPen;
			tr.style.display = hit ? '' : 'none';
			if (hit) shown++;
		});
		if (count) count.textContent = shown;
		if (none)  none.style.display = shown === 0 ? '' : 'none';
	}

	box.addEventListener('keyup', apply);
	if (pen) pen.addEventListener('change', apply);
})();
</script>
</body>
</html>
