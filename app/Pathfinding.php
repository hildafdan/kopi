<?php

use App\Node;

namespace App;

class Pathfinding
{
    public $listNode = [];
    public $start;
    public $target;
    public $root;
    public $pathList = [];
    public $isSearching = true;

    public function getHeuristic () {
        for ($i = 0; $i < count($this->listNode); $i++){
            if($this->listNode[$i]->id != $this->target->id){
                $this->countHeuristic($this->listNode[$i], $this->target);
            }
        }
       $this->BFS($this->start, $this->target);
    }

    function countHeuristic(Node $current, Node $end){
        $biggest = new Node();
        $targetLevel = new Node();

        if ($current->level != $end->level) {
            if ($current->level < $end->level) {
                $biggest = $end;
                $targetLevel = $current;
            }
            elseif ($current->level > $end->level) {
                $biggest = $current;
                $targetLevel = $end;
            }
            while ($biggest->level > $targetLevel->level) {
                $neighbors = $biggest->listNeighbors;
                foreach ($neighbors as $value) {
                    if($value->level < $biggest->level){
                        $biggest = $value;
                        break;
                    }
                }
            }
        }
        else {
            $biggest = $end;
            $targetLevel = $current;            
        }
        // echo $biggest->name.",".$targetLevel->name;
        $root0 = new Node();
        $level = $biggest->level;
        for ($i = 0; $i <= $level; $i++) {
            if($biggest->id == $targetLevel->id){
                $root0 = $biggest;
                break;
            }
            else {
                $neighbors = $biggest->listNeighbors;
                foreach ($neighbors as $value) {
                    if($value->level < $biggest->level){
                        $biggest = $value;
                    }
                }
                $neighbors = $targetLevel->listNeighbors;
                foreach ($neighbors as $value) {
                    if($value->level < $targetLevel->level){
                        $targetLevel = $value;
                    }
                }
            }
        }
        $current->heuristic = ($current->level) - ($root0->level) + ($end->level) - ($root0->level);
        // echo $root0->level.",".$current->name.",".$end->name."=".$current->heuristic."<br>";
    }

    function BFS (Node $s, Node $t) {
        $openList=[];
        $openList = array_prepend($openList, $s);
        $closeList=[];
        $remove = 0;
        
        while ($openList != null) {            
            $temp = new Node ();
            $temp->heuristic = 999;
            $index = 0;
            foreach ($openList as $open){
                $index++;
                if($open->heuristic < $temp->heuristic){
                    $temp = $open;
                    $remove = $index;
                }
            }
            $current = $temp;

            if($current->id == $t->id){
                $this->isSearching = false;
                $openList = null;
                break;
            }
            $cobacoba = $current->listNeighbors;
            foreach( $cobacoba as $neighbor){
                if (!in_array($neighbor, $closeList) && !in_array($neighbor, $openList)){
                    $openList = array_prepend($openList, $neighbor);
                    $neighbor->prev = $current;                
                }
            }
            $closeList = array_prepend($closeList, $current);
            unset($openList[$remove]);

        } 

        if(!$this->isSearching){
            $this->TrackBack();
        }
    }

    public function TrackBack () {
        $this->pathList = [];
        $temp = $this->target;
        $this->pathList = array_prepend($this->pathList, $temp);

        while ($temp->prev != null) {
            $this->pathList = array_prepend($this->pathList, $temp->prev);
            $temp = $temp->prev;
        }
    }
}