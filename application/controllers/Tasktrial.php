<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tasktrial extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Task_model','task');    

    }


    function updatetasksortorder(){
    $taskid = $this->input->post('taskid');
    $order = $this->input->post('selectedid');

    $data = array('sortorder'=>$order);
    $this->db->where('task_id',$taskid);
    $this->db->update('task_management',$data);
    echo "Updated"; exit;
}

// correct one
function transversetree()
{
    $tasks=array();
    $rt=$this->db->select('task_id,task_name,tat_start_from')->from('task_management')->where('status',1)->where('root!=',1)->get();
    if($rt->num_rows()>0)
    {
        foreach($rt->result() as $row)
        {
            if($row->tat_start_from==0)
            {
                $tat_start_from=null;
            }else
            {
                $tat_start_from=$row->tat_start_from;
            }

            $tasks[]=array('task_id'=>$row->task_id,'tat_start_from'=>$tat_start_from);
        }

    }
    

// Sort tasks
usort($tasks, 'sortByDependency');

// Output sorted tasks
$i=0;
$d=array();
$t=array();
foreach ($tasks as $task) {
    //echo $i."-Task ID: " . $task['task_id'] . ", Dependency: " . $task['tat_start_from'] . "<br/>";
     $d[]= $task['tat_start_from'];
     // $t[]=$task['task_id'];
     //echo  $task['tat_start_from'] . "<br/>";
    $i++;
}
//echo "<pre>"; print_r(array_unique($d)); exit;
$y=1;
foreach(array_unique($d) as $row)
{
    
     $d=array('system_created_sort_order'=>$y);
     $this->db->where('task_id',$row);
     $this->db->update('task_management',$d);
    

$y++;
}


exit;

    
}



 public function create_sort_order() {
        $this->create_order();
    }

    // Function to update sort order recursively
    private function update_sort_order($taskId, &$sortOrder) {
        $this->db->set('system_created_sort_order', $sortOrder);
        $this->db->where('task_id', $taskId);
        $this->db->update('task_management');
        $sortOrder++;
        $query = $this->db->get_where('task_management', array('tat_start_from' => $taskId));
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $this->update_sort_order($row->task_id, $sortOrder);
            }
        }
    }

    // Main function to initiate sorting
    private function create_order() {
        $sortOrder = 1; // Initial sort order
        $query = $this->db->get_where('task_management', array('tat_start_from' =>0)); // Get root tasks
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $this->update_sort_order($row->task_id, $sortOrder);
            }
            echo "Sort order updated successfully!";
        } else {
            echo "No root tasks found.";
        }
    }


    
        // Function to sort tasks based on dependency
function sortByDependency($a, $b) {
    if ($a['tat_start_from'] == $b['tat_start_from']) {

        return 0;
    }
    return ($a['tat_start_from'] < $b['tat_start_from']) ? -1 : 1;
}


