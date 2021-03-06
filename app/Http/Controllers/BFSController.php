<?php

namespace App\Http\Controllers;

use App\User;
use App\Node;
use Illuminate\Http\Request;

class BFSController extends Controller
{
    public $head;
    public $tail;
    public $listFamily1=[];
    public $listFamily2=[];
    public $root;
    public $listUser=[];

    //BFSProcess
    public $listNode=[];
    public $start;
    public $target;
    public $pathList = [];
    public $isSearching = true;

    //Temporario
    public $tempRoot;

    public function index(Request $request, User $user)
    {
        $h = $request->get('head');
        $t = $request->get('tail');
        $u1=User::where('nickname', $h)->orWhere('name', $h)->exists();
        $u2=User::where('nickname', $t)->orWhere('name', $t)->exists();
        $begin = microtime(true);
        if ($h and $t) {   
            if ($h != $t){
                if($u1 and $u2) {
                    $users1=User::where('nickname', '=', $h)->orWhere('name', '=', $h)->get();
                    $users2=User::where('nickname', '=', $t)->orWhere('name', '=', $t)->get();
                    
                    $this->searchRoot($users1[0]);
                    $this->head = $users1[0];
                    // echo $users1[0]->name;
                    
                    // echo $this->listFamily1;

                    $this->searchRoot2($users2[0]);
                    $this->tail=$users2[0];
                    // echo $this->listFamily2;

                    $this->matchRoot();
                    // echo $this->root->name;

                    if($this->root != null) {
                        $this->buildGraph($this->root, 0);

                        $this->defineGraph();   
                        $this->defineNeighbor();
                        $this->getHeuristic();
                        $this->bfs($this->start, $this->target);
                        $this->trackStatus();
                    }
                }
            }
        }
        $finish = microtime(true);
        $totaltime = $finish - $begin;
        $totaltime_format = number_format($totaltime,3);

        $checkRoot = $this->root;
        $trackResult = $this->pathList;
        return view('users.bfs', compact('users', 'user', 'u1', 'u2', 'trackResult', 'checkRoot', 'users1', 'users2', 'totaltime_format'));
    }

    public function searchRoot(User $user)
    {
        $this->listFamily1 = array_prepend($this->listFamily1, $user);
        if($user->father_id!=null){
            $user1 = $user->where('id', $user->father_id)->get();
            $this->searchRoot($user1[0]);
        }
        if($user->mother_id!=null){
            $user2 = $user->where('id', $user->mother_id)->get();
            $this->searchRoot($user2[0]);
        }
    }

    public function searchRoot2(User $user)
    {
        $this->listFamily2 = array_prepend($this->listFamily2, $user);
        if($user->father_id!=null){
            $user1 = $user->where('id', $user->father_id)->get();
            $this->searchRoot2($user1[0]);
        }
        if($user->mother_id!=null){
            $user2 = $user->where('id', $user->mother_id)->get();
            $this->searchRoot2($user2[0]);
        }
    }

    public function matchRoot()
    {
        foreach ($this->listFamily1 as $value1) {
            foreach ($this->listFamily2 as $value2) {
                if ($value1 == $value2) {
                    $this->root = $value1;
                }
            }
        }

    }

    public function buildGraph(User $user, int $level)
    {
        $user->level = $level;
        $this->listUser = array_prepend($this->listUser, $user);
        if ($this->root->id == $this->tail->id or $this->root->id == $this->head->id) {
            if ($user->childs->count() > 0){
                $level++;
                foreach($user->childs as $child){
                    $this->buildGraph($child, $level);
                }
            }
        }
        else {
            if($user->name != $this->head->name){
                if($user->name != $this->tail->name) {
                    if ($user->childs->count() > 0){
                        $level++;
                        foreach($user->childs as $child){
                            if($child->mother_id != $this->head->id && $child->mother_id != $this->tail->id)
                                if($child->father_id != $this->head->id && $child->father_id != $this->tail->id)
                                    $this->buildGraph($child, $level);
                        }
                    }
                 }
            }
        }
    }

    public function defineGraph () 
    {
        for ($i = 0; $i < count($this->listUser); $i++){
            $this->listNode = array_prepend($this->listNode, new Node());
        }
        for ($i = 0; $i < count($this->listUser); $i++){   
            $this->listNode[$i]->setNode($this->listUser[$i]->id, $this->listUser[$i]->level, $this->listUser[$i]->name, $this->listUser[$i]->gender_id);
        }
    }

