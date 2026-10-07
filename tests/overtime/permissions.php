<?php
require __DIR__.'/bootstrap.php';
list($m,$db)=ot_fixture();
check((int)$db->pdo->query('SELECT COUNT(*) FROM submodule')->fetchColumn()===5,'Permission migration repeatable');

$p=ot_user_permissions($db,1);
check($p['requests']&&$p['create']&&$p['approvals']&&$p['reports']&&$p['policy_edit']&&$p['leaders_edit'],'Module grants mapped');
check(!$p['decide'],'Approval decision is reserved for user 139');
check(ot_user_permissions($db,139)['decide'],'User 139 can approve without role grants');
check($m->can_request_for_team($m->user(4)),'Administrator can raise requests');
check(!$m->can_request_for_team($m->user(1)),'Ordinary employee cannot raise team requests');

$adminRequest=$m->submit(4,ot_team_input('+1 day',array(5),'Admin entered labour'),bin2hex(random_bytes(32)));
check($m->get_request($adminRequest)['status']==='PENDING_ADMIN','Administrator submission goes to Shubham');
check($m->decide($adminRequest,139,'APPROVE','')==='APPROVED','Only Shubham finalizes administrator request');

$db->query("UPDATE module_capablity SET madd=0 WHERE role_id=4 AND submoduleid IN (SELECT id FROM submodule WHERE submodule='OVERTIME REQUESTS')");
$p=ot_user_permissions($db,4);
check($p['requests']&&!$p['create'],'Read permission does not imply Add');
rejects(function()use($m){$m->submit(4,ot_team_input('+2 days',array(5),''),bin2hex(random_bytes(32)));},'Administrator without Add cannot submit');
$db->query('UPDATE module_capablity SET madd=1 WHERE role_id=4');

foreach(array(1,2,3,4,5,6) as $actor) {
    rejects(function()use($m,$adminRequest,$actor){$m->decide($adminRequest,$actor,'APPROVE','');},'Non-139 user cannot decide');
}
$db->query("UPDATE module_capablity SET medit=0 WHERE role_id=139 AND submoduleid IN (SELECT id FROM submodule WHERE submodule='OVERTIME APPROVALS')");
check(ot_user_permissions($db,139)['decide'],'User 139 approval access is fixed even without legacy grants');

$db->query("UPDATE module_capablity SET medit=0 WHERE role_id=3 AND submoduleid IN (SELECT id FROM submodule WHERE submodule='OVERTIME REQUEST LIMITS')");
check(ot_user_permissions($db,3)['policy']&&!ot_user_permissions($db,3)['policy_edit'],'Master view versus edit distinguished');
rejects(function()use($m){$m->save_policy(3,array());},'Master edit denied');
$db->query('UPDATE module_access SET access=0 WHERE role_id=3');
check(!array_filter(ot_user_permissions($db,3)),'Administrator has no implicit permission bypass when module access is revoked');
$db->query('UPDATE module_access SET access=1 WHERE role_id=3');

$teamRequest=$m->submit(2,ot_team_input('+3 days',array(1),''),bin2hex(random_bytes(32)));
check((int)$m->get_request($teamRequest)['leader_id']===2,'Requester identity is server controlled for team leader request');
rejects(function()use($m){$m->submit(1,ot_team_input('+4 days',array(5),''),bin2hex(random_bytes(32)));},'Ordinary employee cannot submit even with Add grant');

$controller=(new ReflectionClass('Overtime'))->newInstanceWithoutConstructor();
$property=new ReflectionProperty('Overtime','permissions');$property->setValue($controller,array('create'=>false));
try{(new ReflectionMethod('Overtime','permission_guard'))->invoke($controller,'create');throw new LogicException('Route permitted');}catch(RuntimeException $e){check($e->getCode()===403,'Direct route returns forbidden');}
echo "PASS: permission registration, Add/Edit gates, administrator request eligibility, fixed Shubham approval, admin revocation and direct-route denial.\n";