function parentTree()
{
    
     $arr = array(
      array('id'=>100, 'parentid'=>0, 'name'=>'a'),
      array('id'=>101, 'parentid'=>100, 'name'=>'a'),
      array('id'=>102, 'parentid'=>101, 'name'=>'a'),
      array('id'=>103, 'parentid'=>101, 'name'=>'a'),
    );

    $new = array();
    foreach ($arr as $a){
        $new[$a['parentid']][] = $a;
    }
    $tree = $this->createTree($new, array($arr[0]));
    //echo "<pre>"; print_r($tree);
}

    function createTree(&$list, $parent){
        $tree = array();
        foreach ($parent as $k=>$l){
            if(isset($list[$l['id']])){
                $l['children'] = $this->createTree($list, $list[$l['id']]);
            }
            $tree[] = $l;
        } 
        return $tree;
    }


  private function arrangeTasks($graph) {
        $orderedTasks = [];
        $visited = []; // Visited array to prevent revisiting nodes

        // Function for DFS traversal
        $dfs = function($node) use (&$dfs, &$orderedTasks, &$visited, &$graph) {
            $visited[$node] = true; // Mark node as visited

            // Visit each adjacent node if not already visited
            if (isset($graph[$node]) && is_array($graph[$node])) {
                foreach ($graph[$node] as $adjacent) {
                    if (!isset($visited[$adjacent])) {
                        $dfs($adjacent);
                    }
                }
            }

            // Push node to the result array after visiting all dependencies
            array_unshift($orderedTasks, $node);
        };

        // Perform DFS for each node in the graph
        foreach (array_keys($graph) as $node) {
            if (!isset($visited[$node])) {
                $dfs($node);
            }
        }

        return $orderedTasks;
    }

    // Function to arrange tasks based on their dependencies
    private function arrangeTasksOLd($graph) {
        // Check if the first task is dependent on the last task
        $firstTask = array_key_first($graph);
        $lastTask = array_key_last($graph);

        if (in_array($lastTask, $graph[$firstTask])) {
            // Swap the first and last tasks
            $temp = $graph[$firstTask];
            $graph[$firstTask] = $graph[$lastTask];
            $graph[$lastTask] = $temp;
        }

        // Perform topological sorting
        $stack = new SplStack(); // Stack to store sorted tasks
        $visited = []; // Visited array to prevent revisiting nodes

        // Function to visit each node
        $visit = function($node) use (&$visit, &$stack, &$visited, &$graph) {
            $visited[$node] = true; // Mark node as visited

            // Visit each adjacent node if not already visited
            foreach ($graph[$node] as $adjacent) {
                if (!isset($visited[$adjacent])) {
                    $visit($adjacent);
                }
            }

            $stack->push($node); // Push node to stack after visiting all adjacent nodes
        };

        // Visit each node in the graph
        foreach (array_keys($graph) as $node) {
            if (!isset($visited[$node])) {
                $visit($node);
            }
        }

        $orderedTasks = [];
        // Pop elements from stack to get the sorted order
        while (!$stack->isEmpty()) {
            $orderedTasks[] = $stack->pop();
        }

        //echo count($orderedTasks); exit;
        return $orderedTasks;
    }

    public function topologicalTree() {

         $graph1 = [
            'task1' => ['task2', 'task3'],  // task1 depends on task2 and task3
            'task2' => ['task4'],            // task2 depends on task4
            'task3' => [],                   // task3 has no dependencies
            'task4' => ['task5'],            // task4 depends on task5
            'task5' => ['task1'],            // task5 depends on task1
        ];

 // echo "<pre>"; print_r($graph1); 
        // Example graph representing task dependencies
        
    $rt=$this->db->select('task_id')->from('task_management')->where('status',1)->where('root!=',1)->get();
    if($rt->num_rows()>0)
    {
        foreach($rt->result() as $row)
        {
        $g1=array();
        $rtyu1=$this->db->select('task_id')->from('task_management')->where('tat_start_from',$row->task_id)->get();
        if($rtyu1->num_rows()>0)
        {
            foreach($rtyu1->result() as $rowssss)
            {
                $g1[]=$rowssss->task_id;
            }

        }
        $graph[] = array($row->task_id =>$g1);  // task1 depends on 
        }
    }

    // echo "<pre>"; print_r($this->reiterate($graph));exit;
        // Arrange tasks based on their dependencies
        $orderedTasks = $this->arrangeTasks($this->reiterate($graph));

        // Output the ordered tasks
        echo "Order of tasks:\n";
        $y=1;
        foreach ($orderedTasks as $task) {
            echo $task . "<br>";
            $d=array('system_created_sort_order'=>$y);
            $this->db->where('task_id',$task);
            $this->db->update('task_management',$d);
        $y++;
        }
    }


