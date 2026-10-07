<?php
require __DIR__ . '/regression.php';
require_once __DIR__ . '/../../application/helpers/df_delay_helper.php';
$db->detailMode = false;
$db->pdo->exec("INSERT INTO df_release VALUES
(13,'closed','Test','','',1,1,0),
(14,'hold','Test','','',1,0,1),
(15,'completed-late-only','Test','','',1,0,0),
(16,'status-2-only','Test','','',1,0,0),
(17,'no-valid-overdue-date','Test','','',1,0,0);
INSERT INTO df_dispatch_plans VALUES
(13,13,'closed','$past'),
(14,14,'hold','$past'),
(15,15,'completed-late-only','$past'),
(16,16,'status-2-only','$past'),
(17,17,'no-valid-overdue-date','$past');");
foreach ([[20,13,1,0,$past],[21,14,1,0,$past],[22,15,1,1,$past],[23,16,1,2,$past],[24,17,1,0,'0000-00-00'],[25,17,1,0,null],[26,17,1,0,$today]] as $row) $insert->execute($row);
$counts = df_current_delay_counts($db, $today);
check($counts === [3 => 1, 16 => 1], 'Count active DFs only after department and dispatch boundaries are crossed');
$predicate = df_open_overdue_sql($db, 't', $today);
$ids = $db->pdo->query("SELECT df.id FROM df_release df WHERE df.df_status=0 AND df.on_hold=0 AND EXISTS (SELECT 1 FROM task_department_wise_scheduling t WHERE t.df_id=df.id AND $predicate) ORDER BY df.id")->fetchAll(PDO::FETCH_COLUMN);
check($ids === array_keys($counts), 'Delayed list and KPI must agree');
echo "PASS: dashboard/list agreement; completed, closed, hold, invalid dates, due today and status-2 cases.\n";
