<?php
require __DIR__.'/bootstrap.php';
list($m,$db)=ot_fixture();
check($m->module_ready(),'Team schema ready');
$input=ot_team_input();$key=bin2hex(random_bytes(32));
$id=$m->submit(2,$input,$key);$r=$m->get_request($id);
check($r['status']==='PENDING_ADMIN' && (int)$r['df_id']===100,'Team request goes straight to Shubham with DF');
check((int)$r['employee_id']===2 && (int)$r['leader_id']===2,'Requester identity is server controlled');
check((int)$r['requested_minutes']===120 && count($r['assignments'])===4,'Hours and mixed participants saved');
check($m->submit(2,$input,$key)===$id && count($m->history($id))===1,'Submission key prevents duplicate request and audit');
check(count($m->listing($m->user(139),array('inbox'=>true)))===4,'Only 139 inbox receives the four person entries');
check(count($m->listing($m->user(3),array('inbox'=>true)))===0,'Other admin has no approvals');
check(count($m->listing($m->user(1),array()))===0,'Selected worker has no assignment before approval');
check(!Overtime_model::can_view($r,$m->user(1)),'Pending detail hidden from worker');
check(count($m->notifications($m->user(139)))===1,'Approver notified once');
check((int)$m->summary($m->user(139),array())['pending_minutes']===480,'Person-hours include manual workers');
foreach(array(1,2,3,4,5,6) as $actor) rejects(function()use($m,$id,$actor){$m->decide($id,$actor,'APPROVE','');},'Only 139 may approve');
rejects(function()use($m,$input){$m->submit(3,$input,bin2hex(random_bytes(32)));},'Overlapping workers across leaders rejected');
// Failure after the assignment timestamp is updated must roll back the entire approval.
$db->fail_table='overtime_notifications';
try {$m->decide($id,139,'APPROVE','');throw new LogicException('Expected failure');} catch(RuntimeException $e) {}
$db->fail_table='';
check($m->get_request($id)['status']==='PENDING_ADMIN' && !$m->assignments($id)[0]['assigned_at'],'Approval rollback restores status and assignment');
check($m->decide($id,139,'APPROVE','')==='APPROVED','139 approves without optional remarks or admin role');
foreach($m->assignments($id) as $person) check(!empty($person['assigned_at']),'Every person assigned together');
check(count($m->notifications($m->user(1)))===1,'Registered worker receives assignment');
check(Overtime_model::can_view($m->get_request($id),$m->user(1)),'Worker can open assigned request');
check(count($m->listing($m->user(1),array('mine'=>true)))===4,'Assignment reachable from default list');
check(count($m->listing($m->user(6),array()))===0,'Cross-location access denied');
check(count($m->history($id))===2,'Submission and one approval history');
rejects(function()use($m,$id){$m->decide($id,139,'REJECT','');},'A final decision cannot be overwritten');
check((int)$m->summary($m->user(139),array())['approved_minutes']===480,'Four people at two hours equals eight hours');
check((int)$m->summary($m->user(139),array('employee_id'=>1))['approved_minutes']===120,'Person filter counts only matching hours');
check((int)$m->summary($m->user(139),array('department_id'=>10))['approved_minutes']===360,'Worker departments and manual requester department counted');
check(count($m->listing($m->user(139),array('person'=>'Contract worker')))===2,'Manual workers searchable');
check(count($m->listing($m->user(139),array('person'=>'%')))===0,'Search wildcard is escaped');
check(count($m->listing($m->user(139),array('df_id'=>101)))===0,'DF filter');
check(count($m->grouped_report($m->user(139),array(),'employee'))===4,'Manual workers grouped separately by name');
check((int)$m->grouped_report($m->user(139),array(),'df')[0]['approved_minutes']===480,'DF grouped totals');
check(count($m->filter_options($m->user(139))['dfs'])===1,'DF filter options from accessible records');
check(count($m->listing($m->user(139),array(),2,2))===2,'Person-row pagination');
$day=substr($input['start_at'],0,10);
check(count($m->listing($m->user(139),array('from'=>$day,'to'=>$day)))===4,'Inclusive start-date bounds');
check($m->decide($id,2,'CANCEL','Schedule changed')==='CANCELLED','Requester cancels future assignment');
check((int)$m->summary($m->user(139),array())['approved_minutes']===0,'Cancelled hours removed from approved total');
check(count($m->notifications($m->user(1)))===2,'Assigned worker informed of cancellation');
$id2=$m->submit(2,$input,bin2hex(random_bytes(32)));
check($m->decide($id2,139,'REJECT','')==='REJECTED','Rejection remarks optional');
check(!$m->assignments($id2)[0]['assigned_at'],'Rejection never assigns');
rejects(function()use($m,$id2){$m->decide($id2,139,'APPROVE','');},'Rejected request is terminal');
$manual=ot_team_input('+2 days',array(),'Labour only');
$manualId=$m->submit(2,$manual,bin2hex(random_bytes(32)));
check(count($m->assignments($manualId))===1 && $m->assignments($manualId)[0]['user_id']===null,'Manual-only request supported');
check($m->decide($manualId,139,'APPROVE','Proceed')==='APPROVED','Manual-only request approved');
$duplicate=ot_team_input('+3 days',array(1,1),'');
$duplicateId=$m->submit(2,$duplicate,bin2hex(random_bytes(32)));
check(count($m->assignments($duplicateId))===1,'Duplicate PMS IDs deduplicated');
$invalids=array(array('hours'=>'0'),array('hours'=>'25'),array('hours'=>'NaN'),array('hours'=>array('2')),array('df_id'=>'102'),array('df_id'=>'999'),
 array('user_ids'=>array(7),'manual_people'=>''),array('user_ids'=>array(6),'manual_people'=>''),array('user_ids'=>array(array(1))),
 array('user_ids'=>array(),'manual_people'=>''),array('manual_people'=>"Labour A\nlabour a"),array('reason'=>'short'),array('start_at'=>'2026-02-30T18:00'));
