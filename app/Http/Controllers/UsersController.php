<?php

namespace App\Http\Controllers;

use App\Couple;
use App\User;
use App\Node;
use App\Pathfinding;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public $head;
    public $tail;
    public $listFamily1=[];
    public $listFamily2=[];
    public $root;
    public $listUser=[];

    //pathfinding
    public $listNode=[];
    public $start;
    public $target;
    public $rootNode;

    //handling
    public $isReversed = false;

    /**
     * Search user by keyword.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request, User $user)
    {
        $q = $request->get('q');
        $users = [];

        if ($q) {
            $users = User::with('father', 'mother')->where(function ($query) use ($q) {
                $query->where('name', 'like', '%'.$q.'%');
                $query->orWhere('nickname', 'like', '%'.$q.'%');
            })
            ->orderBy('name', 'asc')
            ->paginate(24);
        }

        return view('users.search', compact('users', 'user'));
    }

    /**
     * Display the specified User.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $usersMariageList = [];
        foreach ($user->couples as $spouse) {
            $usersMariageList[$spouse->pivot->id] = $user->name.' & '.$spouse->name;
        }

        $allMariageList = [];
        foreach (Couple::with('husband','wife')->get() as $couple) {
            $allMariageList[$couple->id] = $couple->husband->name.' & '.$couple->wife->name;
        }

        $malePersonList = User::where('gender_id', 1)->pluck('nickname', 'id');
        $femalePersonList = User::where('gender_id', 2)->pluck('nickname', 'id');

        return view('users.show', [
            'user' => $user,
            'usersMariageList' => $usersMariageList,
            'malePersonList' => $malePersonList,
            'femalePersonList' => $femalePersonList,
            'allMariageList' => $allMariageList
        ]);
    }

    /**
     * Display the user's family chart.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function chart(User $user)
    {
        $father = $user->father_id ? $user->father : null;
        $mother = $user->mother_id ? $user->mother : null;

        $fatherGrandpa = $father && $father->father_id ? $father->father : null;
        $fatherGrandma = $father && $father->mother_id ? $father->mother : null;

        $motherGrandpa = $mother && $mother->father_id ? $mother->father : null;
        $motherGrandma = $mother && $mother->mother_id ? $mother->mother : null;

        $childs = $user->childs;
        $colspan = $childs->count();
        $colspan = $colspan < 4 ? 4 : $colspan;

        $siblings = $user->siblings();
        return view('users.chart', compact('user', 'childs', 'father', 'mother', 'fatherGrandpa', 'fatherGrandma', 'motherGrandpa', 'motherGrandma', 'siblings', 'colspan'));
    }

    /**
     * Show user family tree
     * @param  User   $user
     * @return \Illuminate\Http\Response
     */
    public function tree(User $user)
    {
        // dd($user->h + 2);
        return view('users.tree', compact('user'));
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $this->authorize('edit', $user);

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified User in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'nickname'  => 'required|string|max:255',
            'name'      => 'required|string|max:255',
            'gender_id' => 'required|numeric',
            'dob'       => 'nullable|date|date_format:Y-m-d',
            'dod'       => 'nullable|date|date_format:Y-m-d',
            'phone'     => 'nullable|string|max:255',
            'address'   => 'nullable|string|max:255',
            'email'     => 'nullable|string|max:255',
            'password'  => 'nullable|min:6|max:15',
        ]);

        

        $user->nickname = $request->nickname;
        $user->name = $request->get('name');
        $user->gender_id = $request->get('gender_id');
        $user->dob = $request->get('dob');
        $user->dod = $request->get('dod');

        $user->phone = $request->get('phone');
        $user->address = $request->get('address');
        $user->email = $request->get('email');

        if ($request->get('email')) {
            $user->password = bcrypt($request->get('password'));
        }

        $user->save();

        return redirect()->route('users.show', $user->id);
    }

    /**
     * Remove the specified User from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function bfs(Request $request, User $user)
    {
        $h = $request->get('head');
        $t = $request->get('tail');
        $pathfinding = new Pathfinding();

        if ($h and $t) {
            $u1=User::where('nickname', $h)->orWhere('name', $h)->exists();
            $u2=User::where('nickname', $t)->orWhere('name', $t)->exists();

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

                    $pathfinding->listNode = $this->listNode;
                    $pathfinding->start = $this->start;
                    $pathfinding->target = $this->target;
                    $pathfinding->root = $this->rootNode;
                    $pathfinding->isReversed = $this->isReversed;
                    $pathfinding->getHeuristic();
                }
            }
        }
        $lno = $pathfinding->pathList;
        return view('users.bfs', compact('users', 'user', 'u1', 'u2', 'lno', 'users1', 'users2'));
    }

    public function searchRoot(User $user){
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

    public function searchRoot2(User $user){
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

    public function matchRoot(){
        foreach ($this->listFamily1 as $value1) {
            foreach ($this->listFamily2 as $value2) {
                if ($value1 == $value2) {
                    $this->root = $value1;
                }
            }
        }
    }

     public function buildGraph(User $user, int $level){
        $user->level = $level;
        $this->listUser = array_prepend($this->listUser, $user);
        if ($user->childs->count() > 0){
            $level++;
            foreach($user->childs as $child){
                $this->buildGraph($child, $level);
            }
        }
    }

    public function defineGraph () {
        for ($i = 0; $i < count($this->listUser); $i++){
            $this->listNode = array_prepend($this->listNode, new Node());
        }
        for ($i = 0; $i < count($this->listUser); $i++){   
            $this->listNode[$i]->setId($this->listUser[$i]->id, $this->listUser[$i]->level, $this->listUser[$i]->name);
        }
    }

    public function defineNeighbor(){
        foreach($this->listNode as $value){
            if($value->id == $this->head->id){
                $this->start = $value;
            }
            if($value->id == $this->tail->id){
                $this->target = $value;
            }
            if($value->id == $this->root->id) {
                $this->rootNode = $value;
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
}
