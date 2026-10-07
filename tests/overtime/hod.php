<?php
require __DIR__ . '/bootstrap.php';
list($m, $db) = ot_fixture();
$db->query('UPDATE departments SET departmenthead=3 WHERE department_id=10');
$db->query('UPDATE departments SET departmenthead=2 WHERE department_id=20');
check($m->can_request_for_team($m->user(2)), 'Team leader can raise team overtime');
check($m->can_request_for_team($m->user(3)), 'Department head can raise team overtime');
check($m->can_request_for_team($m->user(4)), 'Administrator can raise team overtime');
check(!$m->can_request_for_team($m->user(1)), 'Ordinary employee cannot raise team overtime');

$leaderId = $m->submit(2, ot_team_input('+2 days', array(1), ''), bin2hex(random_bytes(32)));
check($m->get_request($leaderId)['status'] === 'PENDING_ADMIN', 'Team leader request goes directly to Shubham');
$hodId = $m->submit(3, ot_team_input('+3 days', array(5), ''), bin2hex(random_bytes(32)));
check($m->get_request($hodId)['status'] === 'PENDING_ADMIN', 'Department head request goes directly to Shubham');
$adminId = $m->submit(4, ot_team_input('+4 days', array(5), 'Contract support'), bin2hex(random_bytes(32)));
check($m->get_request($adminId)['status'] === 'PENDING_ADMIN', 'Administrator request goes directly to Shubham');

foreach (array($leaderId, $hodId, $adminId) as $id) {
    rejects(function () use ($m, $id) { $m->decide($id, 3, 'APPROVE', ''); }, 'Non-139 approver blocked');
    check($m->decide($id, 139, 'APPROVE', '') === 'APPROVED', 'Shubham approves and assigns');
}
$db->query('UPDATE departments SET departmenthead=0 WHERE department_id=10');
check($m->can_request_for_team($m->user(2)), 'Existing team leadership still permits requests when HOD is unconfigured');
echo "PASS: team leader, HOD and administrator request eligibility with fixed Shubham approval.\n";