function reiterate($originalArray)
{
    $resultArray = [];

// Iterate through the original array
foreach ($originalArray as $innerArray) {
    foreach ($innerArray as $key => $value) {
        // Create a new key for each inner array
        $resultArray[$key] = array_map(function ($task) {
            return $task;
        }, $value);
    }
}

return $resultArray;

}

 /******************new DO NOT REMOVE VERY IMPORTANT *********/

 // Function to get tasks sorted by their execution order
    public function get_sorted_tasks() {
        $tasks = $this->db->select('task_id,task_name,tat_start_from')->get('task_management')->result_array(); // Assuming 'task_management' is the table name
        $sorted_tasks = array();

        // Create a mapping of task IDs to their corresponding task data
        $task_map = array();
        foreach ($tasks as $task) {
            $task_map[$task['task_id']] = $task;
        }

        // Initialize an array to keep track of visited tasks
        $visited = array();

        // Iterate through each task and perform DFS to get the sorted tasks
        foreach ($tasks as $task) {
            if (!isset($visited[$task['task_id']])) {
                $this->dfs($task['task_id'], $task_map, $sorted_tasks, $visited);
            }
        }


        //echo "<pre>";print_r($sorted_tasks); exit;
        $t=0;
        foreach($sorted_tasks as $tasks)
        {
            
            $dd=array('system_created_sort_order'=>$t);
            $this->db->where('task_id',$tasks['task_id']);
            $this->db->update('task_management',$dd);

        $t++;
        }

        //return $sorted_tasks;
    }

    // Recursive function for DFS traversal
    private function dfs($task_id, $task_map, &$sorted_tasks, &$visited) {
        if (!isset($visited[$task_id])) {
            $visited[$task_id] = true;
            $task = $task_map[$task_id];
            $start_from_task_id = $task['tat_start_from'];

            // If the task has no parent task, add it to the sorted tasks array
            if ($start_from_task_id == null || !isset($task_map[$start_from_task_id])) {
                $sorted_tasks[] = $task;
            } else {
                // If the task has a parent task, recursively call dfs for the parent task first
                $this->dfs($start_from_task_id, $task_map, $sorted_tasks, $visited);
                $sorted_tasks[] = $task;
            }

            // If the task has child tasks, recursively call dfs for each child task
            foreach ($task_map as $child_task_id => $child_task) {
                if ($child_task['tat_start_from'] == $task_id) {
                    $this->dfs($child_task_id, $task_map, $sorted_tasks, $visited);
                }
            }
        }
    }


    


    /** TREEE STRUCTURE **/

     // Function to create a tree-like structure based on parent-child relationships
    public function build_task_tree_html($tasks) {
        $task_map = array();

        // Create a mapping of task IDs to their corresponding task data
        foreach ($tasks as $task) {
            $task_map[$task['task_id']] = $task;
        }

        // Initialize the HTML output
        $html = '<ul>';

        // Iterate through each task and build HTML for the task tree
        foreach ($tasks as $task) {
            $start_from_task_id = $task['tat_start_from'];

            // If the task has no parent task, add it as a root node
            if ($start_from_task_id == null || !isset($task_map[$start_from_task_id])) {
                $html .= $this->build_subtree_html($task, $task_map);
            }
        }

        $html .= '</ul>';

        return $html;
    }

    // Recursive function to build HTML subtree for a task and its children
    private function build_subtree_html($task, $task_map) {
        $html = '<li>' . $task['task_name'];

        // Find children tasks and add them to the current task's subtree
        if (isset($task_map[$task['task_id']])) {
            $html .= '<ul>';
            foreach ($task_map as $child_task) {
                if ($child_task['tat_start_from'] == $task['task_id']) {
                    $html .= $this->build_subtree_html($child_task, $task_map);
                }
            }
            $html .= '</ul>';
        }

        $html .= '</li>';

        return $html;
    }

    function createTreeStructure()
    {
        $this->load->view('tree_structure/tree_structure');
    }


      public function CheckHolidaySkip() {

        $start_date='2024-03-19';
        $end_date='2024-04-18';

         $start_date = $this->increment_if_holiday($start_date);
         echo $start_date; exit;

        $days=$this->holidayInBetween($start_date,$end_date);

        $data = $this->adjustDateRange($start_date, $end_date);
      }


        
        function holidayInBetween($start,$end)
        {
            $rt=$this->db->select('id')->from('prestogroup_holiday')->where('holiday_date>=',$start)->where('holiday_date>=',$end)->get();
            return $rt->num_rows();

        }

        function increment_if_holiday($date)
        {

        if ($this->is_holiday($date)>0) {
            // If it's a holiday, increment the day and check again recursively
            $next_date = date('Y-m-d', strtotime($date . ' +1 day'));
            list($next_date) = $next_date;
            return $this->increment_if_holiday($next_date);
        } else {
            // If it's not a holiday, return the date
            return $date;
        }

        }

        function is_holiday($start)
        {
             $rt=$this->db->select('id')->from('prestogroup_holiday')->where('holiday_date',$start)->get();
            return $rt->num_rows();

        }

        function dfwisemis()
        {
            $this->load->view('MIS/df_wise_mis');
        }



        function detectCycleDFS($graph) {
    $visited = [];
    $stack = [];

    foreach ($graph as $node => $edges) {
        if (!isset($visited[$node])) {
            if ($this->dfs1($node, $graph, $visited, $stack)) {
                return true; // Cycle detected
            }
        }
    }
    return false; // No cycle detected
}

