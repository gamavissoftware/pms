<?php
if (!function_exists('taskTreeSafe')) {
	function taskTreeSafe($value)
	{
		return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
	}
}

if (!function_exists('taskTreeDepartmentClass')) {
	function taskTreeDepartmentClass($department)
	{
		return 'dept-color-'.(abs(crc32((string) $department)) % 8);
	}
}

if (!function_exists('taskTreeRenderNode')) {
	function taskTreeRenderNode($node, $level = 1)
	{
		$children = isset($node['children']) ? $node['children'] : array();
		$has_children = count($children) > 0;
		$department = isset($node['department']) ? $node['department'] : 'Unassigned';
		$search = strtolower(trim($node['task_name'].' '.$department.' '.$node['task_type_label'].' '.$node['task_id']));
		$collapsed_class = ($has_children && $level >= 3) ? ' is-collapsed' : '';
		$html = '<li class="task-tree-node level-'.$level.($has_children ? ' has-children' : '').$collapsed_class.'" data-level="'.$level.'" data-department="'.taskTreeSafe($department).'" data-search="'.taskTreeSafe($search).'">';
		$html .= '<div class="task-node-card '.taskTreeDepartmentClass($department).'">';
		$html .= '<div class="task-node-top">';
		$html .= '<span class="task-node-id">#'.(int) $node['task_id'].'</span>';
		$html .= '<span class="task-node-type">'.taskTreeSafe($node['task_type_label']).'</span>';
		$html .= '</div>';
		$html .= '<div class="task-node-title">'.taskTreeSafe($node['task_name']).'</div>';
		$html .= '<div class="task-node-meta">';
		$html .= '<span><i class="fa fa-building-o"></i> '.taskTreeSafe($department).'</span>';
		$html .= '<span><i class="fa fa-clock-o"></i> '.(int) $node['tat'].' day'.((int) $node['tat'] === 1 ? '' : 's').'</span>';
		$html .= '</div>';
		$html .= '<div class="task-node-footer">';
		$html .= '<span>Sort '.(int) $node['sortorder'].'</span>';
		if ($has_children) {
			$html .= '<button type="button" class="tree-toggle" aria-label="Toggle children">'.count($children).' child'.(count($children) === 1 ? '' : 'ren').'</button>';
		} else {
			$html .= '<span class="leaf-label">End step</span>';
		}
		$html .= '</div>';
		$html .= '</div>';

		if ($has_children) {
			$html .= '<ul class="task-tree-children">';
			foreach ($children as $child) {
				$html .= taskTreeRenderNode($child, $level + 1);
			}
			$html .= '</ul>';
		}

		$html .= '</li>';
		return $html;
	}
}

