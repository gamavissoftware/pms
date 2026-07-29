<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Graph_model extends CI_Model {

    public function __construct()
    {
        
        parent::__construct();

    }


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


     function print_subtree($subtree) {
        foreach ($subtree as $node) {
            echo '<li>' . $node['task_name'];
            if (isset($node['children']) && count($node['children']) > 0) {
                echo '<ul>';
                print_subtree($node['children']);
                echo '</ul>';
            }
            echo '</li>';
        }
    }

}