function dfs1($node, $graph, &$visited, &$stack) {
    // Mark the node as visited and add it to the recursion stack
    $visited[$node] = true;
    $stack[$node] = true;

    foreach ($graph[$node] as $neighbor) {
        // If the neighbor is in the recursion stack, a cycle is detected
        if (!empty($stack[$neighbor])) {
            return true;
        }
        // If the neighbor has not been visited, perform DFS on it
        if (!isset($visited[$neighbor])) {
            if ($this->dfs1($neighbor, $graph, $visited, $stack)) {
                return true;
            }
        }
    }
    // Remove the node from the recursion stack
    unset($stack[$node]);
    return false;
}


    function checktaskdependency()
    {
    $task=$this->getalltasks();
    //echo "<pre>"; print_r($task); exit;
    if($this->detectCycleDFS($task)){
    echo "Cycle detected in the tasks!";
    } else {
    echo "No cycles detected.";
    }
    }

    function getalltasks()
    {
        $t=array();
        $resty=$this->db->select('task_id')->from('task_management')->where('status',1)->get();
        if($resty->num_rows()>0)
        {
            foreach($resty->result() as $row)
            {
                $t[]=array($row->task_id=>$this->getDependent_task($row->task_id));
            }
        }

        return $t;
       // echo "<pre>"; print_r($t); exit;
    }

    function getDependent_task($id)
    {   $t=array();
        $resty=$this->db->select('task_id')->from('task_management')->where('tat_start_from',$id)->get();
        if($resty->num_rows()>0)
        {
            foreach($resty->result() as $row)
            {
                $t[]=$row->task_id;
            }
        }

        return $t;

    }


// Convert nested array to adjacency list
function convertToAdjacencyList($nestedArray) {
   $adjacencyList = [];

    foreach ($nestedArray as $entry) {
        foreach ($entry as $node => $dependencies) {
            if (!isset($adjacencyList[$node])) {
                $adjacencyList[$node] = [];
            }
            foreach ($dependencies as $dependency) {
                $adjacencyList[$node][] = $dependency;
            }
        }
    }

    return $adjacencyList;
}

