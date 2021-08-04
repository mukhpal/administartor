<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\CommonMethods;
use App\Rules\MatchOldPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\File;

class ProfileController extends Controller
{
    use CommonMethods;

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
     * Show Admin profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profileData()
    {
        $user = Auth::user();
        $user->profile_pic    =   url($user->folder.$user->profile_pic);
        return view('Admin/profile', $user);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function ChangePass(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);
   
        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
   
        return redirect()->back()
        ->with('pass_success', 'Password changed successfully.');
    }

    
    public function ChangeProfileImg (Request $request)
    {
        // $request->validate([
        //     'profile_pic' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
        // ]);

        $rules = [
            'profile_pic' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
        ];

        $validation = \Validator::make($request->all(), $rules);
            
        if ($validation->fails()) {
            return redirect()->back()
                ->with('error', $validation->messages()->all()[0]);
        }

        $user   =   Auth()->user();

        $uniqueid=uniqid();
        $original_name=$request->file('profile_pic')->getClientOriginalName();
        $size=$request->file('profile_pic')->getSize();
        $extension=$request->file('profile_pic')->getClientOriginalExtension();
        $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
        $path=$request->file('profile_pic')->storeAs('public/uploads/profile_pic/',$filename);
        if( $user->profile_pic ){
            Storage::delete('public/uploads/profile_pic/'.$user->profile_pic);
        }

        $user->profile_pic = $filename;
        $user->folder = SELF::$PROFILE_PIC;
        $user->save();

        return redirect()->back()
            ->with('pp_success', 'Profile image uploaded successfully.');
    }
}
