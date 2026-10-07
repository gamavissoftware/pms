<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DF Progress Gantt board.
 *
 * Pure presentation: every value here is prepared by Gantt_chart_model, and
 * nothing on this page touches the database. Bar geometry arrives as
 * percentages of the timeline width, so zooming is one CSS variable.
 *
 * Expected: $found, $df_id, $message, $header, $timeline, $rows, $stats, $changes
 */

if (!function_exists('gc_e')) {
	function gc_e($value)
	{
		return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
	}
}

if (!function_exists('gc_day')) {
	function gc_day($date, $fallback = '—')
	{
		return ($date !== '' && $date !== NULL) ? date('d M y', strtotime($date)) : $fallback;
	}
}

if (!function_exists('gc_status_label')) {
	function gc_status_label($status)
	{
		$labels = array(
			'done'     => 'Completed',
			'late'     => 'Done late',
			'overdue'  => 'Overdue',
			'running'  => 'In progress',
			'hold'     => 'On hold',
			'upcoming' => 'Not started',
		);

		return isset($labels[$status]) ? $labels[$status] : ucfirst($status);
	}
}

if (!function_exists('gc_delay_label')) {
	function gc_delay_label($days)
	{
		$days = (int) $days;

		if ($days <= 0) {
			return 'On time';
		}

		return $days . ' day' . ($days > 1 ? 's' : '');
	}
}

$df_id    = isset($df_id) ? (int) $df_id : 0;
$found    = !empty($found);
$header   = isset($header) && is_array($header) ? $header : array();
$timeline = isset($timeline) && is_array($timeline) ? $timeline : array();
$rows     = isset($rows) && is_array($rows) ? $rows : array();
$stats    = isset($stats) && is_array($stats) ? $stats : array();
$changes  = isset($changes) && is_array($changes) ? $changes : array('summary' => array(), 'by_department' => array(), 'active' => 0, 'departments' => 0);

$options = isset($options) && is_array($options)
	? $options
	: array('group' => 'milestone', 'scale' => 'week', 'department' => 0);

$departments = isset($departments) && is_array($departments) ? $departments : array();

/**
 * The current URL with some switches changed. Every view control is a plain
 * link, so each combination is bookmarkable and the back button works.
 */
if (!function_exists('gc_url')) {
	function gc_url($df_id, array $options, array $overrides = array())
	{
		$query = array_merge(
			array('group' => $options['group'], 'scale' => $options['scale'], 'dept' => $options['department']),
			$overrides
		);

		foreach ($query as $key => $value) {
			if ($value === '' || $value === 0 || $value === '0') {
				unset($query[$key]);
			}
		}

		return page_url . 'gantt/' . (int) $df_id . (empty($query) ? '' : '?' . http_build_query($query));
	}
}

if (!function_exists('gc_export_url')) {
	function gc_export_url($df_id, array $options, $format)
	{
		$query = array('group' => $options['group'], 'scale' => $options['scale'], 'dept' => $options['department']);

		foreach ($query as $key => $value) {
			if ($value === '' || $value === 0 || $value === '0') {
				unset($query[$key]);
			}
		}

		return page_url . 'gantt/export/' . $format . '/' . (int) $df_id
			. (empty($query) ? '' : '?' . http_build_query($query));
	}
}

$group_labels = array(
	'milestone'  => array('Milestones', 'Curated department milestones, tasks underneath'),
	'department' => array('Departments', 'One row per department, its tasks underneath'),
	'task'       => array('Tasks', 'Every scheduled task as its own row'),
);

$scale_labels = array(
	'week' => array('Week', 'One column per week'),
	'day'  => array('Day', 'One column per day'),
);

$page_title = $found ? ('DF ' . $header['df_no'] . ' — Progress Gantt') : 'Progress Gantt';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo gc_e($page_title); ?></title>

<style>
/* ── tokens ─────────────────────────────────────────────────────────── */
:root{
	--ink:#0f172a; --ink-2:#475569; --ink-3:#94a3b8;
	--line:#e2e8f0; --line-2:#eef2f7;
	--surface:#ffffff; --canvas:#f1f5f9;
	--brand:#1d4ed8; --brand-soft:#eff6ff;

	--plan:#cbd5f5; --plan-line:#a5b4fc;
	--done:#16a34a; --done-soft:#dcfce7;
	--late:#dc2626; --late-soft:#fee2e2;
	--warn:#d97706; --warn-soft:#fef3c7;
	--hold:#7c3aed; --hold-soft:#ede9fe;

	--tl:1600px;            /* timeline width — the zoom control */
	--row-h:34px;
	--sub-h:28px;
}

*{box-sizing:border-box}
html,body{margin:0;padding:0}
body{
	background:var(--canvas); color:var(--ink);
	font:13px/1.45 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
	-webkit-font-smoothing:antialiased;
}
a{color:var(--brand)}
.wrap{padding:16px 18px 26px;max-width:1920px;margin:0 auto}

/* ── header ─────────────────────────────────────────────────────────── */
.hero{
	background:linear-gradient(120deg,#1e3a8a 0%,#1d4ed8 55%,#0ea5e9 100%);
	color:#fff;border-radius:14px;padding:16px 20px;
	display:flex;flex-wrap:wrap;gap:16px;align-items:center;justify-content:space-between;
	box-shadow:0 10px 26px rgba(30,58,138,.22);
}
.hero h1{margin:0;font-size:19px;font-weight:700;letter-spacing:.2px}
.hero .sub{margin-top:3px;font-size:12px;opacity:.86}
.hero-facts{display:flex;flex-wrap:wrap;gap:18px}
.fact{min-width:104px}
.fact .k{font-size:10px;text-transform:uppercase;letter-spacing:.6px;opacity:.75}
.fact .v{font-size:13px;font-weight:600;margin-top:2px}
.hero-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}

