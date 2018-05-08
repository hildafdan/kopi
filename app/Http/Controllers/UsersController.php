<?php

namespace App\Http\Controllers;

use App\Couple;
use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public $head;
    public $tail;
    public $listFamily1=[];
    public $listFamily2=[];
    public $root;
    public $listNode=[];

    /**
     * Search user by keyword.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
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

        return view('users.search', compact('users'));
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
            'city'      => 'nullable|string|max:255',
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
        $user->city = $request->get('city');
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

    public function bfs(Request $request)
    {
        $h = $request->get('head');
        $t = $request->get('tail');

        if ($h and $t) {
            $users1=User::where('nickname', '=', $h)->get();
            $this->searchRoot($users1[0]);
            $this->head = $users1[0];

            echo "<br>";

            $users2=User::where('nickname', '=', $t)->get();
            $this->searchRoot2($users2[0]);
            $this->tail=$users2[0];

            $this->matchRoot();
            $userRoot = User::where('nickname', '=', $this->root)->get();
            $this->buildGraph($userRoot[0]);
            $this->defineNeighbor();
            // $this->getHeuristic();
            $this->pathFinding();
        }
        // dd($this->listFamily1, $this->listFamily2);


        return view('users.bfs', compact('users'));
    }

    public function searchRoot(User $user){
        // $users[];
        // array_push($listKeluarga,[$user->name,$user->father_id,$user->mother_id]);
        $this->listFamily1 = array_prepend($this->listFamily1, $user->name);
        // echo $user->name." -- ";
        
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
        // $users[];
        // array_push($listKeluarga,[$user->name,$user->father_id,$user->mother_id]);
        $this->listFamily2 = array_prepend($this->listFamily2, $user->name);
        // echo $user->name." -- ";
        
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

    public function buildGraph(User $user){
        $this->listNode = array_prepend($this->listNode, $user);
        if ($user->childs->count() > 0){
            foreach($user->childs as $child){
                $this->buildGraph($child);
            }
        }
    }

    public function defineNeighbor(){
        foreach($this->listNode as $value){
            if($value->childs->count() > 0){
                foreach ($value->childs as $child) {
                    $value->listNeighbors = array_prepend($value->listNeighbors, $child);
                }
            }
        }

        foreach ($this->listNode as $value) {
            if($value->father_id != null) {
                foreach($this->listNode as $node) {
                    if ($node->id == $value->father_id) {
                        $value->listNeighbors = array_prepend($value->listNeighbors, $node);
                    }
                }
            }
            if($value->mother_id != null) {
                foreach($this->listNode as $node) {
                    if ($node->id == $value->mother_id) {
                        $value->listNeighbors = array_prepend($value->listNeighbors, $node);
                    }
                }
            }
        }

        foreach ($this->listNode as $value) {
            echo $value->name, " -- ";
            // $this->tail = $value;
            foreach($value->listNeighbors as $anak) {
                echo $anak->name, " ++ ";
            }
            echo "<br>";
        }
        // dd($this->listNode);
    }

    // public function getHeuristic(){
    //     foreach ($this->listNode as $end) {
    //         if($end->id != $this->tail->id) {
    //             foreach ($this->listNode as $value) {
    //                 if($value->id != $this->tail->id) {
    //                     $this->calHeuristic($value, $end);
    //                 }
    //             }
    //         }
    //     }
    //     foreach ($this->listNode as $value) {
    //         echo $value->name, "  ", $value->heuristic, "<br>";
    //     }
    //     // dd(end($this->listNode));
    // }

    // public function calHeuristic(User $current, User $end) {
    //     $isFound=0;
    //     $current->heuristic+=1;
    //     foreach($end->listNeighbors as $neighbor) {
    //         if($neighbor->id == $current->id) {
    //             $isFound=1;
    //         }
    //     }
    //     if ($isFound==0) {
    //         foreach ($end->listNeighbors as $neighbor) {
    //             $this->calHeuristic($current, $neighbor);   
    //         }
    //     }
    // }

    public function pathFinding (){
        // $start;
        // $finish;
        // $temp;
        // foreach($this->listNode as $node) {
        //     if($node->id == $this->head->id){
        //         $start = $node;
        //         $temp = $node;
        //     }
        //     elseif ($node->id == $this->tail->id) {
        //         $finish = $node;
        //     }                
        // }
        // $openList=[];
        // $openList = array_prepend($openList, $start);
        // $closeList=[];
        // while($openList != null){
        //     $temp->cost = 999;
        //     $index = 0;
        //     $remove = 0;
        //     foreach($openList as $node){
        //         $index++;
        //         if($node->cost < $temp->cost){
        //             $temp=$node;
        //             $remove=$index;
        //         }
        //     }
        //     $current = $temp;

        //     if($current->id == $finish->id){
        //         echo "ketemu";
        //     }

        //     $closeList = array_prepend($closeList, $current);
            // unset($openList[$remove]);

            // foreach($current->listNeighbors as $neighbor){
            //     // if(in_array($neighbor, $closeList)){
            //     //     continue;
            //     // }
            //     $tempG = $current->cost + 1;
            //     // if(in_array($neighbor, $openList) == false){
            //     //     $openList = array_prepend($openList, $neighbor);
            //     // }
            //     // elseif($tempG >= $neighbor->cost) {
            //     //     continue;
            //     // }
            //     $neighbor->cost = $tempG;
            //     $neighbor->prev = $current;
            // }
        //}
    }
}