foreach($invalids as $bad) rejects(function()use($m,$bad){$m->submit(2,array_merge(ot_team_input('+10 days'),$bad),bin2hex(random_bytes(32)));},'Invalid team request accepted');
rejects(function()use($m){$m->submit(1,ot_team_input('+10 days'),bin2hex(random_bytes(32)));},'Ordinary employee cannot raise team request');
rejects(function()use($m){$m->submit(2,ot_team_input('-1 day'),bin2hex(random_bytes(32)));},'Backdating window enforced');
$m->save_policy(3,array('max_request_minutes'=>120,'max_daily_minutes'=>180,'past_days'=>0,'future_days'=>90));
$m->submit(2,ot_team_input('+4 days',array(1),''),bin2hex(random_bytes(32)));
$later=ot_team_input('+4 days',array(1),'');$later['start_at']=substr($later['start_at'],0,10).'T20:00';
rejects(function()use($m,$later){$m->submit(3,$later,bin2hex(random_bytes(32)));},'Per-worker daily hours checked across leaders');
$m->save_policy(3,array('max_request_minutes'=>720,'max_daily_minutes'=>720,'past_days'=>0,'future_days'=>90));
$night=ot_team_input('+5 days',array(1),'');$night['start_at']=substr($night['start_at'],0,10).'T23:00';
$nightId=$m->submit(2,$night,bin2hex(random_bytes(32)));
check(substr($m->get_request($nightId)['end_at'],11)==='01:00:00','Hours compute next-day end');
$adjacent=ot_team_input('+6 days',array(1),'');$adjacent['start_at']=substr($adjacent['start_at'],0,10).'T01:00';
check($m->submit(3,$adjacent,bin2hex(random_bytes(32)))>0,'Adjacent worker shifts allowed');
foreach(array('overtime_assignments','overtime_history','overtime_notifications') as $table) {
 $count=(int)$db->pdo->query('SELECT COUNT(*) FROM overtime_requests')->fetchColumn();$db->fail_table=$table;
 try{$m->submit(2,ot_team_input('+20 days'),bin2hex(random_bytes(32)));throw new LogicException('Expected failure');}catch(RuntimeException $e){}
 $db->fail_table='';check((int)$db->pdo->query('SELECT COUNT(*) FROM overtime_requests')->fetchColumn()===$count,'Failed child write rolls back request');
}
// Preserve old self-requests and route their pending approval to 139.
$legacy=array('request_code'=>'OT-LEGACY','employee_id'=>1,'leader_id'=>2,'business_location_id'=>2,'department_id'=>10,'status'=>'PENDING_LEADER','start_at'=>date('Y-m-d').' 01:00:00','end_at'=>date('Y-m-d').' 02:00:00','requested_minutes'=>60,'reason'=>'Existing legacy work','submission_key'=>bin2hex(random_bytes(32)),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s'));
$db->insert('overtime_requests',$legacy);$legacyId=$db->insert_id();
check($m->decide($legacyId,139,'APPROVE','Legacy reviewed')==='APPROVED','Legacy pending request can be finalized by 139');
check((int)$m->summary($m->user(139),array('from'=>date('Y-m-d'),'to'=>date('Y-m-d')))['approved_minutes']===60,'Legacy counted once without assignments');
foreach(array('=SUM(A1)',' +cmd',"\t@calc",'-2+3') as $cell) check(Overtime::csv_cell($cell)[0]==="'",'CSV formula escaped');
class OtTestInput {public $verb='POST',$token='valid';function method(){return $this->verb;}function post($key){return $this->token;}}
class OtTestSession {function userdata($key){return 'valid';}}
$controller=(new ReflectionClass('Overtime'))->newInstanceWithoutConstructor();$controller->input=new OtTestInput();$controller->session=new OtTestSession();
$guard=new ReflectionMethod('Overtime','post_guard');$guard->invoke($controller);
foreach(array(array('GET','valid'),array('POST','wrong'),array('POST',array('valid'))) as $case){$controller->input->verb=$case[0];$controller->input->token=$case[1];try{$guard->invoke($controller);throw new LogicException('Invalid CSRF accepted');}catch(RuntimeException $e){check($e->getCode()===403,'CSRF/verb denied');}}
echo "PASS: team requests, DF, PMS/manual workers, sole approver 139, optional remarks, assignments, notifications, rollback, idempotency, overlap/daily limits, legacy records, report totals/filters, CSV escaping and CSRF.\n";