.btn{
	appearance:none;border:1px solid var(--line);background:var(--surface);color:var(--ink);
	border-radius:8px;padding:6px 11px;font-size:12px;font-weight:600;cursor:pointer;
	display:inline-flex;align-items:center;gap:6px;text-decoration:none;line-height:1.3;
}
.btn:hover{background:var(--brand-soft);border-color:#bfdbfe;color:var(--brand)}
.btn-ghost{background:rgba(255,255,255,.14);border-color:rgba(255,255,255,.3);color:#fff}
.btn-ghost:hover{background:rgba(255,255,255,.26);color:#fff;border-color:rgba(255,255,255,.5)}
.btn[aria-pressed="true"]{background:var(--brand);border-color:var(--brand);color:#fff}
select.btn{padding-right:8px}

.menu{position:relative}
.menu-list{
	position:absolute;right:0;top:calc(100% + 6px);z-index:40;display:none;
	background:var(--surface);border:1px solid var(--line);border-radius:10px;
	box-shadow:0 16px 40px rgba(15,23,42,.22);min-width:266px;overflow:hidden;padding:4px;
}
.menu-list.on{display:block}
.menu-list a{
	display:block;padding:8px 10px;text-decoration:none;color:var(--ink);border-radius:7px;
}
.menu-list a:hover,.menu-list a:focus{background:var(--brand-soft);outline:none}
.menu-list b{display:block;font-size:12.5px;font-weight:600}
.menu-list span{display:block;font-size:11px;color:var(--ink-2);margin-top:1px}

/* ── KPIs ───────────────────────────────────────────────────────────── */
.kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(168px,1fr));gap:12px;margin:14px 0}
.kpi{background:var(--surface);border:1px solid var(--line);border-radius:12px;padding:12px 14px;position:relative;overflow:hidden}
.kpi .k{font-size:10px;font-weight:700;letter-spacing:.7px;text-transform:uppercase;color:var(--ink-3)}
.kpi .v{font-size:23px;font-weight:700;margin-top:5px;letter-spacing:-.4px}
.kpi .h{font-size:11px;color:var(--ink-2);margin-top:2px}
.kpi.good .v{color:var(--done)} .kpi.bad .v{color:var(--late)} .kpi.warn .v{color:var(--warn)}
.meter{height:5px;border-radius:3px;background:var(--line-2);margin-top:8px;overflow:hidden}
.meter i{display:block;height:100%;background:var(--done);border-radius:3px}

/* ── toolbar ────────────────────────────────────────────────────────── */
.seg{display:inline-flex;align-items:center;gap:2px;background:#f1f5f9;border:1px solid var(--line);border-radius:9px;padding:2px}
.seg-k{
	font-size:9.5px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;
	color:var(--ink-3);padding:0 6px 0 4px;
}
.seg-b{
	display:inline-block;padding:4px 10px;border-radius:7px;font-size:11.5px;font-weight:600;
	color:var(--ink-2);text-decoration:none;white-space:nowrap;
}
.seg-b:hover{background:#e2e8f0;color:var(--ink)}
.seg-b.on{background:var(--surface);color:var(--brand);box-shadow:0 1px 3px rgba(15,23,42,.12)}

.toolbar{
	background:var(--surface);border:1px solid var(--line);border-radius:12px;
	padding:10px 12px;display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:12px;
}
.search{position:relative;flex:1 1 220px;max-width:320px}
.search input{
	width:100%;border:1px solid var(--line);border-radius:8px;padding:7px 10px 7px 30px;
	font-size:12px;font-family:inherit;color:var(--ink);background:#fff;
}
.search input:focus{outline:none;border-color:var(--brand);box-shadow:0 0 0 3px rgba(29,78,216,.12)}
.search svg{position:absolute;left:9px;top:50%;transform:translateY(-50%);opacity:.45}
.chips{display:flex;gap:5px;flex-wrap:wrap}
.chip{
	border:1px solid var(--line);background:#fff;border-radius:999px;padding:5px 11px;
	font-size:11px;font-weight:600;cursor:pointer;color:var(--ink-2);font-family:inherit;
}
.chip[aria-pressed="true"]{background:var(--ink);border-color:var(--ink);color:#fff}
.chip .n{opacity:.65;margin-left:4px;font-weight:500}
.spacer{flex:1 1 auto}
.legend{display:flex;gap:14px;flex-wrap:wrap;align-items:center;font-size:11px;color:var(--ink-2)}
.legend b{font-weight:600}
.swatch{display:inline-block;width:22px;height:9px;border-radius:3px;vertical-align:middle;margin-right:5px}
.sw-plan{background:var(--plan);border:1px solid var(--plan-line)}
.sw-closed{background:var(--done-soft);border:1px solid #86efac}
.sw-done{background:var(--done)} .sw-late{background:var(--late)}
.sw-today{background:none;border-left:2px dashed var(--late);width:0;height:12px;margin-right:9px}

/* ── chart shell ────────────────────────────────────────────────────── */
.chart{background:var(--surface);border:1px solid var(--line);border-radius:12px;overflow:hidden}
.frame{overflow:auto;max-height:calc(100vh - 300px);min-height:340px;position:relative}
.grid{
	--cols:40px 174px minmax(180px,1fr) 78px 78px 84px 78px 96px;
	--pane:808px;
	width:calc(var(--pane) + var(--tl));min-width:100%;
}
/* Compact drops the three date columns and hands the space to the timeline. */
.grid.compact{--cols:40px 174px minmax(180px,1fr) 78px 96px;--pane:568px}
.grid.compact .c-date,
.grid.compact .h-date{display:none}

.head{position:sticky;top:0;z-index:6;display:flex;background:#f8fafc;border-bottom:1px solid var(--line)}
.head-pane{
	position:sticky;left:0;z-index:7;flex:0 0 var(--pane);width:var(--pane);
	background:#f8fafc;border-right:2px solid var(--line);
	display:grid;grid-template-columns:var(--cols);align-items:end;
}
.head-pane span{
	padding:8px 8px 9px;font-size:10px;font-weight:700;letter-spacing:.6px;
	text-transform:uppercase;color:var(--ink-3);border-left:1px solid var(--line-2);
}
.head-pane span:first-child{border-left:none}
.head-time{flex:0 0 var(--tl);width:var(--tl);position:relative}
.months{display:flex;height:22px}
.month{
	font-size:10px;font-weight:700;letter-spacing:.4px;text-transform:uppercase;color:var(--ink-2);
	border-left:1px solid var(--line);display:flex;align-items:center;justify-content:center;
	overflow:hidden;white-space:nowrap;padding:0 3px;
}
.month:first-child{border-left:none}
.weeks{display:flex;height:26px;border-top:1px solid var(--line-2)}
.wk{
	flex:1 1 0;min-width:0;border-left:1px solid var(--line-2);
	display:flex;flex-direction:column;align-items:center;justify-content:center;
	font-size:9px;color:var(--ink-3);line-height:1.15;overflow:hidden;
}
.wk b{font-weight:700;color:var(--ink-2);font-size:9px}
.wk.off{background:#f8fafc;color:#cbd5e1}
.wk.now{background:var(--late-soft)}
.wk.now b{color:var(--late)}
.grid.dense .wk span{display:none}
.grid.dense .weeks{height:20px}
.grid.ultra .wk:nth-child(2n) b{display:none}
.grid.micro .wk b{display:none}
.grid.micro .wk:nth-child(4n+1) b{display:block}
.grid.micro .weeks{height:17px}
.grid.nano .wk b,.grid.nano .wk span{display:none}
.grid.nano .weeks{height:8px}

/* ── body ───────────────────────────────────────────────────────────── */
.body{position:relative}
.canvas{position:absolute;top:0;bottom:0;left:var(--pane);width:var(--tl);z-index:0;pointer-events:none}
.band{position:absolute;top:0;bottom:0;background:rgba(148,163,184,.055)}
.gridline{position:absolute;top:0;bottom:0;width:1px;background:var(--line-2)}
.today{position:absolute;top:0;bottom:0;width:0;border-left:2px dashed var(--late);z-index:3}
.today::after{
	content:"TODAY";position:absolute;top:2px;left:4px;font-size:8px;font-weight:800;
	letter-spacing:.8px;color:var(--late);background:#fff;padding:1px 3px;border-radius:3px;
}
/* Near the end of the timeline the caption flips inward, so it neither
   clips nor stretches the scrollable width. */
.today.flip::after{left:auto;right:4px}

.row{display:flex;position:relative;z-index:1;border-bottom:1px solid var(--line-2)}
.row.hidden{display:none}
.row:hover .pane{background:var(--brand-soft)}
.row:hover .lane{background:rgba(29,78,216,.035)}
.row.sub{background:rgba(248,250,252,.55)}

.pane{
	position:sticky;left:0;z-index:2;flex:0 0 var(--pane);width:var(--pane);
	background:var(--surface);border-right:2px solid var(--line);
	display:grid;grid-template-columns:var(--cols);align-items:center;
}
.row.sub .pane{background:#fbfcfe}
.pane>div{
	padding:4px 8px;min-width:0;font-size:11.5px;
	overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
	border-left:1px solid var(--line-2);
}
.pane>div:first-child{border-left:none}
.c-no{font-weight:700;color:var(--ink-3);font-size:10.5px;text-align:center;padding-left:2px;padding-right:2px}
.c-dept{font-weight:600;display:flex;align-items:center;gap:6px}
.dot{width:9px;height:9px;border-radius:50%;flex:0 0 9px;border:1px solid rgba(15,23,42,.14)}
/* Flex so a long task name truncates but the ECN tag beside it never does --
   the tag is the part you are looking for. */
.c-task{font-weight:600;display:flex;align-items:center;gap:5px}
.c-task > span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;min-width:0}
/* The name keeps a floor so a row carrying two tags is still identifiable. */
.c-task > span:first-child{flex:1 1 auto;min-width:86px}
.c-task .ecn{flex:0 0 auto;margin-left:0}
.row.leaf .c-task{font-weight:500}
.c-task .owner{font-weight:400;color:var(--ink-3);font-size:10.5px;margin-left:5px}
.c-task .owner-list{font-weight:400;color:var(--ink-2);font-size:11px}
.tw-gap{display:inline-block;width:11px;flex:0 0 11px}
.row.sub .c-task{padding-left:22px;font-weight:500;color:var(--ink-2);position:relative}
.row.sub .c-task::before{
	content:"";position:absolute;left:10px;top:50%;width:6px;height:1px;background:var(--ink-3);
}
.c-date{color:var(--ink-2);font-variant-numeric:tabular-nums;text-align:center;font-size:11px}
.c-date.none{color:var(--ink-3)}
.c-delay{text-align:center}
.c-prog{display:flex;align-items:center;gap:6px}
.c-prog .meter{flex:1;margin:0}
.c-prog span{font-size:10.5px;font-weight:700;color:var(--ink-2);font-variant-numeric:tabular-nums;min-width:31px;text-align:right}

.pill{
	display:inline-block;padding:1px 7px;border-radius:999px;font-size:10px;font-weight:700;
	letter-spacing:.2px;white-space:nowrap;
}
.p-ok{background:var(--done-soft);color:#166534}
.p-late{background:var(--late-soft);color:#991b1b}
.p-warn{background:var(--warn-soft);color:#92400e}
.p-hold{background:var(--hold-soft);color:#5b21b6}
.p-idle{background:#f1f5f9;color:#64748b}

.tw{
	border:none;background:none;padding:0;cursor:pointer;color:var(--ink-3);
	display:inline-flex;align-items:center;font:inherit;flex:0 0 auto;
}
.tw:hover{color:var(--brand)}
.tw svg{transition:transform .14s ease}
.row.open .tw svg{transform:rotate(90deg)}
.iconbtn{
	border:none;background:none;cursor:pointer;padding:0 2px;color:var(--ink-3);
	display:inline-flex;align-items:center;flex:0 0 auto;
}
.iconbtn:hover{color:var(--brand)}
.iconbtn.alert{color:var(--late)}

/* ── lanes and bars ─────────────────────────────────────────────────── */
.lane{flex:0 0 var(--tl);width:var(--tl);position:relative;z-index:0;height:var(--row-h)}
.row.sub .lane{height:var(--sub-h)}

.bar{position:absolute;border-radius:5px;min-width:3px}
.b-plan{
	top:50%;transform:translateY(-50%);height:17px;
	background:var(--plan);border:1px solid var(--plan-line);
}
.b-plan.done{background:var(--done-soft);border-color:#86efac}
.row.sub .b-plan{height:13px;border-radius:4px}
.b-prog{
	top:50%;transform:translateY(-50%);height:17px;background:var(--done);
	border:1px solid rgba(0,0,0,.06);
}
.row.sub .b-prog{height:13px;border-radius:4px}
.b-prog.part{background:linear-gradient(180deg,#22c55e,#16a34a)}
.b-over{
	top:50%;transform:translateY(-50%);height:17px;border:1px solid rgba(0,0,0,.06);
	background:repeating-linear-gradient(135deg,#ef4444 0 5px,#dc2626 5px 10px);
}
.row.sub .b-over{height:13px;border-radius:4px}
.b-mark{
	position:absolute;top:50%;width:9px;height:9px;margin-left:-5px;
	transform:translateY(-50%) rotate(45deg);
	background:#fff;border:2px solid var(--done);border-radius:2px;z-index:2;
}
.row.sub .b-mark{width:7px;height:7px;margin-left:-4px}
/* ── change control chips ───────────────────────────────────────────── */
.ecn{
	display:inline-block;font-size:9px;font-weight:700;padding:1px 5px;border-radius:4px;
	background:var(--warn-soft);color:#92400e;border:1px solid #fcd34d;text-decoration:none;margin-left:4px;
}
.ecn.on{background:#f97316;border-color:#f97316;color:#fff}
.panel{
	background:#fffbeb;border:1px solid #fcd34d;border-radius:12px;padding:12px 14px;margin-bottom:12px;
	display:flex;flex-wrap:wrap;gap:12px 14px;align-items:center;
}
.panel.quiet{background:var(--surface);border-color:var(--line)}
.panel-head{min-width:260px}
.panel .t{font-size:12px;font-weight:700;color:#92400e;display:flex;align-items:center;gap:6px}
.panel .i{font-size:11.5px;color:#78350f;margin-top:3px}
.panel.quiet .t{color:var(--ink)}
.panel.quiet .i{color:var(--ink-2)}
.panel-actions{display:flex;gap:8px;flex-wrap:wrap}

.ecn-list{
	flex:1 1 100%;display:grid;gap:8px;
	grid-template-columns:repeat(auto-fill,minmax(232px,1fr));
}
.ecn-card{
	display:block;text-decoration:none;background:rgba(255,255,255,.75);
	border:1px solid #fcd34d;border-radius:9px;padding:8px 10px;color:#78350f;
}
.ecn-card:hover{background:#fff;border-color:#f59e0b}
.ecn-card.open{border-left:3px solid #f97316}
.ecn-card.more{display:flex;align-items:center;justify-content:center;font-weight:600;font-size:12px}
.ecn-top{display:flex;align-items:center;justify-content:space-between;gap:8px}
.ecn-top b{font-size:12px;font-weight:700;color:#92400e}
.ecn-state{
	font-size:9px;font-weight:800;letter-spacing:.4px;text-transform:uppercase;
	background:#fef3c7;border:1px solid #fcd34d;border-radius:999px;padding:1px 6px;color:#92400e;
}
.ecn-card.open .ecn-state{background:#f97316;border-color:#f97316;color:#fff}
.ecn-title{
	font-size:11.5px;font-weight:600;color:#7c2d12;margin-top:4px;
	overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
}
.ecn-meta{font-size:10.5px;color:#a16207;margin-top:2px}

/* Rows whose department is under a change request. */
.row.ecn-active .pane{box-shadow:inset 3px 0 0 #f97316}
.row.ecn-active .lane{background:rgba(249,115,22,.05)}
.row.ecn-done .pane{box-shadow:inset 3px 0 0 #fcd34d}

/* ── empty / modal ──────────────────────────────────────────────────── */
.empty{background:var(--surface);border:1px solid var(--line);border-radius:12px;padding:44px 20px;text-align:center}
.empty h2{margin:0 0 6px;font-size:16px}
.empty p{margin:0;color:var(--ink-2);font-size:13px}

.backdrop{
	position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:60;
	display:none;align-items:center;justify-content:center;padding:20px;
}
.backdrop.on{display:flex}
.modal{
	background:#fff;border-radius:14px;width:min(1080px,100%);max-height:86vh;
	display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(15,23,42,.3);overflow:hidden;
}
.modal header{
	padding:13px 16px;border-bottom:1px solid var(--line);display:flex;
	align-items:center;justify-content:space-between;gap:12px;
}
.modal h3{margin:0;font-size:14px;font-weight:700}
.modal .sub{font-size:11px;color:var(--ink-2);margin-top:2px}
.modal .content{padding:0;overflow:auto}
.tabs{display:flex;gap:6px;padding:10px 16px 0}
.tab{
	border:none;background:none;font:inherit;font-size:12px;font-weight:600;color:var(--ink-2);
	padding:6px 10px;border-radius:8px 8px 0 0;cursor:pointer;border-bottom:2px solid transparent;
}
.tab[aria-selected="true"]{color:var(--brand);border-bottom-color:var(--brand);background:var(--brand-soft)}
table.dt{width:100%;border-collapse:collapse;font-size:12px}
table.dt th{
	background:#f8fafc;text-align:left;padding:8px 10px;font-size:10px;text-transform:uppercase;
	letter-spacing:.5px;color:var(--ink-3);border-bottom:1px solid var(--line);position:sticky;top:0;
}
table.dt td{padding:7px 10px;border-bottom:1px solid var(--line-2);vertical-align:top}
table.dt tr:last-child td{border-bottom:none}
.muted{color:var(--ink-3)}
.pad{padding:22px 16px;text-align:center;color:var(--ink-2)}

/* ── print ──────────────────────────────────────────────────────────── */
@media print{
	@page{size:A3 landscape;margin:8mm}

	/* A Gantt with the fills stripped out is not a Gantt. */
	*{-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important}

	body{background:#fff}
	.toolbar,.hero-actions,.tw,.iconbtn,.backdrop{display:none !important}
	.wrap{padding:0;max-width:none}
	.hero{box-shadow:none;border-radius:0;color:#0f172a;background:none;border-bottom:2px solid #0f172a}
	.hero .fact .k,.hero .sub{opacity:1;color:#475569}
	.kpi{box-shadow:none;break-inside:avoid}
	.chart{border:none}

	/* Fit the sheet instead of inheriting the on-screen zoom. */
	.frame{max-height:none;overflow:visible}
	.grid{--pane:430px !important;--tl:1030px !important}
	.grid .c-date,.grid .h-date{display:none !important}
	.grid{--cols:38px 122px minmax(150px,1fr) 74px 88px !important}
	.pane>div,.head-pane span{padding-left:5px;padding-right:5px}
	.wk span{display:none !important}
	.weeks{height:18px}

	.head{position:static}
	.pane,.head-pane{position:static}
	.row{break-inside:avoid}
	.body{break-inside:auto}
}

@media(max-width:900px){
	.wrap{padding:10px}
	:root{--pane:560px}
	.frame{max-height:none}
}
</style>
</head>
<body>

<div class="wrap">

<?php if (!$found) { ?>

	<div class="hero" style="margin-bottom:14px">
		<div>
			<h1>DF Progress Gantt</h1>
			<div class="sub">Department and week-wise plan versus actual.</div>
		</div>
	</div>

	<div class="empty">
		<h2>Nothing to plot</h2>
		<p><?php echo gc_e(isset($message) && $message !== '' ? $message : 'This DF has no schedule yet.'); ?></p>
	</div>

<?php } else {

	$status_counts = array('overdue' => 0, 'running' => 0, 'done' => 0, 'upcoming' => 0, 'hold' => 0);

	foreach ($rows as $r) {
		$key = ($r['status'] === 'late') ? 'done' : $r['status'];

		if (isset($status_counts[$key])) {
			$status_counts[$key]++;
		}
	}

	$status_pill = array(
		'done'     => 'p-ok',
		'late'     => 'p-warn',
		'overdue'  => 'p-late',
		'running'  => 'p-warn',
		'hold'     => 'p-hold',
		'upcoming' => 'p-idle',
	);
?>

	<div class="hero">
		<div>
			<h1>DF <?php echo gc_e($header['df_no']); ?> — Progress Gantt</h1>
			<div class="sub">
				<?php echo gc_e($header['company'] !== '' ? $header['company'] : 'Customer not recorded'); ?>
				· <?php echo count($rows); ?> <?php echo gc_e($stats['unit_noun']); ?><?php echo count($rows) === 1 ? '' : 's'; ?>,
				<?php echo (int) $stats['tasks']; ?> tasks, <?php echo gc_e($timeline['scale']); ?> columns<?php
					if ($options['department'] > 0) {
						foreach ($departments as $dept) {
							if ((int) $dept['department_id'] === $options['department']) {
								echo ' · ' . gc_e($dept['department']) . ' only';
							}
						}
					}
				?>
			</div>
		</div>

		<div class="hero-facts">
			<div class="fact"><div class="k">PO No</div><div class="v"><?php echo gc_e($header['po_no'] !== '' ? $header['po_no'] : '—'); ?></div></div>
			<div class="fact"><div class="k">PO Date</div><div class="v"><?php echo gc_e($header['po_date']); ?></div></div>
			<div class="fact"><div class="k">DF Released</div><div class="v"><?php echo gc_e($header['df_date']); ?></div></div>
			<div class="fact"><div class="k">Marketing</div><div class="v"><?php echo gc_e($header['marketing'] !== '' ? $header['marketing'] : '—'); ?></div></div>
		</div>

		<div class="hero-actions">
<?php
/* ===== CHAT MODULE — "Discuss this DF" =============================
   Opens (or creates) the chat group for this DF, seeded with whoever
   raised it and everyone holding a task on it. See Chat::df().

   Gated on chat_nav_visible(), so it is invisible until
   CHAT_MODULE_VISIBLE is TRUE — the same switch as every other chat
   entry point.

   Also gated on chat_can_manage_df_groups(): this button CREATES the
   group when there is not one yet, and that is marketing's and
   administrators' to do. Chat::df() re-checks on the server, including
   that a marketing user only touches a DF they released; the button
   merely hides itself to match.

   Members of an existing DF group still reach it from their chat
   conversation list, so hiding this closes creation, not access. */
$this->load->helper('chat_access');
if (chat_nav_visible($this) && chat_can_manage_df_groups($this) && $df_id > 0):
?>
			<a class="btn btn-ghost" href="<?php echo page_url; ?>chat/df/<?php echo $df_id; ?>"
			   title="Open this DF's chat group, creating it if it does not exist yet">
				<svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
				DF Group
			</a>
<?php endif; ?>
<!-- ===== END CHAT MODULE ===== -->
			<select class="btn" id="deptSwitch" title="Show one department only">
				<option value="<?php echo gc_e(gc_url($df_id, $options, array('dept' => 0))); ?>"<?php echo $options['department'] === 0 ? ' selected' : ''; ?>>
					All departments
				</option>
				<?php foreach ($departments as $dept) { ?>
					<option value="<?php echo gc_e(gc_url($df_id, $options, array('dept' => $dept['department_id']))); ?>"<?php echo $options['department'] === (int) $dept['department_id'] ? ' selected' : ''; ?>>
						<?php echo gc_e($dept['department']); ?>
					</option>
				<?php } ?>
			</select>
			<div class="menu">
				<button type="button" class="btn btn-ghost" id="exportBtn" aria-expanded="false" aria-haspopup="true">
					<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M12 3v12"/><path d="M7 10l5 5 5-5"/><path d="M4 20h16"/>
					</svg>
					Export
					<svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M6 9l6 7 6-7z"/></svg>
				</button>

				<div class="menu-list" id="exportMenu" role="menu">
					<a role="menuitem" href="<?php echo gc_e(gc_export_url($df_id, $options, 'xlsx')); ?>">
						<b>Excel workbook</b>
						<span>Chart painted across week columns, panes frozen</span>
					</a>
					<a role="menuitem" href="<?php echo gc_e(gc_export_url($df_id, $options, 'pdf')); ?>">
						<b>PDF</b>
						<span>Landscape A4, chart and summary, ready to circulate</span>
					</a>
					<a role="menuitem" href="<?php echo gc_e(gc_export_url($df_id, $options, 'csv')); ?>">
						<b>CSV</b>
						<span>Plain data for your own analysis</span>
					</a>
				</div>
			</div>

			<button type="button" class="btn btn-ghost" onclick="window.print()">Print</button>
		</div>
	</div>

	<div class="kpis">
		<div class="kpi">
			<div class="k">Overall progress</div>
			<div class="v"><?php echo gc_e($stats['progress']); ?>%</div>
			<div class="h"><?php echo (int) $stats['tasks_done']; ?> of <?php echo (int) $stats['tasks']; ?> tasks closed</div>
			<div class="meter"><i style="width:<?php echo (float) $stats['progress']; ?>%"></i></div>
		</div>

		<div class="kpi good">
			<div class="k"><?php echo gc_e($stats['unit_label']); ?></div>
			<div class="v"><?php echo (int) $stats['units_done']; ?><span style="font-size:14px;color:var(--ink-3)">/<?php echo (int) $stats['units']; ?></span></div>
			<div class="h"><?php echo (int) ($stats['units'] - $stats['units_done']); ?> still open</div>
		</div>

		<div class="kpi <?php echo $stats['units_late'] > 0 ? 'bad' : 'good'; ?>">
			<div class="k">Running late</div>
			<div class="v"><?php echo (int) $stats['units_late']; ?></div>
			<div class="h"><?php echo (int) $stats['tasks_overdue']; ?> overdue task<?php echo $stats['tasks_overdue'] === 1 ? '' : 's'; ?></div>
		</div>

		<div class="kpi">
			<div class="k">Planned finish</div>
			<div class="v" style="font-size:17px"><?php echo gc_e(gc_day($stats['planned_finish'])); ?></div>
			<div class="h">Latest scheduled end date</div>
		</div>

		<div class="kpi <?php echo $stats['slippage_days'] > 0 ? 'bad' : 'good'; ?>">
			<div class="k">Projected finish</div>
			<div class="v" style="font-size:17px"><?php echo gc_e(gc_day($stats['projected_finish'])); ?></div>
			<div class="h">
				<?php echo $stats['slippage_days'] > 0
					? gc_e($stats['slippage_days'] . ' day' . ($stats['slippage_days'] > 1 ? 's' : '') . ' behind plan')
					: 'On or ahead of plan'; ?>
			</div>
		</div>

		<div class="kpi <?php echo $stats['open_tickets'] > 0 ? 'warn' : ''; ?>">
			<div class="k">Open tickets</div>
			<div class="v"><?php echo (int) $stats['open_tickets']; ?></div>
			<div class="h">Raised against these departments</div>
		</div>
	</div>

	<?php
	// The change-control block is ALWAYS on the page, even with nothing linked.
	// The old chart showed the "Raise ECN / IOM" button either way, and it is
	// the entry point people come to this screen for -- hiding it on a DF with
	// no rework yet is exactly when it is most needed.
	$change_count    = count($changes['summary']);
	$df_has_changes  = ($change_count > 0);
	?>
	<div class="panel<?php echo $df_has_changes ? '' : ' quiet'; ?>">
		<div class="panel-head">
			<div class="t">
				<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M16 3h5v5"/><path d="M4 20L21 3"/><path d="M21 16v5h-5"/><path d="M15 15l6 6"/><path d="M4 4l5 5"/>
				</svg>
				Rework / ECN / IOM
			</div>
			<div class="i">
				<?php if ($df_has_changes) { ?>
					<?php echo $change_count; ?> linked request<?php echo $change_count === 1 ? '' : 's'; ?>,
					<b><?php echo (int) $changes['active']; ?> still active</b>
					across <?php echo (int) $changes['departments']; ?> department<?php echo (int) $changes['departments'] === 1 ? '' : 's'; ?>.
					Affected rows are tinted and tagged in the chart below.
				<?php } else { ?>
					No rework, ECN or IOM has been raised against this DF yet.
				<?php } ?>
			</div>
		</div>

		<div class="spacer"></div>

		<div class="panel-actions">
			<a class="btn" href="<?php echo page_url; ?>Df_change_control/create/<?php echo $df_id; ?>">Raise ECN / IOM</a>
			<?php if ($df_has_changes) { ?>
				<a class="btn" href="<?php echo page_url; ?>Df_change_control" target="_blank" rel="noopener">Open register</a>
			<?php } ?>
		</div>

		<?php if ($df_has_changes) { ?>
			<div class="ecn-list">
				<?php foreach (array_slice($changes['summary'], 0, 6) as $change) {
					$status  = isset($change['status']) ? (string) $change['status'] : '';
					$is_open = ($status !== 'COMPLETED');
				?>
					<a class="ecn-card<?php echo $is_open ? ' open' : ''; ?>"
					   href="<?php echo page_url; ?>Df_change_control/view/<?php echo (int) $change['id']; ?>"
					   target="_blank" rel="noopener">
						<div class="ecn-top">
							<b><?php echo gc_e($change['change_no']); ?></b>
							<span class="ecn-state"><?php echo gc_e(str_replace('_', ' ', $status)); ?></span>
						</div>
						<div class="ecn-title"><?php echo gc_e($change['title']); ?></div>
						<div class="ecn-meta">
							<?php echo gc_e($change['request_type']); ?>
							· <?php echo gc_e(str_replace('_', ' ', $change['change_category'])); ?>
							· <?php echo (int) $change['active_departments']; ?>/<?php echo (int) $change['total_departments']; ?> departments active
						</div>
					</a>
				<?php } ?>

				<?php if ($change_count > 6) { ?>
					<a class="ecn-card more" href="<?php echo page_url; ?>Df_change_control" target="_blank" rel="noopener">
						+<?php echo $change_count - 6; ?> more
					</a>
				<?php } ?>
			</div>
		<?php } ?>
	</div>

	<div class="toolbar">
		<div class="seg" role="group" aria-label="Row grouping">
			<span class="seg-k">Rows</span>
			<?php foreach ($group_labels as $key => $label) { ?>
				<a class="seg-b<?php echo $options['group'] === $key ? ' on' : ''; ?>"
				   href="<?php echo gc_e(gc_url($df_id, $options, array('group' => $key))); ?>"
				   title="<?php echo gc_e($label[1]); ?>"><?php echo gc_e($label[0]); ?></a>
			<?php } ?>
		</div>

		<div class="seg" role="group" aria-label="Column scale">
			<span class="seg-k">Scale</span>
			<?php foreach ($scale_labels as $key => $label) { ?>
				<a class="seg-b<?php echo $options['scale'] === $key ? ' on' : ''; ?>"
				   href="<?php echo gc_e(gc_url($df_id, $options, array('scale' => $key))); ?>"
				   title="<?php echo gc_e($label[1]); ?>"><?php echo gc_e($label[0]); ?></a>
			<?php } ?>
		</div>

		<div class="search">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
				<circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>
			</svg>
			<input type="search" id="q" placeholder="Search department, task or owner…" autocomplete="off">
		</div>

		<div class="chips" id="statusChips">
			<button type="button" class="chip" data-status="all" aria-pressed="true">All<span class="n"><?php echo count($rows); ?></span></button>
			<button type="button" class="chip" data-status="overdue" aria-pressed="false">Overdue<span class="n"><?php echo (int) $status_counts['overdue']; ?></span></button>
			<button type="button" class="chip" data-status="running" aria-pressed="false">In progress<span class="n"><?php echo (int) $status_counts['running']; ?></span></button>
			<button type="button" class="chip" data-status="done" aria-pressed="false">Completed<span class="n"><?php echo (int) $status_counts['done']; ?></span></button>
			<button type="button" class="chip" data-status="upcoming" aria-pressed="false">Not started<span class="n"><?php echo (int) $status_counts['upcoming']; ?></span></button>
		</div>

		<button type="button" class="btn" id="toggleAll" aria-pressed="false">Expand all</button>
		<button type="button" class="btn" id="compact" aria-pressed="false">Hide dates</button>

		<div class="spacer"></div>

		<div class="legend">
			<span><span class="swatch sw-plan"></span><b>Planned</b></span>
			<span><span class="swatch sw-closed"></span><b>Closed</b></span>
			<span><span class="swatch sw-done"></span><b>Work done</b></span>
			<span><span class="swatch sw-late"></span><b>Overrun</b></span>
			<span><span class="swatch sw-today"></span><b>Today</b></span>
		</div>

		<div style="display:flex;gap:4px;align-items:center">
			<button type="button" class="btn" id="zoomOut" title="Zoom out">−</button>
			<button type="button" class="btn" id="zoomIn" title="Zoom in">+</button>
			<button type="button" class="btn" id="zoomFit" title="Fit to screen">Fit</button>
		</div>
	</div>

	<div class="chart">
		<div class="frame" id="frame">
			<div class="grid" id="grid">

				<div class="head">
					<div class="head-pane">
						<span class="c-no">#</span>
						<span>Department / Owner</span>
						<span>Milestone &amp; task</span>
						<span class="h-date" style="text-align:center">Start</span>
						<span class="h-date" style="text-align:center">Due</span>
						<span class="h-date" style="text-align:center">Actual</span>
						<span style="text-align:center">Delay</span>
						<span>Progress</span>
					</div>

					<div class="head-time">
						<div class="months">
							<?php foreach ($timeline['months'] as $month) { ?>
								<div class="month" style="flex:<?php echo (int) $month['span']; ?> 1 0"><?php echo gc_e($month['label']); ?></div>
							<?php } ?>
						</div>
						<div class="weeks">
							<?php foreach ($timeline['columns'] as $col) {
								$col_class = 'wk';
								if (!empty($col['is_current'])) { $col_class .= ' now'; }
								if (!empty($col['is_weekend'])) { $col_class .= ' off'; }
								$col_title = ($col['from'] === $col['to'])
									? gc_day($col['from'])
									: gc_day($col['from']) . ' to ' . gc_day($col['to']);
							?>
								<div class="<?php echo $col_class; ?>" title="<?php echo gc_e($col_title); ?>">
									<b><?php echo gc_e($col['label']); ?></b>
									<span><?php echo gc_e($col['sub']); ?></span>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>

				<div class="body">
					<div class="canvas">
						<?php
						$band_left = 0.0;
						$week_pct  = 100 / max(1, count($timeline['columns']));
						$band_i    = 0;

						foreach ($timeline['months'] as $month) {
							$band_width = $month['span'] * $week_pct;

							if ($band_i % 2 === 1) {
								echo '<div class="band" style="left:' . round($band_left, 4) . '%;width:' . round($band_width, 4) . '%"></div>';
							}

							$band_left += $band_width;
							$band_i++;
						}

						for ($w = 1; $w < count($timeline['columns']); $w++) {
							echo '<div class="gridline" style="left:' . round($w * $week_pct, 4) . '%"></div>';
						}

						if ($timeline['today_pct'] !== NULL) {
							$flip = ($timeline['today_pct'] > 85) ? ' flip' : '';
							echo '<div class="today' . $flip . '" style="left:' . (float) $timeline['today_pct'] . '%"></div>';
						}
						?>
					</div>

					<?php
					foreach ($rows as $row) {

						$dept_changes    = isset($changes['by_department'][$row['department_id']]) ? $changes['by_department'][$row['department_id']] : array();
						$row_has_changes = !empty($dept_changes['has_requests']);

						// Tinted so an impacted department is findable by eye,
						// stronger while the change is still being worked.
						$change_class = '';
						if (!empty($dept_changes['has_active_requests'])) {
							$change_class = ' ecn-active';
						} elseif ($row_has_changes) {
							$change_class = ' ecn-done';
						}

						$search_key = strtolower($row['department'] . ' ' . $row['name']);

						foreach ($row['children'] as $child) {
							$search_key .= ' ' . strtolower($child['name'] . ' ' . $child['owner']);
						}

						$filter_status = ($row['status'] === 'late') ? 'done' : $row['status'];
						$pill_class    = isset($status_pill[$row['status']]) ? $status_pill[$row['status']] : 'p-idle';
						$tickets       = $row['tickets'];
					?>
					<div class="row group<?php echo empty($row['children']) ? ' leaf' : ''; ?><?php echo $change_class; ?>" id="g<?php echo gc_e($row['row_id']); ?>"
					     data-group="<?php echo gc_e($row['row_id']); ?>"
					     data-status="<?php echo gc_e($filter_status); ?>"
					     data-search="<?php echo gc_e($search_key); ?>">

						<div class="pane">
							<div class="c-no"><?php echo (int) $row['no']; ?></div>

							<div class="c-dept">
								<?php if (!empty($row['children'])) { ?>
									<button type="button" class="tw" data-toggle="<?php echo gc_e($row['row_id']); ?>"
									        title="Show the <?php echo (int) $row['total_count']; ?> task<?php echo (int) $row['total_count'] === 1 ? '' : 's'; ?> behind this row"
									        aria-expanded="false">
										<svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5l10 7-10 7z"/></svg>
									</button>
								<?php } else { ?>
									<span class="tw-gap"></span>
								<?php } ?>
								<span class="dot" style="background:<?php echo gc_e($row['color']); ?>"></span>
								<span style="overflow:hidden;text-overflow:ellipsis"><?php echo gc_e($row['department']); ?></span>
								<button type="button" class="iconbtn"
								        data-tasks="<?php echo (int) $row['department_id']; ?>"
								        data-label="<?php echo gc_e($row['department']); ?>"
								        title="Every task booked to <?php echo gc_e($row['department']); ?> on this DF, with remarks">
									<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
										<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
									</svg>
								</button>
								<?php if ($tickets['total'] > 0) { ?>
									<button type="button" class="iconbtn<?php echo $tickets['open'] > 0 ? ' alert' : ''; ?>"
									        data-tickets="<?php echo (int) $row['department_id']; ?>"
									        data-label="<?php echo gc_e($row['department']); ?>"
									        title="<?php echo (int) $tickets['total']; ?> ticket(s), <?php echo (int) $tickets['open']; ?> open">
										<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<path d="M3 9V6a1 1 0 011-1h16a1 1 0 011 1v3a3 3 0 000 6v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3a3 3 0 000-6z"/>
										</svg>
									</button>
								<?php } ?>
							</div>

							<?php
							// A department row already shows its name one column to the
							// left, so this column carries the people instead.
							$is_department_row = (isset($row['level']) && $row['level'] === 'department');
							$row_caption = $is_department_row
								? ($row['owner'] !== '' ? $row['owner'] : 'No one assigned')
								: $row['name'];
							?>
							<div class="c-task" title="<?php echo gc_e($row['name'] . ($row['owner'] !== '' ? ' — ' . $row['owner'] : '')); ?>">
								<span<?php echo $is_department_row ? ' class="owner-list"' : ''; ?>><?php echo gc_e($row_caption); ?></span>
								<?php if (!$is_department_row && empty($row['children']) && $row['owner'] !== '') { ?>
									<span class="owner"><?php echo gc_e($row['owner']); ?></span>
								<?php } ?>
								<?php if ($row_has_changes) {
									// A grouping row can carry a few tags. A task row cannot:
									// every task in the department would repeat the same ones,
									// and the row tint already says it is impacted.
									$chip_limit = empty($row['children']) ? 1 : 2;

									foreach (array_slice($dept_changes['items'], 0, $chip_limit) as $item) { ?>
										<a class="ecn<?php echo (isset($item['department_status']) && $item['department_status'] !== 'COMPLETED') ? ' on' : ''; ?>"
										   href="<?php echo page_url; ?>Df_change_control/view/<?php echo (int) $item['change_id']; ?>"
										   target="_blank" rel="noopener"
										   title="Linked change request"><?php echo gc_e($item['change_no']); ?></a>
								<?php }
								} ?>
							</div>

							<div class="c-date<?php echo $row['planned_start'] === '' ? ' none' : ''; ?>"><?php echo gc_e(gc_day($row['planned_start'])); ?></div>
							<div class="c-date<?php echo $row['planned_end'] === '' ? ' none' : ''; ?>"><?php echo gc_e(gc_day($row['planned_end'])); ?></div>
							<div class="c-date<?php echo $row['completed_on'] === '' ? ' none' : ''; ?>"><?php echo gc_e(gc_day($row['completed_on'])); ?></div>

							<div class="c-delay">
								<span class="pill <?php echo gc_e($pill_class); ?>" title="<?php echo gc_e(gc_status_label($row['status'])); ?>">
									<?php echo gc_e($row['delay_days'] > 0 ? gc_delay_label($row['delay_days']) : gc_status_label($row['status'])); ?>
								</span>
							</div>

							<div class="c-prog">
								<div class="meter"><i style="width:<?php echo (float) $row['progress']; ?>%"></i></div>
								<span><?php echo gc_e((int) round($row['progress'])); ?>%</span>
							</div>
						</div>

						<div class="lane">
							<?php
							$bars = $row['bars'];

							if ($bars['plan'] !== NULL) { ?>
								<div class="bar b-plan<?php echo $row['is_done'] ? ' done' : ''; ?>" style="left:<?php echo (float) $bars['plan']['left']; ?>%;width:<?php echo (float) $bars['plan']['width']; ?>%"
								     title="Planned <?php echo gc_e(gc_day($row['planned_start']) . ' → ' . gc_day($row['planned_end'])); ?> (<?php echo (int) $bars['plan']['days']; ?> days)"></div>
							<?php }

							if ($bars['progress'] !== NULL) { ?>
								<div class="bar b-prog<?php echo $row['progress'] < 100 ? ' part' : ''; ?>"
								     style="left:<?php echo (float) $bars['progress']['left']; ?>%;width:<?php echo (float) $bars['progress']['width']; ?>%"
								     title="<?php echo (int) $row['done_count']; ?> of <?php echo (int) $row['total_count']; ?> tasks completed (<?php echo gc_e($row['progress']); ?>%)"></div>
							<?php }

							if ($bars['overrun'] !== NULL) { ?>
								<div class="bar b-over" style="left:<?php echo (float) $bars['overrun']['left']; ?>%;width:<?php echo (float) $bars['overrun']['width']; ?>%"
								     title="<?php echo (int) $bars['overrun']['days']; ?> days past the planned end"></div>
							<?php }

							if ($bars['marker'] !== NULL) { ?>
								<div class="b-mark" style="left:<?php echo (float) $bars['marker']['left']; ?>%"
								     title="Actually finished <?php echo gc_e(gc_day($row['completed_on'])); ?>"></div>
							<?php }
							?>
						</div>
					</div>

					<?php
					$sub_no = 0;

					foreach ($row['children'] as $child) {
						$sub_no++;
						$child_status = ($child['status'] === 'late') ? 'done' : $child['status'];
						$child_pill   = isset($status_pill[$child['status']]) ? $status_pill[$child['status']] : 'p-idle';
						$cbars        = $child['bars'];
					?>
						<div class="row sub hidden<?php echo $change_class; ?>" data-child="<?php echo gc_e($row['row_id']); ?>"
						     data-status="<?php echo gc_e($child_status); ?>"
						     data-search="<?php echo gc_e(strtolower($child['name'] . ' ' . $child['owner'] . ' ' . $row['department'])); ?>">

							<div class="pane">
								<div class="c-no"><?php echo (int) $row['no']; ?>.<?php echo $sub_no; ?></div>
								<div class="c-dept" style="font-weight:500;color:var(--ink-2)" title="<?php echo gc_e($child['owner']); ?>">
									<span style="padding-left:17px;overflow:hidden;text-overflow:ellipsis"><?php echo gc_e($child['owner'] !== '' ? $child['owner'] : 'Unassigned'); ?></span>
								</div>
								<div class="c-task" title="<?php echo gc_e($child['name'] . ($child['remarks'] !== '' ? ' — ' . $child['remarks'] : '')); ?>"><?php echo gc_e($child['name']); ?></div>
								<div class="c-date<?php echo $child['planned_start'] === '' ? ' none' : ''; ?>"><?php echo gc_e(gc_day($child['planned_start'])); ?></div>
								<div class="c-date<?php echo $child['planned_end'] === '' ? ' none' : ''; ?>"><?php echo gc_e(gc_day($child['planned_end'])); ?></div>
								<div class="c-date<?php echo $child['completed_on'] === '' ? ' none' : ''; ?>"><?php echo gc_e(gc_day($child['completed_on'])); ?></div>
								<div class="c-delay">
									<span class="pill <?php echo gc_e($child_pill); ?>">
										<?php echo gc_e($child['delay_days'] > 0 ? gc_delay_label($child['delay_days']) : gc_status_label($child['status'])); ?>
									</span>
								</div>
								<div class="c-prog">
									<div class="meter"><i style="width:<?php echo (float) $child['progress']; ?>%"></i></div>
									<span><?php echo gc_e((int) round($child['progress'])); ?>%</span>
								</div>
							</div>

							<div class="lane">
								<?php if ($cbars['plan'] !== NULL) { ?>
									<div class="bar b-plan<?php echo $child['is_done'] ? ' done' : ''; ?>" style="left:<?php echo (float) $cbars['plan']['left']; ?>%;width:<?php echo (float) $cbars['plan']['width']; ?>%"
									     title="Planned <?php echo gc_e(gc_day($child['planned_start']) . ' → ' . gc_day($child['planned_end'])); ?>"></div>
								<?php }

								if ($cbars['progress'] !== NULL) { ?>
									<div class="bar b-prog" style="left:<?php echo (float) $cbars['progress']['left']; ?>%;width:<?php echo (float) $cbars['progress']['width']; ?>%"
									     title="Completed"></div>
								<?php }

								if ($cbars['overrun'] !== NULL) { ?>
									<div class="bar b-over" style="left:<?php echo (float) $cbars['overrun']['left']; ?>%;width:<?php echo (float) $cbars['overrun']['width']; ?>%"
									     title="<?php echo (int) $cbars['overrun']['days']; ?> days late"></div>
								<?php }

								if ($cbars['marker'] !== NULL) { ?>
									<div class="b-mark" style="left:<?php echo (float) $cbars['marker']['left']; ?>%"
									     title="Finished <?php echo gc_e(gc_day($child['completed_on'])); ?>"></div>
								<?php } ?>
							</div>
						</div>
					<?php }
					} ?>

					<div id="noMatch" style="display:none;position:sticky;left:0;width:min(100%,860px);padding:26px 16px;text-align:center;color:var(--ink-2)">
						No milestone or task matches the current filter.
					</div>
				</div>

			</div>
		</div>
	</div>

	<?php if (!empty($stats['critical'])) { ?>
		<div class="panel" style="background:#fef2f2;border-color:#fecaca;margin-top:12px">
			<div class="t" style="color:#991b1b">Biggest slippage right now</div>
			<div class="i" style="color:#7f1d1d">
				<?php
				$bits = array();

				foreach ($stats['critical'] as $c) {
					$bits[] = gc_e($c['department'] . ' — ' . $c['name']) . ' <b>' . (int) $c['delay_days'] . 'd</b>';
				}

				echo implode(' &nbsp;·&nbsp; ', $bits);
				?>
			</div>
		</div>
	<?php } ?>

	<div class="backdrop" id="backdrop">
		<div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
			<header>
				<div>
					<h3 id="modalTitle">Details</h3>
					<div class="sub" id="modalSub"></div>
				</div>
				<button type="button" class="btn" id="modalClose">Close</button>
			</header>
			<div class="content" id="modalBody"><div class="pad">Loading…</div></div>
		</div>
	</div>

<script>
(function () {
	'use strict';

	var DF_ID   = <?php echo (int) $df_id; ?>;
	var BASE    = <?php echo json_encode(page_url); ?>;
	var WEEKS   = <?php echo (int) count($timeline['columns']); ?>;
	var SCALE   = <?php echo json_encode($timeline['scale']); ?>;
	var grid    = document.getElementById('grid');
	var frame   = document.getElementById('frame');

	// ── zoom ────────────────────────────────────────────────────────────
	// Below 26px a week column cannot carry its date caption, below 24px it
	// cannot carry a week number every column, and below 15px it can only
	// carry every fourth. The header sheds labels rather than overlap them.
	// A day column and a week column are not the same size, so the remembered
	// zoom is kept per scale -- reusing one number made the day view open
	// seven times too wide.
	var ZOOM_KEY = 'gantt.colwidth.' + SCALE;
	var MIN_WEEK = (SCALE === 'day') ? 2 : 11;
	var MAX_WEEK = 140;
	var perWeek  = parseFloat(localStorage.getItem(ZOOM_KEY)) || 0;

	function applyZoom(px) {
		px = Math.max(MIN_WEEK, Math.min(MAX_WEEK, px));
		perWeek = px;
		grid.style.setProperty('--tl', Math.round(px * WEEKS) + 'px');
		grid.classList.toggle('dense', px < 26);
		grid.classList.toggle('ultra', px < 24);
		grid.classList.toggle('micro', px < 15);
		grid.classList.toggle('nano', px < 9);
		localStorage.setItem(ZOOM_KEY, String(px));
	}

	function paneWidth() {
		return parseFloat(getComputedStyle(grid).getPropertyValue('--pane')) || 808;
	}

	function fitZoom() {
		applyZoom((frame.clientWidth - paneWidth() - 4) / WEEKS);
		localStorage.removeItem(ZOOM_KEY);
	}

	document.getElementById('zoomIn').onclick  = function () { applyZoom(perWeek * 1.3); };
	document.getElementById('zoomOut').onclick = function () { applyZoom(perWeek / 1.3); };
	document.getElementById('zoomFit').onclick = fitZoom;

	// ── compact columns ─────────────────────────────────────────────────
	// Compact is the better default: on a 1280px screen the three date columns
	// cost more timeline than they are worth, and every date is on a tooltip.
	var compact = document.getElementById('compact');

	function setCompact(on, remember) {
		grid.classList.toggle('compact', on);
		compact.setAttribute('aria-pressed', on ? 'true' : 'false');
		compact.textContent = on ? 'Show dates' : 'Hide dates';
		if (remember !== false) { localStorage.setItem('gantt.compact', on ? '1' : '0'); }
	}

	var storedCompact = localStorage.getItem('gantt.compact');
	setCompact(storedCompact === null ? true : storedCompact === '1', storedCompact !== null);

	compact.onclick = function () {
		setCompact(compact.getAttribute('aria-pressed') !== 'true');
		if (!localStorage.getItem(ZOOM_KEY)) { fitZoom(); }
	};

	// Column width settles first, then the timeline is sized against it.
	// First visit shows the whole project; after that the saved zoom wins.
	if (perWeek > 0) { applyZoom(perWeek); } else { fitZoom(); }

	// In fit mode the chart tracks the window; a pinned zoom does not.
	var resizing;
	window.addEventListener('resize', function () {
		if (localStorage.getItem(ZOOM_KEY)) { return; }
		clearTimeout(resizing);
		resizing = setTimeout(fitZoom, 150);
	});

	// ── expand / collapse ───────────────────────────────────────────────
	var groups = Array.prototype.slice.call(document.querySelectorAll('.row.group'));
	var subs   = Array.prototype.slice.call(document.querySelectorAll('.row.sub'));

	function childrenOf(id) {
		return subs.filter(function (r) { return r.getAttribute('data-child') === String(id); });
	}

	function setOpen(row, open) {
		row.classList.toggle('open', open);
		var tw = row.querySelector('.tw');
		if (tw) { tw.setAttribute('aria-expanded', open ? 'true' : 'false'); }
		applyFilter();
	}

	document.addEventListener('click', function (e) {
		var tw = e.target.closest('.tw');
		if (tw) {
			var row = tw.closest('.row.group');
			setOpen(row, !row.classList.contains('open'));
			return;
		}

		var tk = e.target.closest('[data-tickets]');
		if (tk) {
			openModal('tickets', tk.getAttribute('data-tickets'), tk.getAttribute('data-label'));
			return;
		}

		var td = e.target.closest('[data-tasks]');
		if (td) {
			openModal('tasks', td.getAttribute('data-tasks'), td.getAttribute('data-label'));
			return;
		}
	});

	var toggleAll = document.getElementById('toggleAll');
	toggleAll.onclick = function () {
		var open = toggleAll.getAttribute('aria-pressed') !== 'true';
		toggleAll.setAttribute('aria-pressed', open ? 'true' : 'false');
		toggleAll.textContent = open ? 'Collapse all' : 'Expand all';
		groups.forEach(function (g) { setOpen(g, open); });
	};

	// ── filtering ───────────────────────────────────────────────────────
	var q = document.getElementById('q');
	var activeStatus = 'all';
	var noMatch = document.getElementById('noMatch');

	document.getElementById('statusChips').addEventListener('click', function (e) {
		var chip = e.target.closest('.chip');
		if (!chip) { return; }
		activeStatus = chip.getAttribute('data-status');
		Array.prototype.forEach.call(this.querySelectorAll('.chip'), function (c) {
			c.setAttribute('aria-pressed', c === chip ? 'true' : 'false');
		});
		applyFilter();
	});

	var typing;
	q.addEventListener('input', function () {
		clearTimeout(typing);
		typing = setTimeout(applyFilter, 120);
	});

	function applyFilter() {
		var term = q.value.trim().toLowerCase();
		var shown = 0;

		groups.forEach(function (g) {
			var kids = childrenOf(g.getAttribute('data-group'));
			var open = g.classList.contains('open');

			var kidHit = kids.filter(function (k) {
				var okT = !term || k.getAttribute('data-search').indexOf(term) !== -1;
				var okS = activeStatus === 'all' || k.getAttribute('data-status') === activeStatus;
				return okT && okS;
			});

			var okTerm   = !term || g.getAttribute('data-search').indexOf(term) !== -1;
			var okStatus = activeStatus === 'all' || g.getAttribute('data-status') === activeStatus;
			var visible  = (okTerm && okStatus) || kidHit.length > 0;

			g.classList.toggle('hidden', !visible);
			if (visible) { shown++; }

			// While filtering, reveal only the children that matched.
			var narrowing = term !== '' || activeStatus !== 'all';

			kids.forEach(function (k) {
				var show = visible && (narrowing ? kidHit.indexOf(k) !== -1 : open);
				if (narrowing && open && kidHit.indexOf(k) === -1) { show = false; }
				k.classList.toggle('hidden', !show);
			});
		});

		noMatch.style.display = shown === 0 ? 'block' : 'none';
	}

	// ── modal ───────────────────────────────────────────────────────────
	var backdrop = document.getElementById('backdrop');
	var mTitle   = document.getElementById('modalTitle');
	var mSub     = document.getElementById('modalSub');
	var mBody    = document.getElementById('modalBody');

	function closeModal() { backdrop.classList.remove('on'); }

	document.getElementById('modalClose').onclick = closeModal;
	backdrop.onclick = function (e) { if (e.target === backdrop) { closeModal(); } };
	document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeModal(); } });

	function esc(s) {
		return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}

	function openModal(kind, departmentId, label) {
		mTitle.textContent = kind === 'tickets' ? 'Tickets — ' + label : 'Tasks — ' + label;
		mSub.textContent   = 'DF ' + <?php echo json_encode($header['df_no']); ?>;
		mBody.innerHTML    = '<div class="pad">Loading…</div>';
		backdrop.classList.add('on');

		var body = new URLSearchParams();
		body.append('df_id', DF_ID);
		body.append('department_id', departmentId);

		fetch(BASE + 'gantt/' + kind, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString(),
			credentials: 'same-origin'
		})
		.then(function (r) { return r.json(); })
		.then(function (data) {
			if (!data.success || !data.rows.length) {
				mBody.innerHTML = '<div class="pad">' + esc(data.message || 'Nothing recorded here yet.') + '</div>';
				return;
			}
			mBody.innerHTML = kind === 'tickets' ? ticketTable(data.rows) : taskTable(data.rows);
		})
		.catch(function () {
			mBody.innerHTML = '<div class="pad">Could not load this right now. Please try again.</div>';
		});
	}

	function ticketTable(rows) {
		var html = '<table class="dt"><thead><tr><th>Ticket</th><th>Status</th><th>Task</th><th>Raised by</th><th>Raised on</th><th>Remark</th></tr></thead><tbody>';
		rows.forEach(function (r) {
			html += '<tr><td><b>' + esc(r.ticket_no || '—') + '</b></td>'
			      + '<td><span class="pill ' + (r.status === 'Open' ? 'p-late' : 'p-ok') + '">' + esc(r.status) + '</span></td>'
			      + '<td>' + esc(r.task_name || '—') + '</td>'
			      + '<td>' + esc(r.raised_by || '—') + '</td>'
			      + '<td>' + esc(r.raised_on) + '</td>'
			      + '<td>' + (r.remark ? esc(r.remark) : '<span class="muted">—</span>') + '</td></tr>';
		});
		return html + '</tbody></table>';
	}

	function taskTable(rows) {
		var html = '<table class="dt"><thead><tr><th>Task</th><th>Owner</th><th>Start</th><th>Due</th><th>Actual</th><th>Delay</th><th>Remark</th></tr></thead><tbody>';
		rows.forEach(function (r) {
			html += '<tr><td><b>' + esc(r.task_name) + '</b></td>'
			      + '<td>' + esc(r.owner) + '</td>'
			      + '<td>' + esc(r.planned_start) + '</td>'
			      + '<td>' + esc(r.planned_end) + '</td>'
			      + '<td>' + (r.completed_on ? esc(r.completed_on) : '<span class="muted">Pending</span>') + '</td>'
			      + '<td><span class="pill ' + (r.delay_days > 0 ? 'p-late' : 'p-ok') + '">' + (r.delay_days > 0 ? r.delay_days + 'd' : 'On time') + '</span></td>'
			      + '<td>' + (r.remarks ? esc(r.remarks) : '<span class="muted">—</span>') + '</td></tr>';
		});
		return html + '</tbody></table>';
	}

	// ── misc ────────────────────────────────────────────────────────────
	document.getElementById('deptSwitch').onchange = function () {
		if (this.value) { window.location = this.value; }
	};

	// ── export menu ─────────────────────────────────────────────────────
	var exportBtn  = document.getElementById('exportBtn');
	var exportMenu = document.getElementById('exportMenu');

	function closeExport() {
		exportMenu.classList.remove('on');
		exportBtn.setAttribute('aria-expanded', 'false');
	}

	exportBtn.onclick = function (e) {
		e.stopPropagation();
		var open = !exportMenu.classList.contains('on');
		exportMenu.classList.toggle('on', open);
		exportBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
	};

	// A download leaves the page in place, so the menu has to be dismissed
	// explicitly or it sits open over the chart afterwards.
	exportMenu.addEventListener('click', function () { setTimeout(closeExport, 80); });
	document.addEventListener('click', closeExport);
	document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeExport(); } });

	// Open on today so a long project lands where the work is.
	window.addEventListener('load', function () {
		var today  = document.querySelector('.today');
		var canvas = document.querySelector('.canvas');
		if (!today || !canvas) { return; }
		// Today sits at ~70% of the viewport so the run-up stays visible.
		var x = canvas.offsetLeft + today.offsetLeft - (frame.clientWidth * 0.7);
		frame.scrollLeft = Math.max(0, x);
	});
})();
</script>

<?php } ?>

</div>
</body>
</html>