    public function defineNeighbor()
    {
        foreach($this->listNode as $value){
            if($value->id == $this->head->id){
                $this->start = $value;
            }
            if($value->id == $this->tail->id){
                $this->target = $value;
            }
        }

        for ($i = 0; $i < count($this->listUser); $i++){
            if($this->listUser[$i]->childs->count() > 0){
                for ($j = 0; $j < count($this->listUser[$i]->childs); $j++){
                    foreach ($this->listNode as $key) {
                        if($key->id == $this->listUser[$i]->childs[$j]->id){
                            $this->listNode[$i]->addNeighbor($key);
                        }
                    }
                }
            }
        }
        for ($i = 0; $i < count($this->listUser); $i++){
            if($this->listUser[$i]->father_id != null) {
                for ($j = 0; $j < count($this->listUser); $j++){
                    if ($this->listUser[$i]->father_id == $this->listUser[$j]->id) {
                        $this->listNode[$i]->addNeighbor($this->listNode[$j]);
                    }
                }
            }
        }
        for ($i = 0; $i < count($this->listUser); $i++){
            if($this->listUser[$i]->mother_id != null) {
                for ($j = 0; $j < count($this->listUser); $j++){
                    if ($this->listUser[$i]->mother_id == $this->listUser[$j]->id) {
                        $this->listNode[$i]->addNeighbor($this->listNode[$j]);
                    }
                }
            }
        }
    }

    public function getHeuristic () 
    {
        for ($i = 0; $i < count($this->listNode); $i++){
            if($this->listNode[$i]->id != $this->target->id){
                $this->countHeuristic($this->listNode[$i], $this->target);
            }
        }
    }

    public function countHeuristic(Node $current, Node $end)
    {
        $big = new Node();
        $small = new Node();

        if ($current->level != $end->level) {
            if ($current->level < $end->level) {
                $big = $end;
                $small = $current;
            }
            elseif ($current->level > $end->level) {
                $big = $current;
                $small = $end;
            }
            while ($big->level > $small->level) {
                $neighbors = $big->listNeighbors;
                foreach ($neighbors as $value) {
                    if($value->level < $big->level){
                        $big = $value;
                        break;
                    }
                }
            }
        }
        else {
            $big = $end;
            $small = $current;            
        }
        // echo $big->name.",".$small->name;

        $rootNode = new Node();
        $level = $big->level;
        for ($i = 0; $i <= $level; $i++) {
            if($big->id == $small->id){
                $rootNode = $big;
                break;
            }
            else {
                $neighbors = $big->listNeighbors;
                foreach ($neighbors as $value) {
                    if($value->level < $big->level){
                        $big = $value;
                    }
                }
                $neighbors = $small->listNeighbors;
                foreach ($neighbors as $value) {
                    if($value->level < $small->level){
                        $small = $value;
                    }
                }
            }
        }
        $current->heuristic = ($current->level) - ($rootNode->level) + ($end->level) - ($rootNode->level);
        // echo $rootNode->level.",".$current->name.",".$end->name."=".$current->heuristic."<br>";

        
        if ($current->id == $this->head->id && $end->id == $this->tail->id){
            $this->tempRoot = $rootNode;
        }
    }

    public function bfs (Node $s, Node $t) 
    {
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
            $neighbors = $current->listNeighbors;

            foreach( $neighbors as $neighbor){
                if (!in_array($neighbor, $closeList) && !in_array($neighbor, $openList)){
                    $openList = array_prepend($openList, $neighbor);
                    $neighbor->prev = $current;                
                }
            }
            $closeList = array_prepend($closeList, $current);
            unset($openList[$remove]);
        } 