function testdetect()
{
$nestedArray = [
    [1 => [2, 6, 67]],
    [2 => [3, 4, 8, 31, 68, 86]],
    [3 => [69]],
    [4 => []],
    [6 => []],
    [7 => []],
    [8 => []],
    [9 => [91]],
    [10 => []],
    [11 => [7, 52]],
    [12 => []],
    [13 => []],
    [14 => [15]],
    [15 => [16]],
    [16 => [17]],
    [17 => [18]],
    [18 => [19]],
    [19 => [20]],
    [20 => [21]],
    [21 => [88]],
    [22 => []],
    [23 => [24]],
    [24 => [45]],
    [25 => [26]],
    [26 => [27]],
    [27 => [28, 29, 111]],
    [28 => [32, 47]],
    [29 => [33]],
    [30 => [34]],
    [31 => [35]],
    [32 => []],
    [33 => []],
    [34 => []],
    [35 => []],
    [36 => [37]],
    [37 => [98]],
    [38 => []],
    [39 => [40]],
    [40 => [92]],
    [41 => [44]],
    [42 => []],
    [43 => []],
    [44 => [65, 83, 85]],
    [45 => [25, 46]],
    [46 => [54, 55, 56, 57, 58, 59, 60]],
    [47 => [42]],
    [48 => []],
    [49 => [12]],
    [50 => [11, 22, 51, 82]],
    [51 => [64, 70, 84, 90]],
    [52 => [41]],
    [53 => [13]],
    [54 => [9, 38, 43]],
    [55 => []],
    [56 => []],
    [57 => []],
    [58 => []],
    [59 => []],
    [60 => [61, 79]],
    [61 => [10, 39, 49]],
    [62 => [63]],
    [63 => []],
    [64 => []],
    [65 => [66]],
    [66 => [103]],
    [67 => []],
    [68 => []],
    [69 => []],
    [70 => [71]],
    [71 => []],
    [72 => []],
    [73 => []],
    [74 => []],
    [75 => [76]],
    [76 => [89, 101]],
    [77 => []],
    [78 => []],
    [79 => [80]],
    [80 => [81]],
    [81 => []],
    [82 => []],
    [83 => []],
    [84 => []],
    [85 => []],
    [86 => [36, 87]],
    [87 => [14, 72, 73, 74, 75]],
    [88 => [23, 62]],
    [89 => [30, 96]],
    [90 => [53]],
    [91 => [48]],
    [92 => [93, 95]],
    [93 => [94]],
    [94 => [50]],
    [95 => []],
    [96 => [78, 97]],
    [97 => [77]],
    [98 => [99]],
    [99 => [100]],
    [100 => []],
    [101 => [102]],
    [102 => []],
    [103 => []],
    [111 => [112]],
    [112 => []]
];

// Convert nested array to adjacency list
$graph = $this->convertToAdjacencyList($nestedArray);
// if ($this->detectCycleDFSNew($graph)) {
//     echo "Cycle detected in the tasks!";
// } else {
//     echo "No cycles detected.";
// }
$cycles = $this->detectCyclesAndTasks($graph);
if (!empty($cycles)) {
    echo "Cycles detected in the tasks:\n";
    foreach ($cycles as $cycle) {
        echo "Cycle: " . implode(" -> ", array_unique($cycle)) . "\n";
    }
} else {
    echo "No cycles detected.";
}


}

// Function to perform DFS and detect cycles
function detectCycleDFSNew($graph) {
    $visited = [];
    $stack = [];

    foreach ($graph as $node => $edges) {
        if (!isset($visited[$node])) {
            if ($this->dfsNew($node, $graph, $visited, $stack)) {
                return true; // Cycle detected
            }
        }
    }
    return false; // No cycle detected
}

function dfsNew($node, $graph, &$visited, &$stack, &$cycles, &$cycleNodes) {
    // Mark the node as visited and add it to the recursion stack
    $visited[$node] = true;
    $stack[$node] = true;

    foreach ($graph[$node] as $neighbor) {
        // If the neighbor is in the recursion stack, a cycle is detected
        if (isset($stack[$neighbor])) {
            // Collect nodes in the cycle
            $cycle = [$neighbor];
            foreach ($stack as $key => $value) {
                if ($key == $neighbor || $value) {
                    $cycle[] = $key;
                }
            }
            $cycles[] = $cycle;
        }
        // If the neighbor has not been visited, perform DFS on it
        if (!isset($visited[$neighbor])) {
            $this->dfsNew($neighbor, $graph, $visited, $stack, $cycles, $cycleNodes);
        }
    }

    // Remove the node from the recursion stack
    unset($stack[$node]);
}

function detectCyclesAndTasks($graph) {
    $visited = [];
    $stack = [];
    $cycles = [];
    $cycleNodes = [];

    foreach ($graph as $node => $edges) {
        if (!isset($visited[$node])) {
            $this->dfsNew($node, $graph, $visited, $stack, $cycles, $cycleNodes);
        }
    }
    
    return $cycles;
}


}