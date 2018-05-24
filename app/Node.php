<?php

namespace App;

class Node
{
    public $id = 0;
    public $name = "";
    public $level = 0;
    public $listNeighbors = [];
    public $heuristic = 0;
    public $prev;
    public $status = "";

    public function setId (int $id, int $level, string $name){
        $this->id = $id;
        $this->level = $level;
        $this->name = $name;
    }
    
    public function setHeuristic ($h){
        $this->heuristic = $h;
    }

    public function addNeighbor (Node $neighbor){
        $this->listNeighbors = array_prepend($this->listNeighbors, $neighbor);
    }
}
