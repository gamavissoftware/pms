<?php
require __DIR__ . '/bootstrap.php';
if (!getenv('OVERTIME_TEST_MYSQL_SOCKET')) { fwrite(STDERR,"Set OVERTIME_TEST_MYSQL_SOCKET to an isolated test server.\n"); exit(1); }
if (isset($argv[1]) && $argv[1] === 'worker') {
    $db=new OtDb();$m=new Overtime_model();$m->db=$db;
    $payload=json_decode(base64_decode($argv[2]),true);
    while(microtime(true)<$payload['start'])usleep(1000);
    try {
        if ($payload['action']==='submit') $result=$m->submit(isset($payload['actor']) ? $payload['actor'] : 2,ot_team_input($payload['day'],array(1),''),$payload['key']);
        else $result=$m->decide($payload['id'],$payload['actor'],'APPROVE','Concurrent test decision');
        echo json_encode(array('ok'=>true,'result'=>$result));
    } catch(InvalidArgumentException $e){echo json_encode(array('ok'=>false,'message'=>$e->getMessage()));}
    exit;
}
list($m,$db)=ot_fixture();
putenv('OVERTIME_TEST_MYSQL_DATABASE='.$db->database);
function race($payloads) {
    $running=array();$start=microtime(true)+.5;
    foreach($payloads as $payload){$payload['start']=$start;$pipes=array();$process=proc_open(array(PHP_BINARY,__FILE__,'worker',base64_encode(json_encode($payload))),array(0=>array('pipe','r'),1=>array('pipe','w'),2=>array('pipe','w')),$pipes);fclose($pipes[0]);$running[]=array($process,$pipes);}
    $results=array();foreach($running as $runningProcess){list($process,$pipes)=$runningProcess;$out=stream_get_contents($pipes[1]);$error=stream_get_contents($pipes[2]);fclose($pipes[1]);fclose($pipes[2]);check(proc_close($process)===0,'Worker failed: '.$error);$result=json_decode($out,true);check(is_array($result),'Invalid worker output '.$out);$results[]=$result;}return $results;
}
$key=bin2hex(random_bytes(32));
$results=race(array(array('action'=>'submit','actor'=>2,'day'=>'+1 day','key'=>$key),array('action'=>'submit','actor'=>2,'day'=>'+1 day','key'=>$key)));
check($results[0]['ok']&&$results[1]['ok']&&$results[0]['result']===$results[1]['result'],'Same key resolves to one request');
check((int)$db->pdo->query('SELECT COUNT(*) FROM overtime_requests')->fetchColumn()===1,'One persisted request');
$results=race(array(array('action'=>'submit','actor'=>2,'day'=>'+2 days','key'=>bin2hex(random_bytes(32))),array('action'=>'submit','actor'=>3,'day'=>'+2 days','key'=>bin2hex(random_bytes(32)))));
check(count(array_filter($results,function($r){return $r['ok'];}))===1,'Different keys with overlapping times cannot both succeed');
$id=(int)$db->pdo->query('SELECT MIN(id) FROM overtime_requests')->fetchColumn();
$results=race(array(array('action'=>'decide','id'=>$id,'actor'=>139),array('action'=>'decide','id'=>$id,'actor'=>139)));
check(count(array_filter($results,function($r){return $r['ok'];}))===1,'Shubham decision applied once');
check(count($m->history($id))===2,'One submission and exactly one approval in audit');
check($m->get_request($id)['status']==='APPROVED','Final state approved');
echo "PASS: real MySQL concurrent submissions (same and different keys), overlap locking and repeated Shubham approvals.\n";
