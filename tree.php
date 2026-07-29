<?php 
class TreeNode {
    public $id;
    public $name;
    public $children = [];

    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function addChild(TreeNode $child) {
        $this->children[] = $child;
    }

    public function hasChildren() {
        return !empty($this->children);
    }

    public function printTree($indent = 0) {
        echo str_repeat("&nbsp;&nbsp;", $indent) . $this->name . "<br>";
        foreach ($this->children as $child) {
            $child->printTree($indent + 1);
        }
    }
}

// Create the tree structure
$root = new TreeNode(1, "Root");

$sub1 = new TreeNode(2, "Sublevel 1");
$sub2 = new TreeNode(3, "Sublevel 2");
$sub3 = new TreeNode(4, "Sublevel 3");

$sub11 = new TreeNode(5, "Sublevel 1.1");
$sub12 = new TreeNode(6, "Sublevel 1.2");

$sub21 = new TreeNode(7, "Sublevel 2.1");

$sub1->addChild($sub11);
$sub1->addChild($sub12);

$sub2->addChild($sub21);

$root->addChild($sub1);
$root->addChild($sub2);
$root->addChild($sub3);

// Print the tree structure
$root->printTree();

?>