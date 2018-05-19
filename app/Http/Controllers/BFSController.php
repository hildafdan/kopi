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

    public function index(Request $request, User $user)
    {
        $h = $request->get('head');
        $t = $request->get('tail');
        $u1=User::where('nickname', $h)->orWhere('name', $h)->exists();
        $u2=User::where('nickname', $t)->orWhere('name', $t)->exists();
		$begin = microtime(true);

        if ($h and $t) {
            if($u1 and $u2) {
                $users1=User::where('nickname', '=', $h)->orWhere('name', '=', $h)->get();
                $users2=User::where('nickname', '=', $t)->orWhere('name', '=', $t)->get();
                
                $this->searchRoot($users1[0]);
                $this->head = $users1[0];
                $this->searchRoot2($users2[0]);
                $this->tail=$users2[0];
                $this->matchRoot();
                
                if($this->root != null) {
                    $this->buildGraph($this->root, 0);
                    $this->defineGraph();   
                    $this->defineNeighbor();
                    $this->getHeuristic();
                    $this->bfs($this->start, $this->target);
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
        if ($user->childs->count() > 0){
            $level++;
            foreach($user->childs as $child){
                $this->buildGraph($child, $level);
            }
        }
    }

    public function defineGraph () 
    {
        for ($i = 0; $i < count($this->listUser); $i++){
            $this->listNode = array_prepend($this->listNode, new Node());
        }
        for ($i = 0; $i < count($this->listUser); $i++){   
            $this->listNode[$i]->setId($this->listUser[$i]->id, $this->listUser[$i]->level, $this->listUser[$i]->name);
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
}
