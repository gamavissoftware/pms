<?php 
$CI =& get_instance();
$CI->load->model('Graph_model');
$tasks=$CI->Graph_model->get_sorted_tasks();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Task Tree Chart</title>
    <style>
         .tree {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }
    .tree li {
        padding-left: 20px;
        position: relative;
    }
    .tree li::before {
        content: '';
        position: absolute;
        top: 0;
        left: -10px;
        border-left: 1px solid #000;
        height: 100%;
    }
    .tree li:first-child::before {
        border: none;
    }
    .tree li:last-child::before {
        height: 50%;
    }
    </style>
</head>
<body>
    <ul class="tree">
    <?php
    foreach ($tasks as $task) {
        echo '<li>' . $task['task_name'];
        if (isset($task['children']) && count($task['children']) > 0) {
            echo '<ul>';
            $CI->Graph_model->print_subtree($task['children']);
            echo '</ul>';
        }
        echo '</li>';
    }    
    ?>
</ul>

   
   
</body>
</html>