        if(!$this->isSearching){
            $this->trackBack();
        }
    }

    public function trackBack () 
    {
        $this->pathList = [];
        $temp = $this->target;
        $this->pathList = array_prepend($this->pathList, $temp);

        while ($temp->prev != null) {
            $this->pathList = array_prepend($this->pathList, $temp->prev);
            $temp = $temp->prev;
        }

    }

    public function trackStatus () {
        $temp = $this->target;
        $rootPassed = false;
        while ($temp->prev != null) {
            if (!$rootPassed) {
                if ($this->target->level > $temp->prev->level) {
                    if ($this->target->level - $temp->prev->level == 1) {
                        if($temp->prev->gender_id == 1)
                            $temp->prev->status = trans('status.ayah');
                        else
                            $temp->prev->status = trans('status.ibu');
                    }
                    else if ($this->target->level - $temp->prev->level == 2) {
                        if($temp->prev->gender_id == 1)
                            $temp->prev->status = trans('status.kakek');
                        else
                            $temp->prev->status = trans('status.nenek');
                    }
                    else if ($this->target->level - $temp->prev->level == 3) {
                        $temp->prev->status = trans('status.buyut');  
                    }
                    else if ($this->target->level - $temp->prev->level == 4) {
                        $temp->prev->status = trans('status.bao');  
                    }
                    else if ($this->target->level - $temp->prev->level == 5) {
                        $temp->prev->status = trans('status.janggawareng'); 
                        // $rootPassed = true; 
                    }
                    else if ($this->target->level - $temp->prev->level == 6) {
                        $temp->prev->status = trans('status.udeg-udeg');  
                    }
                    else if ($this->target->level - $temp->prev->level == 7) {
                        $temp->prev->status = trans('status.kakaitsiwur');  
                    }
                    else {
                        $temp->prev->status = trans('status.sesepuh');  
                    }
                }
                else if ($this->target->level < $temp->prev->level) {
                    if ($temp->prev->level - $this->target->level == 1) {
                        $temp->prev->status = trans('status.anak');
                    }
                    else if ($temp->prev->level - $this->target->level == 2) {
                        $temp->prev->status = trans('status.cucu');
                    }
                    else if ($temp->prev->level - $this->target->level == 3) {
                        $temp->prev->status = trans('status.cicit'); 
                    }
                    else if ($temp->prev->level - $this->target->level == 4) {
                        $temp->prev->status = trans('status.bao'); 
                    }
                    else if ($temp->prev->level - $this->target->level == 5) {
                        $temp->prev->status = trans('status.janggawareng');  
                    }
                    else if ($temp->prev->level - $this->target->level == 6) {
                        $temp->prev->status = trans('status.udeg-udeg');  
                    }
                    else if ($temp->prev->level - $this->target->level == 7) {
                        $temp->prev->status = trans('status.kakaitsiwur'); 
                    }
                    else {
                        $temp->prev->status = trans('status.turunan');  
                    }
                }
                else $rootPassed = true;   
            }
            else{
                if ($this->target->level == $temp->prev->level) {
                    if($temp->prev->heuristic == 2){
                        $temp->prev->status = trans('status.kakak/adik');
                    }
                    else {
                        $temp->prev->status = trans('status.sepupu') ;
                    }
                }
                else if ($this->target->level > $temp->prev->level) {
                    if ($this->target->level - $temp->prev->level == 1) {
                        if($temp->prev->gender_id == 1)
                            $temp->prev->status = trans('status.om/paman');
                        else
                            $temp->prev->status = trans('status.tante/bibi');
                    }
                    else if ($this->target->level - $temp->prev->level == 2) {
                        if($temp->prev->gender_id == 1)
                            $temp->prev->status = trans('status.kakek2');
                        else
                            $temp->prev->status = trans('status.nenek2');
                    }
                    else if ($this->target->level - $temp->prev->level == 3) {
                        $temp->prev->status = trans('status.buyut2');  
                    }
                    else if ($this->target->level - $temp->prev->level == 4) {
                        $temp->prev->status = trans('status.bao2'); 
                    }
                    else if ($this->target->level - $temp->prev->level == 5) {
                        $temp->prev->status = trans('status.janggawareng2'); 
                    }
                    else if ($this->target->level - $temp->prev->level == 6) {
                        $temp->prev->status = trans('status.udeg-udeg2'); 
                    }
                    else if ($this->target->level - $temp->prev->level == 7) {
                        $temp->prev->status = trans('status.kakaitsiwur2'); 
                    }
                    else {
                        $temp->prev->status = trans('status.sesepuh');
                    }
                }
                else if ($this->target->level < $temp->prev->level) {
                    if ($temp->prev->level - $this->target->level == 1) {
                        $temp->prev->status = trans('status.keponakan');
                    }
                    else if ($temp->prev->level - $this->target->level == 2) {
                        $temp->prev->status = trans('status.cucu3');
                    }
                    else if ($temp->prev->level - $this->target->level == 3) {
                        $temp->prev->status = trans('status.cicit3');  
                    }
                    else if ($temp->prev->level - $this->target->level == 4) {
                        $temp->prev->status = trans('status.bao3');  
                    }
                    else if ($temp->prev->level - $this->target->level == 5) {
                        $temp->prev->status = trans('status.janggawareng3');  
                    }
                    else if ($temp->prev->level - $this->target->level == 6) {
                        $temp->prev->status = trans('status.udeg-udeg3');  
                    }
                    else if ($temp->prev->level - $this->target->level == 7) {
                        $temp->prev->status = trans('status.kakaitsiwur3');  
                    }
                    else  {
                        $temp->prev->status = trans('status.turunan'); 
                    }
                }   
            }
            if ($temp->prev->level == $this->tempRoot->level) {
                $rootPassed = true;
            }
            $temp = $temp->prev;
        }
    }
}
