<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Traits\CommonMethods;

class UserController extends Controller
{
    use CommonMethods;

    public function show(Request $request, $userId)
    {
        $user = $request->user();
        $user = Auth::user();

        if($user && $userId == $user->id) {
            $return = [
                "code"  =>  $this->errorCodes("success"),
                'message' => 'Success!',
                'data'  =>  $user
            ];
            return response()->json($return);
        }

        $return = [
            "code"  =>  $this->errorCodes("failed"),
            'message' => 'User not found!'
        ];

        return response()->json($return, $this->errorCodes("failed"));
    }

    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $return = [ 'code'   =>  $this->errorCodes("failed") ];

        if(!$user){
            $return['message']  =   "User not found!";
            return response()->json( $return , $this->errorCodes("failed") );
        }

        $rules = [
            'old_password' => 'required|min:6',
            'password' => 'required|min:6'
        ];

        $validation = \Validator::make($request->all(), $rules);
        
        if ($validation->fails()) {
            $return['message']  =   $validation->messages()->all()[0];
            return response()->json( $return , $this->errorCodes("failed") );
        }

        if ((Hash::check($request->old_password, Auth::user()->password)) == false) {
            $return['message']  =   "Check your old password!";
            return response()->json( $return , $this->errorCodes("failed") );
        } else if ((Hash::check(request('password'), Auth::user()->password)) == true) {
            $return['message']  =   "Please enter a password which is not similar then current password.";
            return response()->json( $return , $this->errorCodes("failed") );
        }

        $user->password = bcrypt($request->password);
        $user->save();

        $return = [
            'message' => 'Success',
            "code"  =>  $this->errorCodes("success")
        ];

        return response()->json($return, 200);
    }
}