$roots = isset($roots) ? $roots : array();
$stats = isset($stats) ? $stats : array(
	'total_tasks' => 0,
	'main_tasks' => 0,
	'sub_tasks' => 0,
	'departments' => 0,
	'root_tasks' => 0,
	'max_depth' => 0
);
$tasks = isset($tasks) ? $tasks : array();
$departments = array();
foreach ($tasks as $task) {
	if (!empty($task['department'])) {
		$departments[$task['department']] = true;
	}
}
ksort($departments);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="<?php echo copyright; ?>">
		<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
		<title><?php echo sitetitle;?> Task Tree View</title>
		<link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<style>
			body.task-tree-page { background: #f4f7fb; color: #1f2937; }
			.task-tree-page .wrapper { padding-top: 22px; }
			.tree-page-header, .tree-toolbar, .tree-canvas-shell { background: #ffffff; border: 1px solid #e5ebf2; border-radius: 8px; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06); }
			.tree-page-header { align-items: center; display: flex; gap: 18px; justify-content: space-between; margin-bottom: 16px; padding: 16px 18px; }
			.tree-page-title h4 { color: #0f172a; font-size: 22px; font-weight: 800; line-height: 1.2; margin: 0 0 5px; }
			.tree-page-title span { color: #64748b; display: block; font-size: 13px; font-weight: 600; }
			.tree-page-actions { display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-end; }
			.tree-page-actions .btn, .tree-toolbar .btn { border-radius: 6px; font-weight: 700; }
			.tree-stat-grid { display: grid; gap: 12px; grid-template-columns: repeat(6, minmax(120px, 1fr)); margin-bottom: 16px; }
			.tree-stat { background: #ffffff; border: 1px solid #e5ebf2; border-radius: 8px; box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05); padding: 13px 14px; }
			.tree-stat label { color: #64748b; display: block; font-size: 11px; font-weight: 800; margin-bottom: 5px; text-transform: uppercase; }
			.tree-stat strong { color: #0f172a; display: block; font-size: 24px; line-height: 1; }
			.tree-toolbar { align-items: flex-end; display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 16px; padding: 14px; }
			.tree-toolbar .field { flex: 1 1 220px; }
			.tree-toolbar label { color: #64748b; display: block; font-size: 11px; font-weight: 800; margin-bottom: 5px; text-transform: uppercase; }
			.tree-toolbar .form-control { border-color: #d8e0ea; border-radius: 6px; box-shadow: none; height: 38px; }
			.tree-toolbar-actions { display: flex; flex: 0 0 auto; flex-wrap: wrap; gap: 8px; }
			.tree-canvas-shell { overflow: hidden; position: relative; }
			.tree-canvas-head { align-items: center; border-bottom: 1px solid #e5ebf2; display: flex; justify-content: space-between; padding: 13px 16px; }
			.tree-canvas-head strong { color: #0f172a; font-size: 15px; }
			.tree-result-count { color: #64748b; font-size: 12px; font-weight: 800; text-transform: uppercase; }
			.tree-canvas { background: linear-gradient(#f8fafc 1px, transparent 1px), linear-gradient(90deg, #f8fafc 1px, transparent 1px); background-size: 24px 24px; height: calc(100vh - 355px); min-height: 520px; overflow: auto; padding: 26px; }
			.task-tree { --tree-zoom: 1; display: inline-block; min-width: 100%; padding-bottom: 24px; text-align: center; transform: scale(var(--tree-zoom)); transform-origin: top left; }
			.task-tree ul, .task-tree li { list-style: none; margin: 0; padding: 0; }
			.task-tree > ul, .task-tree-children { display: flex; gap: 22px; justify-content: center; position: relative; }
			.task-tree-children { margin-top: 28px; }
			.task-tree-node { align-items: center; display: flex; flex-direction: column; position: relative; }
			.task-tree-node.has-children > .task-node-card:after { background: #cbd5e1; bottom: -29px; content: ""; height: 29px; left: 50%; position: absolute; width: 2px; }
			.task-tree-node.is-collapsed > .task-tree-children, .task-tree-node.is-filter-hidden { display: none; }
			.task-node-card { background: #ffffff; border: 1px solid #d8e0ea; border-top: 4px solid #0f6c94; border-radius: 8px; box-shadow: 0 8px 22px rgba(15, 23, 42, 0.08); min-height: 150px; padding: 12px; position: relative; text-align: left; transition: box-shadow 0.16s ease, transform 0.16s ease; width: 230px; }
			.task-node-card:hover { box-shadow: 0 14px 30px rgba(15, 23, 42, 0.15); transform: translateY(-2px); }
			.task-node-top, .task-node-meta, .task-node-footer { align-items: center; display: flex; gap: 8px; justify-content: space-between; }
			.task-node-id, .task-node-type, .leaf-label, .tree-toggle { border-radius: 999px; font-size: 11px; font-weight: 800; padding: 4px 8px; }
			.task-node-id { background: #eef2ff; color: #3730a3; }
			.task-node-type { background: #ecfdf5; color: #047857; }
			.task-node-title { color: #0f172a; font-size: 14px; font-weight: 800; line-height: 1.35; margin: 11px 0; min-height: 38px; }
			.task-node-meta { align-items: flex-start; color: #64748b; flex-direction: column; font-size: 12px; font-weight: 700; justify-content: flex-start; margin-bottom: 12px; }
			.task-node-footer { border-top: 1px solid #eef2f7; color: #64748b; font-size: 12px; font-weight: 800; padding-top: 10px; }
			.tree-toggle { background: #0f6c94; border: 0; color: #ffffff; }
			.leaf-label { background: #f1f5f9; color: #64748b; }
			.dept-color-0 { border-top-color: #0f6c94; }
			.dept-color-1 { border-top-color: #0f766e; }
			.dept-color-2 { border-top-color: #b45309; }
			.dept-color-3 { border-top-color: #7c3aed; }
			.dept-color-4 { border-top-color: #be123c; }
			.dept-color-5 { border-top-color: #2563eb; }
			.dept-color-6 { border-top-color: #15803d; }
			.dept-color-7 { border-top-color: #c2410c; }
			.task-node-card.is-match { outline: 3px solid rgba(14, 165, 233, 0.28); }
			.tree-empty { background: #ffffff; border: 1px dashed #cbd5e1; border-radius: 8px; color: #64748b; font-weight: 800; padding: 36px; text-align: center; }
			.tree-page-loader { align-items: center; background: rgba(248, 250, 252, 0.9); display: flex; height: 100%; justify-content: center; left: 0; position: fixed; top: 0; width: 100%; z-index: 9999; }
			.tree-loader-card { align-items: center; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18); display: flex; gap: 13px; min-width: 280px; padding: 16px 18px; }
			.tree-loader-spinner { animation: treeSpin 0.8s linear infinite; border: 3px solid #dbeafe; border-radius: 50%; border-top-color: #0f6c94; height: 30px; width: 30px; }
			.tree-loader-text { color: #0f172a; font-weight: 800; }
			@keyframes treeSpin { to { transform: rotate(360deg); } }
			@media (max-width: 1199px) { .tree-stat-grid { grid-template-columns: repeat(3, minmax(120px, 1fr)); } }
			@media (max-width: 767px) { .tree-page-header { align-items: stretch; flex-direction: column; } .tree-page-actions { justify-content: flex-start; } .tree-stat-grid { grid-template-columns: repeat(2, minmax(120px, 1fr)); } .tree-canvas { height: 620px; } }
		</style>
	</head>
	<body class="task-tree-page">
		<div id="treePageLoader" class="tree-page-loader">
			<div class="tree-loader-card">
				<div class="tree-loader-spinner"></div>
				<div class="tree-loader-text">Preparing task tree...</div>
			</div>
		</div>
		<header id="topnav">
			<?php $this->load->view('common/nav-menu');?>
		</header>
		<div class="wrapper">
			<div class="container-fluid">
				<div class="tree-page-header">
					<div class="tree-page-title">
						<h4>Task Tree View</h4>
						<span>Interactive process map for every active task dependency.</span>
					</div>
					<div class="tree-page-actions">
						<a href="<?php echo page_url;?>Task/taskmanagement" class="btn btn-primary"><i class="fa fa-list"></i> Task Listing</a>
						<a href="<?php echo page_url;?>Task" class="btn btn-success"><i class="fa fa-plus"></i> Add New Task</a>
					</div>
				</div>

				<div class="tree-stat-grid">
					<div class="tree-stat"><label>Total Tasks</label><strong><?php echo (int) $stats['total_tasks'];?></strong></div>
					<div class="tree-stat"><label>Main Tasks</label><strong><?php echo (int) $stats['main_tasks'];?></strong></div>
					<div class="tree-stat"><label>Sub Tasks</label><strong><?php echo (int) $stats['sub_tasks'];?></strong></div>
					<div class="tree-stat"><label>Departments</label><strong><?php echo (int) $stats['departments'];?></strong></div>
					<div class="tree-stat"><label>Root Steps</label><strong><?php echo (int) $stats['root_tasks'];?></strong></div>
					<div class="tree-stat"><label>Max Depth</label><strong><?php echo (int) $stats['max_depth'];?></strong></div>
				</div>

				<div class="tree-toolbar">
					<div class="field">
						<label>Search Task</label>
						<input type="text" class="form-control" id="treeSearch" placeholder="Search by task, department, type, or ID">
					</div>
					<div class="field">
						<label>Department</label>
						<select class="form-control" id="departmentFilter">
							<option value="">All Departments</option>
							<?php foreach (array_keys($departments) as $department) { ?>
								<option value="<?php echo taskTreeSafe($department);?>"><?php echo taskTreeSafe($department);?></option>
							<?php } ?>
						</select>
					</div>
					<div class="field">
						<label>Zoom</label>
						<select class="form-control" id="treeZoom">
							<option value="0.75">75%</option>
							<option value="0.9">90%</option>
							<option value="1" selected>100%</option>
							<option value="1.15">115%</option>
							<option value="1.3">130%</option>
						</select>
					</div>
					<div class="tree-toolbar-actions">
						<button type="button" class="btn btn-default" id="collapseTree"><i class="fa fa-compress"></i> Collapse</button>
						<button type="button" class="btn btn-default" id="expandTree"><i class="fa fa-expand"></i> Expand</button>
						<button type="button" class="btn btn-default" id="resetTree"><i class="fa fa-refresh"></i> Reset</button>
					</div>
				</div>

				<div class="tree-canvas-shell">
					<div class="tree-canvas-head">
						<strong>Active Task Dependency Map</strong>
						<span class="tree-result-count" id="treeResultCount"><?php echo (int) $stats['total_tasks'];?> visible</span>
					</div>
					<div class="tree-canvas" id="treeCanvas">
						<?php if (empty($roots)) { ?>
							<div class="tree-empty">No active task structure found.</div>
						<?php } else { ?>
							<div class="task-tree" id="taskTree">
								<ul>
									<?php foreach ($roots as $root) { echo taskTreeRenderNode($root); } ?>
								</ul>
							</div>
						<?php } ?>
					</div>
				</div>

				<?php $this->load->view('common/footer');?>
			</div>
		</div>
		<script src="<?php echo assets_url;?>js/jquery.min.js"></script>
		<script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
		<script src="<?php echo assets_url;?>js/detect.js"></script>
		<script src="<?php echo assets_url;?>js/fastclick.js"></script>
		<script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
		<script src="<?php echo assets_url;?>js/waves.js"></script>
		<script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
		<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
		<script src="<?php echo assets_url;?>js/jquery.app.js"></script>
		<script>
			(function() {
				var tree = document.getElementById('taskTree');
				var searchInput = document.getElementById('treeSearch');
				var departmentFilter = document.getElementById('departmentFilter');
				var zoomSelect = document.getElementById('treeZoom');
				var resultCount = document.getElementById('treeResultCount');
				function nodes() { return tree ? Array.prototype.slice.call(tree.querySelectorAll('.task-tree-node')) : []; }
				function updateCount() {
					if (!resultCount) { return; }
					var visible = nodes().filter(function(node) { return !node.classList.contains('is-filter-hidden'); }).length;
					resultCount.textContent = visible + ' visible';
				}
				function applyFilter() {
					if (!tree) { return; }
					var query = (searchInput.value || '').toLowerCase().trim();
					var department = departmentFilter.value || '';
					nodes().forEach(function(node) {
						node.classList.remove('is-filter-hidden');
						var card = node.querySelector('.task-node-card');
						if (card) { card.classList.remove('is-match'); }
					});
					function evaluate(node) {
						var ownSearch = node.getAttribute('data-search') || '';
						var ownDepartment = node.getAttribute('data-department') || '';
						var textMatch = !query || ownSearch.indexOf(query) !== -1;
						var deptMatch = !department || ownDepartment === department;
						var ownMatch = textMatch && deptMatch;
						var childMatch = false;
						Array.prototype.slice.call(node.children).forEach(function(child) {
							if (child.classList && child.classList.contains('task-tree-children')) {
								Array.prototype.slice.call(child.children).forEach(function(childNode) {
									if (evaluate(childNode)) { childMatch = true; }
								});
							}
						});
						var visible = ownMatch || childMatch;
						node.classList.toggle('is-filter-hidden', !visible);
						if (ownMatch && query) {
							var card = node.querySelector('.task-node-card');
							if (card) { card.classList.add('is-match'); }
						}
						if ((query || department) && childMatch) { node.classList.remove('is-collapsed'); }
						return visible;
					}
					Array.prototype.slice.call(tree.querySelector('ul').children).forEach(evaluate);
					updateCount();
				}
				if (tree) {
					tree.addEventListener('click', function(event) {
						if (!event.target.classList.contains('tree-toggle')) { return; }
						var node = event.target.closest('.task-tree-node');
						if (node) { node.classList.toggle('is-collapsed'); }
					});
				}
				document.getElementById('collapseTree').addEventListener('click', function() {
					nodes().forEach(function(node) {
						if (node.classList.contains('has-children')) { node.classList.add('is-collapsed'); }
					});
				});
				document.getElementById('expandTree').addEventListener('click', function() {
					nodes().forEach(function(node) { node.classList.remove('is-collapsed'); });
				});
				document.getElementById('resetTree').addEventListener('click', function() {
					searchInput.value = '';
					departmentFilter.value = '';
					zoomSelect.value = '1';
					if (tree) { tree.style.setProperty('--tree-zoom', '1'); }
					nodes().forEach(function(node) {
						node.classList.remove('is-filter-hidden');
						if (parseInt(node.getAttribute('data-level'), 10) >= 3 && node.classList.contains('has-children')) {
							node.classList.add('is-collapsed');
						} else {
							node.classList.remove('is-collapsed');
						}
						var card = node.querySelector('.task-node-card');
						if (card) { card.classList.remove('is-match'); }
					});
					updateCount();
				});
				searchInput.addEventListener('input', applyFilter);
				departmentFilter.addEventListener('change', applyFilter);
				zoomSelect.addEventListener('change', function() {
					if (tree) { tree.style.setProperty('--tree-zoom', zoomSelect.value); }
				});
				window.requestAnimationFrame(function() {
					updateCount();
					var loader = document.getElementById('treePageLoader');
					if (loader) { loader.style.display = 'none'; }
				});
			})();
		</script>
	</body>
</html>
