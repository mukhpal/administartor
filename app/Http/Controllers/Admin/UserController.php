<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\CommonMethods;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = $request->query('filter');

        if (!empty($filter)) {
            $data = User::where('is_admin',NULL)->sortable()
                        ->where('users.first_name', 'like', '%'.$filter.'%')
                        ->paginate(5);
        } else {
            $data = User::where('is_admin',NULL)->sortable()->paginate(5);
        }

        return view('Admin/users/list')->with('data', $data)->with('filter', $filter);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit( $id )
    {
        $user   =   User::find($id);

        if(!$user)
            return redirect()->back()
                ->with('edit_user_error', 'User not found');

        return view('Admin/users/edit', ['user' => $user] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request )
    {
         request()->validate([
            'user' => 'required|integer|exists:users,id',
            'first_name' => 'required',
            'email' => 'required|email|unique:users,email,'. $request->user,
        ]);

        $user   =   User::find($request->user);

        $user->first_name   =   $request->first_name;
        $user->email        =   $request->email;
        $user->save();
    
        return redirect()->route('users')
                ->with('success','User details updated successfully');
    }
}
